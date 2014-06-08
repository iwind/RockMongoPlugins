<?php

/**
 * Only works in "collection.index" action
 */
$action = x("action");
$collection = x("collection");
if ($action != "collection.index" || !preg_match("/\\.files$/", $collection)) {
	return;
}

/**
 * Initialize
 */
$GLOBALS["GRIDFS_EXTENSIONS"] = array();

function gridfs_load_icons() {
	$dir = dirname(__FILE__) . "/icons/32px";
	$extensions = array();
	$handler = opendir($dir);
	while (false !== ($file = readdir($handler))) {
		if (preg_match("/^(\\w+)\\.png$/", $file, $match)) {
			$extensions[] = $match[1];
		}
	}
	closedir($handler);

	$GLOBALS["GRIDFS_EXTENSIONS"] = $extensions;
}

/**
 * Add Javascript and CSS to header
 */
function gridfs_header_event() {
	$extensions = implode("|", $GLOBALS["GRIDFS_EXTENSIONS"]);
	echo <<< JS
<script type="text/javascript">
$(function () {
	if (typeof(currentCollection) != "undefined" && currentCollection.match(/\.files/)) {
		var gridsFSRegexp = /\.({$extensions})$/;
		$("#records .record").each(function () {
			var record = $(this);
			var filename = record.attr("r-file-name");
			if (filename == null || !gridsFSRegexp.test(filename)) {
				record.find(".operation").append(" <a class=\"preview-btn\" title=\"" + filename + "\" href=\"#\">Preview</a>");
			}
			else {
				var extension = filename.match(gridsFSRegexp)[1];
				record.find(".operation").append(" <a class=\"preview-btn\" title=\"" + filename + "\" href=\"#\"><img src=\"app/plugins/gridfs/icons/32px/" + extension + ".png\"/></a>");
			}
			record.find(".operation .preview-btn").click(function () {
				var recordId = $(this).closest(".record").find(".record_row").attr("record_id");
				var div = $("#preview-dialog");
				$.ajax({
					"url":"index.php?action=@gridfs.doc.preview",
					"type":"post",
					"dataType":"json",
					"data":{ "db":currentDb, "collection":currentCollection, "record_id":recordId },
					"success":function (response) {
						//show image
						$("#preview-dialog .dialog-message").hide();
						if (response.code == 200) {
							var previewBox = $("#preview-dialog .image-preview").show();
							previewBox.find(".image-container").html("<img src=\"" + response.data.image + "\"/>");
							previewBox.find(".image-width").text(response.data.width);
							previewBox.find(".image-height").text(response.data.height);
							previewBox.find(".image-size").text(response.data.size);
						}
						else if (response.code == 201) {
							$("#preview-dialog .can-not-preview").show();
						}
						else if (response.code == 501) {
							$("#preview-dialog .invalid-image-format").show();
						}

						div.dialog({
							"modal":true,
							"title":"File Preview",
							"buttons":{
								"Download":function () {
									window.location = "index.php?action=collection.downloadFile&db=" + currentDb + "&collection=" + currentCollection + "&id=" + recordId;
								},
								"Cancel":function () {
									div.dialog("close");
								}
							},
							"width":420,
							"open":function () {
								$("button").blur();
								$(".ui-widget-overlay").unbind("click").click(function () {
									if ($(this).closest(".ui-dialog").length == 0) {
										div.dialog("close");
									}
								});
							}
						});
					}
				});

				return false;
			});
		});
	}
});
</script>
JS;

echo <<<CSS
	<style type="text/css">
	#records .record .operation { position:relative; }
	#records .record .operation .preview-btn img { position:absolute; top:30px; right:0px; }
	#preview-dialog .dialog-message { display:none; }
	#preview-dialog .image-preview { text-align:center; }
	#preview-dialog .image-preview img { max-width:98%; padding:1px; border:1px #ccc solid; }
	#preview-dialog .image-preview .image-dim { margin-top:10px; }
	</style>
CSS;
}

/**
 * Add dialog html to footer
 */
function gridfs_footer_event() {
	echo <<<HTML
<div id="preview-dialog" style="display:none">
	<div class="can-not-preview dialog-message">You can only preview image files.</div>
	<div class="invalid-image-format dialog-message">Invalid image format.</div>
	<div class="image-preview dialog-message">
		<div class="image-container"></div>
		<div class="image-dim"><span class="image-width">0</span>x<span class="image-height">0</span> <span class="image-size">0</span></div>
	</div>
</div>
HTML;
}

/**
 * Replace "Insert" button with "Upload"
 *
 * @param array $items Menu items
 * @param string $dbName Database name
 * @param string $collectionName Collection name
 */
function gridfs_collection_menu_filter(&$items, $dbName, $collectionName) {
	//Replace "Insert" with "Upload"

	$uploadIndex = 0;
	foreach ($items as $index => $item) {
		if ($item["action"] == "collection.createRow") {
			$item["name"] = gridfs_lang("upload");
			$item["action"] = "@gridfs.doc.upload";

			$uploadIndex = $index;
		}
		$items[$index] = $item;
	}
	/**array_splice($items, $uploadIndex + 1, 0, array(
		array (
			"name" => "Touch",
			"action" => "@gridfs.doc.touch"
		)
	));**/
}

function gridfs_lang($code) {
	if (!isset($GLOBALS["GRIDS_LANG"])) {
		$file = dirname(__FILE__) . "/langs/" . __LANG__ . "/message.php";
		if (is_file($file)) {
			$message = array();
			require $file;
			$GLOBALS["GRIDS_LANG"] = $message;
		}
	}

	$ret = null;
	if (isset($GLOBALS["GRIDS_LANG"][$code])) {
		$ret = $GLOBALS["GRIDS_LANG"][$code];
	}

	$args = func_get_args();
	unset($args[0]);
	if (empty($args)) {
		return $ret;
	}

	if (is_null($ret)) {
		$ret = rock_lang($ret);
	}

	return vprintf($ret, $args);
}

/**
 * Execute filters and events
 */
gridfs_load_icons();
REvent::listen("RENDER_PAGE_HEADER_EVENT", "gridfs_header_event");
REvent::listen("RENDER_PAGE_FOOTER_EVENT", "gridfs_footer_event");
RFilter::add("COLLECTION_MENU_FILTER", "gridfs_collection_menu_filter");

?>