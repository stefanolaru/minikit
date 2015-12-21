<?php 
/*
 * MiniKit is maintained by Stefan Olaru
 * - www.stefanolaru.com -
 * ver 2.0
 */

$minikit = new Minikit(); 
 
class Minikit {

	function __construct() {
	
		$this->enable_sessions();
		
		// setup constants
		$this->constants();
	
		// initial stuff
		add_action('after_setup_theme', array($this, 'init'), 15);
		
		//move wpautop filter to AFTER shortcode is processed
		remove_filter( 'the_content', 'wpautop' );
		add_filter( 'the_content', 'wpautop' , 99 );
		add_filter( 'the_content', 'shortcode_unautop', 100 );
		
	}
	
	function enable_sessions() {
		if (!session_id()) {
		    session_start();
		}
	}
	
	function init() {
	
		// cleanup head
		add_action('init', array($this, 'head_cleanup'));
		
		// cleaning up random code around images
		add_filter('the_content', array($this, 'filter_ptags_on_images'));
		
		// cleaning up excerpt
		add_filter('excerpt_more', array($this, 'excerpt_more'));
		
		// dashboard setup
		add_action('wp_dashboard_setup', array($this, 'remove_dashboard_widgets'));
		
		// remove admin bar logo
		add_action('wp_before_admin_bar_render', array($this, 'remove_admin_bar_logo'), 0);
		
		// deregister widgets that are rarely used on wordpress as CMS
		add_action('widgets_init', array($this, 'deregister_default_widgets'));
		
		// change login logo img/url/title
		$this->login_logo();
		
		// theme support
		$this->theme_support();
		
	}
	
	public static function constants() {
	
		// current theme directory
		define( 'THEME_DIR', get_template_directory() );
		
		// current theme URI
		define( 'THEME_URI', get_template_directory_uri() );
		
		// current URL
		define( 'CURRENT_URL', ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
	}
	
	function head_cleanup() {
	
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
		
		// remove WP version from css
		add_filter('style_loader_src', array($this, 'remove_wp_ver_css_js'), 9999);
		// remove Wp version from scripts
		add_filter('script_loader_src', array($this, 'remove_wp_ver_css_js'), 9999);
		
		// remove all emoji crap
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		add_filter( 'tiny_mce_plugins', array($this, 'disable_emojis_tinymce'));
		
	}
	
	function theme_support() {
		// wp thumbnails (sizes handled in functions.php)
		add_theme_support( 'post-thumbnails' );
	
		// wp menus
		add_theme_support( 'menus' );
		
		// add html5
		add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ));
		
		// add title-tag WP 4.1+
		add_theme_support( 'title-tag' );
		
	}
	
	
	function rss_version() {
		return '';		
	}
	
	function remove_wp_ver_css_js($src) {
		if ( strpos( $src, 'ver=' ) )
		    $src = remove_query_arg( 'ver', $src );
		return $src;
	}
	
	function deregister_default_widgets() {
		global $wp_widget_factory;
		
		unregister_widget('WP_Widget_Pages');
	    unregister_widget('WP_Widget_Calendar');
	    unregister_widget('WP_Widget_Links');
	    unregister_widget('WP_Widget_Meta');
	    unregister_widget('WP_Widget_RSS');
	    unregister_widget('WP_Widget_Tag_Cloud');
	     
	    // remove recent comments style & widget
	    remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ));
	    unregister_widget('WP_Widget_Recent_Comments');
	}
	
	function disable_emojis_tinymce( $plugins ) {
		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, array( 'wpemoji' ) );
		} else {
			return array();
		}
	}
	
	function login_logo() {
	
		// change login logo
		add_action('login_enqueue_scripts', array($this, 'login_logo_img'));
		
		// change login logo link url
		add_filter('login_headerurl', array($this, 'login_logo_url'));
		
		// change login logo title
		add_filter('login_headertitle', array($this, 'login_logo_title'));
		
	}
	
	function login_logo_img() {
		//
		$logo_url = null;
		//
		if(file_exists(TEMPLATEPATH.'/img/logo.png')) {
			$logo_url = get_template_directory_uri().'/img/logo.png';
		} elseif (file_exists(TEMPLATEPATH.'/img/logo.jpg')) {
			$logo_url = get_template_directory_uri().'/img/logo.jpg';
		}
		
		if(!empty($logo_url)) {
			echo '<style type="text/css">
			    body.login div#login h1 a {
			        background-image: url('.$logo_url.');
			        padding-bottom: 0;
			        background-size: auto;
			        width: auto;
			    }
			</style>';
		}
	}
	
	function login_logo_url() {
		return get_bloginfo( 'url' );
	}
	
	function login_logo_title() {
		return get_bloginfo('name');
	}
	
	// remove the p from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
	function filter_ptags_on_images($content){
		return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
	}
	
	// This removes the annoying […] to a Read More link
	function excerpt_more($more) {
		global $post;
		// edit here if you like
		return '...  <a href="'. get_permalink($post->ID) . '" title="Read '.get_the_title($post->ID).'">Read more &raquo;</a>';
	}
	
	function remove_dashboard_widgets() {
		global $wp_meta_boxes;
		
		// Remove the quickpress widget
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
	
		// Remove the incomming links widget
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
	}
	
	function remove_admin_bar_logo() {
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu('wp-logo');
	}
	
	function change_wp_url($old_url, $new_url) {
		// useful when you need to migrate the wordpress install to another URL
		// makes the needed DB changes
		global $wpdb;
		
		// update post guid
		$wpdb->query("UPDATE ".$wpdb->posts." SET `guid`=REPLACE(`guid`,'".$old_url."','".$new_url."')");
		// update post content
		$wpdb->query("UPDATE ".$wpdb->posts." SET `post_content`=REPLACE(`post_content`,'".$old_url."','".$new_url."')");
		// update options
		$wpdb->query("UPDATE ".$wpdb->options." SET `option_value`=REPLACE(`option_value`,'".$old_url."','".$new_url."')");
		// update postmeta
		$wpdb->query("UPDATE ".$wpdb->postmeta." SET `meta_value`=REPLACE(`meta_value`,'".$old_url."','".$new_url."')");
	}
	
}

// debug function
function debug($s, $die = false) {
	// print pre
	echo '<pre>'.print_r($s,true).'</pre>';
	// die
	if($die) die();
}

// get excerpt from content
function excerpt_from_content($content, $limit = 20) {
	$excerpt = explode(' ', strip_tags($content), $limit);
	if (count($excerpt)>=$limit) {
		array_pop($excerpt);
		$excerpt = implode(" ",$excerpt).' &hellip;';
	} else {
		$excerpt = implode(" ",$excerpt);
	} 
	$excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
	return $excerpt;
}

/* Admin Customizer */

class MinikitAdmin extends Minikit {

	function __construct() {
		add_action( 'customize_register', array($this, 'customizer'));
	}
	
	function customizer() {
		global $wp_customize;
		
		$wp_customize->add_section( 'minikit_website_settings', array(
		            'title' => 'Website Settings',
		            'priority' => 35
		));
		
		$mods = array(
			'company_name' => 'Company Name',
			'address' => 'Address',
			'email' => 'Email Address',
			'address' => 'Address',
			'phone' => 'Phone',
			'fax' => 'Fax'
		);
		
		foreach($mods as $k=>$v) {
			$wp_customize->add_setting( $k );
			$wp_customize->add_control( $k, array(
				'label' => $v,
				'section' => 'minikit_website_settings'
			));
		}	
	}
}

new MinikitAdmin();