<meta name="viewport" content="width=device-width, initial-scale=1.0">
<div class='CB_BotSettings_Wrapper'>
    <div class='WindowComplex PositionBotSettingOptions CB_Can_Drag'>
        <div class='WindowTitleBar'>{L_BOT_OPTIONS}</div>
        <div class='CB_VerticalTabs_Container'>
            <nav class='CB_Tab_Box CB_Tab_Box_Vertical'>
                <ul>
                    <!-- BEGIN section -->
                    <li id='tab{section.INDEX}' class='CB_VerticalTab_Trigger'
                        data-tooltip="{section.DESCRIPTION}"
                        onclick="CB_displayTab('#charbrowser .CB_Tab_Box_Vertical UL LI', '#tab{section.INDEX}', '#charbrowser DIV.PositionBotSettings TABLE.CB_Table', '#tabbox{section.INDEX}');">
                        {section.TAB}
                    </li>
                    <!-- END section -->
                </ul>
            </nav>
        </div>
    </div>

    <div class='WindowComplex PositionBotSettings CB_Can_Drag'>
        <div class='WindowTitleBar'>{L_BOT_SETTINGS} - {NAME}</div>
        <div style="padding: 5px; text-align: center; background: rgba(0,0,0,0.5); margin: 0 5px; border-radius: 3px; font-size: 10pt; color: #8aa3ff;">
            {STANCE_SELECT}
        </div>
        <!-- BEGIN section -->
        <table id='tabbox{section.INDEX}' class='CB_Table CB_Highlight_Rows'>
            <thead>
            <tr>
                <th style='color: white' colspan='1'>{section.TEXT}</th> <!-- Updated colspan to 3 for the COMMAND column -->
                <th style='color: white' colspan='1'>{section.TEXTA}</th> <!-- Updated colspan to 3 for the COMMAND column -->
                <th style='color: white' colspan='1'>{section.TEXTB}</th> <!-- Updated colspan to 3 for the COMMAND column -->
            </tr>
            </thead>
            <tbody>
            <!-- BEGIN settingrow -->
            <tr>
                <td>{section.settingrow.NAME}</td>
                <td>{section.settingrow.VALUE}</td>
                <td>{section.settingrow.COMMAND}</td>
            </tr>
            <!-- END settingrow -->
            </tbody>
        </table>
        <!-- END section -->
        <a class='CB_Button' href="{INDEX_URL}?page=character&char={NAME}">{L_DONE}</a>
    </div>
</div>

<script type="text/javascript">
    //display the first tab after load
    $( document ).ready(function() {
        // Initialize main horizontal tabs (unchanged)
        CB_displayTab('#charbrowser .CB_BotSettings_Wrapper .CB_Tab_Box UL LI', '#tab0', '#charbrowser DIV.PositionBotSettings TABLE.CB_Table', '#tabbox0');
        // Trigger first vertical tab to sync with main content
        CB_displayTab('#charbrowser .CB_Tab_Box_Vertical UL LI', '#tab0', '#charbrowser DIV.PositionBotSettings TABLE.CB_Table', '#tabbox0');
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        // Existing tab init code...

        // Tooltip JS - Force mobile for testing
        $('.CB_Tab_Box_Vertical UL LI').hover(
            function() {  // On hover in
                var tooltipText = $(this).attr('data-tooltip');
                if (tooltipText) {
                    var $tooltip = $('<div class="custom-tooltip">' + tooltipText + '</div>');
                    var screenWidth = window.innerWidth;
                    var isMobile = window.matchMedia('(max-width: 900px)').matches;

                    // Debug logs (always on)
                    console.log('Tooltip trigger: Screen width=' + screenWidth + ', Forced Mobile mode=' + isMobile);

                    var styles = {
                        position: 'fixed',
                        backgroundColor: 'rgba(11, 11, 16, 0.95)',
                        color: '#FFFFFF',
                        fontFamily: 'arial',
                        fontSize: '8pt',
                        padding: '8px 12px',
                        border: '1px solid #7b714a',
                        borderRadius: '5px',
                        whiteSpace: 'pre-line',
                        zIndex: 9999,
                        boxShadow: '2px 2px 5px rgba(0, 0, 0, 0.8)',
                        pointerEvents: 'none',
                        lineHeight: '1.4'
                    };

                    var tabOffset = $(this).offset();
                    console.log('Tab offset: top=' + tabOffset.top + ', left=' + tabOffset.left);

                    if (isMobile) {
                        styles.left = '5vw';  // Should push ~20px from left on 412px screen
                        styles.top = (tabOffset.top + $(this).outerHeight() + 8) + 'px';
                        styles.width = 'min(280px, calc(90vw - 20px))';  // ~350px max, but caps at ~331px here
                        styles.maxWidth = 'none';
                        styles.transform = 'none';
                        styles.minWidth = '200px';
                    } else {
                        // Desktop fallback (won't hit now)
                        styles.left = 'calc(50% - 260px - 100px - 10px - 250px - 10px)';
                        styles.top = 'calc(50% - 155px)';
                        styles.width = '250px';
                        styles.maxWidth = '250px';
                        styles.transform = 'none';
                    }

                    $tooltip.css(styles).appendTo('body');

                    console.log('Tooltip styles applied: left=' + styles.left + ', top=' + styles.top + ', width=' + styles.width);

                    // Extra: Inspect the element post-apply
                    console.log('Inspect tooltip:', $tooltip[0]);  // Click this in console to select it
                }
            },
            function() {  // On hover out
                $('.custom-tooltip').remove();
            }
        );

        $(window).on('resize', function() {
            $('.custom-tooltip').remove();
            console.log('Resize detected: width=' + window.innerWidth);
        });
    });
</script>