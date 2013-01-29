<?php 
/* require minikit */
require_once('minikit/minikit.php');

/* hide admin bar */
add_filter('show_admin_bar', '__return_false');

/* set a primary crop size */
add_image_size('300x200', 300, 200, true);

/* require minikit shortcodes */
require_once('minikit/shortcodes.php');


?>