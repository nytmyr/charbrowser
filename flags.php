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
 *   March 1, 2011 
 *      Planes of Power updated to reflect current PEQ Quest SVN flagging 
 *      Gates of Discord updated through Qvic access per PEQ Quest SVN flagging 
 *      Gates of Discord updated through Txevu assuming PEQ design does not change 
 *   August 1, 2011
 *      Fixed misprint on GOD flag, KT_2
 *   March 19, 2012
 *      Fixed misprint on GOD flag, KT_3
 *   November 17, 2013 - Sorvani
 *      Fixed bad getflag conditions in sewer 2/3/4 sections
 *      Fixed bad language array index in sewer 4 section
 *   September 26, 2014 - Maudigan
 *      Updated character table name
 *   September 28, 2014 - Maudigan
 *      added code to monitor database performance
 *      altered character profile initialization to remove redundant query
 *   May 24, 2016 - Maudigan
 *      general code cleanup, whitespace correction, removed old comments,
 *      organized some code. A lot has changed, but not much functionally
 *      do a compare to 2.41 to see the differences. 
 *      Implemented new database wrapper.
 *   May 17, 2017 - Maudigan
 *      Added omens of war flags.
 *   January 7, 2018 - Maudigan
 *      Modified database to use a class.
 *   March 9, 2020 - Maudigan
 *      modularized the profile menu output
 *   March 22, 2020 - Maudigan
 *     impemented common.php
 *   April 25, 2020 - Maudigan
 *     implement multi-tenancy
 *   January 17, 2022 - Maudigan
 *     implemented databucket support for Vxed flags
 *
 ***************************************************************************/ 
   
 
/*********************************************
                 INCLUDES
*********************************************/ 
define('INCHARBROWSER', true); 
include_once(__DIR__ . "/include/common.php"); 
include_once(__DIR__ . "/include/profile.php"); 
include_once(__DIR__ . "/include/db.php");
 
 
/*********************************************
             SUPPORT FUNCTIONS
*********************************************/
//check a quest global
function getflag($condition, $flagname) { 
   global $quest_globals;    
   if (!array_key_exists($flagname,$quest_globals)) return 0; 
   if ($quest_globals[$flagname]<$condition) return 0; 
   return 1; 
} 

//check a databuket value
function getdatabucket($keyname) { 
   global $cbsql;    
    $tpl = <<<TPL
   SELECT value 
   FROM data_buckets 
   WHERE `key` = '%s'
TPL;
   $query = sprintf($tpl, $keyname);
   $result = $cbsql->query($query);
   if($row = $cbsql->nextrow($result)) 
      return $row['value'];   
   else  
      return null;
} 


//check a quest global bit
function getbitflag($bitset, $flagname) { 
   global $quest_globals;    
   if (!array_key_exists($flagname,$quest_globals)) return 0; 
   if ($quest_globals[$flagname] & $bitset) return 1; 
   return 0; 
} 


//check a zoneflag
function getzoneflag($zoneid) { 
   global $zone_flags;      
   if (!in_array($zoneid, $zone_flags)) return 0; 
   return 1; 
} 
  
 
/*********************************************
         SETUP PROFILE/PERMISSIONS
*********************************************/
if(!$_GET['char']) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_NO_CHAR']); 
else $charName = $_GET['char']; 

//character initializations
$char = new profile($charName, $cbsql, $cbsql_content, $language, $showsoftdelete, $charbrowser_is_admin_page); //the profile class will sanitize the character name
$charID = $char->char_id(); 
$name = $char->GetValue('name');
$mypermission = GetPermissions($char->GetValue('gm'), $char->GetValue('anon'), $char->char_id());

//block view if user level doesnt have permission 
if ($mypermission['flags']) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ITEM_NO_VIEW']); 
 
 
/*********************************************
        GATHER RELEVANT PAGE DATA
*********************************************/
//get quest globals from the db
$tpl = <<<TPL
SELECT name, value 
FROM quest_globals 
WHERE charid = %s
TPL;
$query = sprintf($tpl, $charID);
$result = $cbsql->query($query);
$quest_globals = array();
while($row = $cbsql->nextrow($result)) 
   $quest_globals[$row['name']] = $row['value']; 

//get zone flags from the db
$tpl = <<<TPL
SELECT zoneID 
FROM zone_flags 
WHERE charID = %s
TPL;
$query = sprintf($tpl, $charID);
$result = $cbsql->query($query);
$zone_flags = array();
while($row = $cbsql->nextrow($result)) 
   $zone_flags[] = $row['zoneID']; 
 
 
/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$name.$language['PAGE_TITLES_FLAGS']; 
include(__DIR__ . "/include/header.php"); 
 
 
/*********************************************
            DROP PROFILE MENU
*********************************************/
output_profile_menu($name, 'flags');
 
 
/*********************************************
              POPULATE BODY
*********************************************/
$cb_template->set_filenames(array( 
   'flags' => 'flags_body.tpl') 
); 


$cb_template->assign_vars(array(  
   'NAME' => $name, 

   'L_DONE' => $language['BUTTON_DONE'], 
   'L_FLAGS' => $language['FLAG_FLAGS']) 
); 

//because they enabled the level bypass and the fact that clicking the door is what sets your zone flag. 
//this will also be important when the 85/15 raid rule is implemented for letting people into zones. 
//for most of the PoP zones, we can not just check the zone flag to know if we have the flag. 
//for each zone i used the zone flag in combination with enough flags for each zone that it would not show erroneously. 
//for some zones it is only 1 other flag, for others it was multiple other flags. 

// use HasFlag in if statement and then set the $cb_template then reuse $HasFlag 
$HasFlag = 0; 


/*********************************************
              MAIN MENUS
*********************************************/

//POP
$cb_template->assign_both_block_vars( "mainhead" , array( 'TEXT' => $language['FLAG_PoP']) ); 

if (getzoneflag(221) && getflag(1, "pop_pon_hedge_jezith") && getflag(1, "pop_pon_construct")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$cb_template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 1, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_PoP_PoNB']) ); 

if (getzoneflag(214) && getflag(1, "pop_poi_behometh_preflag") && getflag(1, "pop_poi_behometh_flag")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$cb_template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 2, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_PoP_PoTactics']) ); 

if (getzoneflag(200) && getflag(1, "pop_pod_elder_fuirstel")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$cb_template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 3, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_PoP_CoD']) ); 

if (getzoneflag(208) && getflag(1, "pop_poj_valor_storms")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$cb_template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 4, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_PoP_PoSPoV']) ); 

if (getzoneflag(211) && getflag(1, "pop_poj_valor_storms") && getflag(1, "pop_pov_aerin_dar")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$cb_template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 5, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_PoP_HoHA']) ); 

if (getzoneflag(209) && getflag(1, "pop_poj_valor_storms") && getflag(1, "pop_pos_askr_the_lost_final")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$cb_template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 6, 'FLAG' =>  $HasFlag, 'TEXT' => $language['FLAG_PoP_BoT']) ); 

if (getzoneflag(220) && getflag(1, "pop_poj_valor_storms") && getflag(1, "pop_pov_aerin_dar") && getflag(1, "pop_hoh_faye") && getflag(1, "pop_hoh_trell") && getflag(1, "pop_hoh_garn")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$cb_template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 7, 'FLAG' =>  $HasFlag, 'TEXT' => $language['FLAG_PoP_HoHB']) ); 

if (getzoneflag(207) && getflag(1, "pop_pod_elder_fuirstel") && getflag(1, "pop_ponb_poxbourne") && getflag(1, "pop_cod_final")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$cb_template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 8, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_PoP_PoTorment']) ); 

if (getzoneflag(212) && getflag(1, "pop_poi_behometh_flag") && getflag(1, "pop_tactics_tallon") && getflag(1, "pop_tactics_vallon") && getflag(1, "pop_hohb_marr") && getflag(1, "pop_pot_saryrn_final")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$cb_template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 9, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_PoP_SolRoTower']) ); 

if (getzoneflag(217) && getflag(1, "pop_poi_behometh_flag") && getflag(1, "pop_tactics_tallon") && getflag(1, "pop_tactics_vallon") && getflag(1, "pop_hohb_marr") && getflag(1, "pop_tactics_ralloz") && getflag(1, "pop_sol_ro_arlyxir") && getflag(1, "pop_sol_ro_dresolik") && getflag(1, "pop_sol_ro_jiva") && getflag(1, "pop_sol_ro_rizlona") && getflag(1, "pop_sol_ro_xuzl") && getflag(1, "pop_sol_ro_solusk")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$cb_template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 10, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_PoP_PoFire']) ); 

if (getzoneflag(216) && getflag(1, "pop_elemental_grand_librarian")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$cb_template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 11, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_PoP_PoAirEarthWater']) ); 

if (getflag(1, "pop_time_maelin") && getflag(1, "pop_fire_fennin_projection") && getflag(1, "pop_wind_xegony_projection") && getflag(1, "pop_water_coirnav_projection") && getflag(1, "pop_eartha_arbitor_projection") && getflag(1, "pop_earthb_rathe")) { $HasFlag = 1; } else { $HasFlag = 0; } 
$cb_template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 12, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_PoP_PoTime']) ); 


//GoD
#$cb_template->assign_both_block_vars( "mainhead" , array( 'TEXT' => $language['FLAG_GoD']) ); 
#$cb_template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 13, 'FLAG' => getflag(1,"god_vxed_access"), 'TEXT' => $language['FLAG_GoD_Vxed']) ); 
#$cb_template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 14, 'FLAG' => getflag(1,"god_tipt_access"), 'TEXT' => $language['FLAG_GoD_Tipt']) ); 
#
#if (getzoneflag(293) && getflag(1, "god_vxed_access") && getflag(1, "god_tipt_access") && getflag(1, "god_kodtaz_access")) { $HasFlag = 1; } else { $HasFlag = 0; } 
#$cb_template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 15, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_GoD_KT_1']) ); 
#$cb_template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 16, 'FLAG' => getflag(12,"ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_R3']) ); 
#$cb_template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 17, 'FLAG' => getflag(14,"ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_R4']) ); 
#
#if (getzoneflag(295) && getflag(1, "god_qvic_access")) { $HasFlag = 1; } else { $HasFlag = 0; } 
#$cb_template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 18, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_GoD_Qvic_1']) ); 
#
#if (getzoneflag(297) && getflag(1, "god_txevu_access")) { $HasFlag = 1; } else { $HasFlag = 0; } 
#$cb_template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 19, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_GoD_Txevu_1']) ); 


//OOW
#$cb_template->assign_both_block_vars( "mainhead" , array( 'TEXT' => $language['FLAG_OOW']) ); 
#
#if (getflag(63, "mpg_group_trials")) { $HasFlag = 1; } else { $HasFlag = 0; } 
#$cb_template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 20, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_OOW_MPG']) ); 
#
#//TODO get criteria for MPG, zone flags?
#if (getflag(63, "mpg_raid_trials") && getflag(1, "oow_rss_taromani_insignias")) { $HasFlag = 1; } else { $HasFlag = 0; } 
#$cb_template->assign_both_block_vars( "mainhead.main" , array( 'ID' => 21, 'FLAG' => $HasFlag, 'TEXT' => $language['FLAG_OOW_COA']) ); 



/*********************************************
           SECONDARY/SUB MENUS POP
*********************************************/
//PoN B 
$cb_template->assign_both_block_vars( "head" , array( 'ID' => 1, 'NAME' => $language['FLAG_PoP_PoNB']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pon_hedge_jezith"), 'TEXT' => $language['FLAG_PoP_PreHedge']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pon_construct"), 'TEXT' => $language['FLAG_PoP_Hedge']) ); 
//Tactics 
$cb_template->assign_both_block_vars( "head" , array( 'ID' => 2, 'NAME' => $language['FLAG_PoP_PoTactics']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poi_dragon"), 'TEXT' => $language['FLAG_PoP_Xana']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poi_behometh_preflag"), 'TEXT' => $language['FLAG_PoP_PreMB']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poi_behometh_flag"), 'TEXT' => $language['FLAG_PoP_MB']) ); 
//CoD 
$cb_template->assign_both_block_vars( "head" , array( 'ID' => 3, 'NAME' => $language['FLAG_PoP_CoD']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pod_alder_fuirstel"), 'TEXT' => $language['FLAG_PoP_PreGrummus']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pod_grimmus_planar_projection"), 'TEXT' => $language['FLAG_PoP_Grummus']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pod_elder_fuirstel"), 'TEXT' => $language['FLAG_PoP_PostGrummus']) ); 
//Valor & Storms 
$cb_template->assign_both_block_vars( "head" , array( 'ID' => 4, 'NAME' => $language['FLAG_PoP_PoSPoV']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_mavuin"), 'TEXT' => $language['FLAG_PoP_PreTrial']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_tribunal"), 'TEXT' => $language['FLAG_PoP_Trial']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_valor_storms"), 'TEXT' => $language['FLAG_PoP_PostTrial']) ); 
//HoH A 
$cb_template->assign_both_block_vars( "head" , array( 'ID' => 5, 'NAME' => $language['FLAG_PoP_HoHA']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_mavuin"), 'TEXT' => $language['FLAG_PoP_PreTrial']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_tribunal"), 'TEXT' => $language['FLAG_PoP_Trial']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_valor_storms"), 'TEXT' => $language['FLAG_PoP_PostTrial']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pov_aerin_dar"), 'TEXT' => $language['FLAG_PoP_AD']) ); 
//BoT 
$cb_template->assign_both_block_vars( "head" , array( 'ID' => 6, 'NAME' => $language['FLAG_PoP_BoT']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_mavuin"), 'TEXT' => $language['FLAG_PoP_PreTrial']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_tribunal"), 'TEXT' => $language['FLAG_PoP_Trial']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_valor_storms"), 'TEXT' => $language['FLAG_PoP_PostTrial']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(3, "pop_pos_askr_the_lost"), 'TEXT' => $language['FLAG_PoP_Askr1']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pos_askr_the_lost_final"), 'TEXT' => $language['FLAG_PoP_Askr2']) ); 
//HoH B 
$cb_template->assign_both_block_vars( "head" , array( 'ID' => 7, 'NAME' => $language['FLAG_PoP_HoHB']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_mavuin"), 'TEXT' => $language['FLAG_PoP_PreTrial']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_tribunal"), 'TEXT' => $language['FLAG_PoP_Trial']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_valor_storms"), 'TEXT' => $language['FLAG_PoP_PostTrial']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pov_aerin_dar"), 'TEXT' => $language['FLAG_PoP_AD']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_hoh_faye"), 'TEXT' => $language['FLAG_PoP_Faye']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_hoh_trell"), 'TEXT' => $language['FLAG_PoP_Trell']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_hoh_garn"), 'TEXT' => $language['FLAG_PoP_Garn']) ); 
//Torment 
$cb_template->assign_both_block_vars( "head" , array( 'ID' => 8, 'NAME' => $language['FLAG_PoP_PoTorment']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pod_alder_fuirstel"), 'TEXT' => $language['FLAG_PoP_PreGrummus']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pod_grimmus_planar_projection"), 'TEXT' => $language['FLAG_PoP_Grummus']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pod_elder_fuirstel"), 'TEXT' => $language['FLAG_PoP_PostGrummus']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pon_hedge_jezith"), 'TEXT' => $language['FLAG_PoP_PreHedge']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pon_construct"), 'TEXT' => $language['FLAG_PoP_Hedge']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_ponb_terris"), 'TEXT' => $language['FLAG_PoP_TT']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_ponb_poxbourne"), 'TEXT' => $language['FLAG_PoP_PostTerris']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_cod_preflag"), 'TEXT' => $language['FLAG_PoP_Carpin']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_cod_bertox"), 'TEXT' => $language['FLAG_PoP_Bertox']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_cod_final"), 'TEXT' => $language['FLAG_PoP_PostBertox']) ); 
//Sol Ro Tower 
$cb_template->assign_both_block_vars( "head" , array( 'ID' => 9, 'NAME' => $language['FLAG_PoP_SolRoTower']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poi_behometh_preflag"), 'TEXT' => $language['FLAG_PoP_PreMB']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poi_behometh_flag"), 'TEXT' => $language['FLAG_PoP_MB']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_tactics_tallon"), 'TEXT' => $language['FLAG_PoP_TZ']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_tactics_vallon"), 'TEXT' => $language['FLAG_PoP_VZ']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pot_shadyglade"), 'TEXT' => $language['FLAG_PoP_PreSaryrn']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pot_newleaf"), 'TEXT' => $language['FLAG_PoP_KoS']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pot_saryrn"), 'TEXT' => $language['FLAG_PoP_Saryrn']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pot_saryrn_final"), 'TEXT' => $language['FLAG_PoP_PostSaryrn']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_hohb_marr"), 'TEXT' => $language['FLAG_PoP_MM']) ); 
//Fire 
$cb_template->assign_both_block_vars( "head" , array( 'ID' => 10, 'NAME' => $language['FLAG_PoP_PoFire']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poi_behometh_preflag"), 'TEXT' => $language['FLAG_PoP_PreMB']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poi_behometh_flag"), 'TEXT' => $language['FLAG_PoP_MB']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_tactics_tallon"), 'TEXT' => $language['FLAG_PoP_TZ']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_tactics_vallon"), 'TEXT' => $language['FLAG_PoP_VZ']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_tactics_ralloz"), 'TEXT' => $language['FLAG_PoP_RZ']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_sol_ro_arlyxir"), 'TEXT' => $language['FLAG_PoP_Arlyxir']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_sol_ro_dresolik"), 'TEXT' => $language['FLAG_PoP_Dresolik']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_sol_ro_jiva"), 'TEXT' => $language['FLAG_PoP_Jiva']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_sol_ro_rizlona"), 'TEXT' => $language['FLAG_PoP_Rizlona']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_sol_ro_xuzl"), 'TEXT' => $language['FLAG_PoP_Xusl']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_sol_ro_solusk"), 'TEXT' => $language['FLAG_PoP_SolRo']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_hohb_marr"), 'TEXT' => $language['FLAG_PoP_MM']) ); 

//Air/Earth/Water 
$cb_template->assign_both_block_vars( "head" , array( 'ID' => 11, 'NAME' => $language['FLAG_PoP_PoAirEarthWater']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pon_hedge_jezith"), 'TEXT' => $language['FLAG_PoP_PreHedge']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pon_construct"), 'TEXT' => $language['FLAG_PoP_Hedge']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_mavuin"), 'TEXT' => $language['FLAG_PoP_PreTrial']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_tribunal"), 'TEXT' => $language['FLAG_PoP_Trial']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_poj_valor_storms"), 'TEXT' => $language['FLAG_PoP_PostTrial']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_ponb_terris"), 'TEXT' => $language['FLAG_PoP_TT']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_ponb_poxbourne"), 'TEXT' => $language['FLAG_PoP_PostTerris']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pod_alder_fuirstel"), 'TEXT' => $language['FLAG_PoP_PreGrummus']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pod_grimmus_planar_projection"), 'TEXT' => $language['FLAG_PoP_Grummus']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pod_elder_fuirstel"), 'TEXT' => $language['FLAG_PoP_PostGrummus']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(3, "pop_pos_askr_the_lost"), 'TEXT' => $language['FLAG_PoP_Askr1']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pos_askr_the_lost_final"), 'TEXT' => $language['FLAG_PoP_Askr2']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_bot_agnarr"), 'TEXT' => $language['FLAG_PoP_Agnarr']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pov_aerin_dar"), 'TEXT' => $language['FLAG_PoP_AD']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_hoh_faye"), 'TEXT' => $language['FLAG_PoP_Faye']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_hoh_trell"), 'TEXT' => $language['FLAG_PoP_Trell']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_hoh_garn"), 'TEXT' => $language['FLAG_PoP_Garn']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_hohb_marr"), 'TEXT' => $language['FLAG_PoP_MM']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_cod_preflag"), 'TEXT' => $language['FLAG_PoP_Carpin']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_cod_bertox"), 'TEXT' => $language['FLAG_PoP_Bertox']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_cod_final"), 'TEXT' => $language['FLAG_PoP_PostBertox']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pot_shadyglade"), 'TEXT' => $language['FLAG_PoP_PreSaryrn']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pot_saryrn"), 'TEXT' => $language['FLAG_PoP_Saryrn']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pot_newleaf"), 'TEXT' => $language['FLAG_PoP_KoS']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_pot_saryrn_final"), 'TEXT' => $language['FLAG_PoP_PostSaryrn']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_tactics_ralloz"), 'TEXT' => $language['FLAG_PoP_RZ']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_elemental_grand_librarian"), 'TEXT' => $language['FLAG_PoP_Maelin']) ); 
//Time 
$cb_template->assign_both_block_vars( "head" , array( 'ID' => 12, 'NAME' => $language['FLAG_PoP_PoTime']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_fire_fennin_projection"), 'TEXT' => $language['FLAG_PoP_Fennin']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_wind_xegony_projection"), 'TEXT' => $language['FLAG_PoP_Xegony']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_water_coirnav_projection"), 'TEXT' => $language['FLAG_PoP_Coirnav']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_eartha_arbitor_projection"), 'TEXT' => $language['FLAG_PoP_Arbitor']) ); 
$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "pop_earthb_rathe"), 'TEXT' => $language['FLAG_PoP_Rathe']) ); 



/*********************************************
           SECONDARY/SUB MENUS GoD
*********************************************/
#//Vxed 
#$cb_template->assign_both_block_vars( "head" , array( 'ID' => 13, 'NAME' => $language['FLAG_GoD_Vxed']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "god_vxed_access"), 'TEXT' => $language['FLAG_GoD_KT_2']) ); 
#//Sewer 1 
#if (getdatabucket($charID."-god_snplant") == '1') $cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => "1", 'TEXT' => $language['FLAG_GoD_Sewer_1_1']) ); 
#elseif (getdatabucket($charID."-god_snplant") == 'T') $cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => "1", 'TEXT' => $language['FLAG_GoD_Sewer_1_T']) ); 
#else $cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => "0", 'TEXT' => $language['FLAG_GoD_Sewer_1_1']) ); 
#//Sewer 2
#if (getdatabucket($charID."-god_sncrematory") == '1') $cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => "1", 'TEXT' => $language['FLAG_GoD_Sewer_2_1']) ); 
#elseif (getdatabucket($charID."-god_sncrematory") == 'T') $cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => "1", 'TEXT' => $language['FLAG_GoD_Sewer_2_T']) ); 
#else $cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => "0", 'TEXT' => $language['FLAG_GoD_Sewer_2_1']) ); 
#//Sewer 3
#if (getdatabucket($charID."-god_snlair") == '1') $cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => "1", 'TEXT' => $language['FLAG_GoD_Sewer_3_1']) ); 
#elseif (getdatabucket($charID."-god_snlair") == 'T') $cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => "1", 'TEXT' => $language['FLAG_GoD_Sewer_3_T']) ); 
#else $cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => "0", 'TEXT' => $language['FLAG_GoD_Sewer_3_1']) ); 
#//Sewer 4
#if (getdatabucket($charID."-god_snpool") == '1') $cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => "1", 'TEXT' => $language['FLAG_GoD_Sewer_4_1']) ); 
#elseif (getdatabucket($charID."-god_snpool") == 'T') $cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => "1", 'TEXT' => $language['FLAG_GoD_Sewer_4_T']) ); 
#else $cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => "0", 'TEXT' => $language['FLAG_GoD_Sewer_4_1']) ); 
#//Tipt 
#$cb_template->assign_both_block_vars( "head" , array( 'ID' => 14, 'NAME' => $language['FLAG_GoD_Tipt']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "god_tipt_access"), 'TEXT' => $language['FLAG_GoD_KT_3']) ); 
#//KT
#$cb_template->assign_both_block_vars( "head" , array( 'ID' => 15, 'NAME' => $language['FLAG_GoD_KT_1']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "god_vxed_access"), 'TEXT' => $language['FLAG_GoD_KT_2']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "god_tipt_access"), 'TEXT' => $language['FLAG_GoD_KT_3']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "god_kodtaz_access"), 'TEXT' => $language['FLAG_GoD_KT_4']) ); 
#//Request Ikkinz Raids 1-3 
#$cb_template->assign_both_block_vars( "head" , array( 'ID' => 16, 'NAME' => $language['FLAG_GoD_Ikky_R3']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(2, "ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_2']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(3, "ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_3']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(4, "ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_4']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(5, "ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_5']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(6, "ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_6']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(7, "ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_7']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(8, "ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_8']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(9, "ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_9']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(10, "ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_10']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(11, "ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_11']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(12, "ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_12']) ); 
#//request Ikkinz Raid 4 
#$cb_template->assign_both_block_vars( "head" , array( 'ID' => 17, 'NAME' => $language['FLAG_GoD_Ikky_R4']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(13, "ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_13']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(14, "ikky"), 'TEXT' => $language['FLAG_GoD_Ikky_14']) ); 
#//Qvic 
#$cb_template->assign_both_block_vars( "head" , array( 'ID' => 18, 'NAME' => $language['FLAG_GoD_Qvic_1']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "god_qvic_access"), 'TEXT' => $language['FLAG_GoD_Qvic_2']) ); 
#//Txevu 
#$cb_template->assign_both_block_vars( "head" , array( 'ID' => 19, 'NAME' => $language['FLAG_GoD_Txevu_1']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getflag(1, "god_txevu_access"), 'TEXT' => $language['FLAG_GoD_Txevu_2']) ); 



/*********************************************
           SECONDARY/SUB MENUS OOW
*********************************************/ 
//Muramite Proving Grounds
#$cb_template->assign_both_block_vars( "head" , array( 'ID' => 20, 'NAME' => $language['FLAG_OOW_MPG']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getbitflag(1, "mpg_group_trials"), 'TEXT' => $language['FLAG_OOW_MPG_FEAR']) );  
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getbitflag(2, "mpg_group_trials"), 'TEXT' => $language['FLAG_OOW_MPG_INGENUITY']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getbitflag(4, "mpg_group_trials"), 'TEXT' => $language['FLAG_OOW_MPG_WEAPONRY']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getbitflag(8, "mpg_group_trials"), 'TEXT' => $language['FLAG_OOW_MPG_SUBVERSION']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getbitflag(16, "mpg_group_trials"), 'TEXT' => $language['FLAG_OOW_MPG_EFFICIENCY']) );
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getbitflag(32, "mpg_group_trials"), 'TEXT' => $language['FLAG_OOW_MPG_DESTRUCTION']) );
#//Citadel of Anguish
#$cb_template->assign_both_block_vars( "head" , array( 'ID' => 21, 'NAME' => $language['FLAG_OOW_COA']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getbitflag(1, "mpg_raid_trials"), 'TEXT' => $language['FLAG_OOW_COA_HATE']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getbitflag(2, "mpg_raid_trials"), 'TEXT' => $language['FLAG_OOW_COA_ENDURANCE']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getbitflag(4, "mpg_raid_trials"), 'TEXT' => $language['FLAG_OOW_COA_FORESIGHT']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getbitflag(8, "mpg_raid_trials"), 'TEXT' => $language['FLAG_OOW_COA_SPECIALIZATION']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getbitflag(16, "mpg_raid_trials"), 'TEXT' => $language['FLAG_OOW_COA_ADAPTATION']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getbitflag(32, "mpg_raid_trials"), 'TEXT' => $language['FLAG_OOW_COA_CORRUPTION']) ); 
#$cb_template->assign_both_block_vars( "head.flags" , array( 'FLAG' => getbitflag(1, "oow_rss_taromani_insignias"), 'TEXT' => $language['FLAG_OOW_COA_TAROMANI']) ); 


 
/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$cb_template->pparse('flags'); 

$cb_template->destroy; 

include(__DIR__ . "/include/footer.php"); 
?>