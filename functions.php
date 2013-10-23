<?php 
/* require minikit */
require_once('minikit/minikit.php');

/* hide admin bar */
add_filter('show_admin_bar', '__return_false');

/* add post thumbnails theme support */
add_theme_support('post-thumbnails');

/* set a primary crop size */
add_image_size('300x200', 300, 200, true);

/* require minikit shortcodes */
require_once('minikit/shortcodes.php');

/* register sidebars */
register_sidebar(array(
	'name' => 'Sidebar',
	'before_widget' => '<div id="%1$s" class="widget %2$s">',
	'after_widget' => '</div>',
	'before_title' => '<h3 class="widget-title">',
	'after_title' => '</h3>',
));

/* OPTIONAL */

/* load basic widgets */
// require_once('minikit/widgets/minikit-image-widget.php');

?>