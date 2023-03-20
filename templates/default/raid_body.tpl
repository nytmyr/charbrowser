<div class='WindowComplex PositionKeys CB_Can_Drag'>
	<div class='WindowTitleBar'>{L_RAID} - {NAME}</div>
	<h2>
		Sort By <br>
		------------------------------------------------------ <br>
		NPC Name <a href=http://vegaseq.com/charbrowser/index.php?page=raidpoints&char={NAME}&order=1>+</a>/<a href=http://vegaseq.com/charbrowser/index.php?page=raidpoints&char={NAME}&order=2>-</a>
		| Point Value <a href=http://vegaseq.com/charbrowser/index.php?page=raidpoints&char={NAME}&order=3>+</a>/<a href=http://vegaseq.com/charbrowser/index.php?page=raidpoints&char={NAME}&order=4>-</a>
		| Zone <a href=http://vegaseq.com/charbrowser/index.php?page=raidpoints&char={NAME}&order=5>+</a>/<a href=http://vegaseq.com/charbrowser/index.php?page=raidpoints&char={NAME}&order=6>-</a>
		| Difficulty <a href=http://vegaseq.com/charbrowser/index.php?page=raidpoints&char={NAME}&order=7>+</a>/<a href=http://vegaseq.com/charbrowser/index.php?page=raidpoints&char={NAME}&order=8>-</a>
	</h2>
	<!-- BEGIN raidtotal -->
	<font color=yellow>Total Points - {raidtotal.NPC_TOTALPTS}<br>
	<!-- END raidtotal -->
	<font color=lightblue>----- Epic Quests Completed -----<br>
	<!-- BEGIN epictotal -->
	<a href='{epictotal.ITEM}'>{epictotal.ITEM_NAME}</a> <font color=green>- {epictotal.ITEM_PTS} point(s)<br>
	<!-- END epictotal -->
	<font color=lightblue>----- Raid Targets Killed -----<br>
	<!-- BEGIN raid -->
	<a href='{raid.NPC}'>{raid.NPC_NAME}</a> <font color=green>- {raid.NPC_PTS} point(s) - <a href='{raid.NPC_ZONESN}'>{raid.NPC_ZONELN}</a><br>
	<!-- END raid -->
	<a class='CB_Button' href="{INDEX_URL}?page=character&char={NAME}">{L_DONE}</a>
</div>

