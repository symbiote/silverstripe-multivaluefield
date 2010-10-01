(function ($) {
	$().ready(function () {

		var addNewTextfield = function () {
			// check to see if the one after us is there already - if so, we don't need a new one
			var li = $(this).parent().next('li');
			
			if ($(this).val() && $(this).val().length == 0) {
				// lets also clean up if needbe
				var nextText = li.find('input.mventryfield');
				if (nextText && nextText.val() && nextText.val().length == 0) {
					li.detach();
				}
			} else {
				if (li.length) {
					return;
				}
				var parentUl = $(this).parents('ul.multivaluefieldlist');
				var newTextfield = $(this).clone();
				newTextfield.val('');
				$('<li>').appendTo(parentUl).append(newTextfield);
			}

			$(this).trigger('multiValueFieldAdded');
		}

		$('.mventryfield').livequery(function () {
			if (this.nodeName.toLowerCase() == 'input') {
				$(this).keyup(addNewTextfield);
			} else {
				$(this).change(addNewTextfield);
			}
			
		});

//		$('.mvtextfield').livequery(function () {
//			$(this).keyup(addNewTextfield);
//		});
	});
})(jQuery);