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
 *   April 17, 2020 - initial revision (Maudigan) 
 *   April 25, 2020 - Maudigan
 *     implement multi-tenancy
 *   May 3, 2020 - Maudigan
 *     optimize character initialization
 *   March 16, 2022 - Maudigan
 *     added item type to the API for each item
 *      
 ***************************************************************************/
  
 
/*********************************************
                 INCLUDES
*********************************************/ 
define('INCHARBROWSER', true);
include_once(__DIR__ . "/include/common.php");
include_once(__DIR__ . "/include/bot_profile.php");
include_once(__DIR__ . "/include/profile.php");
include_once(__DIR__ . "/include/itemclass.php");
include_once(__DIR__ . "/include/db.php");
  
 
/*********************************************
         SETUP PROFILE/PERMISSIONS
*********************************************/
if(!$_GET['bot']) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_NO_CHAR']);
else $botName = $_GET['bot'];
     
//bot initializations 
$bot = new bot_profile($botName, $cbsql, $cbsql_content, $language, $charbrowser_is_admin_page); //the profile class will sanitize the bot name
$charID = $bot->char_id(); 
$botID = $bot->bot_id(); 
$botName = $bot->GetValue('name');
$userip = getIPAddress(); 
$ownercheck = 0;

//char initialization      
$char = new profile($charID, $cbsql, $cbsql_content, $language, $showsoftdelete, $charbrowser_is_admin_page);
$charName = $char->GetValue('name');
$mypermission = GetPermissions($char->GetValue('gm'), $char->GetValue('anon'), $char->char_id());
$userip = getIPAddress(); 

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
	$botsip = $cbsql->fetch_all($result);  
foreach($botsip as $botip) {
	if ($botip['ip'] == $userip || $userip == $defaultedlocalhost || $userip == $localipaddress || $userip == $defaultgateway) {
		$ownercheck = 1;
	}
}

//block view if user level doesnt have permission
if ($mypermission['bots'] && $ownercheck != 1) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_PERMISSIONS_ERROR']);
 
/*********************************************
        GATHER RELEVANT PAGE DATA
*********************************************/
//get bot info
$class      = $bot->GetValue('class');


/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$botName.$language['PAGE_TITLES_CHARACTER'];
include(__DIR__ . "/include/header.php");
 
 
/*********************************************
            DROP PROFILE MENU
*********************************************/
output_profile_menu($charName, 'bot');

 
/*********************************************
              POPULATE BODY
*********************************************/
$cb_template->set_filenames(array(
  'bot' => 'bot_body.tpl')
);


$cb_template->assign_both_vars(array(  
   'NAME' => $charName,
   'REGEN' => number_format($bot->getRegen()),
   'FT' => number_format($bot->getFT()),
   'DS' => number_format($bot->getDS()),
   'HASTE' => $bot->getHaste(),
   'DELETED' => (($char->GetValue('deleted_at')) ? " ".$language['CHAR_DELETED']:""),
   'FIRST_NAME' => $botName,
   'LAST_NAME' => $bot->GetValue('last_name'),
   'TITLE' => $bot->GetValue('title'),
   'LEVEL' => $bot->GetValue('level'),
   'CLASS' => $dbclassnames[$class],
   'RACE' => $dbracenames[$bot->GetValue('race')],
   'RACE_ID' => $bot->GetValue('race'),
   'GENDER_ID' => $bot->GetValue('gender'),
   'FACE_ID' => $bot->GetValue('face'),
   'AVATAR_IMG' => getAvatarImage($bot->GetValue('race'), $bot->GetValue('gender'), $bot->GetValue('face')),
   'CLASS_NUM' => $class,
   'DEITY' => $dbdeities[$bot->GetValue('deity')],
   'HP' => number_format($bot->GetItemHP()),
   'MANA' => number_format($bot->GetItemMana()),
   'ENDR' => number_format($bot->GetItemEndurance()),
   'AC' => number_format($bot->GetItemAC()),
   'ATK' => number_format($bot->GetItemATK()),
   'GS' => number_format($bot->getGS()),
   'GSAVG' => number_format($bot->getGS() / 20),
   'STR' => number_format($bot->getSTR()),
   'STA' => number_format($bot->getSTA()),
   'DEX' => number_format($bot->getDEX()),
   'AGI' => number_format($bot->getAGI()),
   'INT' => number_format($bot->getINT()),
   'WIS' => number_format($bot->getWIS()),
   'CHA' => number_format($bot->getCHA()),
   'HSTR' => number_format($bot->getHSTR()),  
   'HSTA' => number_format($bot->getHSTA()),  
   'HDEX' => number_format($bot->getHDEX()),  
   'HAGI' => number_format($bot->getHAGI()),  
   'HINT' => number_format($bot->getHINT()),  
   'HWIS' => number_format($bot->getHWIS()),  
   'HCHA' => number_format($bot->getHCHA()), 
   'POISON' => $bot->getPR(),
   'FIRE' => $bot->getFR(),
   'MAGIC' => $bot->getMR(),
   'DISEASE' => $bot->getDR(),
   'COLD' => $bot->getCR(),
   'CORRUPT' => $bot->getCOR(),
   'HPOISON' => $bot->getHPR(), 
   'HFIRE' => $bot->getHFR(), 
   'HMAGIC' => $bot->getHMR(), 
   'HDISEASE' => $bot->getHDR(), 
   'HCOLD' => $bot->getHCR(), 
   'HCORRUPT' => $bot->getHCOR(),
   'WEIGHT' => round($bot->getWT()/10))
);

$cb_template->assign_vars(array(  
   'L_HEADER_INVENTORY' => $language['CHAR_INVENTORY'],
   'L_REGEN' => $language['CHAR_REGEN'],
   'L_FT' => $language['CHAR_FT'],
   'L_DS' => $language['CHAR_DS'],
   'L_HASTE' => $language['CHAR_HASTE'],
   'L_HP' => $language['CHAR_HP'],
   'L_MANA' => $language['CHAR_MANA'],
   'L_ENDR' => $language['CHAR_ENDR'],
   'L_AC' => $language['CHAR_AC'],
   'L_ATK' => $language['CHAR_ATK'],
   'L_GS' => $language['CHAR_GS'],
   'L_GSAVG' => $language['CHAR_GSAVG'],
   'L_STR' => $language['CHAR_STR'],
   'L_STA' => $language['CHAR_STA'],
   'L_DEX' => $language['CHAR_DEX'],
   'L_AGI' => $language['CHAR_AGI'],
   'L_INT' => $language['CHAR_INT'],
   'L_WIS' => $language['CHAR_WIS'],
   'L_CHA' => $language['CHAR_CHA'],
   'L_HSTR' => $language['CHAR_HSTR'],  
   'L_HSTA' => $language['CHAR_HSTA'], 
   'L_HDEX' => $language['CHAR_HDEX'], 
   'L_HAGI' => $language['CHAR_HAGI'], 
   'L_HINT' => $language['CHAR_HINT'], 
   'L_HWIS' => $language['CHAR_HWIS'], 
   'L_HCHA' => $language['CHAR_HCHA'], 
   'L_POISON' => $language['CHAR_POISON'],
   'L_MAGIC' => $language['CHAR_MAGIC'],
   'L_DISEASE' => $language['CHAR_DISEASE'],
   'L_FIRE' => $language['CHAR_FIRE'],
   'L_COLD' => $language['CHAR_COLD'],
   'L_CORRUPT' => $language['CHAR_CORRUPT'],
   'L_HPOISON' => $language['CHAR_HPOISON'], 
   'L_HMAGIC' => $language['CHAR_HMAGIC'], 
   'L_HDISEASE' => $language['CHAR_HDISEASE'], 
   'L_HFIRE' => $language['CHAR_HFIRE'], 
   'L_HCOLD' => $language['CHAR_HCOLD'], 
   'L_HCORRUPT' => $language['CHAR_HCORRUPT'],
   'L_WEIGHT' => $language['CHAR_WEIGHT'],
   'L_DONE' => $language['BUTTON_DONE'])
);

//---------------------------------
//     SLOTS TEMPLATE VARS
//---------------------------------
//EQUIPMENT
for ( $i = SLOT_EQUIPMENT_START; $i <= SLOT_EQUIPMENT_END; $i++ ) {
   $cb_template->assign_block_vars("equipslots", array( 
      'SLOT' => $i)
   );
}


//---------------------------------
// ITEM ICONS TEMPLATE VARS
//---------------------------------
$allitems = $bot->getAllItems();


//EQUIPMENT
foreach ($allitems as $value) {
   if ($value->type() != EQUIPMENT) continue; 
   $cb_template->assign_block_vars("equipitem", array( 
      'SLOT' => $value->slot(),      
      'ICON' => $value->icon(),      
      'STACK' => $value->stack())
   );
}


//---------------------------------
//   ITEM WINDOW TEMPLATE VARS
//---------------------------------
//the item inspect windows that hold
//the item stats. this does equipment,
//inventory, bank and shared bank
foreach ($allitems as $value) {   
   $cb_template->assign_both_block_vars("item", array(
      'SLOT' => $value->slot(),     
      'ICON' => $value->icon(),   
      'NAME' => $value->name(),  
      'STACK' => $value->stack(),
      'ID' => $value->id(),
      'LINK' => QuickTemplate($link_item, array('ITEM_ID' => $value->id())),
      'HTML' => $value->html(),
      'ITEMTYPE' => $value->skill())
   );
   for ( $i = 0 ; $i < $value->augcount() ; $i++ ) {
      $cb_template->assign_both_block_vars("item.augment", array(       
         'AUG_NAME' => $value->augname($i),
         'AUG_ID' => $value->augid($i),
         'AUG_LINK' => QuickTemplate($link_item, array('ITEM_ID' => $value->augid($i))),
         'AUG_ICON' => $value->augicon($i),
         'AUG_HTML' => $value->aughtml($i))
      );
   }
}
 
 
/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$cb_template->pparse('bot');

$cb_template->destroy;

/*
	CUSTOM COMMAND SETTINGS WINDOW
*/

$tpl = <<<TPL
	SELECT *
	FROM bot_data bd
	WHERE bd.name LIKE '$botName'
TPL;

$result = $cbsql->query($tpl);
if (!$cbsql->rows($result)) cb_message('Your bot', 'Not your bot');
	$bots = $cbsql->fetch_all($result);
foreach($bots as $bot) {
	if ($bot['stop_melee_level'] > 65) {
		$sml = '<font color=limegreen>Will always melee<font color=white>';
	}
	else if ($bot['stop_melee_level'] > $bot['level']) {
		$sml = '<font color=green>Will stop meleeing at level ' . $bot['stop_melee_level'] . '<font color=white>';
	}	else {
		$sml = '<font color=yellow>Stopped meleeing at level ' . $bot['stop_melee_level'] . '<font color=white>';
	}
	if ($ownercheck) {
		$filler .= "
					<div class='col-md-12 head'>
						<div class='float-right'>
							<font color=yellow>EXPORT BOT GEAR DATA <br>
							ALL BOTS -> [
							<u><i><b><a href='". $cb_index_url . "?page=exportBotGearData&bot=$botName&csvtype=all'>[w/o Item Names</a> | <a href='". $cb_index_url . "?page=exportBotGearData&bot=$botName&csvtype=all&showitemnames=true'>w/ Item Names]</a></b></i></u>
							] <br> THIS OWNER'S BOTS -> [ 
							<u><i><b><a href='". $cb_index_url . "?page=exportBotGearData&bot=$botName&csvtype=owner'>[w/o Item Names</a> | <a href='". $cb_index_url . "?page=exportBotGearData&bot=$botName&csvtype=owner&showitemnames=true'>w/ Item Names]</a></b></i></u>
							] <br> THIS BOT ONLY -> [ 
							<u><i><b><a href='". $cb_index_url . "?page=exportBotGearData&bot=$botName&csvtype=this'>[w/o Item Names</a> | <a href='". $cb_index_url . "?page=exportBotGearData&bot=$botName&csvtype=this&showitemnames=true'>w/ Item Names]</a></b></i></u>
							]
							<br><i><u>All exports include the owning character's data</u></i>
							<br><font color=pink><i><u>*It is recommended to use Google Sheets for Number Only exports*</u></i>
							<br><i><u>*Conditional Formatting won't work properly on Excel with Hyperlinks*</u></i><font color=white>
						</div>
					</div>
					";
		$filler .= "|------------------------------------------------------------|<br>";
	}
	$filler .= $sml . '<font color=lightblue> | ^sml<font color=white><br>';
	$filler .= 'Auto Cast Resists is ' . ($bot['auto_resist'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled') . '<font color=lightblue> | ^autoresist<font color=white><br>';
	$filler .= 'Auto Cast Damage Shields is ' . ($bot['auto_ds'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled') . '<font color=lightblue> | ^autods<font color=white><br>';
	$filler .= 'Behind Mob is ' . ($bot['behind_mob'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled') . '<font color=lightblue> | ^behindmob<font color=white><br>';
	$filler .= 'Caster Range is ' . ($bot['caster_range'] ? '<font color=green>' . $bot['caster_range'] . ' units<font color=white>' . '' : '<font color=red>disabled') . '<font color=lightblue> | ^casterrange<font color=white><br>';
	$filler .= 'Hold Buffs is ' . ($bot['hold_buffs'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled') . '<font color=lightblue> | ^holdbuffs<font color=white><br>';
	$filler .= 'Hold Cures is ' . ($bot['hold_cures'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled') . '<font color=lightblue> | ^holdcures<font color=white><br>';
	$filler .= 'Hold DoTs is ' . ($bot['hold_dots'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled') . '<font color=lightblue> | ^holddots<font color=white><br>';
	$filler .= 'Hold Dispels is ' . ($bot['hold_dispels'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled') . '<font color=lightblue> | ^holddispels<font color=white><br>';
	$filler .= 'Hold Debuffs is ' . ($bot['hold_debuffs'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled') . '<font color=lightblue> | ^holddebuffs<font color=white><br>';
	$filler .= 'Hold Escapes is ' . ($bot['hold_escapes'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled') . '<font color=lightblue> | ^holdescapes<font color=white><br>';
	$filler .= 'Hold Hate Redux is ' . ($bot['hold_hateredux'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled') . '<font color=lightblue> | ^holdhateredux<font color=white><br>';
	$filler .= 'Hold Heals is ' . ($bot['hold_heals'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled') . '<font color=lightblue> | ^holdheals<font color=white><br>';
	$filler .= 'Hold In Combat Buffs is ' . ($bot['hold_incombatbuffs'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled') . '<font color=lightblue> | ^holdincombatbuffs<font color=white><br>';
	$filler .= 'Hold In Combat Buff Songs is ' . ($bot['hold_incombatbuffsongs'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled') . '<font color=lightblue> | ^holdincombatbuffsongs<font color=white><br>';
	$filler .= 'Hold Lifetaps is ' . ($bot['hold_lifetaps'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled') . '<font color=lightblue> | ^holdlifetaps<font color=white><br>';
	$filler .= 'Hold Mez is ' . ($bot['hold_mez'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled') . '<font color=lightblue> | ^holdmez<font color=white><br>';
	$filler .= 'Hold Nukes is ' . ($bot['hold_nukes'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled') . '<font color=lightblue> | ^holdnukes<font color=white><br>';
	$filler .= 'Hold OOC Buff Songs is ' . ($bot['hold_outofcombatbuffsongs'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled') . '<font color=lightblue> | ^holdoutofcombatbuffsongs<font color=white><br>';
	$filler .= 'Hold Pet Heals is ' . ($bot['hold_pet_heals'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled') . '<font color=lightblue> | ^holdpetheals<font color=white><br>';
	$filler .= 'Hold Roots is ' . ($bot['hold_roots'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled') . '<font color=lightblue> | ^holdroots<font color=white><br>';
	$filler .= 'Hold Slows is ' . ($bot['hold_slows'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled') . '<font color=lightblue> | ^holdslows<font color=white><br>';
	$filler .= 'Hold Snares is ' . ($bot['hold_snares'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled') . '<font color=lightblue> | ^holdsnares<font color=white><br>';
	$filler .= 'Buff Delay is <font color=green>' . number_format($bot['buff_delay'] / 1000, 2, '.', '') . 's<font color=lightblue> | ^buffdelay<font color=white><br>';
	$filler .= 'Complete Heal Delay is <font color=green>' . number_format($bot['complete_heal_delay'] / 1000, 2, '.', '') . 's<font color=lightblue> | ^completehealdelay<font color=white><br>';
	$filler .= 'Cure Delay is <font color=green>' . number_format($bot['cure_delay'] / 1000, 2, '.', '') . 's<font color=lightblue> | ^curedelay<font color=white><br>';
	$filler .= 'Debuff Delay is <font color=green>' . number_format($bot['debuff_delay'] / 1000, 2, '.', '') . 's<font color=lightblue> | ^debuffdelay<font color=white><br>';
	$filler .= 'Dispel Delay is <font color=green>' . number_format($bot['dispel_delay'] / 1000, 2, '.', '') . 's<font color=lightblue> | ^dispeldelay<font color=white><br>';
	$filler .= 'DoT Delay is <font color=green>' . number_format($bot['dot_delay'] / 1000, 2, '.', '') . 's<font color=lightblue> | ^dotdelay<font color=white><br>';
	$filler .= 'Escape Delay is <font color=green>' . number_format($bot['escape_delay'] / 1000, 2, '.', '') . 's<font color=lightblue> | ^escapedelay<font color=white><br>';
	$filler .= 'Fast Heal Delay is <font color=green>' . number_format($bot['fast_heal_delay'] / 1000, 2, '.', '') . 's<font color=lightblue> | ^fasthealdelay<font color=white><br>';
	$filler .= 'Hate Redux Delay is <font color=green>' . number_format($bot['hate_redux_delay'] / 1000, 2, '.', '') . 's<font color=lightblue> | ^hatereduxdelay<font color=white><br>';
	$filler .= 'Heal Delay is <font color=green>' . number_format($bot['heal_delay'] / 1000, 2, '.', '') . 's<font color=lightblue> | ^healdelay<font color=white><br>';
	$filler .= 'Heal Over Time Delay is <font color=green>' . number_format($bot['hot_heal_delay'] / 1000, 2, '.', '') . 's<font color=lightblue> | ^hothealdelay<font color=white><br>';
	$filler .= 'In-Combat Buff Delay is <font color=green>' . number_format($bot['incombatbuff_delay'] / 1000, 2, '.', '') . 's<font color=lightblue> | ^incombatbuffdelay<font color=white><br>';
	$filler .= 'Lifetap Delay is <font color=green>' . number_format($bot['lifetap_delay'] / 1000, 2, '.', '') . 's<font color=lightblue> | ^lifetapdelay<font color=white><br>';
	$filler .= 'Mez Delay is <font color=green>' . number_format($bot['mez_delay'] / 1000, 2, '.', '') . 's<font color=lightblue> | ^mezdelay<font color=white><br>';
	$filler .= 'Nuke Delay is <font color=green>' . number_format($bot['nuke_delay'] / 1000, 2, '.', '') . 's<font color=lightblue> | ^nukedelay<font color=white><br>';
	$filler .= 'Root Delay is <font color=green>' . number_format($bot['root_delay'] / 1000, 2, '.', '') . 's<font color=lightblue> | ^rootdelay<font color=white><br>';
	$filler .= 'Slow Delay is <font color=green>' . number_format($bot['slow_delay'] / 1000, 2, '.', '') . 's<font color=lightblue> | ^slowdelay<font color=white><br>';
	$filler .= 'Snare Delay is <font color=green>' . number_format($bot['snare_delay'] / 1000, 2, '.', '') . 's<font color=lightblue> | ^snaredelay<font color=white><br>';
	$filler .= 'Buff Max Threshold is <font color=green>' . $bot['buff_threshold'] . '% HP<font color=lightblue> | ^buffthreshold<font color=white><br>';
	$filler .= 'Complete Heal Max Threshold is <font color=green>' . $bot['complete_heal_threshold'] . '% HP<font color=lightblue> | ^completehealthreshold<font color=white><br>';
	$filler .= 'Cure Max Threshold is <font color=green>' . $bot['cure_threshold'] . '% HP<font color=lightblue> | ^curethreshold<font color=white><br>';
	$filler .= 'Debuff Max Threshold is <font color=green>' . $bot['debuff_threshold'] . '% HP<font color=lightblue> | ^debuffthreshold<font color=white><br>';
	$filler .= 'Dispel Max Threshold is <font color=green>' . $bot['dispel_threshold'] . '% HP<font color=lightblue> | ^dispelthreshold<font color=white><br>';
	$filler .= 'DoT Max Threshold is <font color=green>' . $bot['dot_threshold'] . '% HP<font color=lightblue> | ^dotthreshold<font color=white><br>';
	$filler .= 'Escape Max Threshold is <font color=green>' . $bot['escape_threshold'] . '% HP<font color=lightblue> | ^escapethreshold<font color=white><br>';
	$filler .= 'Fast Heal Max Threshold is <font color=green>' . $bot['fast_heal_threshold'] . '% HP<font color=lightblue> | ^fasthealthreshold<font color=white><br>';
	$filler .= 'Hate Redux Max Threshold is <font color=green>' . $bot['hate_redux_threshold'] . '% HP<font color=lightblue> | ^hatereduxthreshold<font color=white><br>';
	$filler .= 'Heal Max Threshold is <font color=green>' . $bot['heal_threshold'] . '% HP<font color=lightblue> | ^healthreshold<font color=white><br>';
	$filler .= 'Heal Over Time Max Threshold is <font color=green>' . $bot['hot_heal_threshold'] . '% HP<font color=lightblue> | ^hothealthreshold<font color=white><br>';
	$filler .= 'In-Combat Buff Max Threshold is <font color=green>' . $bot['incombatbuff_threshold'] . '% HP<font color=lightblue> | ^incombatbuffthreshold<font color=white><br>';
	$filler .= 'Lifetap Max Threshold is <font color=green>' . $bot['lifetap_threshold'] . '% HP<font color=lightblue> | ^lifetapthreshold<font color=white><br>';
	$filler .= 'Mez Max Threshold is <font color=green>' . $bot['mez_threshold'] . '% HP<font color=lightblue> | ^mezthreshold<font color=white><br>';
	$filler .= 'Nuke Max Threshold is <font color=green>' . $bot['nuke_threshold'] . '% HP<font color=lightblue> | ^nukethreshold<font color=white><br>';
	$filler .= 'Root Max Threshold is <font color=green>' . $bot['root_threshold'] . '% HP<font color=lightblue> | ^rootthreshold<font color=white><br>';
	$filler .= 'Slow Max Threshold is <font color=green>' . $bot['slow_threshold'] . '% HP<font color=lightblue> | ^slowthreshold<font color=white><br>';
	$filler .= 'Snare Max Threshold is <font color=green>' . $bot['snare_threshold'] . '% HP<font color=lightblue> | ^snarethreshold<font color=white><br>';
	$filler .= 'Buff Min Threshold is <font color=green>' . $bot['buff_min_threshold'] . '% HP<font color=lightblue> | ^buffminthreshold<font color=white><br>';
	$filler .= 'Cure Min Threshold is <font color=green>' . $bot['cure_min_threshold'] . '% HP<font color=lightblue> | ^cureminthreshold<font color=white><br>';
	$filler .= 'Debuff Min Threshold is <font color=green>' . $bot['debuff_min_threshold'] . '% HP<font color=lightblue> | ^debuffminthreshold<font color=white><br>';
	$filler .= 'Dispel Min Threshold is <font color=green>' . $bot['dispel_min_threshold'] . '% HP<font color=lightblue> | ^dispelminthreshold<font color=white><br>';
	$filler .= 'DoT Min Threshold is <font color=green>' . $bot['dot_min_threshold'] . '% HP<font color=lightblue> | ^dotminthreshold<font color=white><br>';
	$filler .= 'Escape Min Threshold is <font color=green>' . $bot['escape_min_threshold'] . '% HP<font color=lightblue> | ^escapeminthreshold<font color=white><br>';
	$filler .= 'Hate Redux Min Threshold is <font color=green>' . $bot['hate_redux_min_threshold'] . '% HP<font color=lightblue> | ^hatereduxminthreshold<font color=white><br>';
	$filler .= 'In-Combat Buff Min Threshold is <font color=green>' . $bot['incombatbuff_min_threshold'] . '% HP<font color=lightblue> | ^incombatbuffminthreshold<font color=white><br>';
	$filler .= 'Lifetap Min Threshold is <font color=green>' . $bot['lifetap_min_threshold'] . '% HP<font color=lightblue> | ^lifetapminthreshold<font color=white><br>';
	$filler .= 'Mez Min Threshold is <font color=green>' . $bot['mez_min_threshold'] . '% HP<font color=lightblue> | ^mezminthreshold<font color=white><br>';
	$filler .= 'Nuke Min Threshold is <font color=green>' . $bot['nuke_min_threshold'] . '% HP<font color=lightblue> | ^nukeminthreshold<font color=white><br>';
	$filler .= 'Root Min Threshold is <font color=green>' . $bot['root_min_threshold'] . '% HP<font color=lightblue> | ^rootminthreshold<font color=white><br>';
	$filler .= 'Slow Min Threshold is <font color=green>' . $bot['slow_min_threshold'] . '% HP<font color=lightblue> | ^slowminthreshold<font color=white><br>';
	$filler .= 'Snare Min Threshold is <font color=green>' . $bot['snare_min_threshold'] . '% HP<font color=lightblue> | ^snareminthreshold<font color=white><br>';
}
cb_botcommandsettings('Custom Settings', $filler);

include(__DIR__ . "/include/footer.php");

?>