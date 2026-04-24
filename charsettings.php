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
 *  - Implement
 *  October 13, 2025 - Nite
 *   - Finish implementing
 **************************************************************************/

if ( !defined('INCHARBROWSER') )
{
    define('INCHARBROWSER', true);
}

include_once(__DIR__ . "/include/common.php");
include_once(__DIR__ . "/include/profile.php");
include_once(__DIR__ . "/include/db.php");

/*********************************************
 * SETUP CHARACTER CLASS & PERMISSIONS
*********************************************/
$charName = preg_Get_Post('char', '/^[a-zA-Z0-9]*$/', false, $language['MESSAGE_ERROR'], $language['MESSAGE_NO_CHAR'], true);

// char initializations
$char = new Charbrowser_Character($charName, $showsoftdelete, $charbrowser_is_admin_page); //the Charbrowser_Character class will sanitize the character name
$name = $char->GetValue('name');

// Prevent access if user level doesn't have permission
if (!OwnerCheck($char->char_id()) && $char->Permission('charsettings')) $cb_error->message_die($language['MESSAGE_NOTICE'],$language['MESSAGE_ITEM_NO_VIEW']);

/*********************************************
 * DROP HEADER
*********************************************/

$d_title = " - ".$name.$language['PAGE_TITLES_CHAR_SETTINGS'];
include(__DIR__ . "/include/header.php");

/*********************************************
 * DROP PROFILE MENU
*********************************************/

output_profile_menu($name, 'charsettings');

/*********************************************
 * POPULATE BODY
********************************************/

GenerateBotSettingsPage("charsettings", "charsettings_body.tpl", $char, $name);

include(__DIR__ . "/include/footer.php");
?>