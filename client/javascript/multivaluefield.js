jQuery(function($) {
	function addNewField() {
		var self = $(this);
		var val = self.val();
		var keySep = '__';

		// check to see if the one after us is there already - if so, we don't need a new one
		var li = $(this).closest('li').next('li');

		if (!val) {
			// lets also clean up if need be
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

			var append = self.closest("li").clone()
				.find(".has-chzn").show().removeClass("").data("chosen", null).end()
				.find(".chzn-container").remove().end();

			// Assign the new inputs a unique ID, so that chosen picks up
			// the correct container.
			append.find('input, select, textarea').val('').each(function () {
				var pos = this.id.lastIndexOf(keySep);
				if (pos !== -1) {
					pos = pos + keySep.length;

					var maxId  = parseInt(this.id.substr(pos));
					var nextId = maxId + 1;

					this.id = this.id.substr(0, pos) + nextId; // nextId auto-converted to string here
				}
			});

			append.appendTo(self.parents("ul.multivaluefieldlist"));
		}

		$(this).trigger('multiValueFieldAdded');
	}

	$(document).on("keyup", ".mventryfield", addNewField);
	$(document).on("change", ".mventryfield:not(input)", addNewField);

	if ($.fn.sortable) {
		if ($.entwine) {
			$('ul.multivaluefieldlist').entwine({
				onmatch: function () {
					$(this).sortable();
				}
			})
		} else {
			$('ul.multivaluefieldlist').sortable();
		}
	}

});
