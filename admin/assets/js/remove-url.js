/* global chCommentBlockEdit */

jQuery( document ).ready( function( $ ) {
	$( ".comment-remove-url" ).on( "click", function( e ) {
		e.preventDefault();

		var commentId = $( this ).data( "comment-id" );

		$.ajax( {
			url: chCommentBlockEdit.ajax_url,
			type: "POST",
			data: {
				action: "ch_remove_comment_url",
				commentId: commentId,
				nonce: chCommentBlockEdit.nonce,
			},
			/**
			 * Handle the AJAX response.
			 *
			 * @param {Object} response The response object.
			 * @param {boolean} response.success Indicates if the request was successful.
			 * @param {string} response.data The response data.
       *
       * @returns {void}
			 */
			success: function( response ) {
				if ( response.success ) {
					// Reload the page to reflect the changes.
					location.reload();
				} else {
					console.error( response.data );
				}
			},
		} );
	} );
} );
