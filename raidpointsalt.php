<?php

define('INCHARBROWSER', true);

include_once(__DIR__ . "/include/common.php");
include_once(__DIR__ . "/include/profile.php");
include_once(__DIR__ . "/include/db.php");

/***********************************************
 SETUP PROFILE/PERMISSIONS
***********************************************/
$charName = preg_Get_Post('char', '/^[a-zA-Z0-9]*$/', false, $language['MESSAGE_ERROR'], $language['MESSAGE_NO_CHAR'], true);

// character initializations
$char = new Charbrowser_Character($charName, $showsoftdelete, $charbrowser_is_admin_page); //the Charbrowser_Character class will sanitize the character name
$charName = $char->GetValue('name');
$charID = $char->char_id();

//block view if user level doesnt have permission
if (!OwnerCheck($char->char_id()) && $char->Permission('raidpoints')) $cb_error->message_die($language['MESSAGE_NOTICE'],$language['MESSAGE_ITEM_NO_VIEW']);

/***********************************************
 GATHER RELEVANT PAGE DATA
 Sorting is now handled client-side via JS.
 All four datasets are fetched on every load.
***********************************************/

// ── 1. KILLED RAID TARGETS ───────────────────────────────────────────────────
$tpl_killed = <<<TPL
SELECT
    db.`key`,
    db.`value`                                                        AS EarnedValue,
    cd.`id`                                                           AS CharID,
    cd.`name`                                                         AS CharName,
    n.`id`                                                            AS NPCID,
    REPLACE(REPLACE(n.`name`,'_',' '),'#','')                         AS NPCName,
    n.`difficulty`                                                    AS NPCDiff,
    n.`raid_points`                                                   AS RaidPts,
    z.`short_name`                                                    AS ZoneSN,
    z.`long_name`                                                     AS ZoneLN
FROM data_buckets db
INNER JOIN character_data cd
    ON cd.`id` = db.`character_id`  
INNER JOIN npc_types n
    ON n.`id` = SUBSTRING(db.`key`, INSTR(SUBSTRING(db.`key`, 16), "-")+16)
LEFT JOIN zone z
    ON z.`zoneidnumber` = FLOOR(CAST(n.`id` / 1000 AS DOUBLE))
WHERE db.`key` LIKE 'PlayerRaidKill-%'
  AND cd.`id` = $charID
ORDER BY n.`difficulty` ASC
TPL;

$result_killed = $cbsql->query($tpl_killed);
$raid_killed   = $cbsql->fetch_all($result_killed);

// ── 2. UNKILLED RAID TARGETS ─────────────────────────────────────────────────
$tpl_unkilled = <<<TPL
SELECT
    n.`difficulty`                                AS NPCDiff,
    n.`raid_points`                               AS RaidPts,
    n.`id`                                        AS NPCID,
    REPLACE(REPLACE(n.`name`,'_',' '),'#','')     AS NPCName,
    z.`short_name`                                AS ZoneSN,
    z.`long_name`                                 AS ZoneLN
FROM npc_types n
INNER JOIN zone z
    ON z.zoneidnumber = FLOOR(CAST(n.id / 1000 AS DOUBLE))
WHERE NOT EXISTS (
    SELECT *
    FROM qs_player_npc_kill_record kr
    INNER JOIN qs_player_npc_kill_record_entries kre
        ON kre.event_id = kr.fight_id
    WHERE kr.npc_id = n.id
      AND kre.char_id = $charID
)
AND NOT EXISTS (
    SELECT *
	FROM data_buckets db
	WHERE db.`character_id` = $charID AND
	CASE 
		WHEN n.id = 96368 THEN db.`key` LIKE 'PlayerRaidKill-96369' -- Real Faydedar
		WHEN n.id = 96369 THEN db.`key` LIKE 'PlayerRaidKill-96368' -- Triggered Faydedar
		WHEN n.id = 89154 THEN db.`key` LIKE 'PlayerRaidKill-89181' -- Real Trakanon
		WHEN n.id = 89181 THEN db.`key` LIKE 'PlayerRaidKill-89154' -- Triggered Trakanon
		ELSE db.`key` LIKE CONCAT('PlayerRaidKill-', n.id)
	END
)
  AND n.`level`       > 51
  AND n.`level`       < 99
  AND n.loottable_id  > 0
  AND n.raid_target   = 1
  AND n.raid_points   > 0
  AND n.id NOT BETWEEN 128041 AND 128044
ORDER BY n.`difficulty` ASC
TPL;

$result_unkilled = $cbsql->query($tpl_unkilled);
$raid_unkilled   = $cbsql->fetch_all($result_unkilled);

// ── 3. COMPLETED EPICS ───────────────────────────────────────────────────────
$tpl_epics_complete = <<<TPL
SELECT
    db.`key`,
    db.`value`                                                                         AS EarnedValue,
    cd.`id`                                                                            AS CharID,
    cd.`name`                                                                          AS CharName,
    i.`id`                                                                             AS ItemID,
    i.`name`                                                                           AS ItemName
FROM data_buckets db
INNER JOIN items i ON i.`id` = CAST(SUBSTRING_INDEX(db.`key`, '-', -1) AS UNSIGNED)
INNER JOIN character_data cd ON cd.`id` = db.`character_id`
WHERE db.`key` LIKE 'RaidPtsEpicTurnIn-%'
AND db.`character_id` = $charID
ORDER BY i.`name` ASC
TPL;

$result_epics_complete = $cbsql->query($tpl_epics_complete);
$epics_complete        = $cbsql->fetch_all($result_epics_complete);

// ── 4. INCOMPLETE EPICS ──────────────────────────────────────────────────────
// Finds items marked as epic (epic = 1) that have NOT been turned in yet for
// this character. Adjust the WHERE clause if your server uses a different
// mechanism to flag epic-eligible items (e.g. a specific loottable, item name
// pattern, or custom column).
$tpl_epics_incomplete = <<<TPL
SELECT
    i.`id`   AS ItemID,
    i.`name` AS ItemName,
    CASE
        WHEN i.`id` = 19436 THEN 3 -- Spell: Summon Orb
        WHEN i.`id` = 10650 THEN 4 -- Staff of the Serpent
        WHEN i.`id` = 20487 THEN 1 -- Swiftwind
        WHEN i.`id` = 14383 THEN 4 -- Innoruuk's Curse
        WHEN i.`id` = 20542 THEN 3 -- Singing Short Sword
        WHEN i.`id` = 10651 THEN 2 -- Spear of Fate
        WHEN i.`id` = 10099 THEN 3 -- Fiery Defender
        WHEN i.`id` = 17859 THEN 3 -- Red Scabbard
        WHEN i.`id` = 11057 THEN 2 -- Ragebringer
        WHEN i.`id` = 20544 THEN 3 -- Scythe of the Shadowed Soul
        WHEN i.`id` = 20488 THEN 1 -- Earthcaller
        WHEN i.`id` = 20490 THEN 2 -- Nature Walker's Scimitar
        WHEN i.`id` = 8495 THEN 3 -- Claw of the Savage Spirit
        WHEN i.`id` = 14341 THEN 4 -- Staff of the Four
        WHEN i.`id` = 5532 THEN 2 -- Water Sprinkler of Nem Ankh
        WHEN i.`id` = 10652 THEN 3 -- Celestial Fists
        ELSE 0
    END      AS PointValue
FROM items i
WHERE i.`epicitem` = 1
AND i.id < 600000
AND i.id NOT IN (8496, 10908) -- Claw of the Savage Spirit, Jagged Blade of War
    AND NOT EXISTS (
        SELECT *
        FROM data_buckets db2
        INNER JOIN character_data cd2 ON cd2.`id` = db2.`character_id`
        WHERE db2.`key` LIKE CONCAT('RaidPtsEpicTurnIn-', i.`id`)
        AND db2.`character_id` = $charID
    )
ORDER BY i.`name` ASC
TPL;

$result_epics_incomplete = $cbsql->query($tpl_epics_incomplete);
$epics_incomplete        = $cbsql->fetch_all($result_epics_incomplete);

// ── 5. TOTAL RAID POINTS ─────────────────────────────────────────────────────
$tpl_total = <<<TPL
SELECT db.`value` AS TotalPts
FROM data_buckets db
WHERE db.`key` LIKE 'PlayerRaidPoints'
AND db.`character_id` = $charID
TPL;

$result_total = $cbsql->query($tpl_total);
$raidtotal    = $cbsql->fetch_all($result_total);

/***********************************************
 DROP HEADER
***********************************************/

$d_title = " - " . $name . $language['PAGE_TITLES_RAID'];
include(__DIR__ . "/include/header.php");

/***********************************************
 DROP PROFILE MENU
***********************************************/

output_profile_menu($name, 'raid');

/***********************************************
 POPULATE BODY
***********************************************/

$cb_template->set_filenames(array('raid' => 'raid_body_alt.tpl'));

$cb_template->assign_both_vars(array('NAME' => $name));

$cb_template->assign_vars(array(
    'L_RAID' => $language['RAID_RAIDPTS'],
    'L_DONE' => $language['BUTTON_DONE'],
));

$cb_template->assign_both_block_vars("tab_counts", array(
    'RAID_KILLED' => $result_killed->num_rows,
    'RAID_UNKILLED' => $result_unkilled->num_rows,
    'EPIC_COMPLETE' => $result_epics_complete->num_rows,
    'EPIC_INCOMPLETE' => $result_epics_incomplete->num_rows,
));

// Killed NPCs
foreach ($raid_killed as $row) {
    $earnedDisplay = $row['EarnedValue'];
    if ($row['EarnedValue'] == 0 && $row['RaidPts'] > 0) {
        $earnedDisplay = 'Dupe - 0';
    }
    if ($row['RaidPts'] == 0) {
        $earnedDisplay = 'No Value - 0';
    }

    $cb_template->assign_both_block_vars("killed", array(
        'NPC_NAME'   => $row['NPCName'],
        'NPC_ID'     => $row['NPCID'],
        'NPC'        => 'http://vegaseq.com/Allaclone/?a=npc&id=' . $row['NPCID'],
        'NPC_PTS'    => $earnedDisplay,
        'NPC_RAWPTS' => $row['EarnedValue'],
        'NPC_ZONESN' => 'http://vegaseq.com/Allaclone/?a=zone&name=' . $row['ZoneSN'],
        'NPC_ZONELN' => $row['ZoneLN'],
        'NPC_DIFF'   => number_format($row['NPCDiff']),
        'NPC_RAWDIFF'=> (int)$row['NPCDiff']
    ));
}

// Unkilled NPCs
foreach ($raid_unkilled as $row) {
    $cb_template->assign_both_block_vars("unkilled", array(
        'NPC_NAME'    => $row['NPCName'],
        'NPC_ID'      => $row['NPCID'],
        'NPC'         => 'http://vegaseq.com/Allaclone/?a=npc&id=' . $row['NPCID'],
        'NPC_PTS'     => $row['RaidPts'],
        'NPC_RAWPTS'  => (int)$row['RaidPts'],
        'NPC_ZONESN'  => 'http://vegaseq.com/Allaclone/?a=zone&name=' . $row['ZoneSN'],
        'NPC_ZONELN'  => $row['ZoneLN'],
        'NPC_DIFF'    => number_format($row['NPCDiff']),
        'NPC_RAWDIFF' => (int)$row['NPCDiff']
    ));
}

// Completed Epics
foreach ($epics_complete as $row) {
    $cb_template->assign_both_block_vars("epics_complete", array(
        'ITEM_NAME' => $row['ItemName'],
        'ITEM_ID'   => $row['ItemID'],
        'ITEM'      => 'http://vegaseq.com/Allaclone/?a=item&id=' . $row['ItemID'],
        'ITEM_PTS'  => $row['EarnedValue']
    ));
}

// Incomplete Epics
foreach ($epics_incomplete as $row) {
    $cb_template->assign_both_block_vars("epics_incomplete", array(
        'ITEM_NAME' => $row['ItemName'],
        'ITEM_ID'   => $row['ItemID'],
        'ITEM'      => 'http://vegaseq.com/Allaclone/?a=item&id=' . $row['ItemID'],
        'ITEM_PTS'  => $row['PointValue']
    ));
}

// Total Points
foreach ($raidtotal as $row) {
    $cb_template->assign_both_block_vars("raidtotal", array(
        'NPC_TOTALPTS' => $row['TotalPts']
    ));
}

/***********************************************
 OUTPUT BODY AND FOOTER
***********************************************/

$cb_template->pparse('raid');
$cb_template->destroy;

include(__DIR__ . "/include/footer.php");
?>
