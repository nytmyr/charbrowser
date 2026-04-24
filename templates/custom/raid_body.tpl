<div class="WindowComplex PositionRaidPoints CB_Can_Drag">
	<div class="WindowTitleBar">{L_RAID} - {NAME}</div>
	<nav class='CB_Tab_Box'>
		<ul>
			<!-- BEGIN tabs -->
			<li id='tab{tabs.ID}' onclick="CB_displayTab('#charbrowser NAV.CB_Tab_Box UL LI', '#tab{tabs.ID}', '#charbrowser DIV.PositionRaidPointsLeft TABLE', '#tabbox{tabs.ID}');">{tabs.TEXT}</li> 
			<!-- END tabs -->
		</ul>
	</nav>
	<div class='WindowNestedBlue PositionRaidPointsLeft'>

		<!-- TAB 1: KILLED -->
		<table id='tabbox1' class='CB_Table CB_Highlight_Rows sortable-table'>
			<thead>
				<tr>
					<th data-type="str" style="cursor:pointer;">{L_NPC_NAME}</th>
					<th data-type="num" style="cursor:pointer;">{L_POINTS}</th>
					<th data-type="str" style="cursor:pointer;">{L_ZONE}</th>
					<th data-type="num" style="cursor:pointer;">{L_DIFFICULTY}</th>
				</tr>
			</thead>
			<tbody>
<!-- BEGIN killed -->
				<tr>
					<td data-val="{killed.NPC_NAME}"><a href="{killed.NPC}" target="_blank">{killed.NPC_NAME}</a></td>
					<td data-val="{killed.NPC_RAWPTS}">{killed.NPC_PTS}</td>
					<td data-val="{killed.NPC_ZONELN}"><a href="{killed.NPC_ZONESN}" target="_blank">{killed.NPC_ZONELN}</a></td>
					<td data-val="{killed.NPC_RAWDIFF}">{killed.NPC_DIFF}</td>
				</tr>
<!-- END killed -->
			</tbody>
		</table>

		<!-- TAB 2: UNKILLED -->
		<table id='tabbox2' class='CB_Table CB_Highlight_Rows sortable-table'>
			<thead>
				<tr>
					<th data-type="str" style="cursor:pointer;">{L_NPC_NAME}</th>
					<th data-type="num" style="cursor:pointer;">{L_POINTS}</th>
					<th data-type="str" style="cursor:pointer;">{L_ZONE}</th>
					<th data-type="num" style="cursor:pointer;">{L_DIFFICULTY}</th>
				</tr>
			</thead>
			<tbody>
<!-- BEGIN unkilled -->
				<tr>
					<td data-val="{unkilled.NPC_NAME}"><a href="{unkilled.NPC}" target="_blank">{unkilled.NPC_NAME}</a></td>
					<td data-val="{unkilled.NPC_RAWPTS}">{unkilled.NPC_PTS}</td>
					<td data-val="{unkilled.NPC_ZONELN}"><a href="{unkilled.NPC_ZONESN}" target="_blank">{unkilled.NPC_ZONELN}</a></td>
					<td data-val="{unkilled.NPC_RAWDIFF}">{unkilled.NPC_DIFF}</td>
				</tr>
<!-- END unkilled -->
			</tbody>
		</table>

		<!-- TAB 3: EPICS COMPLETE -->
		<table id='tabbox3' class='CB_Table CB_Highlight_Rows sortable-table'>
			<thead>
				<tr>
					<th data-type="str" style="cursor:pointer;">{L_ITEM_NAME}</th>
					<th data-type="num" style="cursor:pointer;">{L_POINTS_EARNED}</th>
				</tr>
			</thead>
			<tbody>
<!-- BEGIN epicscomplete -->
				<tr>
					<td data-val="{epicscomplete.ITEM_NAME}"><a href="{epicscomplete.ITEM_LINK}" target="_blank">{epicscomplete.ITEM_NAME}</a></td>
					<td data-val="{epicscomplete.ITEM_PTS}">{epicscomplete.ITEM_PTS}</td>
				</tr>
<!-- END epicscomplete -->
			</tbody>
		</table>

		<!-- TAB 4: EPICS INCOMPLETE -->
		<table id='tabbox4' class='CB_Table CB_Highlight_Rows sortable-table'>
			<thead>
				<tr>
					<th data-type="str" style="cursor:pointer;">{L_ITEM_NAME}</th>
                    <th data-type="num" style="cursor:pointer;">{L_POINTS_WORTH}</th>                    
				</tr>
			</thead>
			<tbody>
<!-- BEGIN epicsincomplete -->
				<tr>
					<td data-val="{epicsincomplete.ITEM_NAME}"><a href="{epicsincomplete.ITEM_LINK}" target="_blank">{epicsincomplete.ITEM_NAME}</a></td>
                    <td data-val="{epicsincomplete.ITEM_PTS}">{epicsincomplete.ITEM_PTS}</td>
				</tr>
<!-- END epicsincomplete -->
			</tbody>
		</table>

	</div>
	<div class='PositionRaidPointsRight'>
		<table class='CB_Table'>
			<tbody>
				<tr><td>{L_TOTAL_POINTS}:</td><td>{TOTAL_POINTS}</td></tr>
			</tbody>
		</table>
	</div>
	<a class='CB_Button' href="{INDEX_URL}?page=character&char={NAME}">{L_DONE}</a>
</div>

<style>
    .sortable-table th.sorted-asc::after { content: " ▲"; font-size: 0.8em; }
    .sortable-table th.sorted-desc::after { content: " ▼"; font-size: 0.8em; }
    .sortable-table th { transition: background 0.2s; }
    .sortable-table th:hover { background: rgba(255,255,255,0.1); }
    .sortable-table th:not(.sorted-asc):not(.sorted-desc)::after { content: "▲▼"; font-size: 0.8em; }
</style>

<script>
	//display the first tab after load
	$( document ).ready(function() {
		CB_displayTab('#charbrowser NAV.CB_Tab_Box UL LI', '#tab1', '#charbrowser DIV.PositionRaidPointsLeft TABLE', '#tabbox1');
	});

	document.addEventListener('DOMContentLoaded', function() {
	    // 2. Sorting Logic
        document.querySelectorAll('.sortable-table').forEach(table => {
            const headers = table.querySelectorAll('th');
            headers.forEach((th, colIndex) => {
                th.addEventListener('click', () => {
                    const type = th.getAttribute('data-type') || 'str';
                    const isAscending = !th.classList.contains('sorted-asc');

                    headers.forEach(h => h.classList.remove('sorted-asc', 'sorted-desc'));
                    th.classList.add(isAscending ? 'sorted-asc' : 'sorted-desc');

                    const tbody = table.tBodies[0];
                    const rows = Array.from(tbody.rows);

                    rows.sort((a, b) => {
                        const cellA = a.cells[colIndex];
                        const cellB = b.cells[colIndex];
                        if (!cellA || !cellB) return 0;

                        const valA = (cellA.getAttribute('data-val') || cellA.textContent).trim();
                        const valB = (cellB.getAttribute('data-val') || cellB.textContent).trim();

                        if (type === 'num') {
                            return isAscending ? (parseFloat(valA) - parseFloat(valB)) : (parseFloat(valB) - parseFloat(valA));
                        }
                        return isAscending ? valA.localeCompare(valB) : valB.localeCompare(valA);
                    });

                    rows.forEach(row => tbody.appendChild(row));
                });
            });
        });
    });
</script>
