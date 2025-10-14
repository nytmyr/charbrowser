<?php

class BotSettingCategories
{
    public const BaseSetting = 0;
    public const SpellHold = 1;
    public const SpellDelay = 2;
    public const SpellMinThreshold = 3;
    public const SpellMaxThreshold = 4;
    public const SpellTypeResistLimit = 5;
    public const SpellTypeAggroCheck = 6;
    public const SpellTypeMinManaPct = 7;
    public const SpellTypeMaxManaPct = 8;
    public const SpellTypeMinHPPct = 9;
    public const SpellTypeMaxHPPct = 10;
    public const SpellTypeIdlePriority = 11;
    public const SpellTypeEngagedPriority = 12;
    public const SpellTypePursuePriority = 13;
    public const SpellTypeAEOrGroupTargetCount = 14;
    public const SpellTypeAnnounceCast = 15;

    public const START = self::BaseSetting;
    public const START_NO_BASE = self::SpellHold;
    public const START_CLIENT = self::SpellDelay;
    public const END_CLIENT = self::SpellMaxThreshold;
    public const END = self::SpellTypeAnnounceCast;
}

$bot_setting_category_names = [
    BotSettingCategories::BaseSetting => "Base Setting",
    BotSettingCategories::SpellHold => "Spell Holds",
    BotSettingCategories::SpellDelay => "Spell Delays",
    BotSettingCategories::SpellMinThreshold => "Spell Minimum Thresholds",
    BotSettingCategories::SpellMaxThreshold => "Spell Maximum Thresholds",
    BotSettingCategories::SpellTypeResistLimit => "Spell Resist Limits",
    BotSettingCategories::SpellTypeAggroCheck => "Spell Aggro Checks",
    BotSettingCategories::SpellTypeMinManaPct => "Spell Min Mana Percent",
    BotSettingCategories::SpellTypeMaxManaPct => "Spell Max Mana Percent",
    BotSettingCategories::SpellTypeMinHPPct => "Spell Min HP Percent",
    BotSettingCategories::SpellTypeMaxHPPct => "Spell Max HP Percent",
    BotSettingCategories::SpellTypeIdlePriority => "Spell Idle Priority",
    BotSettingCategories::SpellTypeEngagedPriority => "Spell Engaged Priority",
    BotSettingCategories::SpellTypePursuePriority => "Spell Pursue Priority",
    BotSettingCategories::SpellTypeAEOrGroupTargetCount => "Spell Target Counts",
    BotSettingCategories::SpellTypeAnnounceCast => "Spell Announce Casts"
];

$bot_setting_category_short_names = [
    BotSettingCategories::BaseSetting => "BaseSetting",
    BotSettingCategories::SpellHold => "SpellHolds",
    BotSettingCategories::SpellDelay => "SpellDelays",
    BotSettingCategories::SpellMinThreshold => "SpellMinThresholds",
    BotSettingCategories::SpellMaxThreshold => "SpellMaxThresholds",
    BotSettingCategories::SpellTypeResistLimit => "SpellResistLimits",
    BotSettingCategories::SpellTypeAggroCheck => "SpellAggroChecks",
    BotSettingCategories::SpellTypeMinManaPct => "SpellMinManaPct",
    BotSettingCategories::SpellTypeMaxManaPct => "SpellMaxManaPct",
    BotSettingCategories::SpellTypeMinHPPct => "SpellMinHPPct",
    BotSettingCategories::SpellTypeMaxHPPct => "SpellMaxHPPct",
    BotSettingCategories::SpellTypeIdlePriority => "SpellIdlePriority",
    BotSettingCategories::SpellTypeEngagedPriority => "SpellEngagedPriority",
    BotSettingCategories::SpellTypePursuePriority => "SpellPursuePriority",
    BotSettingCategories::SpellTypeAEOrGroupTargetCount => "SpellTargetCounts",
    BotSettingCategories::SpellTypeAnnounceCast => "SpellAnnounceCasts"
];

$bot_setting_window_tab_names = [
    BotSettingCategories::BaseSetting => $language['BOT_SETTINGS_BASE'],
    BotSettingCategories::SpellHold => $language['BOT_SETTINGS_HOLDS'],
    BotSettingCategories::SpellDelay => $language['BOT_SETTINGS_DELAYS'],
    BotSettingCategories::SpellMinThreshold => $language['BOT_SETTINGS_MINTHRESHOLDS'],
    BotSettingCategories::SpellMaxThreshold => $language['BOT_SETTINGS_MAXTHRESHOLDS'],
    BotSettingCategories::SpellTypeResistLimit => $language['BOT_SETTINGS_ResistLimit'],
    BotSettingCategories::SpellTypeAggroCheck => $language['BOT_SETTINGS_AggroCheck'],
    BotSettingCategories::SpellTypeMinManaPct => $language['BOT_SETTINGS_MinManaPct'],
    BotSettingCategories::SpellTypeMaxManaPct => $language['BOT_SETTINGS_MaxManaPct'],
    BotSettingCategories::SpellTypeMinHPPct => $language['BOT_SETTINGS_MinHPPct'],
    BotSettingCategories::SpellTypeMaxHPPct => $language['BOT_SETTINGS_MaxHPPct'],
    BotSettingCategories::SpellTypeIdlePriority => $language['BOT_SETTINGS_IdlePriority'],
    BotSettingCategories::SpellTypeEngagedPriority => $language['BOT_SETTINGS_EngagedPriority'],
    BotSettingCategories::SpellTypePursuePriority => $language['BOT_SETTINGS_PursuePriority'],
    BotSettingCategories::SpellTypeAEOrGroupTargetCount => $language['BOT_SETTINGS_AEOrGroupTargetCount'],
    BotSettingCategories::SpellTypeAnnounceCast => $language['BOT_SETTINGS_AnnounceCast']
];

$bot_setting_category_descriptions = [
    BotSettingCategories::BaseSetting => "Basic settings to adjust your bot's behavior",
    BotSettingCategories::SpellHold => "Controls whether a bot holds the specified spell type or not",
    BotSettingCategories::SpellDelay => "Controls the delay between casts for a specific spell type",
    BotSettingCategories::SpellMinThreshold => "Controls the maximum target HP threshold for a spell to be cast for a specific type",
    BotSettingCategories::SpellMaxThreshold => "Controls the minimum target HP threshold for a spell to be cast for a specific type",
    BotSettingCategories::SpellTypeResistLimit => "Controls the resist limits for bots to cast spells on their target",
    BotSettingCategories::SpellTypeAggroCheck => "Toggles whether or not bots will cast a spell type if they think it will get them aggro",
    BotSettingCategories::SpellTypeMinManaPct => "Controls at what mana percent a bot will start casting different spell types",
    BotSettingCategories::SpellTypeMaxManaPct => "Controls at what mana percent a bot will stop casting different spell types",
    BotSettingCategories::SpellTypeMinHPPct => "Controls at what HP percent a bot will start casting different spell types",
    BotSettingCategories::SpellTypeMaxHPPct => "Controls at what HP percent a bot will stop casting different spell types",
    BotSettingCategories::SpellTypeIdlePriority => "Controls the order of casts by spell type when out of combat",
    BotSettingCategories::SpellTypeEngagedPriority => "Controls the order of casts by spell type when engaged in combat",
    BotSettingCategories::SpellTypePursuePriority => "Controls the order of casts by spell type when pursuing in combat",
    BotSettingCategories::SpellTypeAEOrGroupTargetCount => "Sets the required target amount for group/AE spells by spell type",
    BotSettingCategories::SpellTypeAnnounceCast => "Turn on or off cast announcements by spell type"
];

$bot_setting_base_category_descriptions = [
    BotBaseSettings::ExpansionBitmask => "Unused",
    BotBaseSettings::ShowHelm => "Toggles the helm visibility of a bot between shown and hidden",
    BotBaseSettings::FollowDistance => "Distance at which a bot will follow its followee",
    BotBaseSettings::StopMeleeLevel => "Sets the level a caster or spell-casting fighter bot will stop melee combat",
    BotBaseSettings::EnforceSpellSettings => "Toggles your Bot to cast only spells in their spell settings list via ^spells",
    BotBaseSettings::RangedSetting => "Toggles a ranged bot between melee and ranged weapon use. Enabled = Ranged.",
    BotBaseSettings::PetSetTypeSetting => "Orders a Magician bot to use a specified pet type",
    BotBaseSettings::BehindMob => "Toggles whether or not your bot tries to stay behind a mob",
    BotBaseSettings::DistanceRanged => "Controls the range casters and ranged will try to stay away from a mob",
    BotBaseSettings::IllusionBlock => "Control whether or not illusion effects will land on the bot if casted by another player or bot",
    BotBaseSettings::MaxMeleeRange => "Toggles whether your bot is at max melee range or not. This will disable all special abilities, including taunt.",
    BotBaseSettings::MedInCombat => "Toggles whether or a not a bot will attempt to med or sit to heal in combat",
    BotBaseSettings::SitHPPct => "HP threshold for a bot to start sitting in combat if allowed",
    BotBaseSettings::SitManaPct => "Mana threshold for a bot to start sitting in combat if allowed"
];

$bot_setting_category_commands = [
    BotSettingCategories::BaseSetting => "Base Setting",
    BotSettingCategories::SpellHold => "^spellholds",
    BotSettingCategories::SpellDelay => "^spelldelays",
    BotSettingCategories::SpellMinThreshold => "^spellminthresholds",
    BotSettingCategories::SpellMaxThreshold => "^spellmaxthresholds",
    BotSettingCategories::SpellTypeResistLimit => "^spellresistlimits",
    BotSettingCategories::SpellTypeAggroCheck => "^spellaggrochecks",
    BotSettingCategories::SpellTypeMinManaPct => "^spellminmanapct",
    BotSettingCategories::SpellTypeMaxManaPct => "^spellmaxmanapct",
    BotSettingCategories::SpellTypeMinHPPct => "^spellminhppct",
    BotSettingCategories::SpellTypeMaxHPPct => "^spellmaxhppct",
    BotSettingCategories::SpellTypeIdlePriority => "^spellidlepriority",
    BotSettingCategories::SpellTypeEngagedPriority => "^spellengagedpriority",
    BotSettingCategories::SpellTypePursuePriority => "^spellpursuepriority",
    BotSettingCategories::SpellTypeAEOrGroupTargetCount => "^spelltargetcount",
    BotSettingCategories::SpellTypeAnnounceCast => "^spellannouncecasts"
];

$bot_base_setting_commands = [
    BotBaseSettings::ExpansionBitmask => "Unused",
    BotBaseSettings::ShowHelm => "^togglehelm",
    BotBaseSettings::FollowDistance => "^folowdistance",
    BotBaseSettings::StopMeleeLevel => "^stopmeleelevel",
    BotBaseSettings::EnforceSpellSettings => "^enforcespellsettings",
    BotBaseSettings::RangedSetting => "^bottoggleranged",
    BotBaseSettings::PetSetTypeSetting => "^petsettype",
    BotBaseSettings::BehindMob => "^behindmob",
    BotBaseSettings::DistanceRanged => "^distanceranged",
    BotBaseSettings::IllusionBlock => "^illusionblock",
    BotBaseSettings::MaxMeleeRange => "^maxmeleerange",
    BotBaseSettings::MedInCombat => "^sitincombat",
    BotBaseSettings::SitHPPct => "^sithppct",
    BotBaseSettings::SitManaPct => "^sitmanapct"
];

class BotPriorityCategories
{
    public const IDLE = 0;
    public const ENGAGED = 1;
    public const PURSUE = 2;

    public const START = self::IDLE;
    public const END = self::PURSUE;
}

class BotBaseSettings
{
    public const ExpansionBitmask = 0;
    public const ShowHelm = 1;
    public const FollowDistance = 2;
    public const StopMeleeLevel = 3;
    public const EnforceSpellSettings = 4;
    public const RangedSetting = 5;
    public const PetSetTypeSetting = 6;
    public const BehindMob = 7;
    public const DistanceRanged = 8;
    public const IllusionBlock = 9;
    public const MaxMeleeRange = 10;
    public const MedInCombat = 11;
    public const SitHPPct = 12;
    public const SitManaPct = 13;

    public const START_ALL = self::ExpansionBitmask;
    public const START = self::ShowHelm;
    public const END = self::SitManaPct;
}

$bot_base_setting_names = [
    BotBaseSettings::ExpansionBitmask => "Expansion Bitmask",
    BotBaseSettings::ShowHelm => "Show Helm",
    BotBaseSettings::FollowDistance => "Follow Distance",
    BotBaseSettings::StopMeleeLevel => "Stop Melee Level",
    BotBaseSettings::EnforceSpellSettings => "Enforce Spell Settings",
    BotBaseSettings::RangedSetting => "Ranged Setting",
    BotBaseSettings::PetSetTypeSetting => "Pet Set Type Setting",
    BotBaseSettings::BehindMob => "Behind Mob",
    BotBaseSettings::DistanceRanged => "Distance Ranged",
    BotBaseSettings::IllusionBlock => "Illusion Block",
    BotBaseSettings::MaxMeleeRange => "Max Melee Range",
    BotBaseSettings::MedInCombat => "Med In Combat",
    BotBaseSettings::SitHPPct => "Sit HP Pct",
    BotBaseSettings::SitManaPct => "Sit Mana Pct"
];


class BotSpellTypes
{
    public const Nuke = 0;
    public const RegularHeal = 1;
    public const Root = 2;
    public const Buff = 3;
    public const Escape = 4;
    public const Pet = 5;
    public const Lifetap = 6;
    public const Snare = 7;
    public const DOT = 8;
    public const Dispel = 9;
    public const InCombatBuff = 10;
    public const Mez = 11;
    public const Charm = 12;
    public const Slow = 13;
    public const Debuff = 14;
    public const Cure = 15;
    public const Resurrect = 16;
    public const HateRedux = 17;
    public const InCombatBuffSong = 18;
    public const OutOfCombatBuffSong = 19;
    public const PreCombatBuff = 20;
    public const PreCombatBuffSong = 21;
    public const Fear = 22;
    public const Stun = 23;
    public const HateLine = 24;
    public const GroupCures = 25;
    public const CompleteHeal = 26;
    public const FastHeals = 27;
    public const VeryFastHeals = 28;
    public const GroupHeals = 29;
    public const GroupCompleteHeals = 30;
    public const GroupHoTHeals = 31;
    public const HoTHeals = 32;
    public const AENukes = 33;
    public const AERains = 34;
    public const AEMez = 35;
    public const AEStun = 36;
    public const AEDebuff = 37;
    public const AESlow = 38;
    public const AESnare = 39;
    public const AEFear = 40;
    public const AEDispel = 41;
    public const AERoot = 42;
    public const AEDoT = 43;
    public const AELifetap = 44;
    public const AEHateLine = 45;
    public const PBAENuke = 46;
    public const PetBuffs = 47;
    public const PetRegularHeals = 48;
    public const PetCompleteHeals = 49;
    public const PetFastHeals = 50;
    public const PetVeryFastHeals = 51;
    public const PetHoTHeals = 52;
    public const PetCures = 53;
    public const DamageShields = 54;
    public const ResistBuffs = 55;
    public const PetDamageShields = 56;
    public const PetResistBuffs = 57;

    // Command Spell Types
    public const Teleport = 100; // this is handled by ^depart so uses other logic
    public const Lull = 101;
    public const Succor = 102;
    public const BindAffinity = 103;
    public const Identify = 104;
    public const Levitate = 105;
    public const Rune = 106;
    public const WaterBreathing = 107;
    public const Size = 108;
    public const Invisibility = 109;
    public const MovementSpeed = 110;
    public const SendHome = 111;
    public const SummonCorpse = 112;
    public const AELull = 113;

    // Discipline Types
    public const Discipline = 200;
    public const DiscAggressive = 201;
    public const DiscDefensive = 202;
    public const DiscUtility = 203;

    public const START = self::Nuke;
    public const END = self::PetResistBuffs;
    public const COMMANDED_START = self::Lull;
    public const COMMANDED_END = self::AELull;
    public const DISCIPLINE_START = self::Discipline;
    public const DISCIPLINE_END = self::DiscUtility;
    public const PARENT_TYPE_END = self::PreCombatBuffSong;
}

$spell_type_names = [
    BotSpellTypes::Nuke => "Nuke",
    BotSpellTypes::RegularHeal => "Regular Heal",
    BotSpellTypes::Root => "Root",
    BotSpellTypes::Buff => "Buff",
    BotSpellTypes::Escape => "Escape",
    BotSpellTypes::Pet => "Pet",
    BotSpellTypes::Lifetap => "Lifetap",
    BotSpellTypes::Snare => "Snare",
    BotSpellTypes::DOT => "DoT",
    BotSpellTypes::Dispel => "Dispel",
    BotSpellTypes::InCombatBuff => "In-Combat Buff",
    BotSpellTypes::Mez => "Mez",
    BotSpellTypes::Charm => "Charm",
    BotSpellTypes::Slow => "Slow",
    BotSpellTypes::Debuff => "Debuff",
    BotSpellTypes::Cure => "Cure",
    BotSpellTypes::GroupCures => "Group Cure",
    BotSpellTypes::PetCures => "Pet Cure",
    BotSpellTypes::Resurrect => "Resurrect",
    BotSpellTypes::HateRedux => "Hate Reduction",
    BotSpellTypes::InCombatBuffSong => "In-Combat Buff Song",
    BotSpellTypes::OutOfCombatBuffSong => "Out-of-Combat Buff Song",
    BotSpellTypes::PreCombatBuff => "Pre-Combat Buff",
    BotSpellTypes::PreCombatBuffSong => "Pre-Combat Buff Song",
    BotSpellTypes::Fear => "Fear",
    BotSpellTypes::Stun => "Stun",
    BotSpellTypes::CompleteHeal => "Complete Heal",
    BotSpellTypes::FastHeals => "Fast Heal",
    BotSpellTypes::VeryFastHeals => "Very Fast Heal",
    BotSpellTypes::GroupHeals => "Group Heal",
    BotSpellTypes::GroupCompleteHeals => "Group Complete Heal",
    BotSpellTypes::GroupHoTHeals => "Group HoT Heal",
    BotSpellTypes::HoTHeals => "HoT Heal",
    BotSpellTypes::AENukes => "AE Nuke",
    BotSpellTypes::AERains => "AE Rain",
    BotSpellTypes::AEMez => "AE Mez",
    BotSpellTypes::AEStun => "AE Stun",
    BotSpellTypes::AEDebuff => "AE Debuff",
    BotSpellTypes::AESlow => "AE Slow",
    BotSpellTypes::AESnare => "AE Snare",
    BotSpellTypes::AEFear => "AE Fear",
    BotSpellTypes::AEDispel => "AE Dispel",
    BotSpellTypes::AERoot => "AE Root",
    BotSpellTypes::AEDoT => "AE DoT",
    BotSpellTypes::AELifetap => "AE Lifetap",
    BotSpellTypes::PBAENuke => "PBAE Nuke",
    BotSpellTypes::PetBuffs => "Pet Buff",
    BotSpellTypes::PetRegularHeals => "Pet Regular Heal",
    BotSpellTypes::PetCompleteHeals => "Pet Complete Heal",
    BotSpellTypes::PetFastHeals => "Pet Fast Heal",
    BotSpellTypes::PetVeryFastHeals => "Pet Very Fast Heal",
    BotSpellTypes::PetHoTHeals => "Pet HoT Heal",
    BotSpellTypes::DamageShields => "Damage Shield",
    BotSpellTypes::ResistBuffs => "Resist Buff",
    BotSpellTypes::PetDamageShields => "Pet Damage Shield",
    BotSpellTypes::PetResistBuffs => "Pet Resist Buff",
    BotSpellTypes::HateLine => "Hate Line",
    BotSpellTypes::AEHateLine => "AE Hate Line",
    BotSpellTypes::Lull => "Lull",
    BotSpellTypes::Teleport => "Teleport",
    BotSpellTypes::Succor => "Succor",
    BotSpellTypes::BindAffinity => "Bind Affinity",
    BotSpellTypes::Identify => "Identify",
    BotSpellTypes::Levitate => "Levitate",
    BotSpellTypes::Rune => "Rune",
    BotSpellTypes::WaterBreathing => "Water Breathing",
    BotSpellTypes::Size => "Size",
    BotSpellTypes::Invisibility => "Invisibility",
    BotSpellTypes::MovementSpeed => "Movement Speed",
    BotSpellTypes::SendHome => "Send Home",
    BotSpellTypes::SummonCorpse => "Summon Corpse",
    BotSpellTypes::AELull => "AE Lull"
];

$spell_type_short_names = [
    BotSpellTypes::Nuke => "nukes",
    BotSpellTypes::RegularHeal => "regularheals",
    BotSpellTypes::Root => "roots",
    BotSpellTypes::Buff => "buffs",
    BotSpellTypes::Escape => "escapes",
    BotSpellTypes::Pet => "pets",
    BotSpellTypes::Lifetap => "lifetaps",
    BotSpellTypes::Snare => "snares",
    BotSpellTypes::DOT => "dots",
    BotSpellTypes::Dispel => "dispels",
    BotSpellTypes::InCombatBuff => "incombatbuffs",
    BotSpellTypes::Mez => "mez",
    BotSpellTypes::Charm => "charms",
    BotSpellTypes::Slow => "slows",
    BotSpellTypes::Debuff => "debuffs",
    BotSpellTypes::Cure => "cures",
    BotSpellTypes::GroupCures => "groupcures",
    BotSpellTypes::PetCures => "petcures",
    BotSpellTypes::Resurrect => "resurrects",
    BotSpellTypes::HateRedux => "hateredux",
    BotSpellTypes::InCombatBuffSong => "incombatbuffsongs",
    BotSpellTypes::OutOfCombatBuffSong => "outofcombatbuffsongs",
    BotSpellTypes::PreCombatBuff => "precombatbuffs",
    BotSpellTypes::PreCombatBuffSong => "precombatbuffsongs",
    BotSpellTypes::Fear => "fears",
    BotSpellTypes::Stun => "stuns",
    BotSpellTypes::CompleteHeal => "completeheals",
    BotSpellTypes::FastHeals => "fastheals",
    BotSpellTypes::VeryFastHeals => "veryfastheals",
    BotSpellTypes::GroupHeals => "groupheals",
    BotSpellTypes::GroupCompleteHeals => "groupcompleteheals",
    BotSpellTypes::GroupHoTHeals => "grouphotheals",
    BotSpellTypes::HoTHeals => "hotheals",
    BotSpellTypes::AENukes => "aenukes",
    BotSpellTypes::AERains => "aerains",
    BotSpellTypes::AEMez => "aemez",
    BotSpellTypes::AEStun => "aestuns",
    BotSpellTypes::AEDebuff => "aedebuffs",
    BotSpellTypes::AESlow => "aeslows",
    BotSpellTypes::AESnare => "aesnares",
    BotSpellTypes::AEFear => "aefears",
    BotSpellTypes::AEDispel => "aedispels",
    BotSpellTypes::AERoot => "aeroots",
    BotSpellTypes::AEDoT => "aedots",
    BotSpellTypes::AELifetap => "aelifetaps",
    BotSpellTypes::PBAENuke => "pbaenukes",
    BotSpellTypes::PetBuffs => "petbuffs",
    BotSpellTypes::PetRegularHeals => "petregularheals",
    BotSpellTypes::PetCompleteHeals => "petcompleteheals",
    BotSpellTypes::PetFastHeals => "petfastheals",
    BotSpellTypes::PetVeryFastHeals => "petveryfastheals",
    BotSpellTypes::PetHoTHeals => "pethotheals",
    BotSpellTypes::DamageShields => "damageshields",
    BotSpellTypes::ResistBuffs => "resistbuffs",
    BotSpellTypes::PetDamageShields => "petdamageshields",
    BotSpellTypes::PetResistBuffs => "petresistbuffs",
    BotSpellTypes::HateLine => "hatelines",
    BotSpellTypes::AEHateLine => "aehatelines",
    BotSpellTypes::Lull => "lull",
    BotSpellTypes::Teleport => "teleport",
    BotSpellTypes::Succor => "succor",
    BotSpellTypes::BindAffinity => "bindaffinity",
    BotSpellTypes::Identify => "identify",
    BotSpellTypes::Levitate => "levitate",
    BotSpellTypes::Rune => "rune",
    BotSpellTypes::WaterBreathing => "waterbreathing",
    BotSpellTypes::Size => "size",
    BotSpellTypes::Invisibility => "invisibility",
    BotSpellTypes::MovementSpeed => "movementspeed",
    BotSpellTypes::SendHome => "sendhome",
    BotSpellTypes::SummonCorpse => "summoncorpse",
    BotSpellTypes::AELull => "aelull"
];

class BotStance {
    public const Invalid = 0;
    public const Passive = 1;
	public const Balanced = 2;
	public const Efficient = 3;
	public const Aggressive = 5;
	public const Assist = 6;
	public const Burn = 7;
	public const AEBurn = 9;

    public const START = self::Passive;
    public const END = self::AEBurn;
}

$bot_stance_names = [
   BotStance::Invalid => "Invalid",
   BotStance::Passive => "Passive",
   BotStance::Balanced => "Balanced",
   BotStance::Efficient => "Efficient",
   BotStance::Aggressive => "Aggressive",
   BotStance::Assist => "Assist",
   BotStance::Burn => "Burn",
   BotStance::AEBurn => "AEBurn"
];

$language['BOT_SETTING_OPTIONS'] = array (
    -1 => 'Any Race',
    1 => 'HUM',
    2 => 'BAR',
    4 => 'ERU',
    8 => 'ELF',
    16 => 'HIE',
    32 => 'DEF',
    64 => 'HEF',
    128 => 'DWF',
    256 => 'TRL',
    512 => 'OGR',
    1024 => 'HFL',
    2048 => 'GNM',
    4096 => 'IKS',
    8192 => 'VAH',
    16384 => 'FRG',
    32768 => 'DRK' //added 2/25/2014
);
