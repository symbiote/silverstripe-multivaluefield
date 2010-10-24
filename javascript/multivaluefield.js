(function ($) {
	$().ready(function () {

		var addNewTextfield = function () {
			// check to see if the one after us is there already - if so, we don't need a new one
			var li = $(this).parent().next('li');
			
			if ($(this).val() && $(this).val().length == 0) {
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
				var parentUl = $(this).parents('ul.multivaluefieldlist');
				var liClone = $(this).closest('li').clone();
//				var newTextfield = $(this).clone();
//				newTextfield.val('');
				liClone.find('input').val('');
				liClone.find('select').val('');
//				$('<li>').appendTo(parentUl).append(newTextfield);
				liClone.appendTo(parentUl);
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