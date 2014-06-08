<?php

function ace_header_event() {
	echo '<script src="app/plugins/ace/js/ace.js" type="text/javascript" charset="utf-8"></script>';
	echo <<<JS
<script type="text/javascript">
//diable tabby
$.fn.tabby = function () {};

$(function () {
	if ($("#row_data").length > 0) {
		var textarea = $("#row_data");
		var newId = "row_data_editor";
		var pre = $(document.createElement("pre"));
		pre.css("border", "1px #ccc solid");
		pre.css("width", textarea.width());
		pre.css("height", textarea.height());
		pre.css("margin", "0px");
		pre.insertBefore(textarea);
		pre.attr("id", newId);
		pre.text(textarea.val());
		textarea.hide();
		var editor = ace.edit(newId);
		editor.setTheme("ace/theme/xcode");
		if ($("select[name='format']").val() == "array") {
			editor.getSession().setMode("ace/mode/php");
		}
		else {
			editor.getSession().setMode("ace/mode/javascript");
		}
		editor.on("change", function () {
			textarea.val(editor.getValue());
		});

		$("select[name='format']").change(function () {
			if ($(this).val() == "array") {
				editor.getSession().setMode("ace/mode/php");
			}
			else {
				editor.getSession().setMode("ace/mode/javascript");
			}

			setTimeout(function () {
				editor.setValue(textarea.val());
			}, 2000);
		});
	}
	if ($("textarea[name='criteria']").length > 0) {
		var textarea = $("textarea[name='criteria']");
		var newId = "query_criteria_editor";
		var pre = $(document.createElement("pre"));
		pre.css("border", "1px #ccc solid");
		pre.css("width", textarea.width());
		pre.css("height", textarea.height());
		pre.css("margin", "0px");
		pre.insertBefore(textarea);
		pre.attr("id", newId);
		pre.text(textarea.val());
		textarea.hide();
		var editor = ace.edit(newId);
		editor.setTheme("ace/theme/xcode");
		if (currentFormat == "array") {
			editor.getSession().setMode("ace/mode/php");
		}
		else {
			editor.getSession().setMode("ace/mode/javascript");
		}
		editor.on("change", function () {
			textarea.val(editor.getValue());
		});
	}
});

</script>
JS;

echo <<< CSS
<style type="text/css">
.ace_error, .ace_warning, .ace_info { background-image:none!important; }
.ace_operator { color:green; }
.ace_identifier { color:blue; }
.ace_paren { color:green!important; }
</style>
CSS;

}

REvent::listen("RENDER_PAGE_HEADER_EVENT", "ace_header_event");

?>