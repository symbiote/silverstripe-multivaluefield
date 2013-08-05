jQuery(function($) {
	function addNewField() {
		var self = $(this);
		var val = self.val();

		// check to see if the one after us is there already - if so, we don't need a new one
		var li = $(this).closest('li').next('li');

		if (!val) {
			// lets also clean up if needbe
			var nextText = li.find('input.mventryfield');
			var detach = true;

			nextText.each (function () {
				if ($(this) && $(this).val() && $(this).val().length > 0) {
					detach = false;
				}
			});

			if (detach) {
				li.detach();
			}

		} else {
			if (li.length) {
				return;
			}

			self.closest("li").clone()
				.find("input").val("").end()
				.find("select").val("").end()
				.appendTo(self.parents("ul.multivaluefieldlist"));
		}

		$(this).trigger('multiValueFieldAdded');
	}

	$(document).on("keyup", ".mventryfield", addNewField);
	$(document).on("change", ".mventryfield:not(input)", addNewField);
});
