<?php

/**
 * This is a sample plugin for developer
 */

//MORE filters and events go: http://rockmongo.org/wiki/pluginDevelop

//Add Menu to Database Operation Menu
function sampleplugin_db_menu_filter(&$items, $dbName) {
	$items[] = array( "name" => "SAMPLE MENU1", "url" => "http://google.com" );
	$items[] = array( "name" => "SAMPLE MENU2", "action" => "@sampleplugin.menu2.index" ); // Link to controllers/menu2.php "index" action
	$items[] = array( "name" => "SAMPLE MENU3", "action" => "@sampleplugin.menu2.about", "params" => array( "dbname" => $dbName ) ); // Link to controllers/menu2.php "about" action, and pass the params to the action
}
RFilter::add("DB_MENU_FILTER", "sampleplugin_db_menu_filter");

//Add Menu to Collection Operation Menu
function sampleplugin_collection_menu_filter(&$items, $dbName, $collectionName) {
	$items[] = array( "name" => "SAMPLE MENU1", "url" => "http://google.com" );
	$items[] = array( "name" => "SAMPLE MENU2", "action" => "@sampleplugin.menu2.index", "params" => array( "dbname" => $dbName, "collectionname" => $collectionName ));
	$items[] = array( "name" => "SAMPLE MENU2", "action" => "@sampleplugin.menu2.nut");
}
RFilter::add("COLLECTION_MENU_FILTER", "sampleplugin_collection_menu_filter");

//Add Menu to Document Operation Menu
function sampleplugin_doc_menu_filter(&$items, $dbName, $collectionName, $docId, $docIndex) {
	$items[] = array( "name" => "SAMPLE MENU1", "url" => "http://google.com" );
	$items[] = array( "name" => "SAMPLE MENU2", "action" => "@sampleplugin.menu2.index", "params" => array( "dbname" => $dbName, "collectionname" => $collectionName ));
}
RFilter::add("DOC_MENU_FILTER", "sampleplugin_doc_menu_filter");


//Add My Own CSS
function sampleplugin_css_event() {
	echo '
<style type="text/css">
.dbs li a { color:green; font-size:16px; }
</style>
<link rel="stylesheet" href="MY-CSS-FILE" type="text/css"/>
';
}
REvent::listen("RENDER_PAGE_HEADER_EVENT", "sampleplugin_css_event");

//Add My Own Javascript
function sampleplugin_js_event() {
	echo '
<script type="text/javascript">
$(function () {
	$(".dbs li a").click(function () {
		alert("You clicked the sampleplugin");
	});
});
</script>
<script type="text/javascript" src="MY-JAVASCRIPT-FILE"></script>
';
}
//REvent::listen("RENDER_PAGE_HEADER_EVENT", "sampleplugin_js_event");



?>