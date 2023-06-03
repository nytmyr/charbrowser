 <?php
 define('INCHARBROWSER', true);
include_once(__DIR__ . "/include/common.php");
include_once(__DIR__ . "/include/profile.php");
include_once(__DIR__ . "/include/db.php");

/*********************************************
         SETUP PROFILE/PERMISSIONS
*********************************************/
if(!$_GET['char']) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_NO_CHAR']);
else $charName = $_GET['char'];
$order = (isset($_GET['order']) ? addslashes($_GET["order"]) : "n.`difficulty` ASC");
$unkilled = (isset($_GET['unkilled']) ? addslashes($_GET["unkilled"]) : 0);


//character initializations
$char = new profile($charName, $cbsql, $cbsql_content, $language, $showsoftdelete, $charbrowser_is_admin_page); //the profile class will sanitize the character name
$charID = $char->char_id();
$name = $char->GetValue('name');
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
	$bots = $cbsql->fetch_all($result);  
foreach($bots as $bot) {
	if ($bot['ip'] == $userip || $userip == $defaultedlocalhost || $userip == $localipaddress || $userip == $defaultgateway) {
		$ownercheck = 1;
	}
}
	
//block view if user level doesnt have permission
if ($mypermission['raidpoints'] && $ownercheck != 1) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_PERMISSIONS_ERROR']);

/*********************************************
        GATHER RELEVANT PAGE DATA
*********************************************/
//get raid kills from the character db

if ($order == 1) { $order = "NPCName ASC"; }
if ($order == 2) { $order = "NPCName DESC"; }
if ($order == 3) { $order = "n.`raid_points` ASC"; }
if ($order == 4) { $order = "n.`raid_points` DESC"; }
if ($order == 5) { $order = "z.`long_name` ASC"; }
if ($order == 6) { $order = "z.`long_name` DESC"; }
if ($order == 7) { $order = "n.`difficulty` ASC"; }
if ($order == 8) { $order = "n.`difficulty` DESC"; }
if (!$unkilled) {
	$killtype = "Killed";
	$tpl = <<<TPL
	SELECT db.`key`, db.`value` AS EarnedValue, cd.`id` AS CharID, cd.`name` AS CharName, n.`id` AS NPCID, REPLACE(REPLACE(n.`name`,'_',' '),'#','') AS NPCName, n.`difficulty` AS NPCDiff, n.`raid_points` AS RaidPts, z.`short_name` AS ZoneSN, z.`long_name` AS ZoneLN -- , SUM(db.`value)
	FROM data_buckets db
	INNER JOIN character_data cd ON cd.`id` = SUBSTRING(db.`key`, 16, INSTR(SUBSTRING(db.`key`, 16), "-")+1)
	INNER JOIN npc_types n ON n.`id` = SUBSTRING(db.`key`, INSTR(SUBSTRING(db.`key`, 16), "-")+16)
	LEFT JOIN zone z ON z.`zoneidnumber` = FLOOR(CAST(n.`id` / 1000 AS DOUBLE))
	WHERE db.`key` LIKE 'PlayerRaidKill-%'
	AND cd.`id` = $charID
	-- AND n.`raid_points` > 0
	ORDER BY $order
	TPL;
} else {
	$killtype = "Unkilled";
	$tpl = <<<TPL
	SELECT n.`difficulty` AS NPCDiff, n.`raid_points` AS EarnedValue, n.`id` AS NPCID, REPLACE(REPLACE(n.`name`,'_',' '),'#','') AS NPCName, n.`raid_points` AS RaidPts, z.`short_name` AS ZoneSN, z.`long_name` AS ZoneLN -- , SUM(db.`value)
	FROM npc_types n
	INNER JOIN zone z ON z.zoneidnumber = FLOOR(CAST(n.id / 1000 AS DOUBLE))
	WHERE NOT EXISTS (SELECT *
				FROM qs_player_npc_kill_record kr
				INNER JOIN qs_player_npc_kill_record_entries kre ON kre.event_id = kr.fight_id
				WHERE kr.npc_id = n.id
				AND kre.char_id = $charID)
	AND n.`level` > 51 AND n.`level` < 99 AND n.loottable_id > 0 
	AND n.raid_target = 1
	AND n.raid_points > 0
	AND n.loottable_id > 0
	AND n.id NOT BETWEEN 128041 AND 128044
	ORDER BY $order
	TPL;
}
#$query = sprintf($tpl, $charID);
$result = $cbsql->query($tpl);

$raid = $cbsql->fetch_all($result);  

//get raid total points from db
$tpl = <<<TPL
SELECT db.`value` AS TotalPts
FROM data_buckets db
INNER JOIN character_data cd ON cd.`id` = SUBSTRING(db.`key`, 18)
WHERE db.`key` LIKE 'PlayerRaidPoints-%'
AND cd.`id` = $charID
TPL;
#$query = sprintf($tpl, $charID);
$result = $cbsql->query($tpl);

$raidtotal = $cbsql->fetch_all($result);  

//get completed epics from db
$tpl = <<<TPL
SELECT db.`key`, db.`value` AS EarnedValue, cd.`id` AS CharID, cd.`name` AS CharName, i.`id` AS ItemID, i.`name` AS ItemName, db.`value` AS EarnedValue
FROM data_buckets db
INNER JOIN items i ON i.`id` = SUBSTRING(db.`key`, 19, INSTR(SUBSTRING(db.`key`, 19), "-")+1)
INNER JOIN character_data cd ON cd.`id` = SUBSTRING(db.`key`, INSTR(SUBSTRING(db.`key`, 19), "-")+19)
WHERE db.`key` LIKE 'RaidPtsEpicTurnIn-%'
AND cd.`id` = $charID
-- AND n.`raid_points` > 0
ORDER BY db.`value` ASC
TPL;
#$query = sprintf($tpl, $charID);
$result = $cbsql->query($tpl);

$epictotal = $cbsql->fetch_all($result);  

/*********************************************
               DROP HEADER
*********************************************/
$d_title = " - ".$name.$language['PAGE_TITLES_RAID'];
include(__DIR__ . "/include/header.php");


/*********************************************
            DROP PROFILE MENU
*********************************************/
output_profile_menu($name, 'raid');

/*********************************************
              POPULATE BODY
*********************************************/

$cb_template->set_filenames(array(
   'raid' => 'raid_body.tpl')
);

$cb_template->assign_both_vars(array(
   'NAME' => $name)
);
$cb_template->assign_vars(array(
   'L_RAID' => $language['RAID_RAIDPTS'],
   'L_DONE' => $language['BUTTON_DONE'])
);

foreach ($raid as $raidpts) {
	if ($raidpts["EarnedValue"] == 0 && $raidpts["RaidPts"] > 0) {
		$raidpts["EarnedValue"] = 'Dupe - 0';
	}
	if ($raidpts["RaidPts"] == 0) {
		$raidpts["EarnedValue"] = 'No Value - 0';
	}
   $cb_template->assign_both_block_vars("raid", array(
      'NPC_NAME' => $raidpts['NPCName'],
      'NPC_ID' => $raidpts["NPCID"],
      'NPC' => 'http://vegaseq.com/Allaclone/?a=npc&id=' . $raidpts["NPCID"],
	  'NPC_PTS' => $raidpts["EarnedValue"],
	  'NPC_ZONESN' => 'http://vegaseq.com/Allaclone/?a=zone&name=' . $raidpts["ZoneSN"],
	  'NPC_ZONELN' => $raidpts["ZoneLN"],
	  'NPC_DIFF' => number_format($raidpts["NPCDiff"]))
   );
}

foreach ($raidtotal as $raidtotals) {
   $cb_template->assign_both_block_vars("raidtotal", array(
		'NPC_TOTALPTS' => $raidtotals["TotalPts"])
   );
}

foreach ($epictotal as $epictotals) {
	$cb_template->assign_both_block_vars("epictotal", array(
		'ITEM_NAME' => $epictotals['ItemName'],
		'ITEM_ID' => $epictotals["ItemID"],
		'ITEM' => 'http://vegaseq.com/Allaclone/?a=item&id=' . $epictotals["ItemID"],
		'ITEM_PTS' => $epictotals["EarnedValue"])
	);
}

$cb_template->assign_both_block_vars("raidkilltype", array('KILL_TYPE' => $killtype));

/*********************************************
           OUTPUT BODY AND FOOTER
*********************************************/
$cb_template->pparse('raid');

$cb_template->destroy;

include(__DIR__ . "/include/footer.php");

 ?>