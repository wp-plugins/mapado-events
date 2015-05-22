/**
 * Admin functions for
 * mapado settings page
 */

(function($) {

$(document).ready(function() {
	/* Slug in lower case */
	$('#mapado_user_lists').on( 'change', '.list-slug', function() {
		$(this).val( $(this).val().toLowerCase() );
	});

	/* Import list action */
	$('#mapado_user_lists').on('click', '.list .button', function(e) {
		var $button	= $(this);
		var $parent	= $(this).parents('tr:eq(0)');

		if ( $parent.find('.list-slug').val() != '' && !$(this).hasClass('disabled') ) {
			/* Disable button during update */
			$(this).addClass('disabled');

			/* Action to do */
			var action	= 'import';
			if ( $(this).hasClass('button-delete') )
				action	= 'delete';

			$.post( ajaxurl, {
				action			: 'ajaxUpdateListSettings',
				uuid			: $parent.attr('data-uuid'),
				slug			: $parent.find('.list-slug').val(),
				title			: $parent.find('.list-title').val(),
				mapado_action	: action

			},
			function( json ) {
				/* No error, refresh list */
				if ( json.state == 'updated' )
					loadUserLists();

				$('#mapado-userlists-notifications').addClass( json.state ).html( '<p><strong>' + json.msg + '</strong></p>' ).show();

			}, 'json');
		}

		e.preventDefault();
		return false;
	});

	loadUserLists();

	/* Refresh user lists */
	$('#mapado_user_lists_refresh').click(function(e) {
		loadUserLists();

		e.preventDefault();
		return false;
	});
});



/**
 * Load dynamically user lists
 */
function loadUserLists () {
	$('#mapado_user_lists tbody').html( '<tr><td colspan="3">' + ajaxUserLists.msg + '</td></tr>' );

	if ( ajaxUserLists.load === true ) {
		$.post( ajaxurl, {
			action	: 'ajaxGetUserLists'
		},
		function( html ) {
			$('#mapado_user_lists tbody').html( html );
		});
	}
}

})(jQuery);