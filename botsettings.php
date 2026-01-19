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
 * October 13, 2025 - Nite
 *  - Finish implementing
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
 * SETUP CHARACTER CLASS & PERMISSIONS
*********************************************/

$botName = preg_Get_Post('bot', '/^[a-zA-Z]+$/', false, $language['MESSAGE_ERROR'],$language['MESSAGE_NO_BOT'], true);

// bot initializations
$bot = new Charbrowser_Bot($botName); //the profile class will sanitize the bot name

$charID = $bot->char_id();
$botName = $bot->GetValue('name');

// char initialization
$char = new Charbrowser_Character($charID, $showsoftdelete, $charbrowser_is_admin_page);
$charName = $char->GetValue('name');

// Prevent access if user level doesn't have permission
if (!OwnerCheck($charID) && $char->Permission('botsettings')) $cb_error->message_die($language['MESSAGE_NOTICE'],$language['MESSAGE_ITEM_NO_VIEW']);

// Get selected stance (fallback to bot's current stance)
$selected_stance = preg_Get_Post('stance', '/^[1-9]+$/', $bot->GetStance(), $language['MESSAGE_ERROR'], $language['MESSAGE_INVALID_STANCE']);

if (!IsValidBotStance($selected_stance)) {
    $selected_stance = $bot->GetStance() ? $bot->GetStance() : BotStance::Efficient;
}

$base_link = (($charbrowser_wrapped) ? $_SERVER['SCRIPT_NAME'] : "index.php") . "?page=botsettings&bot=$botName";

/*********************************************
 * DROP HEADER
********************************************/

$d_title = " - " . $botName . $language['PAGE_TITLES_CHARACTER'];
include(__DIR__ . "/include/header.php");

/*********************************************
 * DROP PROFILE MENU
*********************************************/

output_bot_profile_menu($charName, $botName, 'botsettings');

/*********************************************
 * POPULATE BODY
********************************************/

GenerateBotSettingsPage("botsettings", "botsettings_body.tpl", $bot, $botName, true, $selected_stance, $base_link);

include(__DIR__ . "/include/footer.php");
?>