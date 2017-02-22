<?php
/**
 * @package yoast_comment_hacks\admin
 */

?>
<div class="inside">
	<table class="form-table editcomment">
		<tr valign="top">
			<td class="first" style="width: 120px;"><label for="yst_comment_parent"><?php _e( 'Comment parent:', 'yoast-comment-hacks' ); ?></label></td>
			<td><input type="text" name="yst_comment_parent" size="30" value="<?php echo esc_attr( $comment->comment_parent ); ?>" id="yst_comment_parent" /></td>
		</tr>
	</table>
</div>
