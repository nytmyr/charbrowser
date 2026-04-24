<style>
/* ── Scoped only to .raid-table — no global table/th/td overrides ── */

.raid-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11px;
    table-layout: auto;
}
.raid-table th {
    background: #2a1e0e;
    color: #d4a84b;
    padding: 5px 8px;
    text-align: center;
    cursor: pointer;
    white-space: nowrap;
    border: 1px solid #3a2a1a;
    user-select: none;
    resize: horizontal;
    overflow: auto;
}
.raid-table th:hover { background: #3a2a12; color: #f0c060; }
.raid-table th.sorted-asc::after  { content: " \25B2"; font-size: 9px; }
.raid-table th.sorted-desc::after { content: " \25BC"; font-size: 9px; }
.raid-table th:not(.sorted-asc):not(.sorted-desc)::after { content: " \21C5"; font-size: 9px; opacity: 0.5; }

.raid-table td {
    padding: 4px 8px;
    border: 1px solid #2a2010;
    vertical-align: middle;
    color: #dcdcdc;
}
.raid-table tr:hover td { background: #1e1608; }
.raid-table tr:nth-child(even) td { background: #16120a; }
.raid-table tr:nth-child(even):hover td { background: #1e1608; }
.raid-table td a { color: lightblue; text-decoration: none; }
.raid-table td a:hover { color: #f0c060; text-decoration: underline; }

.pts-cell  { text-align: center; font-weight: bold; color: green; }
.diff-cell { text-align: center; color: #e0c060; font-family: monospace; }
.zone-cell { text-align: center; }
.name-cell { text-align: left; }

/* ── Tab Navigation ── */
.raid-tabs {
    display: flex;
    justify-content: center;
    gap: 4px;
    margin: 6px 0 0 0;
    padding: 0;
    list-style: none;
    border-bottom: 1px solid #5a4a2a;
}
.raid-tabs li a {
    display: block;
    padding: 4px 12px;
    background: #1a1410;
    color: #a08060;
    text-decoration: none;
    border: 1px solid #5a4a2a;
    border-bottom: none;
    font-size: 11px;
    font-weight: bold;
}
.raid-tabs li a:hover { background: #0cd16e6b; color: #d4a84b; }
.raid-tabs li.active a {
    background: #2e2212;
    color: yellow;
    border-color: #8a6a2a;
    border-bottom: 1px solid #2e2212;
    margin-bottom: -1px;
    position: relative;
}
.tab-badge {
    display: inline-block;
    background: #5a3a10;
    color: #e0c060;
    border-radius: 8px;
    padding: 0 5px;
    font-size: 10px;
    margin-left: 3px;
    font-weight: normal;
}

/* ── Tab Panels ── */
.tab-panel { display: none; padding: 6px 0 0 0; }
.tab-panel.active { display: block; }

/* ── Empty State ── */
.raid-empty {
    padding: 14px;
    text-align: center;
    color: #666;
    font-style: italic;
    font-size: 11px;
}
</style>

<div class="WindowComplex PositionRaid CB_Can_Drag">
    <div class="WindowTitleBar">{L_RAID} - {NAME}</div>
    <center>

    <!-- BEGIN raidtotal -->
    <font color="yellow">Total Points - {raidtotal.NPC_TOTALPTS}</font><br>
    <!-- END raidtotal -->

    <!-- BEGIN tab_counts -->
    <ul class="raid-tabs" id="raidTabNav">
        <li class="active" data-tab="tab-killed">
            <a href="#tab-killed">&#x2611; Killed <span class="tab-badge">{tab_counts.RAID_KILLED}</span></a>
        </li>
        <li data-tab="tab-unkilled">
            <a href="#tab-unkilled">&#x2717; Unkilled <span class="tab-badge">{tab_counts.RAID_UNKILLED}</span></a>
        </li>
        <li data-tab="tab-epics-complete">
            <a href="#tab-epics-complete">&#x2605; Epics Complete <span class="tab-badge">{tab_counts.EPIC_COMPLETE}</span></a>
        </li>
        <li data-tab="tab-epics-incomplete">
            <a href="#tab-epics-incomplete">&#x2606; Epics Incomplete <span class="tab-badge">{tab_counts.EPIC_INCOMPLETE}</span></a>
        </li>
    </ul>
    <!-- END tab_counts -->

    <!-- TAB 1 - KILLED -->
    <div class="tab-panel active" id="tab-killed">
        <table class="raid-table sortable-table" id="tbl-killed">
            <colgroup>
                <col style="width:35%">
                <col style="width:8%">
                <col style="width:40%">
                <col style="width:17%">
            </colgroup>
            <thead>
                <tr>
                    <th data-col="0" data-type="str">Name</th>
                    <th data-col="1" data-type="num">Points</th>
                    <th data-col="2" data-type="str">Zone</th>
                    <th data-col="3" data-type="num">Difficulty</th>
                </tr>
            </thead>
            <tbody>
<!-- BEGIN killed -->
                <tr>
                    <td class="name-cell" data-val="{killed.NPC_NAME}"><a href="{killed.NPC}" target="_blank">{killed.NPC_NAME}</a></td>
                    <td class="pts-cell"  data-val="{killed.NPC_RAWPTS}">{killed.NPC_PTS}</td>
                    <td class="zone-cell" data-val="{killed.NPC_ZONELN}"><a href="{killed.NPC_ZONESN}" target="_blank">{killed.NPC_ZONELN}</a></td>
                    <td class="diff-cell" data-val="{killed.NPC_RAWDIFF}">{killed.NPC_DIFF}</td>
                </tr>
<!-- END killed -->
            </tbody>
        </table>
        <div class="raid-empty" id="empty-killed" style="display:none">No killed raid targets found.</div>
    </div>

    <!-- TAB 2 - UNKILLED -->
    <div class="tab-panel" id="tab-unkilled">
        <table class="raid-table sortable-table" id="tbl-unkilled">
            <colgroup>
                <col style="width:35%">
                <col style="width:8%">
                <col style="width:40%">
                <col style="width:17%">
            </colgroup>
            <thead>
                <tr>
                    <th data-col="0" data-type="str">Name</th>
                    <th data-col="1" data-type="num">Points</th>
                    <th data-col="2" data-type="str">Zone</th>
                    <th data-col="3" data-type="num">Difficulty</th>
                </tr>
            </thead>
            <tbody>
<!-- BEGIN unkilled -->
                <tr>
                    <td class="name-cell" data-val="{unkilled.NPC_NAME}"><a href="{unkilled.NPC}" target="_blank">{unkilled.NPC_NAME}</a></td>
                    <td class="pts-cell"  data-val="{unkilled.NPC_RAWPTS}">{unkilled.NPC_PTS}</td>
                    <td class="zone-cell" data-val="{unkilled.NPC_ZONELN}"><a href="{unkilled.NPC_ZONESN}" target="_blank">{unkilled.NPC_ZONELN}</a></td>
                    <td class="diff-cell" data-val="{unkilled.NPC_RAWDIFF}">{unkilled.NPC_DIFF}</td>
                </tr>
<!-- END unkilled -->
            </tbody>
        </table>
        <div class="raid-empty" id="empty-unkilled" style="display:none">No unkilled raid targets — you've slain them all!</div>
    </div>

    <!-- TAB 3 - EPICS COMPLETE -->
    <div class="tab-panel" id="tab-epics-complete">
        <table class="raid-table sortable-table" id="tbl-epics-complete">
            <thead>
                <tr>
                    <th data-col="0" data-type="str">Item Name</th>
                    <th data-col="1" data-type="num">Points Earned</th>
                </tr>
            </thead>
            <tbody>
<!-- BEGIN epics_complete -->
                <tr>
                    <td class="name-cell" data-val="{epics_complete.ITEM_NAME}"><a href="{epics_complete.ITEM}" target="_blank">{epics_complete.ITEM_NAME}</a></td>
                    <td class="pts-cell"  data-val="{epics_complete.ITEM_PTS}">{epics_complete.ITEM_PTS}</td>
                </tr>
<!-- END epics_complete -->
            </tbody>
        </table>
        <div class="raid-empty" id="empty-epics-complete" style="display:none">No epic turn-ins completed yet.</div>
    </div>

    <!-- TAB 4 - EPICS INCOMPLETE -->
    <div class="tab-panel" id="tab-epics-incomplete">
        <table class="raid-table sortable-table" id="tbl-epics-incomplete">
            <thead>
                <tr>
                    <th data-col="0" data-type="str">Item Name</th>
                </tr>
            </thead>
            <tbody>
<!-- BEGIN epics_incomplete -->
                <tr>
                    <td class="name-cell" data-val="{epics_incomplete.ITEM_NAME}"><a href="{epics_incomplete.ITEM}" target="_blank">{epics_incomplete.ITEM_NAME}</a></td>
                </tr>
<!-- END epics_incomplete -->
            </tbody>
        </table>
        <div class="raid-empty" id="empty-epics-incomplete" style="display:none">All epics are complete!</div>
    </div>

    <br>
    <a class="CB_Button" href="{U_PROFILE}">{L_DONE}</a>

    </center>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Tab Switching Logic
    const tabs = document.querySelectorAll('#raidTabNav a');
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            document.querySelectorAll('.tab-panel').forEach(c => c.classList.remove('active'));
            document.querySelectorAll('#raidTabNav li').forEach(li => li.classList.remove('active'));
            document.getElementById(targetId).classList.add('active');
            this.parentElement.classList.add('active');
        });
    });

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
                    const valA = (a.cells[colIndex].getAttribute('data-val') || a.cells[colIndex].textContent).trim();
                    const valB = (b.cells[colIndex].getAttribute('data-val') || b.cells[colIndex].textContent).trim();
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
