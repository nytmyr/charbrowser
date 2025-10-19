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
include_once(__DIR__ . "/include/bot_profile.php");
include_once(__DIR__ . "/include/bot.php");
include_once(__DIR__ . "/include/character.php");

function OwnerCheck($charID) {
    global $cbsql, $defaultedlocalhost, $localipaddress, $defaultgateway, $publicip;

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

function IsClientBotSettingCategory($category_id): bool {
    switch ($category_id) {
        case BotSettingCategories::BaseSetting:
        case BotSettingCategories::SpellDelay:
        case BotSettingCategories::SpellMinThreshold:
        case BotSettingCategories::SpellMaxThreshold:
            return true;
        default:
            return false;
    }
}

function IsClientBotBaseSetting($setting_id): bool {
    switch ($setting_id) {
        case BotBaseSettings::IllusionBlock:
            return true;
        default:
            return false;
    }
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
            return false;
    }
}

function IsPureMeleeClass($class_id): bool {
    switch ($class_id) {
        case CB_CLASS_WARRIOR:
        case CB_CLASS_MONK:
        case CB_CLASS_ROGUE:
        case CB_CLASS_BERSERKER:
            return true;
        default:
            return false;
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
        case BotSettingCategories::BaseSetting:
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
        case BotSettingCategories::SpellHold:
            $s_held = true;
            break;
        case BotSettingCategories::SpellDelay:
            $s_seconds = true;
            break;
        case BotSettingCategories::SpellMinThreshold:
        case BotSettingCategories::SpellMaxThreshold:
        case BotSettingCategories::SpellTypeMinManaPct:
        case BotSettingCategories::SpellTypeMaxManaPct:
        case BotSettingCategories::SpellTypeMinHPPct:
        case BotSettingCategories::SpellTypeMaxHPPct:
            if ($setting_category == BotSettingCategories::SpellMaxThreshold && !$value) {
                $s_enabled = true;
                break;
            }
            $s_pct = true;
            break;
        case BotSettingCategories::SpellTypeResistLimit:
        case BotSettingCategories::SpellTypeAggroCheck:
        case BotSettingCategories::SpellTypeAnnounceCast:
            $s_enabled = true;
            break;
        case BotSettingCategories::SpellTypeIdlePriority:
        case BotSettingCategories::SpellTypeEngagedPriority:
        case BotSettingCategories::SpellTypePursuePriority:
            if ($value == 0) {
                $s_enabled = true;
            }
            break;
        case BotSettingCategories::SpellTypeAEOrGroupTargetCount:
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

function FormBaseSettingsDescriptionString($start, $is_bot = false): string {
    global $bot_setting_base_category_descriptions, $bot_base_setting_names;

    $description = "";

    for ($i = $start; $i <= BotBaseSettings::END; ++$i) {
        if (!$is_bot && !IsClientBotBaseSetting($i)) {
            continue;
        }

        $description .= "<b><u>" . $bot_base_setting_names[$i] . "</b></u> - " . $bot_setting_base_category_descriptions[$i] . "\n";
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
}

function IsAEOrGroupBotSpellType($spell_type): bool {
    return IsAEBotSpellType($spell_type) || IsGroupBotSpellType($spell_type);
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
}

function GenerateBotSettingsPage($page, $page_body, $entity, $entity_name, $is_bot = false, $selected_stance = BotStance::Invalid, $base_link = ""): void {
    global $bot_setting_window_tab_names, $bot_base_setting_names, $spell_type_names, $bot_default_settings, $bot_setting_category_descriptions, $bot_base_setting_commands, $bot_setting_category_commands, $language, $cb_template, $cb_error, $bot_stance_names, $bot_setting_category_names;

    $current_stance = $is_bot ? $entity->GetStance() : $selected_stance;
    $bot_settings = $entity->GetTable('bot_settings');
    $bot_default_settings = $entity->GetTable('bot_default_settings');

    /* DEBUG OUTPUT
    echo "-------------START MODIFIED SETTINGS-------------<br>";

    foreach ($bot_settings as $stance => $categories) {
        echo "<pre>Stance: " . $bot_stance_names[$stance] . " [" . $stance . "]</pre>"; //deleteme

        foreach ($categories as $category => $spell_types) {
            echo "<pre>\tSetting Category: " . $bot_setting_category_names[$category] . " [" . $category . "]</pre>"; //deleteme

            foreach ($spell_types as $spell_type => $value) {
                if ($category == BotSettingCategories::BASE_SETTING) {
                    echo "<pre>\t\tSetting Type: " . $bot_base_setting_names[$spell_type] . " [" . $spell_type . "]</pre>"; //deleteme
                }
                else {
                    echo "<pre>\t\tSpell Type: " . $spell_type_names[$spell_type] . " [" . $spell_type . "]</pre>"; //deleteme
                }

                echo "<pre>\t\t\tValue: " . $value . "</pre>"; //deleteme
            }
        }

        echo "-------------END MODIFIED SETTINGS-------------<br>";
    }

    // DEBUG OUTPUT
    echo "-------------START DEFAULT SETTINGS-------------<br>";

    foreach ($bot_default_settings as $stance => $categories) {
        echo "<pre>Stance: " . $bot_stance_names[$stance] . " [" . $stance . "]</pre>"; //deleteme

        foreach ($categories as $category => $spell_types) {
            echo "<pre>\tSetting Category: " . $bot_setting_category_names[$category] . " [" . $category . "]</pre>"; //deleteme

            foreach ($spell_types as $spell_type => $value) {
                if ($category == BotSettingCategories::BASE_SETTING) {
                    echo "<pre>\t\tSetting Type: " . $bot_base_setting_names[$spell_type] . " [" . $spell_type . "]</pre>"; //deleteme
                }
                else {
                    echo "<pre>\t\tSpell Type: " . $spell_type_names[$spell_type] . " [" . $spell_type . "]</pre>"; //deleteme
                }

                echo "<pre>\t\t\tValue: " . $value . "</pre>"; //deleteme
            }
        }

        echo "-------------END DEFAULT SETTINGS-------------<br>";
    }
    */

    if (!is_array($bot_default_settings)) {
        $cb_error->message_die($language['MESSAGE_ERROR'], $language['MESSAGE_ERROR_BOT_DEFAULT_SETTINGS']);
    }

    if ($is_bot) {
        // Stance selector dropdown
        $stance_names = [
            BotStance::Passive => BotStance::Passive . "- Passive" . ($current_stance == BotStance::Passive ? ' (Current)' : ''),
            BotStance::Balanced => BotStance::Balanced . "- Balanced" . ($current_stance == BotStance::Balanced ? ' (Current)' : ''),
            BotStance::Efficient => BotStance::Efficient . "- Efficient" . ($current_stance == BotStance::Efficient ? ' (Current)' : ''),
            BotStance::Aggressive => BotStance::Aggressive . "- Aggressive" . ($current_stance == BotStance::Aggressive ? ' (Current)' : ''),
            BotStance::Assist => BotStance::Assist . "- Assist" . ($current_stance == BotStance::Assist ? ' (Current)' : ''),
            BotStance::Burn => BotStance::Burn . "- Burn" . ($current_stance == BotStance::Burn ? ' (Current)' : ''),
            BotStance::AEBurn => BotStance::AEBurn . "- AEBurn" . ($current_stance == BotStance::AEBurn ? ' (Current)' : ''),
        ];

        $stance_options = '';

        for ($s = BotStance::START; $s <= BotStance::END; ++$s) {
            if (IsValidBotStance($s)) {
                $selected = ($s == $selected_stance) ? ' selected' : '';
                $name = $stance_names[$s] ?? "Stance $s";
                $stance_options .= "<option value='$s'$selected>$name</option>";
            }
        }
    }

    #$stance_html = "<label for='stance-select'>Stance: </label><select id='stance-select' onchange=\"window.location.href = '$base_link&stance=' + this.value;\">$stance_options</select>";
    $stance_html = $is_bot ? "<label for='stance-select'>Stance: </label><select id='stance-select' onchange=\"var newUrl = '$base_link'; if (newUrl.indexOf('stance=') !== -1) { newUrl = newUrl.replace(/&?stance=[0-9]+/, ''); } newUrl += (newUrl.indexOf('?') !== -1 ? '&' : '?') + 'stance=' + this.value; window.location.href = newUrl;\">$stance_options</select>" : "";
    $setting_sections = array();

    for ($i = BotSettingCategories::START; $i <= BotSettingCategories::END; ++$i) {
        if (!$is_bot && !IsClientBotSettingCategory($i)) {
            continue;
        }

        if ($i == BotSettingCategories::BaseSetting) {
            for ($x = BotBaseSettings::START; $x <= BotBaseSettings::END; ++$x) {
                if (!$is_bot && !IsClientBotBaseSetting($x)) {
                    continue;
                }

                $command_name = $bot_base_setting_commands[$x] ?? 'Unknown Command';

                if (!$is_bot && $x == BotBaseSettings::IllusionBlock) {
                    $setting_sections[$bot_setting_window_tab_names[$i]][$x] = array('ID' => $x, 'NAME' => '<font color=teal>' . $bot_base_setting_names[$x] . '</font>', 'VALUE' => GetSettingValueSuffix($i, $x, $entity->getIllusionBlock(), $entity->GetValue('level'), $entity->getIllusionBlock() != $bot_default_settings[$selected_stance][$i][$x]) . '</font>', 'COMMAND' => '<font color=lightslategrey>' . $command_name . '</font>'); // deleteme
                }
                else {
                    $setting_sections[$bot_setting_window_tab_names[$i]][$x] = array('ID' => $x, 'NAME' => '<font color=teal>' . $bot_base_setting_names[$x] . '</font>', 'VALUE' => isset($bot_settings[$selected_stance][$i][$x]) ? GetSettingValueSuffix($i, $x, $bot_settings[$selected_stance][$i][$x], $entity->GetValue('level'), true) . '</font>' : GetSettingValueSuffix($i, $x, $bot_default_settings[$selected_stance][$i][$x], $entity->GetValue('level')) . '</font>', 'COMMAND' => '<font color=lightslategrey>' . $command_name . '</font>');
                }
            }
        }
        else {
            for ($x = BotSpellTypes::START; $x <= BotSpellTypes::END; ++$x) {
                if (!$is_bot && !IsClientBotSpellType($x)) {
                    continue;
                }

                $setting_sections[$bot_setting_window_tab_names[$i]][$x] = array('ID' => $x, 'NAME' => '<font color=teal>' . $spell_type_names[$x] . '</font>', 'VALUE' =>  isset($bot_settings[$selected_stance][$i][$x]) ? GetSettingValueSuffix($i, $x, $bot_settings[$selected_stance][$i][$x], $entity->GetValue('level'), true) . '</font>' : GetSettingValueSuffix($i, $x, $bot_default_settings[$selected_stance][$i][$x], $entity->GetValue('level')) . '</font>', 'COMMAND' => '<font color=lightslategrey>' . ($bot_setting_category_commands[$i] . " " . $x ?? 'Unknown Command') . '</font>'); // deleteme
            }
        }
    }

    $cb_template->set_filenames(array(
        $page => $page_body)
    );

    $i = 0;

    foreach ($setting_sections as $header => $setting) {
        if (!$is_bot && !IsClientBotSettingCategory($i)) {
            ++$i;
        }

        //echo "DEBUG: Starting header='$header', i=$i<br>";
        $cb_template->assign_block_vars("section",
            array(
                'TEXT' => ($i == BotSettingCategories::BaseSetting ? 'Setting Name' : 'Spell Type'),
                'DESCRIPTION' => ($i == BotSettingCategories::BaseSetting ? FormBaseSettingsDescriptionString(BotBaseSettings::START, $is_bot) : $bot_setting_category_descriptions[$i]),
                'TEXTA' => 'Value',
                'TEXTB' => 'Command',
                'TAB' => $header,
                'INDEX' => $i
            )
        );

        $current_index = $i;  // Assign, then increment

        //echo "DEBUG: Assigned INDEX=$current_index for $header, checking sort condition...<br>";

        // Sort ONLY idle, engaged and pursue
        if (in_array($current_index, [
            BotSettingCategories::SpellTypeIdlePriority,
            BotSettingCategories::SpellTypeEngagedPriority,
            BotSettingCategories::SpellTypePursuePriority
        ])) {
            //echo "DEBUG: TRIGGERED SORT for $header (index $current_index)<br>";
            usort($setting, function($a, $b) {
                $valA = $a['VALUE'] ?? '';
                $valB = $b['VALUE'] ?? '';

                // Helper to get sort key: extract number, 999 if "Disabled"/0/non-numeric
                $getSortKey = function($val) use (&$strip_tags_helper) {  // Closure to reuse strip logic
                    if (stripos($val, 'Disabled') !== false) {
                        return 999;
                    }

                    // Approximate strip_tags (remove <...> tags)
                    if (!function_exists('strip_tags_helper')) {
                        function strip_tags_helper($text) {
                            return preg_replace('/<[^>]*>/', '', $text);
                        }
                    }

                    $clean = strip_tags_helper($val);
                    $cleanTrim = trim($clean);

                    if ($cleanTrim === '0' || empty($cleanTrim)) {
                        return 999;
                    }

                    // Extract digits only
                    $numStr = preg_replace('/[^0-9]/', '', $clean);
                    $num = (int) $numStr;

                    return ($num > 0) ? $num : 999;
                };

                $sortA = $getSortKey($valA);
                $sortB = $getSortKey($valB);

                return $sortA - $sortB;  // Ascending
            });
            //echo "<br>";

            /*
            echo "DEBUG: Sort complete for $header. First 3 VALUES after: ";
            for ($j = 0; $j < min(3, count($setting)); $j++) {
                $val = $setting[$j]['VALUE'];

                if (stripos($val, 'Disabled') !== false) {
                    //echo "Disabled | ";
                } else {
                    $clean = preg_replace('/<[^>]*>/', '', $val);  // Strip tags
                    $numStr = preg_replace('/[^0-9]/', '', $clean);
                    //echo (int)$numStr . " | ";
                }
            }

            $lowCount = 0; $highCount = 0;

            foreach ($setting as $item) {
                $val = $item['VALUE'];

                if (stripos($val, 'Disabled') !== false) {
                    $highCount++;
                } else {
                    $clean = preg_replace('/<[^>]*>/', '', $val);
                    $numStr = preg_replace('/[^0-9]/', '', $clean);
                    $num = (int)$numStr;
                    if ($num > 0 && $num < 999) $lowCount++;
                }
            }
            echo " (Lows: $lowCount, Highs/Disabled: $highCount)<br>";
            */
        } else {
            //echo "DEBUG: SKIPPED SORT for $header (index $current_index)<br>";
        }

        $x = 0;

        foreach ($setting as $settingrow) {
            if ($current_index == BotSettingCategories::SpellTypeAEOrGroupTargetCount && !IsAEOrGroupBotSpellType($x)) {
                //echo "DEBUG: Skipping non-AE row $x for AE category<br>";
                ++$x;
                continue;
            }

            ++$x;
            $cb_template->assign_both_block_vars("section.settingrow", $settingrow);
        }
        //echo "DEBUG: Finished $header (assigned $x rows)<br><hr>";
        ++$i;

    }

    $cb_template->assign_both_vars(array(
            'NAME' => $entity_name)
    );

    $cb_template->assign_vars(array(
        'L_BOT_SETTINGS' => $language['SETTINGS_BOT_SETTINGS'],
        'L_BOT_OPTIONS' => $language['SETTINGS_BOT_OPTIONS'],
        'L_DONE' => $language['BUTTON_DONE'],
        'STANCE_SELECT' => $stance_html,
        'NOTE' => "* signifies that the setting has been modified"
    ));

    /*********************************************
    OUTPUT BODY
    *********************************************/
    $cb_template->pparse($page);

    $cb_template->destroy();
}
?>