<?php
/***************************************************************************
*
*   This program is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation; either version 2 of the License, or
*   (at your option) any later version.
*
*   Portions of this program are derived from publicly licensed software
*   projects including, but not limited to phpBB, Magelo Clone,
*   EQEmulator, EQEditor, and Allakhazam Clone.
*
*                                  Author:
*                                   Nite
*
*   September 21, 2025 - Nite
*      - Implement
*
***************************************************************************/




if ( !defined('INCHARBROWSER') )
{
die("Hacking attempt");
}

include_once(__DIR__ . "/include/common.php");
include_once(__DIR__ . "/include/language.php");
include_once(__DIR__ . "/include/config.php");
include_once(__DIR__ . "/include/db.php");
include_once(__DIR__ . "/include/profile.php");

function OwnerCheck($charID) {
    global $cbsql;
    global $defaultedlocalhost;
    global $localipaddress;
    global $defaultgateway;
    global $publicip;
    $userip = getIPAddress();

    if (
        $userip == $defaultedlocalhost ||
        $userip == $localipaddress ||
        $userip == $defaultgateway ||
        $userip == $publicip
    ) {
        return true;
    }

    $tpl =
    <<<TPL
        SELECT ai.ip as ip
        FROM character_data cd
        INNER JOIN account_ip AI on ai.accid = cd.account_id
        WHERE cd.id = $charID
        ORDER BY ai.lastused DESC
        LIMIT 1
    TPL;

    $result = $cbsql->query($tpl);
    $bots = $cbsql->fetch_all($result);

    foreach($bots as $bot) {
        if ($bot['ip'] == $userip) {
            return true;
        }
    }

    return false;
}

function IsCasterClass($class_id): bool {
    switch ($class_id) {
        case CB_CLASS_CLERIC:
        case CB_CLASS_DRUID:
        case CB_CLASS_SHAMAN:
        case CB_CLASS_NECROMANCER:
        case CB_CLASS_WIZARD:
        case CB_CLASS_MAGICIAN:
        case CB_CLASS_ENCHANTER:
            return true;
        default:
            break;
    }

    return false;
}

function IsPureMeleeClass($class_id): bool {
    switch ($class_id) {
        case CB_CLASS_WARRIOR:
        case CB_CLASS_MONK:
        case CB_CLASS_ROGUE:
        case CB_CLASS_BERSERKER:
            return true;
        default:
            break;
    }

    return false;
}

function IsClientBotSettingCategory($category_id): bool {
    switch ($category_id) {
        case BotSettingCategories::SPELL_DELAY:
        case BotSettingCategories::SPELL_MAX_THRESHOLD:
        case BotSettingCategories::SPELL_MIN_THRESHOLD:
            return true;
        default:
            return false;
    }
    return false;
}

function IsClientBotSpellType($spell_type): bool {
    switch ($spell_type) {
        case BotSpellTypes::RegularHeal:
        case BotSpellTypes::CompleteHeal:
        case BotSpellTypes::GroupCompleteHeals:
        case BotSpellTypes::FastHeals:
        case BotSpellTypes::VeryFastHeals:
        case BotSpellTypes::GroupHeals:
        case BotSpellTypes::GroupHoTHeals:
        case BotSpellTypes::HoTHeals:
        case BotSpellTypes::PetRegularHeals:
        case BotSpellTypes::PetCompleteHeals:
        case BotSpellTypes::PetFastHeals:
        case BotSpellTypes::PetVeryFastHeals:
        case BotSpellTypes::PetHoTHeals:
        case BotSpellTypes::Buff:
        case BotSpellTypes::Cure:
        case BotSpellTypes::GroupCures:
        case BotSpellTypes::PetCures:
        case BotSpellTypes::DamageShields:
        case BotSpellTypes::PetDamageShields:
        case BotSpellTypes::PetBuffs:
        case BotSpellTypes::ResistBuffs:
        case BotSpellTypes::PetResistBuffs:
            return true;
        default:
            return false;
    }

    return false;
}

function GetDefaultClientSpellTypeDelay($spell_type): int {
    switch ($spell_type) {
        case BotSpellTypes::VeryFastHeals:
        case BotSpellTypes::PetVeryFastHeals:
            return 1500;
        case BotSpellTypes::FastHeals:
        case BotSpellTypes::PetFastHeals:
            return 2500;
        case BotSpellTypes::GroupHeals:
        case BotSpellTypes::RegularHeal:
        case BotSpellTypes::PetRegularHeals:
            return 4000;
        case BotSpellTypes::CompleteHeal:
        case BotSpellTypes::GroupCompleteHeals:
        case BotSpellTypes::PetCompleteHeals:
            return 8000;
        case BotSpellTypes::GroupHoTHeals:
        case BotSpellTypes::HoTHeals:
        case BotSpellTypes::PetHoTHeals:
            return 22000;
        case BotSpellTypes::Cure:
            return 2000;
        case BotSpellTypes::GroupCures:
            return 3000;
        case BotSpellTypes::PetCures:
            return 5000;
        default:
            return 100;
    }
}

function GetDefaultClientSpellTypeMinThresholds($spell_type): int {
    switch ($spell_type) {
        case BotSpellTypes::VeryFastHeals:
        case BotSpellTypes::PetVeryFastHeals:
        case BotSpellTypes::FastHeals:
        case BotSpellTypes::PetFastHeals:
        case BotSpellTypes::GroupHeals:
        case BotSpellTypes::RegularHeal:
        case BotSpellTypes::PetRegularHeals:
        case BotSpellTypes::CompleteHeal:
        case BotSpellTypes::GroupCompleteHeals:
        case BotSpellTypes::PetCompleteHeals:
        case BotSpellTypes::GroupHoTHeals:
        case BotSpellTypes::HoTHeals:
        case BotSpellTypes::PetHoTHeals:
        case BotSpellTypes::Cure:
        case BotSpellTypes::GroupCures:
        case BotSpellTypes::PetCures:
        default:
            return 0;
    }
}

function GetDefaultClientSpellTypeMaxThresholds($spell_type, $class_id): int {
    switch ($spell_type) {
        case BotSpellTypes::VeryFastHeals:
        case BotSpellTypes::PetVeryFastHeals:
            return 25;
        case BotSpellTypes::FastHeals:
        case BotSpellTypes::PetFastHeals:
            return 40;
        case BotSpellTypes::GroupHeals:
        case BotSpellTypes::RegularHeal:
        case BotSpellTypes::PetRegularHeals:
            return 60;
        case BotSpellTypes::CompleteHeal:
        case BotSpellTypes::GroupCompleteHeals:
        case BotSpellTypes::PetCompleteHeals: {
            if ($class_id == CB_CLASS_NECROMANCER || $class_id == CB_CLASS_SHAMAN) {
                return 55;
            }
            else {
                return 80;
            }
        }
        case BotSpellTypes::GroupHoTHeals:
        case BotSpellTypes::HoTHeals:
        case BotSpellTypes::PetHoTHeals: {
            if ($class_id == CB_CLASS_NECROMANCER || $class_id == CB_CLASS_SHAMAN) {
                return 70;
            } else {
                return 90;
            }
        }
        case BotSpellTypes::Buff:
        case BotSpellTypes::Cure:
        case BotSpellTypes::GroupCures:
        case BotSpellTypes::PetCures:
        case BotSpellTypes::PetBuffs:
        case BotSpellTypes::PetDamageShields:
        case BotSpellTypes::PetResistBuffs:
        case BotSpellTypes::ResistBuffs:
        default:
            return 100;
    }
}

function GetDefaultBotBaseSetting($bot_setting, $stance, $class_id): int {
    switch ($bot_setting) {
        case BotBaseSettings::ExpansionBitmask:
            #return RuleI(Bots, BotExpansionSettings);
        case BotBaseSettings::ShowHelm:
            return true;
        case BotBaseSettings::FollowDistance:
            #return RuleI(Bots, DefaultFollowDistance);
        case BotBaseSettings::StopMeleeLevel:
            if (IsCasterClass($class_id)) {
                #return RuleI(Bots, CasterStopMeleeLevel);
            }
            else {
                return 255;
            }
        case BotBaseSettings::PetSetTypeSetting:
            return 0;
        case BotBaseSettings::BehindMob:
            if ($class_id == CB_CLASS_ROGUE || (IsPureMeleeClass($class_id) && $class_id != CB_CLASS_WARRIOR)) {
                return true;
            }
            else {
                return false;
            }
        case BotBaseSettings::DistanceRanged:
            switch ($class_id) {
                case CB_CLASS_WARRIOR:
                case CB_CLASS_MONK:
                case CB_CLASS_ROGUE:
                case CB_CLASS_BERSERKER:
                    return 0;
                case CB_CLASS_BARD:
                    return 30;
                default:
                    return 90;
            }
        case BotBaseSettings::MedInCombat:
            if (IsCasterClass($class_id)) {
                return true;
            }

            return false;
        case BotBaseSettings::SitHPPct:
        case BotBaseSettings::SitManaPct:
            return 80;
        case BotBaseSettings::EnforceSpellSettings:
        case BotBaseSettings::RangedSetting:
        case BotBaseSettings::IllusionBlock:
        case BotBaseSettings::MaxMeleeRange:
        default:
            return false;
    }
}

function GetDefaultSpellTypeDelay($spell_type, $stance, $bot_class): int {
    switch ($spell_type) {
        case BotSpellTypes::VeryFastHeals:
        case BotSpellTypes::PetVeryFastHeals:
            return 1500;
        case BotSpellTypes::FastHeals:
        case BotSpellTypes::PetFastHeals:
            return 2500;
        case BotSpellTypes::GroupHeals:
        case BotSpellTypes::RegularHeal:
        case BotSpellTypes::PetRegularHeals:
            return 4000;
        case BotSpellTypes::CompleteHeal:
        case BotSpellTypes::GroupCompleteHeals:
        case BotSpellTypes::PetCompleteHeals:
            return 8000;
        case BotSpellTypes::GroupHoTHeals:
        case BotSpellTypes::HoTHeals:
        case BotSpellTypes::PetHoTHeals:
            return 22000;
        case BotSpellTypes::Cure:
            return 2000;
        case BotSpellTypes::GroupCures:
            return 3000;
        case BotSpellTypes::PetCures:
            return 5000;
        case BotSpellTypes::AEDoT:
        case BotSpellTypes::DOT:
            switch ($stance) {
                case Stance::AEBurn:
                case Stance::Burn:
                    return 100;
                case Stance::Aggressive:
                    return 2000;
                case Stance::Efficient:
                    return 8000;
                default:
                    return 4000;
            }
        case BotSpellTypes::AENukes:
        case BotSpellTypes::AERains:
        case BotSpellTypes::PBAENuke:
        case BotSpellTypes::Nuke:
        case BotSpellTypes::AESnare:
        case BotSpellTypes::Snare:
        case BotSpellTypes::AEDebuff:
        case BotSpellTypes::Debuff:
        case BotSpellTypes::AESlow:
        case BotSpellTypes::Slow:
        case BotSpellTypes::AEStun:
        case BotSpellTypes::Stun:
            switch ($stance) {
                case Stance::AEBurn:
                case Stance::Burn:
                    return 100;
                case Stance::Aggressive:
                    return 3000;
                case Stance::Efficient:
                    return 10000;
                default:
                    return 6000;
            }
        case BotSpellTypes::AERoot:
        case BotSpellTypes::Root:
            return 8000;
        case BotSpellTypes::Fear:
        case BotSpellTypes::AEFear:
            return 15000;
        default:
            return 100;
    }
}

function GetDefaultSpellTypeMinThreshold($spell_type, $stance, $bot_class): int {
    switch ($spell_type) {
        case BotSpellTypes::AEDebuff:
        case BotSpellTypes::Debuff:
        case BotSpellTypes::AEDispel:
        case BotSpellTypes::Dispel:
        case BotSpellTypes::AESlow:
        case BotSpellTypes::Slow:
            switch ($stance) {
                case Stance::AEBurn:
                case Stance::Burn:
                case Stance::Aggressive:
                    return 0;
                default:
                    return 20;
            }
        case BotSpellTypes::AENukes:
        case BotSpellTypes::AERains:
        case BotSpellTypes::PBAENuke:
        case BotSpellTypes::Nuke:
            switch ($stance) {
                case Stance::AEBurn:
                case Stance::Burn:
                case Stance::Aggressive:
                    return 0;
                default:
                    return 5;
            }
        case BotSpellTypes::AEDoT:
        case BotSpellTypes::DOT:
            switch ($stance) {
                case Stance::AEBurn:
                case Stance::Burn:
                case Stance::Aggressive:
                    return 0;
                case Stance::Efficient:
                    return 40;
                default:
                    return 25;
            }
        case BotSpellTypes::Mez:
        case BotSpellTypes::AEMez:
            return 85;
        default:
            return 0;
    }
}

function GetDefaultSpellTypeMaxThreshold($spell_type, $stance, $bot_class): int {
	switch ($spell_type) {
        case BotSpellTypes::Escape:
        case BotSpellTypes::VeryFastHeals:
        case BotSpellTypes::PetVeryFastHeals:
            switch ($stance) {
                case Stance::AEBurn:
                case Stance::Burn:
                case Stance::Aggressive:
                    return 40;
                case Stance::Efficient:
                default:
                    return 25;
            }
        case BotSpellTypes::AELifetap:
        case BotSpellTypes::Lifetap:
        case BotSpellTypes::FastHeals:
        case BotSpellTypes::PetFastHeals:
            switch ($stance) {
                case Stance::AEBurn:
                case Stance::Burn:
                case Stance::Aggressive:
                    return 55;
                case Stance::Efficient:
                    return 35;
                default:
                    return 40;
            }
        case BotSpellTypes::GroupHeals:
        case BotSpellTypes::RegularHeal:
            if ($bot_class == CB_CLASS_NECROMANCER || $bot_class == CB_CLASS_SHAMAN) {
            return 60;
        }

			switch ($stance) {
                case Stance::AEBurn:
                case Stance::Burn:
                case Stance::Aggressive:
                    return 70;
                case Stance::Efficient:
                    return 50;
                default:
                    return 60;
            }
        case BotSpellTypes::PetRegularHeals:
            switch ($stance) {
                case Stance::AEBurn:
                case Stance::Burn:
                case Stance::Aggressive:
                    return 70;
                case Stance::Efficient:
                    return 50;
                default:
                    return 60;
            }
        case BotSpellTypes::CompleteHeal:
        case BotSpellTypes::GroupCompleteHeals:
            if ($bot_class == CB_CLASS_NECROMANCER || ($bot_class == CB_CLASS_SHAMAN && !GetSpellTypeHold(BotSpellTypes::InCombatBuff))) {
            return 55;
        }

			switch ($stance) {
                case Stance::AEBurn:
                case Stance::Burn:
                case Stance::Aggressive:
                    return 90;
                case Stance::Efficient:
                    return 65;
                default:
                    return 80;
            }
        case BotSpellTypes::PetCompleteHeals:
            switch ($stance) {
                case Stance::AEBurn:
                case Stance::Burn:
                case Stance::Aggressive:
                    return 90;
                case Stance::Efficient:
                    return 65;
                default:
                    return 80;
            }
        case BotSpellTypes::AENukes:
        case BotSpellTypes::AERains:
        case BotSpellTypes::PBAENuke:
        case BotSpellTypes::AEStun:
        case BotSpellTypes::Nuke:
        case BotSpellTypes::AEDoT:
        case BotSpellTypes::DOT:
        case BotSpellTypes::AERoot:
        case BotSpellTypes::Root:
        case BotSpellTypes::AESlow:
        case BotSpellTypes::Slow:
        case BotSpellTypes::AESnare:
        case BotSpellTypes::Snare:
        case BotSpellTypes::AEFear:
        case BotSpellTypes::Fear:
        case BotSpellTypes::AEDispel:
        case BotSpellTypes::Dispel:
        case BotSpellTypes::AEDebuff:
        case BotSpellTypes::Debuff:
        case BotSpellTypes::Stun:
            switch ($stance) {
                case Stance::AEBurn:
                case Stance::Burn:
                    return 100;
                case Stance::Aggressive:
                    return 100;
                case Stance::Efficient:
                    return 90;
                default:
                    return 99;
            }
        case BotSpellTypes::GroupHoTHeals:
        case BotSpellTypes::HoTHeals:
        case BotSpellTypes::PetHoTHeals:
            if ($bot_class == CB_CLASS_NECROMANCER || ($bot_class == CB_CLASS_SHAMAN && !GetSpellTypeHold(BotSpellTypes::InCombatBuff))) {
            return 70;
        }
			else {
            switch ($stance) {
                case Stance::AEBurn:
                case Stance::Burn:
                case Stance::Aggressive:
                    return 95;
                case Stance::Efficient:
                    return 80;
                default:
                    return 90;
            }
        }
        case BotSpellTypes::Buff:
        case BotSpellTypes::Charm:
        case BotSpellTypes::Cure:
        case BotSpellTypes::GroupCures:
        case BotSpellTypes::PetCures:
        case BotSpellTypes::DamageShields:
        case BotSpellTypes::HateRedux:
        case BotSpellTypes::InCombatBuff:
        case BotSpellTypes::InCombatBuffSong:
        case BotSpellTypes::Mez:
        case BotSpellTypes::AEMez:
        case BotSpellTypes::OutOfCombatBuffSong:
        case BotSpellTypes::Pet:
        case BotSpellTypes::PetBuffs:
        case BotSpellTypes::PreCombatBuff:
        case BotSpellTypes::PreCombatBuffSong:
        case BotSpellTypes::PetDamageShields:
        case BotSpellTypes::PetResistBuffs:
        case BotSpellTypes::ResistBuffs:
        case BotSpellTypes::Resurrect:
        case BotSpellTypes::HateLine:
        case BotSpellTypes::AEHateLine:
        default:
            return 100;
    }
}

function IsValidBotStance($stance): bool {
    switch ($stance) {
        case BotStance::Passive:
        case BotStance::Balanced:
        case BotStance::Efficient:
        case BotStance::Aggressive:
        case BotStance::Assist:
        case BotStance::Burn:
        case BotStance::AEBurn:
            return true;
        default:
            return false;
    }
}

function GetSettingValueSuffix($setting_category, $setting_type, $value, $bot_level, $modified = false): string {
    $s_pct = false;
    $s_enabled = false;
    $s_held = false;
    $s_seconds = false;
    $s_sml = false;

    #$color_enabled = ($modified ? "<font color=darkorange>" : "<font color=forestgreen>");
    #$color_disabled = ($modified ? "<font color=darkred>" : "<font color=indianred>");
    $color_enabled = "<font color=forestgreen>";
    $color_disabled = "<font color=indianred>";
    $style = ($modified ? "<b><i>" : "");
    $suffix = "</font>" . ($modified ? "</b></i>" : "");
    $modified_indicator = ($modified ? "*" : "");

    switch ($setting_category) {
        case BotSettingCategories::BASE_SETTING:
            switch ($setting_type) {
                case BotBaseSettings::StopMeleeLevel:
                    $s_sml = true;
                    break;
                case BotBaseSettings::ShowHelm:
                case BotBaseSettings::EnforceSpellSettings:
                case BotBaseSettings::RangedSetting:
                case BotBaseSettings::BehindMob:
                case BotBaseSettings::IllusionBlock:
                case BotBaseSettings::MaxMeleeRange:
                case BotBaseSettings::MedInCombat:
                    $s_enabled = true;
                    break;
                case BotBaseSettings::SitHPPct:
                case BotBaseSettings::SitManaPct:
                    $s_pct = true;
                    break;
                case BotBaseSettings::PetSetTypeSetting:
                case BotBaseSettings::DistanceRanged:
                case BotBaseSettings::ExpansionBitmask:
                case BotBaseSettings::FollowDistance:
                default:
                    break;
            }
            break;
        case BotSettingCategories::SPELL_HOLD:
            $s_held = true;
            break;
        case BotSettingCategories::SPELL_DELAY:
            $s_seconds = true;
            break;
        case BotSettingCategories::SPELL_MIN_THRESHOLD:
        case BotSettingCategories::SPELL_MAX_THRESHOLD:
        case BotSettingCategories::SPELL_TYPE_MIN_MANA_PCT:
        case BotSettingCategories::SPELL_TYPE_MAX_MANA_PCT:
        case BotSettingCategories::SPELL_TYPE_MIN_HP_PCT:
        case BotSettingCategories::SPELL_TYPE_MAX_HP_PCT:
            if ($setting_category == BotSettingCategories::SPELL_MAX_THRESHOLD && !$value) {
                $s_enabled = true;
                break;
            }
            $s_pct = true;
            break;
        case BotSettingCategories::SPELL_TYPE_RESIST_LIMIT:
        case BotSettingCategories::SPELL_TYPE_AGGRO_CHECK:
        case BotSettingCategories::SPELL_TYPE_ANNOUNCE_CAST:
            $s_enabled = true;
            break;
        case BotSettingCategories::SPELL_TYPE_IDLE_PRIORITY:
        case BotSettingCategories::SPELL_TYPE_ENGAGED_PRIORITY:
        case BotSettingCategories::SPELL_TYPE_PURSUE_PRIORITY:
            if ($value == 0) {
                $s_enabled = true;
            }
            break;
        case BotSettingCategories::SPELL_TYPE_AE_OR_GROUP_TARGET_COUNT:
        default:
            break;
    }

    if ($s_held) {
        return $style . ($value ? $color_disabled . "Holding" : $color_enabled . "Allowed") . $modified_indicator . $suffix;
    }

    if ($s_pct) {
        return $style . $color_enabled . $value . "%" . $modified_indicator . $suffix;
    }

    if ($s_enabled) {
        return $style . ($value ? $color_enabled . "Enabled" : $color_disabled . "Disabled") . $modified_indicator . $suffix;
    }

    if ($s_seconds) {
        return $style . $color_enabled . ($value / 1000) . "s" . $modified_indicator . $suffix;
    }

    if ($s_sml) {
        return $style . ($value > $bot_level ? $color_enabled . $value : $color_disabled . $value) . $modified_indicator . $suffix;
    }

    return $style . $color_enabled . $value . $modified_indicator . $suffix;
}

function FormBaseSettingsDescriptionString($start): string {
    global $bot_setting_base_category_descriptions, $botBaseSettings_names;

    $description = "";

    for ($i = $start; $i <= BotBaseSettings::END; ++$i) {
        $description .= "<b><u>" . $botBaseSettings_names[$i] . "</b></u> - " . $bot_setting_base_category_descriptions[$i] . "\n";
    }

    return $description;
}

function IsAEBotSpellType($spell_type): bool {
    switch ($spell_type) {
        case BotSpellTypes::AEDebuff:
        case BotSpellTypes::AEFear:
        case BotSpellTypes::AEMez:
        case BotSpellTypes::AENukes:
        case BotSpellTypes::AERains:
        case BotSpellTypes::AESlow:
        case BotSpellTypes::AESnare:
        case BotSpellTypes::AEStun:
        case BotSpellTypes::AEDispel:
        case BotSpellTypes::AEDoT:
        case BotSpellTypes::PBAENuke:
        case BotSpellTypes::AELifetap:
        case BotSpellTypes::AERoot:
        case BotSpellTypes::AEHateLine:
        case BotSpellTypes::AELull:
            return true;
        default:
            return false;
    }

    return false;
}

function IsGroupBotSpellType($spell_type): bool {
    switch ($spell_type) {
        case BotSpellTypes::GroupCures:
        case BotSpellTypes::GroupCompleteHeals:
        case BotSpellTypes::GroupHeals:
        case BotSpellTypes::GroupHoTHeals:
            return true;
        default:
            return false;
    }

    return false;
}

function IsAEOrGroupBotSpellType($spell_type): bool {
    return IsAEBotSpellType($spell_type) || IsGroupBotSpellType($spell_type);
}
?>