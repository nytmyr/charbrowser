<div class='WindowComplex PositionKeys CB_Can_Drag'>
   <div class='WindowTitleBar'>{L_RAID} - {NAME}</div>
   <h2>NPC Name - Point Value - Zone</h2>
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

