<?php 
/*
 * MiniKit is maintained by Stefan Olaru
 * Inspired by Bones https://github.com/eddiemachado/bones
 * - www.stefanolaru.com -
 */

// debug function
function debug($s) {
	echo '<pre>'.print_r($s,true).'</pre>';
}

// fire initial functions
add_action('after_setup_theme','init_minikit', 15);

function init_minikit() {
	// launching operation cleanup
	add_action('init', 'minikit_head_cleanup');
	
	// enqueue base scripts and styles
	add_action('wp_enqueue_scripts', 'minikit_register_js_and_css', 999);
	
	// launching this stuff after theme setup
	add_action('after_setup_theme','minikit_theme_support');
	
	// cleaning up random code around images
	add_filter('the_content', 'minikit_filter_ptags_on_images');
	
	// cleaning up excerpt
	add_filter('excerpt_more', 'minikit_excerpt_more');
	
}

function minikit_head_cleanup() {
	global $wp_widget_factory; 
	// EditURI link
	remove_action( 'wp_head', 'rsd_link' );
	// windows live writer
	remove_action( 'wp_head', 'wlwmanifest_link' );
	// index link
	remove_action( 'wp_head', 'index_rel_link' );
	// previous link
	remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
	// start link
	remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
	// links for adjacent posts
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	// WP version
	remove_action( 'wp_head', 'wp_generator');
	// remove recent comments style
	remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
	// remove WP version from css
	add_filter('style_loader_src', 'minikit_remove_wp_ver_css_js', 9999);
	// remove Wp version from scripts
	add_filter('script_loader_src', 'minikit_remove_wp_ver_css_js', 9999);
}

// remove WP version from RSS
function minikit_rss_version() { return ''; }

// remove WP version from scripts
function minikit_remove_wp_ver_css_js( $src ) {
    if ( strpos( $src, 'ver=' ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
}


function minikit_register_js_and_css() {
	if(!is_admin()) {
		// register jquery, modernizr & main.js
		wp_register_script('modernizr', get_template_directory_uri(). '/js/vendor/modernizr-2.6.2.min.js', array(), null, false);
		wp_register_script('jquery', get_template_directory_uri(). '/js/vendor/jquery-1.8.3.min.js', array(),null, true);
		wp_register_script('main', get_template_directory_uri(). '/js/main.js', array(), null, true);

		// enque scripts		
		wp_enqueue_script('modernizr');
		wp_enqueue_script('jquery');
		wp_enqueue_script('main');
		
		// register normalize css and main css
		wp_register_style('normalize', get_template_directory_uri().'/css/normalize.css', array(), '', 'all');
		wp_register_style('style', get_template_directory_uri().'/css/style.css', array(), '', 'all');
		
		// enque styles
		wp_enqueue_style('normalize');
		wp_enqueue_style('style');
	}
}

function minikit_theme_support() {

	// wp thumbnails (sizes handled in functions.php)
	add_theme_support('post-thumbnails');

	// default thumb size
	set_post_thumbnail_size(125, 125, true);

	// rss thingy
	add_theme_support('automatic-feed-links');

	// wp menus
	add_theme_support( 'menus' );

	// registering wp3+ menus
	register_nav_menus(
		array(
			'primary' => __( 'Primary'),
			'footer' => __( 'Footer')
		)
	);
}


/* VARIOUS FUNCTIONS */

// remove the p from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
function minikit_filter_ptags_on_images($content){
   return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

// This removes the annoying […] to a Read More link
function minikit_excerpt_more($more) {
	global $post;
	// edit here if you like
	return '...  <a href="'. get_permalink($post->ID) . '" title="Read '.get_the_title($post->ID).'">Read more &raquo;</a>';
}

function changeWPURL($old_url,$new_url) {
	// useful when you need to migrate the wordpress install to another URL
	// makes the needed DB changes
	global $wpdb;
	// update post guid
	$wpdb->query("UPDATE ".$wpdb->posts." SET `guid`=REPLACE(`guid`,'".$old_url."','".$new_url."')");
	// update post content
	$wpdb->query("UPDATE ".$wpdb->posts." SET `post_content`=REPLACE(`post_content`,'".$old_url."','".$new_url."')");
	// update options
	$wpdb->query("UPDATE ".$wpdb->options." SET `option_value`=REPLACE(`option_value`,'".$old_url."','".$new_url."')");
}

// get current URL
function currentURL() {
	 $pageURL = 'http';
	 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	 $pageURL .= "://";
	 if ($_SERVER["SERVER_PORT"] != "80") {
	  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	 } else {
	  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	 }
	 return $pageURL;
}

?>