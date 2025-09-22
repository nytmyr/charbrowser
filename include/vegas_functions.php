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
*                           Maudigan(Airwalking)
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

?>