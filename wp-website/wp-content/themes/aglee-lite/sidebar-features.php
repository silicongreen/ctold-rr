<?php
/**
 * The sidebar containing the Feature Section.
 *
 * @package Aglee Lite
 */

if ( ! is_active_sidebar( 'aglee_featured_section' ) ) {
	return;
}
?>

<div id="secondary" class="widget-area" role="complementary">
	<?php dynamic_sidebar( 'aglee_featured_section' ); ?>
</div><!-- #secondary -->