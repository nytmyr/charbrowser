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
if ($userip == '192.168.1.1') {
	$userip = '192.168.1.13';
}

//char initialization      
$char = new profile($charID, $cbsql, $cbsql_content, $language, $showsoftdelete, $charbrowser_is_admin_page);
$charName = $char->GetValue('name');
$mypermission = GetPermissions($char->GetValue('gm'), $char->GetValue('anon'), $char->char_id());

//block view if user level doesnt have permission
if ($mypermission['bots']) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ITEM_NO_VIEW']);
 
 
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
	if (!$cbsql->rows($result)) cb_message_die($language['BOTS_BOTS']." - ".$name,$language['MESSAGE_NO_BOTS']);
		$bots = $cbsql->fetch_all($result);  
	foreach($bots as $bot) {
		if ((($bot['ip'] == $userip && $ownercheck != 1) || $userip == '192.168.1.13')) {
			$ownercheck = 1;
		}
	}
if ($ownercheck == 1) {
	$cb_template->set_filenames(array(
	'botsettings' => 'bots_body.tpl')
	);
	$tpl = <<<TPL
		SELECT bd.level, bd.stop_melee_level, hold_buffs, hold_cures, hold_dots, hold_dispels
		, hold_debuffs, hold_escapes, hold_hateredux, hold_heals, hold_incombatbuffs, hold_incombatbuffsongs, hold_lifetaps
		, hold_mez, hold_nukes, hold_outofcombatbuffsongs, hold_roots, hold_slows, hold_snares, nuke_delay, auto_resist, auto_ds
		, behind_mob, caster_range, debuff_delay, slow_delay, dot_delay, lifetap_delay, heal_delay, fast_heal_delay, complete_heal_delay, hot_heal_delay
		, heal_threshold, fast_heal_threshold, complete_heal_threshold, hot_heal_threshold
		FROM bot_data bd
		WHERE bd.name LIKE '$botName'
	TPL;
	
	$result = $cbsql->query($tpl);
	if (!$cbsql->rows($result)) cb_message('Your bot', 'Not your bot');
		$bots = $cbsql->fetch_all($result);
	$filler;
	foreach($bots as $bot) {
		if ($bot['stop_melee_level'] > 65) {
			$sml = '<font color=limegreen>Will always melee<font color=white>';
		}
		else if ($bot['stop_melee_level'] > $bot['level']) {
			$sml = '<font color=green>Will stop meleeing at level ' . $bot['stop_melee_level'] . '<font color=white>';
		}	else {
			$sml = '<font color=yellow>Stopped meleeing at level ' . $bot['stop_melee_level'] . '<font color=white>';
		}
		$filler .= $sml . '<br>';
		$filler .= 'Hold Buffs is ' . ($bot['hold_buffs'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Hold Cures is ' . ($bot['hold_cures'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Hold DoTs is ' . ($bot['hold_dots'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Hold Dispels is ' . ($bot['hold_dispels'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Hold Debuffs is ' . ($bot['hold_debuffs'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Hold Escapes is ' . ($bot['hold_escapes'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Hold Hate Redux is ' . ($bot['hold_hateredux'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Hold Heals is ' . ($bot['hold_heals'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Hold In Combat Buffs is ' . ($bot['hold_incombatbuffs'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Hold In Combat Buff Songs is ' . ($bot['hold_incombatbuffsongs'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Hold Lifetaps is ' . ($bot['hold_lifetaps'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Hold Mez is ' . ($bot['hold_mez'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Hold Nukes is ' . ($bot['hold_nukes'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Hold OOC Buff Songs is ' . ($bot['hold_outofcombatbuffsongs'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Hold Roots is ' . ($bot['hold_roots'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Hold Slows is ' . ($bot['hold_slows'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Hold Snares is ' . ($bot['hold_snares'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Nuke Delay is ' . ($bot['nuke_delay'] ? '<font color=green>' . number_format($bot['nuke_delay'] / 1000, 2, '.', '') . 's<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Auto Cast Resists is ' . ($bot['auto_resist'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Auto Cast Damage Shields is ' . ($bot['auto_ds'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Behind Mob is ' . ($bot['behind_mob'] ? '<font color=green>enabled<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Caster Range is ' . ($bot['caster_range'] ? '<font color=green>' . $bot['caster_range'] . ' units<font color=white>' . '' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Debuff Delay is ' . ($bot['debuff_delay'] ? '<font color=green>' . number_format($bot['debuff_delay'] / 1000, 2, '.', '') . 's<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Slow Delay is ' . ($bot['slow_delay'] ? '<font color=green>' . number_format($bot['slow_delay'] / 1000, 2, '.', '') . 's<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'DoT Delay is ' . ($bot['dot_delay'] ? '<font color=green>' . number_format($bot['dot_delay'] / 1000, 2, '.', '') . 's<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Lifetap Delay is ' . ($bot['lifetap_delay'] ? '<font color=green>' . number_format($bot['lifetap_delay'] / 1000, 2, '.', '') . 's<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Heal Delay is ' . ($bot['heal_delay'] ? '<font color=green>' . number_format($bot['heal_delay'] / 1000, 2, '.', '') . 's<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Fast Heal Delay is ' . ($bot['fast_heal_delay'] ? '<font color=green>' . number_format($bot['fast_heal_delay'] / 1000, 2, '.', '') . 's<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Complete Heal Delay is ' . ($bot['complete_heal_delay'] ? '<font color=green>' . number_format($bot['complete_heal_delay'] / 1000, 2, '.', '') . 's<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Heal Over Time Delay is ' . ($bot['hot_heal_delay'] ? '<font color=green>' . number_format($bot['hot_heal_delay'] / 1000, 2, '.', '') . 's<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Regular Heal Threshold is ' . ($bot['heal_threshold'] ? '<font color=green>' . $bot['heal_threshold'] . '% HP<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Fast Heal Threshold is ' . ($bot['fast_heal_threshold'] ? '<font color=green>' . $bot['fast_heal_threshold'] . '% HP<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Complete Heal Threshold is ' . ($bot['complete_heal_threshold'] ? '<font color=green>' . $bot['complete_heal_threshold'] . '% HP<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
		$filler .= 'Heal Over Time Threshold is ' . ($bot['hot_heal_threshold'] ? '<font color=green>' . $bot['hot_heal_threshold'] . '% HP<font color=white>' : '<font color=red>disabled<font color=white>') . '<br>';
	}
	cb_message('Settings', $filler);

}

include(__DIR__ . "/include/footer.php");

?>