<?php 

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

//block view if user level doesnt have permission
if ($mypermission['bots']) cb_message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ITEM_NO_VIEW']);

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
		if ($bot['ip'] == $userip || $userip == $defaultedlocalhost || $userip == $localipaddress || $userip == $defaultgateway) {
			$ownercheck = 1;
			$userip = $bot['ip'];
		}
	}
if ($ownercheck == 1) {
	// Fetch records from database 
		$csvtype = (isset($_GET['csvtype']) ? $_GET['csvtype'] : "");
		$showitemnames = (isset($_GET['showitemnames']) ? $_GET['showitemnames'] : "");
		if ($csvtype == "all") {
			if ($showitemnames == "true") {
				$filename = "All-itemnames-bot-data_" . date('Y-m-d') . ".csv"; 
			} else {
				$filename = "All-bot-data_" . date('Y-m-d') . ".csv"; 
			}
			$where = "ai.ip = '$userip'";
			$groupby = "GROUP BY b.bot_id, bi.slot_id";
		}
		if ($csvtype == "owner") {
			if ($showitemnames == "true") {
				$filename = "$charName-owned-itemnames-bot-data_" . date('Y-m-d') . ".csv"; 
			} else {
				$filename = "$charName-owned-bot-data_" . date('Y-m-d') . ".csv"; 
			}
			$where = "cd.id = $charID";
			$groupby = "GROUP BY b.bot_id, bi.slot_id";
		}
		if ($csvtype == "this") {
			if ($showitemnames == "true") {
				$filename = "$botName-itemnames-data_" . date('Y-m-d') . ".csv"; 
			} else {
				$filename = "$botName-data_" . date('Y-m-d') . ".csv"; 
			}
			$where = "b.bot_id = $botID";
			$groupby = "-- GROUP BY b.bot_id, bi.slot_id";
		}
		if ($csvtype == "thischar") {
			if ($showitemnames == "true") {
				$filename = "$charName-itemnames-data_" . date('Y-m-d') . ".csv"; 
			} else {
				$filename = "$charName-data_" . date('Y-m-d') . ".csv"; 
			}
		}
		if ($csvtype != "thischar") {
			$tpl = 
					"
						SELECT cd.name AS Owner, b.name AS BotName
							,	CASE
								WHEN b.class = 1 THEN 'Warrior'
								WHEN b.class = 2 THEN 'Cleric'
								WHEN b.class = 3 THEN 'Paladin'
								WHEN b.class = 4 THEN 'Ranger'
								WHEN b.class = 5 THEN 'Shadowknight'
								WHEN b.class = 6 THEN 'Druid'
								WHEN b.class = 7 THEN 'Monk'
								WHEN b.class = 8 THEN 'Bard'
								WHEN b.class = 9 THEN 'Rogue'
								WHEN b.class = 10 THEN 'Shaman'
								WHEN b.class = 11 THEN 'Necromancer'
								WHEN b.class = 12 THEN 'Wizard'
								WHEN b.class = 13 THEN 'Magician'
								WHEN b.class = 14 THEN 'Enchanter'
								WHEN b.class = 15 THEN 'Beastlord'
								WHEN b.class = 16 THEN 'Berserker'
								ELSE 'None'
							END AS 'Class'
							, i.GearScore, bi.slot_id as SlotID,
							CASE WHEN bi.slot_id = 1 THEN i.GearScore ELSE 0 END AS 'Ear1',
							CASE WHEN bi.slot_id = 2 THEN i.GearScore ELSE 0 END AS 'Head',
							CASE WHEN bi.slot_id = 3 THEN i.GearScore ELSE 0 END AS 'Face',
							CASE WHEN bi.slot_id = 4 THEN i.GearScore ELSE 0 END AS 'Ear2',
							CASE WHEN bi.slot_id = 5 THEN i.GearScore ELSE 0 END AS 'Neck',
							CASE WHEN bi.slot_id = 6 THEN i.GearScore ELSE 0 END AS 'Shoulders',
							CASE WHEN bi.slot_id = 7 THEN i.GearScore ELSE 0 END AS 'Arms',
							CASE WHEN bi.slot_id = 8 THEN i.GearScore ELSE 0 END AS 'Back',
							CASE WHEN bi.slot_id = 9 THEN i.GearScore ELSE 0 END AS 'Wrist1',
							CASE WHEN bi.slot_id = 10 THEN i.GearScore ELSE 0 END AS 'Wrist2',
							CASE WHEN bi.slot_id = 11 THEN i.GearScore ELSE 0 END AS 'Range',
							CASE WHEN bi.slot_id = 12 THEN i.GearScore ELSE 0 END AS 'Hands',
							CASE WHEN bi.slot_id = 13 THEN i.GearScore ELSE 0 END AS 'Primary',
							CASE WHEN bi.slot_id = 14 THEN i.GearScore ELSE 0 END AS 'Secondary',
							CASE WHEN bi.slot_id = 15 THEN i.GearScore ELSE 0 END AS 'Finger1',
							CASE WHEN bi.slot_id = 16 THEN i.GearScore ELSE 0 END AS 'Finger2',
							CASE WHEN bi.slot_id = 17 THEN i.GearScore ELSE 0 END AS 'Chest',
							CASE WHEN bi.slot_id = 18 THEN i.GearScore ELSE 0 END AS 'Legs',
							CASE WHEN bi.slot_id = 19 THEN i.GearScore ELSE 0 END AS 'Feet',
							CASE WHEN bi.slot_id = 20 THEN i.GearScore ELSE 0 END AS 'Waist'
							, i.Name AS ItemName, CONCAT('http://vegaseq.com/Allaclone/?a=item&id=',i.id) AS Allaclone 
						FROM account_ip ai
						INNER JOIN ACCOUNT a ON a.id = ai.accid
						INNER JOIN character_data cd ON cd.account_id = a.id
						INNER JOIN bot_data b ON b.owner_id = cd.id
						INNER JOIN bot_inventories bi ON bi.bot_id = b.bot_id
						INNER JOIN items i ON i.id = bi.item_id
						WHERE $where
						$groupby
						ORDER BY cd.`level` DESC, cd.aa_points_spent DESC, b.name ASC, bi.slot_id ASC
					"; 
				$result = $cbsql->query($tpl);
				if (!$cbsql->rows($result)) cb_message('Success', 'Failed @ ' . $userip . ' - ' . $where . ' - ' . $filename);
					$rows = $cbsql->fetch_all($result);
					
					/*START View */
					
					//foreach($rows as $row) {
					//	$delimiter = "\"	\"";
					//	$filler .= "\"";
					//	$filler .= $row['Owner'] . "" . $delimiter . "" . $row['BotName'] . "" . $delimiter . "" . $row['Class'] . "" . $delimiter . "" . $row['GearScore'] . "" . $delimiter . "" . $row['Slot'] . "" . $delimiter . "" . $row['ItemName'] . "" . $delimiter . "" . $row['Allaclone'];
					//	$filler .= "\"";
					//	$filler .= "<br>";
					//} 
					//cb_botcommandsettings('"Owner" "BotName" "Class" "GearScore" "Slot" "ItemName" "Allaclone"', $filler);
					
					/* END */
					
					$delimiter = ","; 
					// Create a file pointer 
					$f = fopen('php://memory', 'w'); 
					
					// Set column headers 
					$fields = array('Owner', 'Bot Name', 'Class', 'AVG GearScore', 'Ear1', 'Ear2', 'Head', 'Face', 'Neck', 'Shoulders', 'Arms', 'Back', 'Wrist1', 'Wrist2', 'Range', 'Hands', 'Primary', 'Secondary', 'Finger1', 'Finger2', 'Chest', 'Legs', 'Feet', 'Waist'); 
					fputcsv($f, $fields, $delimiter); 
					$selectedOwner = "None";
					$selectedName = "None";
					$selectedClass = "None";
					$selectedGearScore = 0;
					$selectedAvgGearScore = "=AVERAGE(INDIRECT(\"E\"&ROW()):INDIRECT(\"X\"&ROW()))";
					$selectedEar1 = 0;
					$selectedHead = 0;
					$selectedFace = 0;
					$selectedEar2 = 0;
					$selectedNeck = 0;
					$selectedShoulders = 0;
					$selectedArms = 0;
					$selectedBack = 0;
					$selectedWrist1 = 0;
					$selectedWrist2 = 0;
					$selectedRange = 0;
					$selectedHands = 0;
					$selectedPrimary = 0;
					$selectedSecondary = 0;
					$selectedFinger1 = 0;
					$selectedFinger2 = 0;
					$selectedChest = 0;
					$selectedLegs = 0;
					$selectedFeet = 0;
					$selectedWaist = 0;
					$selectedEar1Name = "Empty";
					$selectedHeadName = "Empty";
					$selectedFaceName = "Empty";
					$selectedEar2Name = "Empty";
					$selectedNeckName = "Empty";
					$selectedShouldersName = "Empty";
					$selectedArmsName = "Empty";
					$selectedBackName = "Empty";
					$selectedWrist1Name = "Empty";
					$selectedWrist2Name = "Empty";
					$selectedRangeName = "Empty";
					$selectedHandsName = "Empty";
					$selectedPrimaryName = "Empty";
					$selectedSecondaryName = "Empty";
					$selectedFinger1Name = "Empty";
					$selectedFinger2Name = "Empty";
					$selectedChestName = "Empty";
					$selectedLegsName = "Empty";
					$selectedFeetName = "Empty";
					$selectedWaistName = "Empty";
					$selectedCharName = 'None';
					$selectedCharGearScore = 0;
					$selectedCharEar1 = 0;
					$selectedCharHead = 0;
					$selectedCharFace = 0;
					$selectedCharEar2 = 0;
					$selectedCharNeck = 0;
					$selectedCharShoulders = 0;
					$selectedCharArms = 0;
					$selectedCharBack = 0;
					$selectedCharWrist1 = 0;
					$selectedCharWrist2 = 0;
					$selectedCharRange = 0;
					$selectedCharHands = 0;
					$selectedCharPrimary = 0;
					$selectedCharSecondary = 0;
					$selectedCharFinger1 = 0;
					$selectedCharFinger2 = 0;
					$selectedCharChest = 0;
					$selectedCharLegs = 0;
					$selectedCharFeet = 0;
					$selectedCharWaist = 0;
					$selectedCharEar1Name = "Empty";
					$selectedCharHeadName = "Empty";
					$selectedCharFaceName = "Empty";
					$selectedCharEar2Name = "Empty";
					$selectedCharNeckName = "Empty";
					$selectedCharShouldersName = "Empty";
					$selectedCharArmsName = "Empty";
					$selectedCharBackName = "Empty";
					$selectedCharWrist1Name = "Empty";
					$selectedCharWrist2Name = "Empty";
					$selectedCharRangeName = "Empty";
					$selectedCharHandsName = "Empty";
					$selectedCharPrimaryName = "Empty";
					$selectedCharSecondaryName = "Empty";
					$selectedCharFinger1Name = "Empty";
					$selectedCharFinger2Name = "Empty";
					$selectedCharChestName = "Empty";
					$selectedCharLegsName = "Empty";
					$selectedCharFeetName = "Empty";
					$selectedCharWaistName = "Empty";
					// Output each row of the data, format line as csv and write to file pointer
					foreach($rows as $row) {
						if ($selectedName != $row['BotName']) {
							if ($selectedName != "None") {
								//$blankLine = array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""); 
								//fputcsv($f, $blankLine, $delimiter); 
								
								if ($showitemnames == "true") {
									$lineData = array("", "=HYPERLINK(\"http://vegaseq.com/charbrowser/index.php?page=bot&bot=" . $selectedName . "\", \"" . $selectedName . "\")", $selectedClass, $selectedGearScore / 20, $selectedEar1, $selectedEar2, $selectedHead, $selectedFace, $selectedNeck, $selectedShoulders, $selectedArms, $selectedBack, $selectedWrist1, $selectedWrist2, $selectedRange, $selectedHands, $selectedPrimary, $selectedSecondary, $selectedFinger1, $selectedFinger2, $selectedChest, $selectedLegs, $selectedFeet, $selectedWaist); 
									fputcsv($f, $lineData, $delimiter);
									$lineNameData = array("", "", "", "", $selectedEar1Name, $selectedEar2Name, $selectedHeadName, $selectedFaceName, $selectedNeckName, $selectedShouldersName, $selectedArmsName, $selectedBackName, $selectedWrist1Name, $selectedWrist2Name, $selectedRangeName, $selectedHandsName, $selectedPrimaryName, $selectedSecondaryName, $selectedFinger1Name, $selectedFinger2Name, $selectedChestName, $selectedLegsName, $selectedFeetName, $selectedWaistName); 
									fputcsv($f, $lineNameData, $delimiter); 
								} else {
									$lineData = array("", "=HYPERLINK(\"http://vegaseq.com/charbrowser/index.php?page=bot&bot=" . $selectedName . "\", \"" . $selectedName . "\")", $selectedClass, $selectedGearScore / 20, $selectedEar1, $selectedEar2, $selectedHead, $selectedFace, $selectedNeck, $selectedShoulders, $selectedArms, $selectedBack, $selectedWrist1, $selectedWrist2, $selectedRange, $selectedHands, $selectedPrimary, $selectedSecondary, $selectedFinger1, $selectedFinger2, $selectedChest, $selectedLegs, $selectedFeet, $selectedWaist); 
									fputcsv($f, $lineData, $delimiter);
								}
							}
							$selectedName = $row['BotName'];
							$selectedClass = $row['Class'];
							$selectedAvgGearScore = "=AVERAGE(INDIRECT(\"E\"&ROW()):INDIRECT(\"X\"&ROW()))";
							$selectedGearScore = 0;
							$selectedEar1 = 0;
							$selectedHead = 0;
							$selectedFace = 0;
							$selectedEar2 = 0;
							$selectedNeck = 0;
							$selectedShoulders = 0;
							$selectedArms = 0;
							$selectedBack = 0;
							$selectedWrist1 = 0;
							$selectedWrist2 = 0;
							$selectedRange = 0;
							$selectedHands = 0;
							$selectedPrimary = 0;
							$selectedSecondary = 0;
							$selectedFinger1 = 0;
							$selectedFinger2 = 0;
							$selectedChest = 0;
							$selectedLegs = 0;
							$selectedFeet = 0;
							$selectedWaist = 0;
							$selectedEar1Name = "Empty";
							$selectedHeadName = "Empty";
							$selectedFaceName = "Empty";
							$selectedEar2Name = "Empty";
							$selectedNeckName = "Empty";
							$selectedShouldersName = "Empty";
							$selectedArmsName = "Empty";
							$selectedBackName = "Empty";
							$selectedWrist1Name = "Empty";
							$selectedWrist2Name = "Empty";
							$selectedRangeName = "Empty";
							$selectedHandsName = "Empty";
							$selectedPrimaryName = "Empty";
							$selectedSecondaryName = "Empty";
							$selectedFinger1Name = "Empty";
							$selectedFinger2Name = "Empty";
							$selectedChestName = "Empty";
							$selectedLegsName = "Empty";
							$selectedFeetName = "Empty";
							$selectedWaistName = "Empty";
						}
						if ($selectedOwner != $row['Owner']) {
							$botOwner = $row['Owner'];
							$tpl = 
								"
								SELECT cd.name AS Owner 
									,	CASE
										WHEN cd.class = 1 THEN 'Warrior'
										WHEN cd.class = 2 THEN 'Cleric'
										WHEN cd.class = 3 THEN 'Paladin'
										WHEN cd.class = 4 THEN 'Ranger'
										WHEN cd.class = 5 THEN 'Shadowknight'
										WHEN cd.class = 6 THEN 'Druid'
										WHEN cd.class = 7 THEN 'Monk'
										WHEN cd.class = 8 THEN 'Bard'
										WHEN cd.class = 9 THEN 'Rogue'
										WHEN cd.class = 10 THEN 'Shaman'
										WHEN cd.class = 11 THEN 'Necromancer'
										WHEN cd.class = 12 THEN 'Wizard'
										WHEN cd.class = 13 THEN 'Magician'
										WHEN cd.class = 14 THEN 'Enchanter'
										WHEN cd.class = 15 THEN 'Beastlord'
										WHEN cd.class = 16 THEN 'Berserker'
										ELSE 'None'
									END AS 'Class'
									, i.GearScore, inv.slotid as SlotID,
									CASE WHEN inv.slotid = 1 THEN i.GearScore ELSE 0 END AS 'Ear1',
									CASE WHEN inv.slotid = 2 THEN i.GearScore ELSE 0 END AS 'Head',
									CASE WHEN inv.slotid = 3 THEN i.GearScore ELSE 0 END AS 'Face',
									CASE WHEN inv.slotid = 4 THEN i.GearScore ELSE 0 END AS 'Ear2',
									CASE WHEN inv.slotid = 5 THEN i.GearScore ELSE 0 END AS 'Neck',
									CASE WHEN inv.slotid = 6 THEN i.GearScore ELSE 0 END AS 'Shoulders',
									CASE WHEN inv.slotid = 7 THEN i.GearScore ELSE 0 END AS 'Arms',
									CASE WHEN inv.slotid = 8 THEN i.GearScore ELSE 0 END AS 'Back',
									CASE WHEN inv.slotid = 9 THEN i.GearScore ELSE 0 END AS 'Wrist1',
									CASE WHEN inv.slotid = 10 THEN i.GearScore ELSE 0 END AS 'Wrist2',
									CASE WHEN inv.slotid = 11 THEN i.GearScore ELSE 0 END AS 'Range',
									CASE WHEN inv.slotid = 12 THEN i.GearScore ELSE 0 END AS 'Hands',
									CASE WHEN inv.slotid = 13 THEN i.GearScore ELSE 0 END AS 'Primary',
									CASE WHEN inv.slotid = 14 THEN i.GearScore ELSE 0 END AS 'Secondary',
									CASE WHEN inv.slotid = 15 THEN i.GearScore ELSE 0 END AS 'Finger1',
									CASE WHEN inv.slotid = 16 THEN i.GearScore ELSE 0 END AS 'Finger2',
									CASE WHEN inv.slotid = 17 THEN i.GearScore ELSE 0 END AS 'Chest',
									CASE WHEN inv.slotid = 18 THEN i.GearScore ELSE 0 END AS 'Legs',
									CASE WHEN inv.slotid = 19 THEN i.GearScore ELSE 0 END AS 'Feet',
									CASE WHEN inv.slotid = 20 THEN i.GearScore ELSE 0 END AS 'Waist'
									, i.Name AS ItemName, CONCAT('http://vegaseq.com/Allaclone/?a=item&id=',i.id) AS Allaclone 
								FROM character_data cd
								-- FROM account_ip ai
								-- INNER JOIN ACCOUNT a ON a.id = ai.accid
								-- INNER JOIN character_data cd ON cd.account_id = a.id
								-- INNER JOIN bot_data b ON b.owner_id = cd.id
								INNER JOIN inventory inv ON inv.charid = cd.id
								INNER JOIN items i ON i.id = inv.itemid
								-- WHERE $where
								WHERE cd.`name` LIKE '$botOwner'
								AND inv.slotid BETWEEN 1 AND 20
								";
							$resultchar = $cbsql->query($tpl);
							if (!$cbsql->rows($resultchar)) cb_message('Success', 'Failed @ ' . $userip . ' - ' . $where . ' - ' . $filename);
								$rowchars = $cbsql->fetch_all($resultchar);
								foreach($rowchars as $rowchar) {
									$selectedCharName = $rowchar['Owner'];
									$selectedCharClass = $rowchar['Class'];
									$selectedCharGearScore += $rowchar['GearScore'];
									if ($rowchar['Ear1'] > 0) { $selectedCharEar1 = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")"; $selectedCharEar1Name = $rowchar['ItemName'];}
									if ($rowchar['Head'] > 0) { $selectedCharHead = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")"; $selectedCharHeadName = $rowchar['ItemName'];}
									if ($rowchar['Face'] > 0) { $selectedCharFace = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")"; $selectedCharFaceName = $rowchar['ItemName'];}
									if ($rowchar['Ear2'] > 0) { $selectedCharEar2 = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")"; $selectedCharEar2Name = $rowchar['ItemName'];}
									if ($rowchar['Neck'] > 0) { $selectedCharNeck = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")"; $selectedCharNeckName = $rowchar['ItemName'];}
									if ($rowchar['Shoulders'] > 0) { $selectedCharShoulders = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")"; $selectedCharShouldersName = $rowchar['ItemName'];}
									if ($rowchar['Arms'] > 0) { $selectedCharArms = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")"; $selectedCharArmsName = $rowchar['ItemName'];}
									if ($rowchar['Back'] > 0) { $selectedCharBack = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")"; $selectedCharBackName = $rowchar['ItemName'];}
									if ($rowchar['Wrist1'] > 0) { $selectedCharWrist1 = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")"; $selectedCharWrist1Name = $rowchar['ItemName'];}
									if ($rowchar['Wrist2'] > 0) { $selectedCharWrist2 = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")"; $selectedCharWrist2Name = $rowchar['ItemName'];}
									if ($rowchar['Range'] > 0) { $selectedCharRange = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")"; $selectedCharRangeName = $rowchar['ItemName'];}
									if ($rowchar['Hands'] > 0) { $selectedCharHands = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")"; $selectedCharHandsName = $rowchar['ItemName'];}
									if ($rowchar['Primary'] > 0) { $selectedCharPrimary = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")"; $selectedCharPrimaryName = $rowchar['ItemName'];}
									if ($rowchar['Secondary'] > 0) { $selectedCharSecondary = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")"; $selectedCharSecondaryName = $rowchar['ItemName'];}
									if ($rowchar['Finger1'] > 0) { $selectedCharFinger1 = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")"; $selectedCharFinger1Name = $rowchar['ItemName'];}
									if ($rowchar['Finger2'] > 0) { $selectedCharFinger2 = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")"; $selectedCharFinger2Name = $rowchar['ItemName'];}
									if ($rowchar['Chest'] > 0) { $selectedCharChest = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")"; $selectedCharChestName = $rowchar['ItemName'];}
									if ($rowchar['Legs'] > 0) { $selectedCharLegs = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")"; $selectedCharLegsName = $rowchar['ItemName'];}
									if ($rowchar['Feet'] > 0) { $selectedCharFeet = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")"; $selectedCharFeetName = $rowchar['ItemName'];}
									if ($rowchar['Waist'] > 0) { $selectedCharWaist = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")"; $selectedCharWaistName = $rowchar['ItemName'];}	
								}
							if ($selectedOwner != "None") {
								$blankLine = array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""); 
								fputcsv($f, $blankLine, $delimiter); 
								$lineData = array("--", "--", "--");
								fputcsv($f, $lineData, $delimiter); 
							}
							$selectedOwner = $row['Owner'];
							$blankLine = array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""); 
							fputcsv($f, $blankLine, $delimiter);
							
							if ($showitemnames == "true") {
								$lineData = array("=HYPERLINK(\"http://vegaseq.com/charbrowser/index.php?page=character&char=" . $selectedCharName . "\", \"" . $selectedCharName . "\")", "", $selectedCharClass, $selectedCharGearScore / 20, $selectedCharEar1, $selectedCharEar2, $selectedCharHead, $selectedCharFace, $selectedCharNeck, $selectedCharShoulders, $selectedCharArms, $selectedCharBack, $selectedCharWrist1, $selectedCharWrist2, $selectedCharRange, $selectedCharHands, $selectedCharPrimary, $selectedCharSecondary, $selectedCharFinger1, $selectedCharFinger2, $selectedCharChest, $selectedCharLegs, $selectedCharFeet, $selectedCharWaist); 
								fputcsv($f, $lineData, $delimiter);
								$lineNameData = array("", "", "", "", $selectedCharEar1Name, $selectedCharEar2Name, $selectedCharHeadName, $selectedCharFaceName, $selectedCharNeckName, $selectedCharShouldersName, $selectedCharArmsName, $selectedCharBackName, $selectedCharWrist1Name, $selectedCharWrist2Name, $selectedCharRangeName, $selectedCharHandsName, $selectedCharPrimaryName, $selectedCharSecondaryName, $selectedCharFinger1Name, $selectedCharFinger2Name, $selectedCharChestName, $selectedCharLegsName, $selectedCharFeetName, $selectedCharWaistName); 
								fputcsv($f, $lineNameData, $delimiter);
							} else {
								$lineData = array("=HYPERLINK(\"http://vegaseq.com/charbrowser/index.php?page=character&char=" . $selectedCharName . "\", \"" . $selectedCharName . "\")", "", $selectedCharClass, $selectedCharGearScore / 20, $selectedCharEar1, $selectedCharEar2, $selectedCharHead, $selectedCharFace, $selectedCharNeck, $selectedCharShoulders, $selectedCharArms, $selectedCharBack, $selectedCharWrist1, $selectedCharWrist2, $selectedCharRange, $selectedCharHands, $selectedCharPrimary, $selectedCharSecondary, $selectedCharFinger1, $selectedCharFinger2, $selectedCharChest, $selectedCharLegs, $selectedCharFeet, $selectedCharWaist); 
								fputcsv($f, $lineData, $delimiter);
							} 
							$selectedCharName = 'None';
							$selectedCharGearScore = 0;
							$selectedCharEar1 = 0;
							$selectedCharHead = 0;
							$selectedCharFace = 0;
							$selectedCharEar2 = 0;
							$selectedCharNeck = 0;
							$selectedCharShoulders = 0;
							$selectedCharArms = 0;
							$selectedCharBack = 0;
							$selectedCharWrist1 = 0;
							$selectedCharWrist2 = 0;
							$selectedCharRange = 0;
							$selectedCharHands = 0;
							$selectedCharPrimary = 0;
							$selectedCharSecondary = 0;
							$selectedCharFinger1 = 0;
							$selectedCharFinger2 = 0;
							$selectedCharChest = 0;
							$selectedCharLegs = 0;
							$selectedCharFeet = 0;
							$selectedCharWaist = 0;
							$selectedCharEar1Name = "Empty";
							$selectedCharHeadName = "Empty";
							$selectedCharFaceName = "Empty";
							$selectedCharEar2Name = "Empty";
							$selectedCharNeckName = "Empty";
							$selectedCharShouldersName = "Empty";
							$selectedCharArmsName = "Empty";
							$selectedCharBackName = "Empty";
							$selectedCharWrist1Name = "Empty";
							$selectedCharWrist2Name = "Empty";
							$selectedCharRangeName = "Empty";
							$selectedCharHandsName = "Empty";
							$selectedCharPrimaryName = "Empty";
							$selectedCharSecondaryName = "Empty";
							$selectedCharFinger1Name = "Empty";
							$selectedCharFinger2Name = "Empty";
							$selectedCharChestName = "Empty";
							$selectedCharLegsName = "Empty";
							$selectedCharFeetName = "Empty";
							$selectedCharWaistName = "Empty";
						}
						$selectedGearScore += $row['GearScore'];
						if ($row['Ear1'] > 0) { $selectedEar1 = "=HYPERLINK(\"" . $row['Allaclone'] . "\", \"" . $row['GearScore'] . "\")"; $selectedEar1Name = $row['ItemName'];}
						if ($row['Head'] > 0) { $selectedHead = "=HYPERLINK(\"" . $row['Allaclone'] . "\", \"" . $row['GearScore'] . "\")"; $selectedHeadName = $row['ItemName'];}
						if ($row['Face'] > 0) { $selectedFace = "=HYPERLINK(\"" . $row['Allaclone'] . "\", \"" . $row['GearScore'] . "\")"; $selectedFaceName = $row['ItemName'];}
						if ($row['Ear2'] > 0) { $selectedEar2 = "=HYPERLINK(\"" . $row['Allaclone'] . "\", \"" . $row['GearScore'] . "\")"; $selectedEar2Name = $row['ItemName'];}
						if ($row['Neck'] > 0) { $selectedNeck = "=HYPERLINK(\"" . $row['Allaclone'] . "\", \"" . $row['GearScore'] . "\")"; $selectedNeckName = $row['ItemName'];}
						if ($row['Shoulders'] > 0) { $selectedShoulders = "=HYPERLINK(\"" . $row['Allaclone'] . "\", \"" . $row['GearScore'] . "\")"; $selectedShouldersName = $row['ItemName'];}
						if ($row['Arms'] > 0) { $selectedArms = "=HYPERLINK(\"" . $row['Allaclone'] . "\", \"" . $row['GearScore'] . "\")"; $selectedArmsName = $row['ItemName'];}
						if ($row['Back'] > 0) { $selectedBack = "=HYPERLINK(\"" . $row['Allaclone'] . "\", \"" . $row['GearScore'] . "\")"; $selectedBackName = $row['ItemName'];}
						if ($row['Wrist1'] > 0) { $selectedWrist1 = "=HYPERLINK(\"" . $row['Allaclone'] . "\", \"" . $row['GearScore'] . "\")"; $selectedWrist1Name = $row['ItemName'];}
						if ($row['Wrist2'] > 0) { $selectedWrist2 = "=HYPERLINK(\"" . $row['Allaclone'] . "\", \"" . $row['GearScore'] . "\")"; $selectedWrist2Name = $row['ItemName'];}
						if ($row['Range'] > 0) { $selectedRange = "=HYPERLINK(\"" . $row['Allaclone'] . "\", \"" . $row['GearScore'] . "\")"; $selectedRangeName = $row['ItemName'];}
						if ($row['Hands'] > 0) { $selectedHands = "=HYPERLINK(\"" . $row['Allaclone'] . "\", \"" . $row['GearScore'] . "\")"; $selectedHandsName = $row['ItemName'];}
						if ($row['Primary'] > 0) { $selectedPrimary = "=HYPERLINK(\"" . $row['Allaclone'] . "\", \"" . $row['GearScore'] . "\")"; $selectedPrimaryName = $row['ItemName'];}
						if ($row['Secondary'] > 0) { $selectedSecondary = "=HYPERLINK(\"" . $row['Allaclone'] . "\", \"" . $row['GearScore'] . "\")"; $selectedSecondaryName = $row['ItemName'];}
						if ($row['Finger1'] > 0) { $selectedFinger1 = "=HYPERLINK(\"" . $row['Allaclone'] . "\", \"" . $row['GearScore'] . "\")"; $selectedFinger1Name = $row['ItemName'];}
						if ($row['Finger2'] > 0) { $selectedFinger2 = "=HYPERLINK(\"" . $row['Allaclone'] . "\", \"" . $row['GearScore'] . "\")"; $selectedFinger2Name = $row['ItemName'];}
						if ($row['Chest'] > 0) { $selectedChest = "=HYPERLINK(\"" . $row['Allaclone'] . "\", \"" . $row['GearScore'] . "\")"; $selectedChestName = $row['ItemName'];}
						if ($row['Legs'] > 0) { $selectedLegs = "=HYPERLINK(\"" . $row['Allaclone'] . "\", \"" . $row['GearScore'] . "\")"; $selectedLegsName = $row['ItemName'];}
						if ($row['Feet'] > 0) { $selectedFeet = "=HYPERLINK(\"" . $row['Allaclone'] . "\", \"" . $row['GearScore'] . "\")"; $selectedFeetName = $row['ItemName'];}
						if ($row['Waist'] > 0) { $selectedWaist = "=HYPERLINK(\"" . $row['Allaclone'] . "\", \"" . $row['GearScore'] . "\")"; $selectedWaistName = $row['ItemName'];}
					} 
					//$blankLine = array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""); 
					//fputcsv($f, $blankLine, $delimiter); 
					if ($showitemnames == "true") {
						$lineData = array("", "=HYPERLINK(\"http://vegaseq.com/charbrowser/index.php?page=bot&bot=" . $selectedName . "\", \"" . $selectedName . "\")", $selectedClass, $selectedGearScore / 20, $selectedEar1, $selectedEar2, $selectedHead, $selectedFace, $selectedNeck, $selectedShoulders, $selectedArms, $selectedBack, $selectedWrist1, $selectedWrist2, $selectedRange, $selectedHands, $selectedPrimary, $selectedSecondary, $selectedFinger1, $selectedFinger2, $selectedChest, $selectedLegs, $selectedFeet, $selectedWaist); 
						fputcsv($f, $lineData, $delimiter); 
						$lineNameData = array("", "", "", "", $selectedEar1Name, $selectedEar2Name, $selectedHeadName, $selectedFaceName, $selectedNeckName, $selectedShouldersName, $selectedArmsName, $selectedBackName, $selectedWrist1Name, $selectedWrist2Name, $selectedRangeName, $selectedHandsName, $selectedPrimaryName, $selectedSecondaryName, $selectedFinger1Name, $selectedFinger2Name, $selectedChestName, $selectedLegsName, $selectedFeetName, $selectedWaistName); 
						fputcsv($f, $lineNameData, $delimiter); 
					} else {
						if ($csvtype == "this") {
							$selectedGearScore = $selectedGearScore / 4;
						}
						$lineData = array("", "=HYPERLINK(\"http://vegaseq.com/charbrowser/index.php?page=bot&bot=" . $selectedName . "\", \"" . $selectedName . "\")", $selectedClass, $selectedGearScore / 20, $selectedEar1, $selectedEar2, $selectedHead, $selectedFace, $selectedNeck, $selectedShoulders, $selectedArms, $selectedBack, $selectedWrist1, $selectedWrist2, $selectedRange, $selectedHands, $selectedPrimary, $selectedSecondary, $selectedFinger1, $selectedFinger2, $selectedChest, $selectedLegs, $selectedFeet, $selectedWaist); 
						fputcsv($f, $lineData, $delimiter); 
					}
					
					// Move back to beginning of file 
					fseek($f, 0); 
					
					// Set headers to download file rather than displayed 
					header('Content-Type: text/csv'); 
					header('Content-Disposition: attachment; filename="' . $filename . '";'); 
					
					//output all remaining data on a file pointer 
					fpassthru($f); 
		}
		if ($csvtype == "thischar") {
			$tpl = 
				"
				SELECT cd.name AS Owner 
					,	CASE
						WHEN cd.class = 1 THEN 'Warrior'
						WHEN cd.class = 2 THEN 'Cleric'
						WHEN cd.class = 3 THEN 'Paladin'
						WHEN cd.class = 4 THEN 'Ranger'
						WHEN cd.class = 5 THEN 'Shadowknight'
						WHEN cd.class = 6 THEN 'Druid'
						WHEN cd.class = 7 THEN 'Monk'
						WHEN cd.class = 8 THEN 'Bard'
						WHEN cd.class = 9 THEN 'Rogue'
						WHEN cd.class = 10 THEN 'Shaman'
						WHEN cd.class = 11 THEN 'Necromancer'
						WHEN cd.class = 12 THEN 'Wizard'
						WHEN cd.class = 13 THEN 'Magician'
						WHEN cd.class = 14 THEN 'Enchanter'
						WHEN cd.class = 15 THEN 'Beastlord'
						WHEN cd.class = 16 THEN 'Berserker'
						ELSE 'None'
					END AS 'Class'
					, i.GearScore, inv.slotid as SlotID,
					CASE WHEN inv.slotid = 1 THEN i.GearScore ELSE 0 END AS 'Ear1',
					CASE WHEN inv.slotid = 2 THEN i.GearScore ELSE 0 END AS 'Head',
					CASE WHEN inv.slotid = 3 THEN i.GearScore ELSE 0 END AS 'Face',
					CASE WHEN inv.slotid = 4 THEN i.GearScore ELSE 0 END AS 'Ear2',
					CASE WHEN inv.slotid = 5 THEN i.GearScore ELSE 0 END AS 'Neck',
					CASE WHEN inv.slotid = 6 THEN i.GearScore ELSE 0 END AS 'Shoulders',
					CASE WHEN inv.slotid = 7 THEN i.GearScore ELSE 0 END AS 'Arms',
					CASE WHEN inv.slotid = 8 THEN i.GearScore ELSE 0 END AS 'Back',
					CASE WHEN inv.slotid = 9 THEN i.GearScore ELSE 0 END AS 'Wrist1',
					CASE WHEN inv.slotid = 10 THEN i.GearScore ELSE 0 END AS 'Wrist2',
					CASE WHEN inv.slotid = 11 THEN i.GearScore ELSE 0 END AS 'Range',
					CASE WHEN inv.slotid = 12 THEN i.GearScore ELSE 0 END AS 'Hands',
					CASE WHEN inv.slotid = 13 THEN i.GearScore ELSE 0 END AS 'Primary',
					CASE WHEN inv.slotid = 14 THEN i.GearScore ELSE 0 END AS 'Secondary',
					CASE WHEN inv.slotid = 15 THEN i.GearScore ELSE 0 END AS 'Finger1',
					CASE WHEN inv.slotid = 16 THEN i.GearScore ELSE 0 END AS 'Finger2',
					CASE WHEN inv.slotid = 17 THEN i.GearScore ELSE 0 END AS 'Chest',
					CASE WHEN inv.slotid = 18 THEN i.GearScore ELSE 0 END AS 'Legs',
					CASE WHEN inv.slotid = 19 THEN i.GearScore ELSE 0 END AS 'Feet',
					CASE WHEN inv.slotid = 20 THEN i.GearScore ELSE 0 END AS 'Waist'
					, i.Name AS ItemName, CONCAT('http://vegaseq.com/Allaclone/?a=item&id=',i.id) AS Allaclone 
				FROM character_data cd
				-- FROM account_ip ai
				-- INNER JOIN ACCOUNT a ON a.id = ai.accid
				-- INNER JOIN character_data cd ON cd.account_id = a.id
				-- INNER JOIN bot_data b ON b.owner_id = cd.id
				INNER JOIN inventory inv ON inv.charid = cd.id
				INNER JOIN items i ON i.id = inv.itemid
				WHERE cd.`name` LIKE '$name'
				AND inv.slotid BETWEEN 1 AND 20
				";
			$resultchar = $cbsql->query($tpl);
			if (!$cbsql->rows($resultchar)) cb_message('Success', 'Failed @ ' . $userip . ' - ' . $where . ' - ' . $filename);
				$rowchars = $cbsql->fetch_all($resultchar);
				foreach($rowchars as $rowchar) {
					$selectedCharName = $rowchar['Owner'];
					$selectedCharClass = $rowchar['Class'];
					$selectedCharGearScore += $rowchar['GearScore'];
					if ($rowchar['Ear1'] > 0) { $selectedCharEar1 = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")";}
					if ($rowchar['Head'] > 0) { $selectedCharHead = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")";}
					if ($rowchar['Face'] > 0) { $selectedCharFace = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")";}
					if ($rowchar['Ear2'] > 0) { $selectedCharEar2 = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")";}
					if ($rowchar['Neck'] > 0) { $selectedCharNeck = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")";}
					if ($rowchar['Shoulders'] > 0) { $selectedCharShoulders = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")";}
					if ($rowchar['Arms'] > 0) { $selectedCharArms = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")";}
					if ($rowchar['Back'] > 0) { $selectedCharBack = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")";}
					if ($rowchar['Wrist1'] > 0) { $selectedCharWrist1 = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")";}
					if ($rowchar['Wrist2'] > 0) { $selectedCharWrist2 = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")";}
					if ($rowchar['Range'] > 0) { $selectedCharRange = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")";}
					if ($rowchar['Hands'] > 0) { $selectedCharHands = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")";}
					if ($rowchar['Primary'] > 0) { $selectedCharPrimary = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")";}
					if ($rowchar['Secondary'] > 0) { $selectedCharSecondary = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")";}
					if ($rowchar['Finger1'] > 0) { $selectedCharFinger1 = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")";}
					if ($rowchar['Finger2'] > 0) { $selectedCharFinger2 = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")";}
					if ($rowchar['Chest'] > 0) { $selectedCharChest = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")";}
					if ($rowchar['Legs'] > 0) { $selectedCharLegs = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")";}
					if ($rowchar['Feet'] > 0) { $selectedCharFeet = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")";}
					if ($rowchar['Waist'] > 0) { $selectedCharWaist = "=HYPERLINK(\"" . $rowchar['Allaclone'] . "\", \"" . $rowchar['GearScore'] . "\")";}
				}
			$blankLine = array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""); 
			fputcsv($f, $blankLine, $delimiter);
			
			if ($showitemnames == "true") {
				$lineData = array("=HYPERLINK(\"http://vegaseq.com/charbrowser/index.php?page=character&char=" . $selectedCharName . "\", \"" . $selectedCharName . "\")", "", $selectedCharClass, $selectedCharGearScore / 20, $selectedCharEar1, $selectedCharEar2, $selectedCharHead, $selectedCharFace, $selectedCharNeck, $selectedCharShoulders, $selectedCharArms, $selectedCharBack, $selectedCharWrist1, $selectedCharWrist2, $selectedCharRange, $selectedCharHands, $selectedCharPrimary, $selectedCharSecondary, $selectedCharFinger1, $selectedCharFinger2, $selectedCharChest, $selectedCharLegs, $selectedCharFeet, $selectedCharWaist); 
				fputcsv($f, $lineData, $delimiter);
				$lineNameData = array("", "", "", "", $selectedCharEar1Name, $selectedCharEar2Name, $selectedCharHeadName, $selectedCharFaceName, $selectedCharNeckName, $selectedCharShouldersName, $selectedCharArmsName, $selectedCharBackName, $selectedCharWrist1Name, $selectedCharWrist2Name, $selectedCharRangeName, $selectedCharHandsName, $selectedCharPrimaryName, $selectedCharSecondaryName, $selectedCharFinger1Name, $selectedCharFinger2Name, $selectedCharChestName, $selectedCharLegsName, $selectedCharFeetName, $selectedCharWaistName); 
				fputcsv($f, $lineNameData, $delimiter);
			} else {
				$lineData = array("=HYPERLINK(\"http://vegaseq.com/charbrowser/index.php?page=character&char=" . $selectedCharName . "\", \"" . $selectedCharName . "\")", "", $selectedCharClass, $selectedCharGearScore / 20, $selectedCharEar1, $selectedCharEar2, $selectedCharHead, $selectedCharFace, $selectedCharNeck, $selectedCharShoulders, $selectedCharArms, $selectedCharBack, $selectedCharWrist1, $selectedCharWrist2, $selectedCharRange, $selectedCharHands, $selectedCharPrimary, $selectedCharSecondary, $selectedCharFinger1, $selectedCharFinger2, $selectedCharChest, $selectedCharLegs, $selectedCharFeet, $selectedCharWaist); 
				fputcsv($f, $lineData, $delimiter);
			} 
		}
}
 
?>