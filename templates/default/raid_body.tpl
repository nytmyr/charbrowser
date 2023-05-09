<div class='WindowComplex PositionRaid CB_Can_Drag'>
	<center>
	<div class='WindowTitleBar'>{L_RAID} - {NAME}</div>
	<h2>
		----------------------Killed Raid Targets---------------------- <br>
		Name <a href=http://vegaseq.com/charbrowser/index.php?page=raidpoints&char={NAME}&order=1>+</a>/<a href=http://vegaseq.com/charbrowser/index.php?page=raidpoints&char={NAME}&order=2>-</a>
		| Point Value <a href=http://vegaseq.com/charbrowser/index.php?page=raidpoints&char={NAME}&order=3>+</a>/<a href=http://vegaseq.com/charbrowser/index.php?page=raidpoints&char={NAME}&order=4>-</a>
		| Zone <a href=http://vegaseq.com/charbrowser/index.php?page=raidpoints&char={NAME}&order=5>+</a>/<a href=http://vegaseq.com/charbrowser/index.php?page=raidpoints&char={NAME}&order=6>-</a>
		| Difficulty <a href=http://vegaseq.com/charbrowser/index.php?page=raidpoints&char={NAME}&order=7>+</a>/<a href=http://vegaseq.com/charbrowser/index.php?page=raidpoints&char={NAME}&order=8>-</a>
		<br>
		<br>--------------------Unkilled Raid Targets-------------------- <br>
		Name <a href=http://vegaseq.com/charbrowser/index.php?page=raidpoints&char={NAME}&unkilled=1&order=1>+</a>/<a href=http://vegaseq.com/charbrowser/index.php?page=raidpoints&char={NAME}&unkilled=1&order=2>-</a>
		| Point Value <a href=http://vegaseq.com/charbrowser/index.php?page=raidpoints&char={NAME}&unkilled=1&order=3>+</a>/<a href=http://vegaseq.com/charbrowser/index.php?page=raidpoints&char={NAME}&unkilled=1&order=4>-</a>
		| Zone <a href=http://vegaseq.com/charbrowser/index.php?page=raidpoints&char={NAME}&unkilled=1&order=5>+</a>/<a href=http://vegaseq.com/charbrowser/index.php?page=raidpoints&char={NAME}&unkilled=1&order=6>-</a>
		| Difficulty <a href=http://vegaseq.com/charbrowser/index.php?page=raidpoints&char={NAME}&unkilled=1&order=7>+</a>/<a href=http://vegaseq.com/charbrowser/index.php?page=raidpoints&char={NAME}&unkilled=1&order=8>-</a>
		<br><br>
	</h2>
	<!-- BEGIN raidtotal -->
	<font color=yellow>Total Points - {raidtotal.NPC_TOTALPTS}<br><br>
	<!-- END raidtotal -->
	<font color=lightblue>----- Epic Quests Completed -----<br>
	<!-- BEGIN epictotal -->
	<a href='{epictotal.ITEM}'>{epictotal.ITEM_NAME}</a> <font color=green>- {epictotal.ITEM_PTS} point(s)<br>
	<!-- END epictotal -->
	<!-- BEGIN raidkilltype -->
	<br><font color=lightblue>----- {raidkilltype.KILL_TYPE} Raid Targets -----<br>
	<!-- END raidkilltype -->
	<!-- BEGIN raid -->
	<a href='{raid.NPC}'>{raid.NPC_NAME}</a> <font color=green>- {raid.NPC_PTS} point(s) - <a href='{raid.NPC_ZONESN}'>{raid.NPC_ZONELN}</a> - {raid.NPC_DIFF}<br>
	<!-- END raid -->
	<a class='CB_Button' href="{INDEX_URL}?page=character&char={NAME}">{L_DONE}</a>
	</center>
</div>