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
 *   September 26, 2014 - Maudigan
 *      Updated character table name
 *   September 28, 2014 - Maudigan
 *      added code to destroy template when finished
 *      added code to monitor database performance
 *   May 24, 2016 - Maudigan
 *      general code cleanup, whitespace correction, removed old comments,
 *      organized some code. A lot has changed, but not much functionally
 *      do a compare to 2.41 to see the differences. 
 *      Implemented new database wrapper.
 *   January 7, 2018 - Maudigan
 *      Modified database to use a class.
 *   March 7, 2020 - Maudigan
 *      modified to accommodate soft deletes
 *   March 8, 2020 - Maudigan
 *      make soft deletes display if this is a wrapped install
 *      and the admin flag is turned on
 *   March 14, 2020 - Maudigan
 *      fixed the missing space between AND in the query
 *   March 15, 2020 - Maudigan
 *      implemented guild page
 *   March 22, 2020 - Maudigan
 *     impemented common.php
 *   April 2, 2020 - Maudigan
 *     dont show anon guild members names
 *   April 17, 2020 - Maudigan
 *     show a nicer error when there are no results
 *   May 4, 2020 - Maudigan
 *     reduce the nyumber of queries, implement the where building function
 ***************************************************************************/
 
 
/*********************************************
                 INCLUDES
*********************************************/ 
define('INCHARBROWSER', true);
include_once(__DIR__ . "/include/common.php");
include_once(__DIR__ . "/include/db.php");

 
/*********************************************
             GET/VALIDATE VARS
*********************************************/ 
$start      = (($_GET['start']) ? $_GET['start'] : "0");
$orderby    = (($_GET['orderby']) ? $_GET['orderby'] : "name");
$direction  = (($_GET['direction']=="DESC") ? "DESC" : "ASC");
$name       = $_GET['name'];
$guild      = $_GET['guild'];

//build baselink
$baselink= (($charbrowser_wrapped) ? $_SERVER['SCRIPT_NAME'] : "index.php") . "?page=search&name=$name&guild=$guild";

//security for injection attacks
if (!IsAlphaSpace($name)) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_NAME_ALPHA']);
if (!IsAlphaSpace($guild)) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_GUILD_ALPHA']);
if (!IsAlphaSpace($orderby)) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ORDER_ALPHA']);
if (!is_numeric($start)) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_START_NUMERIC']);
 
 
/*********************************************
        BUILD AND EXECUTE THE SEARCH
*********************************************/ 
//build where clause
$filters = array();
if (!$showsoftdelete && !$charbrowser_is_admin_page) $filters[] = "character_data.deleted_at IS NULL"; 
if ($name) $filters[] = "character_data.name LIKE '%".str_replace("_", "%", str_replace(" ","%",$name))."%'"; 
if ($guild) {
   $filters[] = "guilds.name LIKE '%".str_replace("_", "%", str_replace(" ","%",$guild))."%'";
   
   //if the char is anon, dont show them in a guild search
   if (!$showguildwhenanon && !$charbrowser_is_admin_page) $filters[] = "character_data.anon != '1'";
}
$where = generate_where($filters);

//build the query, leave a spot for the where
//and the orderby clauses
$tpl = <<<TPL
SELECT character_data.class, character_data.level, 
       character_data.name, guilds.name AS guildname, 
       character_data.deleted_at, character_data.anon
FROM character_data
INNER JOIN account ON account.id = character_data.account_id
LEFT JOIN guild_members
       ON character_data.id = guild_members.char_id 
LEFT JOIN guilds
       ON guilds.id = guild_members.guild_id 
%s
AND status < 80
ORDER BY %s %s
TPL;
 
$query = sprintf($tpl, $where, $orderby, $direction);
$result = $cbsql->query($query);

//fetch the results
$characters = $cbsql->fetch_all($result);
$totalchars = count($characters);

//error if there is no guild
if (!$totalchars) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_NO_RESULTS_ITEMS']);

 
 
/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$language['PAGE_TITLES_SEARCH'];
include(__DIR__ . "/include/header.php");
 
 
/*********************************************
              POPULATE BODY
*********************************************/
//build body template
$cb_template->set_filenames(array(
   'body' => 'search_body.tpl')
);

$cb_template->assign_vars(array(  
   'ORDER_LINK' => $baselink."&start=$start&direction=".(($direction=="ASC") ? "DESC":"ASC"), 
   'PAGINATION' => cb_generate_pagination("$baselink&orderby=$orderby&direction=$direction", $totalchars, $numToDisplay, $start, true),
   
   'L_RESULTS' => $language['SEARCH_RESULTS'],
   'L_NAME' => $language['SEARCH_NAME'],
   'L_LEVEL' => $language['SEARCH_LEVEL'],
   'L_CLASS' => $language['SEARCH_CLASS'],)
);

$finish = $start + $numToDisplay;
for ($i = $start; $i < $finish; $i++) {
   $character = $characters[$i];
   //dont show anon guild names unless config enables it
   if ($character["anon"] != 1 || $showguildwhenanon || $charbrowser_is_admin_page) {
      $charguildname = getGuildLink($character["guildname"]);
   }
   else {
      $charguildname = "";
   }
   $cb_template->assign_both_block_vars("characters", array( 
      'CLASS' => $dbclassnames[$character["class"]],      
      'LEVEL' => $character["level"],     
      'DELETED' => (($character["deleted_at"]) ? " ".$language['CHAR_DELETED']:""),
      'NAME' => $character["name"],
      'GUILD_NAME' => $charguildname )
   );
}
 
 
/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$cb_template->pparse('body');

$cb_template->destroy;

include(__DIR__ . "/include/footer.php");
?>