jQuery(function ($) {
	// Duplicator:
	var duplicator = $('.ckeditor-duplicator').symphonyDuplicator({collapsible:true});

	function bindFunctionality() {
		$("select[name^=ckeditor_link_templates][name$=\'[section_id]\']").change(
			function () {
				var label = $(":selected", this).text();
				$("optgroup, option", $(this).parent().next()).attr("disabled", "disabled");
				$("optgroup[label=\'" + label + "\'], optgroup[label=\'" + label + "\'] option", $(this).parent().next()).removeAttr("disabled");
				var currentSelected = $("option:selected", $(this).parent().next());
				if (currentSelected.val() == "" || currentSelected.attr("disabled") == true) {
					$("option:first", $(this).parent().next()).attr("selected", "selected");
				}
			}).change();
	}

	$("ol.ckeditor-templates .constructor").click(function () {
		bindFunctionality();
	});
	bindFunctionality();
});