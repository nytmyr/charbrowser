<?php

class BotSettingCategories
{
    public const BASE_SETTING = 0;
    public const SPELL_HOLD = 1;
    public const SPELL_DELAY = 2;
    public const SPELL_MIN_THRESHOLD = 3;
    public const SPELL_MAX_THRESHOLD = 4;
    public const SPELL_TYPE_RESIST_LIMIT = 5;
    public const SPELL_TYPE_AGGRO_CHECK = 6;
    public const SPELL_TYPE_MIN_MANA_PCT = 7;
    public const SPELL_TYPE_MAX_MANA_PCT = 8;
    public const SPELL_TYPE_MIN_HP_PCT = 9;
    public const SPELL_TYPE_MAX_HP_PCT = 10;
    public const SPELL_TYPE_IDLE_PRIORITY = 11;
    public const SPELL_TYPE_ENGAGED_PRIORITY = 12;
    public const SPELL_TYPE_PURSUE_PRIORITY = 13;
    public const SPELL_TYPE_AE_OR_GROUP_TARGET_COUNT = 14;
    public const SPELL_TYPE_ANNOUNCE_CAST = 15;

    public const START = self::BASE_SETTING;
    public const START_NO_BASE = self::SPELL_HOLD;
    public const START_CLIENT = self::SPELL_DELAY;
    public const END_CLIENT = self::SPELL_MAX_THRESHOLD;
    public const END = self::SPELL_TYPE_ANNOUNCE_CAST;
}

$bot_setting_category_names = [
    BotSettingCategories::BASE_SETTING => "Base Setting",
    BotSettingCategories::SPELL_HOLD => "Spell Holds",
    BotSettingCategories::SPELL_DELAY => "Spell Delays",
    BotSettingCategories::SPELL_MIN_THRESHOLD => "Spell Minimum Thresholds",
    BotSettingCategories::SPELL_MAX_THRESHOLD => "Spell Maximum Thresholds",
    BotSettingCategories::SPELL_TYPE_RESIST_LIMIT => "Spell Resist Limits",
    BotSettingCategories::SPELL_TYPE_AGGRO_CHECK => "Spell Aggro Checks",
    BotSettingCategories::SPELL_TYPE_MIN_MANA_PCT => "Spell Min Mana Percent",
    BotSettingCategories::SPELL_TYPE_MAX_MANA_PCT => "Spell Max Mana Percent",
    BotSettingCategories::SPELL_TYPE_MIN_HP_PCT => "Spell Min HP Percent",
    BotSettingCategories::SPELL_TYPE_MAX_HP_PCT => "Spell Max HP Percent",
    BotSettingCategories::SPELL_TYPE_IDLE_PRIORITY => "Spell Idle Priority",
    BotSettingCategories::SPELL_TYPE_ENGAGED_PRIORITY => "Spell Engaged Priority",
    BotSettingCategories::SPELL_TYPE_PURSUE_PRIORITY => "Spell Pursue Priority",
    BotSettingCategories::SPELL_TYPE_AE_OR_GROUP_TARGET_COUNT => "Spell Target Counts",
    BotSettingCategories::SPELL_TYPE_ANNOUNCE_CAST => "Spell Announce Casts"
];

$bot_setting_category_short_names = [
    BotSettingCategories::BASE_SETTING => "BaseSetting",
    BotSettingCategories::SPELL_HOLD => "SpellHolds",
    BotSettingCategories::SPELL_DELAY => "SpellDelays",
    BotSettingCategories::SPELL_MIN_THRESHOLD => "SpellMinThresholds",
    BotSettingCategories::SPELL_MAX_THRESHOLD => "SpellMaxThresholds",
    BotSettingCategories::SPELL_TYPE_RESIST_LIMIT => "SpellResistLimits",
    BotSettingCategories::SPELL_TYPE_AGGRO_CHECK => "SpellAggroChecks",
    BotSettingCategories::SPELL_TYPE_MIN_MANA_PCT => "SpellMinManaPct",
    BotSettingCategories::SPELL_TYPE_MAX_MANA_PCT => "SpellMaxManaPct",
    BotSettingCategories::SPELL_TYPE_MIN_HP_PCT => "SpellMinHPPct",
    BotSettingCategories::SPELL_TYPE_MAX_HP_PCT => "SpellMaxHPPct",
    BotSettingCategories::SPELL_TYPE_IDLE_PRIORITY => "SpellIdlePriority",
    BotSettingCategories::SPELL_TYPE_ENGAGED_PRIORITY => "SpellEngagedPriority",
    BotSettingCategories::SPELL_TYPE_PURSUE_PRIORITY => "SpellPursuePriority",
    BotSettingCategories::SPELL_TYPE_AE_OR_GROUP_TARGET_COUNT => "SpellTargetCounts",
    BotSettingCategories::SPELL_TYPE_ANNOUNCE_CAST => "SpellAnnounceCasts"
];

$bot_setting_window_tab_names = [
    BotSettingCategories::BASE_SETTING => $language['BOT_SETTINGS_BASE'],
    BotSettingCategories::SPELL_HOLD => $language['BOT_SETTINGS_HOLDS'],
    BotSettingCategories::SPELL_DELAY => $language['BOT_SETTINGS_DELAYS'],
    BotSettingCategories::SPELL_MIN_THRESHOLD => $language['BOT_SETTINGS_MINTHRESHOLDS'],
    BotSettingCategories::SPELL_MAX_THRESHOLD => $language['BOT_SETTINGS_MAXTHRESHOLDS'],
    BotSettingCategories::SPELL_TYPE_RESIST_LIMIT => $language['BOT_SETTINGS_ResistLimit'],
    BotSettingCategories::SPELL_TYPE_AGGRO_CHECK => $language['BOT_SETTINGS_AggroCheck'],
    BotSettingCategories::SPELL_TYPE_MIN_MANA_PCT => $language['BOT_SETTINGS_MinManaPct'],
    BotSettingCategories::SPELL_TYPE_MAX_MANA_PCT => $language['BOT_SETTINGS_MaxManaPct'],
    BotSettingCategories::SPELL_TYPE_MIN_HP_PCT => $language['BOT_SETTINGS_MinHPPct'],
    BotSettingCategories::SPELL_TYPE_MAX_HP_PCT => $language['BOT_SETTINGS_MaxHPPct'],
    BotSettingCategories::SPELL_TYPE_IDLE_PRIORITY => $language['BOT_SETTINGS_IdlePriority'],
    BotSettingCategories::SPELL_TYPE_ENGAGED_PRIORITY => $language['BOT_SETTINGS_EngagedPriority'],
    BotSettingCategories::SPELL_TYPE_PURSUE_PRIORITY => $language['BOT_SETTINGS_PursuePriority'],
    BotSettingCategories::SPELL_TYPE_AE_OR_GROUP_TARGET_COUNT => $language['BOT_SETTINGS_AEOrGroupTargetCount'],
    BotSettingCategories::SPELL_TYPE_ANNOUNCE_CAST => $language['BOT_SETTINGS_AnnounceCast']
];

$bot_setting_category_descriptions = [
    BotSettingCategories::BASE_SETTING => "Basic settings to adjust your bot's behavior",
    BotSettingCategories::SPELL_HOLD => "Controls whether a bot holds the specified spell type or not",
    BotSettingCategories::SPELL_DELAY => "Controls the delay between casts for a specific spell type",
    BotSettingCategories::SPELL_MIN_THRESHOLD => "Controls the maximum target HP threshold for a spell to be cast for a specific type",
    BotSettingCategories::SPELL_MAX_THRESHOLD => "Controls the minimum target HP threshold for a spell to be cast for a specific type",
    BotSettingCategories::SPELL_TYPE_RESIST_LIMIT => "Controls the resist limits for bots to cast spells on their target",
    BotSettingCategories::SPELL_TYPE_AGGRO_CHECK => "Toggles whether or not bots will cast a spell type if they think it will get them aggro",
    BotSettingCategories::SPELL_TYPE_MIN_MANA_PCT => "Controls at what mana percent a bot will start casting different spell types",
    BotSettingCategories::SPELL_TYPE_MAX_MANA_PCT => "Controls at what mana percent a bot will stop casting different spell types",
    BotSettingCategories::SPELL_TYPE_MIN_HP_PCT => "Controls at what HP percent a bot will start casting different spell types",
    BotSettingCategories::SPELL_TYPE_MAX_HP_PCT => "Controls at what HP percent a bot will stop casting different spell types",
    BotSettingCategories::SPELL_TYPE_IDLE_PRIORITY => "Controls the order of casts by spell type when out of combat",
    BotSettingCategories::SPELL_TYPE_ENGAGED_PRIORITY => "Controls the order of casts by spell type when engaged in combat",
    BotSettingCategories::SPELL_TYPE_PURSUE_PRIORITY => "Controls the order of casts by spell type when pursuing in combat",
    BotSettingCategories::SPELL_TYPE_AE_OR_GROUP_TARGET_COUNT => "Sets the required target amount for group/AE spells by spell type",
    BotSettingCategories::SPELL_TYPE_ANNOUNCE_CAST => "Turn on or off cast announcements by spell type"
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
    BotSettingCategories::BASE_SETTING => "Base Setting",
    BotSettingCategories::SPELL_HOLD => "^spellholds",
    BotSettingCategories::SPELL_DELAY => "^spelldelays",
    BotSettingCategories::SPELL_MIN_THRESHOLD => "^spellminthresholds",
    BotSettingCategories::SPELL_MAX_THRESHOLD => "^spellmaxthresholds",
    BotSettingCategories::SPELL_TYPE_RESIST_LIMIT => "^spellresistlimits",
    BotSettingCategories::SPELL_TYPE_AGGRO_CHECK => "^spellaggrochecks",
    BotSettingCategories::SPELL_TYPE_MIN_MANA_PCT => "^spellminmanapct",
    BotSettingCategories::SPELL_TYPE_MAX_MANA_PCT => "^spellmaxmanapct",
    BotSettingCategories::SPELL_TYPE_MIN_HP_PCT => "^spellminhppct",
    BotSettingCategories::SPELL_TYPE_MAX_HP_PCT => "^spellmaxhppct",
    BotSettingCategories::SPELL_TYPE_IDLE_PRIORITY => "^spellidlepriority",
    BotSettingCategories::SPELL_TYPE_ENGAGED_PRIORITY => "^spellengagedpriority",
    BotSettingCategories::SPELL_TYPE_PURSUE_PRIORITY => "^spellpursuepriority",
    BotSettingCategories::SPELL_TYPE_AE_OR_GROUP_TARGET_COUNT => "^spelltargetcount",
    BotSettingCategories::SPELL_TYPE_ANNOUNCE_CAST => "^spellannouncecasts"
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

$botBaseSettings_names = [
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
