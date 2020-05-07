$(document).ready(function() {
	$('.modal').modal();
	$(".button-collapse").sideNav();
	$('input[type=date]').on('change', function(event) {
		var $target = $(event.target);
		if ($target.val() == '') {
			$target.removeClass('valid');
		} else {
			$target.addClass('valid');
		}
	});
	$('.chips').material_chip({
		placeholder: 'Rechercher...',
		secondaryPlaceholder: 'Ajouter un tag',
		autocompleteOptions: {
			data: TAGS,
			limit: Infinity,
			minLength: 1
		}
	});
	SELECTED_TAGS = [];
	$('.chips').on('chip.add', function(event, chip){
		SELECTED_TAGS.push(chip.tag);
		$(event.target).siblings('input[type=hidden]').attr('value', JSON.stringify(SELECTED_TAGS));
	});

	$('.chips').on('chip.delete', function(event, chip){
		for (var i = 0; i < SELECTED_TAGS.length; i++) {
			if (SELECTED_TAGS[i] == chip.tag) {
				break;
			}
		}
		SELECTED_TAGS.splice(i, 1);
		$(event.target).siblings('input[type=hidden]').attr('value', JSON.stringify(SELECTED_TAGS));
	});
});