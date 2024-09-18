<?php
/**
 * Parent Box view.
 */

/**
 * The comment object.
 *
 * @var WP_Comment $comment
 */
?>
<div class="inside">
	<table class="form-table editcomment">
		<tr valign="top">
			<td class="first">
				<label for="epch_comment_parent"><?php esc_html_e( 'Comment parent:', 'comment-hacks' ); ?></label>
			</td>
			<td>
				<input type="text" name="epch_comment_parent" size="30" value="<?php echo esc_attr( $comment->comment_parent ); ?>" id="epch_comment_parent" />
			</td>
		</tr>
	</table>
</div>
