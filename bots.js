function bot_settings() {
	u = ".page-content-ajax";
	query_string = get_form_query_string("bot_settings_form");
	console.log(query_string);
	entire_query = "?a=items_search&v_ajax&" + query_string;
	push_query = "?a=items_search&" + query_string;
	$.get(entire_query, function (data) {
		history.pushState('page_pop', push_query, push_query);
		$(u).html(data);
	});
}