<center>
	<table>
		<tbody>
			<tr>
				<td><font color=white>When to stop melee combat: 
					<select name=" . $stop_melee_level . ">
						<option value=" . $stop_melee_level . ">$stop_melee_level (Current)</option>
						<option value=1>1</option><option value=2>2</option><option value=3>3</option><option value=4>4</option><option value=5>5</option><option value=6>6</option>
						<option value=7>7</option>
					</select>
				</td>
				<br>
				<td><font color=white>Hold Buffs: 
					<select name=" . $hold_buffs . ">
						<option value=" . $hold_buffs . "> " . ($hold_buffs ? 'Enabled' : 'Disabled') . " (Current)</option>
						<option value=1>Enabled</option><option value=2>Disabled</option>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
</center>
<tr>
	<td colspan = '2'>
		<div class='CB_Button' onclick="none();">Save</div>
		<div class='CB_Button' onclick="none();">Reset</div>
	</td>
</tr>