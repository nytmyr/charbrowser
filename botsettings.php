<?php
/***************************************************************************
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Portions of this program are derived from publicly licensed software
 * projects including, but not limited to phpBB, Magelo Clone,
 * EQEmulator, EQEditor, and Allakhazam Clone.
 *
 *                                   Author:
 *                                    Nite
 *
 * October 1, 2025 - Nite
 * - Implement
 **************************************************************************/

if ( !defined('INCHARBROWSER') )
{
    define('INCHARBROWSER', true);
}
include_once(__DIR__ . "/include/common.php");
include_once(__DIR__ . "/include/profile.php");
include_once(__DIR__ . "/include/db.php");
include_once(__DIR__ . "/include/bot_profile.php");
include_once(__DIR__ . "/include/bot.php");

/*********************************************
SETUP CHARACTER CLASS & PERMISSIONS
 *********************************************/
$botName = preg_Get_Post('bot', '/^[a-zA-Z]+$/', false, $language['MESSAGE_ERROR'],$language['MESSAGE_NO_BOT'], true);

//bot initializations
$bot = new Charbrowser_Bot($botName); //the profile class will sanitize the bot name

$charID = $bot->char_id();
$botID = $bot->bot_id();
$botName = $bot->GetValue('name');
$userip = getIPAddress();
$ownercheck = OwnerCheck($charID);

//char initialization
$char = new Charbrowser_Character($charID, $showsoftdelete, $charbrowser_is_admin_page);
$charName = $char->GetValue('name');

//block view if user level doesnt have permission
if ($char->Permission('botsettings')) $cb_error->message_die($language['MESSAGE_NOTICE'],$language['MESSAGE_ITEM_NO_VIEW']);

// Get selected stance (fallback to bot's default)
$selected_stance = preg_Get_Post('stance', '/^[1-9]+$/', $bot->GetStance(), $language['MESSAGE_ERROR'], $language['MESSAGE_INVALID_STANCE']);

$current_stance = $bot->GetStance();

if (!IsValidBotStance($selected_stance)) {
    $selected_stance = $bot->GetStance();
}

$baselink=(($charbrowser_wrapped) ? $_SERVER['SCRIPT_NAME'] : "index.php") . "?page=botsettings&bot=$botName";

/*********************************************
 * GATHER RELEVANT PAGE DATA
 ********************************************/
//get bot info
$class = $bot->GetValue('class');

/*********************************************
 * DROP HEADER
 ********************************************/
$d_title = " - " . $botName . $language['PAGE_TITLES_CHARACTER'];
include(__DIR__ . "/include/header.php");

/*********************************************
DROP PROFILE MENU
 *********************************************/
output_bot_profile_menu($charName, $botName, 'botsettings');

/*********************************************
 * POPULATE BODY
 ********************************************/

// Gather bot settings from database
$bot_settings = $bot->GetTable('bot_settings');
$settings = array();

$bot_default_settings = $bot->GetTable('bot_default_settings');

if (!is_array($bot_default_settings)) {
    $cb_error->message_die($language['MESSAGE_ERROR'], $language['MESSAGE_ERROR_BOT_DEFAULT_SETTINGS']);
}

// Build stance dropdown
$stance_names = [
    BotStance::Passive => BotStance::Passive . "- Passive" . ($current_stance == BotStance::Passive ? ' (Current)': ''),
    BotStance::Balanced => BotStance::Balanced . "- Balanced" . ($current_stance == BotStance::Balanced ? ' (Current)': ''),
    BotStance::Efficient => BotStance::Efficient . "- Efficient" . ($current_stance == BotStance::Efficient ? ' (Current)': ''),
    BotStance::Aggressive => BotStance::Aggressive . "- Aggressive" . ($current_stance == BotStance::Aggressive ? ' (Current)': ''),
    BotStance::Assist => BotStance::Assist . "- Assist" . ($current_stance == BotStance::Assist ? ' (Current)': ''),
    BotStance::Burn => BotStance::Burn . "- Burn" . ($current_stance == BotStance::Burn ? ' (Current)': ''),
    BotStance::AEBurn => BotStance::AEBurn . "- AEBurn" . ($current_stance == BotStance::AEBurn ? ' (Current)': ''),
];

$stance_options = '';

for ($s = BotStance::START; $s <= BotStance::END; ++$s) {
    if (IsValidBotStance($s)) {
        $selected = ($s == $selected_stance) ? ' selected' : '';
        $name = $stance_names[$s] ?? "Stance $s";
        $stance_options .= "<option value='$s'$selected>$name</option>";
    }
}

#$stance_html = "<label for='stance-select'>Stance: </label><select id='stance-select' onchange=\"window.location.href = '$baselink&stance=' + this.value;\">$stance_options</select>";
$stance_html = "<label for='stance-select'>Stance: </label><select id='stance-select' onchange=\"var newUrl = '$baselink'; if (newUrl.indexOf('stance=') !== -1) { newUrl = newUrl.replace(/&?stance=[0-9]+/, ''); } newUrl += (newUrl.indexOf('?') !== -1 ? '&' : '?') + 'stance=' + this.value; window.location.href = newUrl;\">$stance_options</select>";

$settingsections = array();

for ($i = BotSettingCategories::START; $i <= BotSettingCategories::END; ++$i) {
    if ($i == BotSettingCategories::BASE_SETTING) {
        for ($x = BotBaseSettings::START; $x <= BotBaseSettings::END; ++$x) {
            $command_name = $bot_base_setting_commands[$x] ?? 'Unknown Command';
            $settingsections[$bot_setting_window_tab_names[$i]][$x] = array('ID' => $x, 'NAME' => '<font color=teal>' . $botBaseSettings_names[$x] . '</font>', 'VALUE' =>  isset($bot_settings[$selected_stance][$i][$x]) ? GetSettingValueSuffix($i, $x, $bot_settings[$selected_stance][$i][$x], $bot->GetValue('level'), true) . '</font>' : GetSettingValueSuffix($i, $x, $bot_default_settings[$selected_stance][$i][$x], $bot->GetValue('level')) . '</font>', 'COMMAND' => '<font color=lightslategrey>' . $command_name . '</font>'); // deleteme
        }
    }
    else {
        for ($x = BotSpellTypes::START; $x <= BotSpellTypes::END; ++$x) {
            $settingsections[$bot_setting_window_tab_names[$i]][$x] = array('ID' => $x, 'NAME' => '<font color=teal>' . $spell_type_names[$x] . '</font>', 'VALUE' =>  isset($bot_settings[$selected_stance][$i][$x]) ? GetSettingValueSuffix($i, $x, $bot_settings[$selected_stance][$i][$x], $bot->GetValue('level'), true) . '</font>' : GetSettingValueSuffix($i, $x, $bot_default_settings[$selected_stance][$i][$x], $bot->GetValue('level')) . '</font>', 'COMMAND' => '<font color=lightslategrey>' . ($bot_setting_category_commands[$i] . " " . $x ?? 'Unknown Command') . '</font>'); // deleteme
        }
    }
}

$cb_template->set_filenames(array(
    'botsettings' => 'botsettings_body.tpl')
);

$i = 0;

foreach ($settingsections as $header => $setting) {
    //echo "DEBUG: Starting header='$header', i=$i<br>";
    $cb_template->assign_block_vars("section",
        array(
            'TEXT' => ($i == BotSettingCategories::BASE_SETTING ? 'Setting Name' : 'Spell Type'),
            'DESCRIPTION' => ($i == BotSettingCategories::BASE_SETTING ? FormBaseSettingsDescriptionString(BotBaseSettings::START) : $bot_setting_category_descriptions[$i]),
            'TEXTA' => 'Value',
            'TEXTB' => 'Command',
            'TAB' => $header,
            'INDEX' => $i
        )
    );

    $current_index = $i++;  // Assign, then increment

    //echo "DEBUG: Assigned INDEX=$current_index for $header, checking sort condition...<br>";

    // Sort ONLY idle, engaged and pursue
    if (in_array($current_index, [
        BotSettingCategories::SPELL_TYPE_IDLE_PRIORITY,
        BotSettingCategories::SPELL_TYPE_ENGAGED_PRIORITY,
        BotSettingCategories::SPELL_TYPE_PURSUE_PRIORITY
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
        if ($current_index == BotSettingCategories::SPELL_TYPE_AE_OR_GROUP_TARGET_COUNT && !IsAEOrGroupBotSpellType($x)) {
            //echo "DEBUG: Skipping non-AE row $x for AE category<br>";
            ++$x;
            continue;
        }

        ++$x;
        $cb_template->assign_both_block_vars("section.settingrow", $settingrow);
    }
    //echo "DEBUG: Finished $header (assigned $x rows)<br><hr>";
}

$cb_template->assign_both_vars(array(
        'NAME' => $botName)
);

$cb_template->assign_vars(array(
    'L_BOT_SETTINGS' => $language['SETTINGS_BOT_SETTINGS'],
    'L_BOT_OPTIONS' => $language['SETTINGS_BOT_OPTIONS'],
    'L_DONE' => $language['BUTTON_DONE'],
    'STANCE_SELECT' => $stance_html,  // Pass stance dropdown to template
    'NOTE' => "* signifies that the setting has been modified"  // Pass stance dropdown to template
));

/*********************************************
OUTPUT BODY AND FOOTER
 *********************************************/
$cb_template->pparse('botsettings');

$cb_template->destroy();

include(__DIR__ . "/include/footer.php");
?>