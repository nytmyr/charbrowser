<div class='WindowSimpleFancy PositionBazaar CB_Can_Drag CB_Should_ZOrder'>
   <div class='WindowTitleBar'>{L_BAZAAR}{STORENAME}</div>
   <div class='PositionBazaarTop'>
      <div class='PositionBazaarLeft'>
         <form method='GET' name='bazaar' action='{INDEX_URL}'>
            <input type='hidden' name='page' value='bazaar'>
            
            <label for='item'>{L_SEARCH_NAME}</label>
            <input name='item' id='item' type='text' value='{ITEM}' autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
            
            <label for='seller'>{L_NAME}</label>
            <input name='char' id='seller' type='text' value='{SELLER}' autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">

            <label for='class'>{L_SEARCH_CLASS}</label>
            <select name='class' id='class'>
               <!-- BEGIN select_class -->
               <option value='{select_class.VALUE}' {select_class.SELECTED}>{select_class.OPTION}</option>
               <!-- END select_class -->
            </select>

            <label for='race'>{L_SEARCH_RACE}</label>
            <select name='race' id='race'>
               <!-- BEGIN select_race -->
               <option value='{select_race.VALUE}' {select_race.SELECTED}>{select_race.OPTION}</option>
               <!-- END select_race -->
            </select>

            <label for='slot'>{L_SEARCH_SLOT}</label>
            <select name='slot' id='slot'>
               <!-- BEGIN select_slot -->
               <option value='{select_slot.VALUE}' {select_slot.SELECTED}>{select_slot.OPTION}</option>
               <!-- END select_slot -->
            </select>

            <label for='stat'>{L_SEARCH_STAT}</label>
            <select name='stat' id='stat'>
               <!-- BEGIN select_stat -->
               <option value='{select_stat.VALUE}' {select_stat.SELECTED}>{select_stat.OPTION}</option>
               <!-- END select_stat -->
            </select>

            <label for='type'>{L_SEARCH_TYPE}</label>
            <select name='type' id='type'>
               <!-- BEGIN select_type -->
               <option value='{select_type.VALUE}' {select_type.SELECTED}>{select_type.OPTION}</option>
               <!-- END select_type -->
            </select>

            <label for='item'>{L_SEARCH_PRICE_MIN}</label>
            <input name='pricemin' id='pricemin' type='text' value='{PRICE_MIN}'>

            <label for='item'>{L_SEARCH_PRICE_MAX}</label>
            <input name='pricemax' id='pricemax' type='text' value='{PRICE_MAX}'>
            <input class='CB_Button' type='submit' value='{L_SEARCH}'>
         </form>
      </div>
      <div class='PositionBazaarRight'>
         <div class='WindowNestedBlue StaticTableHeadParent'>
            <table class='CB_Table CB_Highlight_Rows'>
               <thead> 
                  <tr>                  
                     <th><a href="{ORDER_LINK}&orderby=Name">{L_ITEM}</a></th>
                     <th><a href="{ORDER_LINK}&orderby=tradercost">{L_PRICE}</a></th>
                     <th><a href="{ORDER_LINK}&orderby=charactername">{L_NAME}</a></th>
                     <!-- BEGIN switch_stat -->
                     <th><a href="{ORDER_LINK}&orderby={switch_stat.STAT}">{switch_stat.L_STAT}</a></th>
                     <!-- END switch_stat -->
                  </tr>
               </thead>
               </tbody>
                  <!-- BEGIN items -->
                  <tr>
                     <td>
                        <a hoverChild='#item{items.SLOT}' class='CB_HoverParent' href='#'>
                           <div class='TableSlot ItemSmall_{items.ICON}'></div>
                           {items.NAME}
                        </a>
                     </td>
                     <td>{items.PRICE}</td>
                     <td><a href='{INDEX_URL}?page=character&char={items.SELLER}'>{items.SELLER}</a></td>
                     <!-- BEGIN stat_col -->
                     <td>{items.stat_col.STAT}</td>
                     <!-- END stat_col -->
                  </tr>
                  <!-- END items -->
               </tbody>
            </table>
         </div>
      </div>
   </div>
   <div class='PositionBazaarBottom'>
      <div class='CB_Pagination'>{PAGINATION}</div>
   </div>
</div>

<!-- ITEM WINDOWS -->
<!-- BEGIN items --> 
<div class='WindowComplex PositionItem CB_Can_Drag CB_HoverChild CB_Should_ZOrder' id='item{items.SLOT}'> 
   <div class='WindowTitleBar'>
      <a href='{items.LINK}'>{items.NAME}</a>
      <div class='WindowTile' onclick='cbPopup_tileItems();' title='click to tile all open popups'></div>
      <div class='WindowCloseAll' onclick='cbPopup_closeAllItems();' title='click to close all open popups'></div>
      <div class='WindowClose' onclick='cbPopup_closeItem("#item{items.SLOT}");' title='click to close this popup'></div>
   </div> 
   <div class='Stats'> 
      <div class='Slot slotlocinspect slotimage'></div> 
      <div class='Slot Item_{items.ICON} slotlocinspect'><span>{items.STACK}</span></div>        
      {items.HTML} 
   </div> 
</div> 
<!-- END items --> 

