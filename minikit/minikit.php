<?php 
/*
 * MiniKit is maintained by Stefan Olaru
 * Inspired by Bones https://github.com/eddiemachado/bones
 * - www.stefanolaru.com -
 */
session_start(); 

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
		// deregister wp jquery
		wp_deregister_script('jquery');
	
		// register jquery, modernizr & main.js
		wp_register_script('modernizr', get_template_directory_uri(). '/js/vendor/modernizr-2.6.2.min.js', array(), null, false);
		wp_register_script('jquery', get_template_directory_uri(). '/js/vendor/jquery-1.9.1.min.js', array(),null, true);
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
	// update postmeta
	$wpdb->query("UPDATE ".$wpdb->postmeta." SET `meta_value`=REPLACE(`meta_value`,'".$old_url."','".$new_url."')");
}

// get current URL
function currentURL($add_qs = array()) {
	global $wp;
	$current_url = add_query_arg(array_merge($wp->query_vars, $add_qs), home_url($wp->request));
	return $current_url;
}

/* add login logo */
function minikit_login_logo() {
	if(file_exists(TEMPLATEPATH.'/img/logo.png')) {
		echo '<style type="text/css">
	        body.login div#login h1 a {
	            background-image: url('.get_template_directory_uri().'/img/logo.png);
	            padding-bottom: 0;
	            background-size: auto;
	            width: auto;
	        }
	    </style>';
    }
}
add_action('login_enqueue_scripts', 'minikit_login_logo');

/* change login logo url */
function minikit_login_logo_url() {
    return get_bloginfo( 'url' );
}
add_filter('login_headerurl', 'minikit_login_logo_url');


/* change login logo url title */
function minikit_login_logo_url_title() {
    return get_bloginfo('name');
}
add_filter('login_headertitle', 'minikit_login_logo_url_title');

/* remove wp admin bar logo */
function minikit_logo_admin_bar_remove() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('wp-logo');
}
add_action('wp_before_admin_bar_render', 'minikit_logo_admin_bar_remove', 0);

// Create the function to use in the action hook
function minikit_remove_dashboard_widgets() {
	global $wp_meta_boxes;

	// Remove the quickpress widget
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_quick_press']);

	// Remove the incomming links widget
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
} 
add_action('wp_dashboard_setup', 'minikit_remove_dashboard_widgets' );


/* encryption stuff */

function safe_b64encode($string) {
	$data = base64_encode($string);
	$data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
	return $data;
}

function safe_b64decode($string) {
	$data = str_replace(array('-', '_'), array('+', '/'), $string);
	$mod4 = strlen($data) % 4;
	if ($mod4) {
		$data .= substr('====', $mod4);
	}
	return base64_decode($data);
}

function encrypt($string, $key) {
	return safe_b64encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
}

function decrypt($string, $key) {
	return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), safe_b64decode($string), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
}

/* define encryption key */ 
define('ENCRYPTION_KEY', get_option('admin_email'));

/* Contact Form */

class MinikitContact {
	// errors
	public $errors = array();
	public $is_spam = false;
	public $to;
	public $subject;
	public $success;
	public $atts = array();
	
	function __construct() {
		// validate form
		$this->validate();
		
		// check validation errors errors
		if(!empty($this->errors)) {
			// set errors session
			$_SESSION['mk-validation-errors'] = $this->errors;
			
			// stop execution
			return false;
		}
		
		// get attributes
		if(!empty($_POST['mk-atts'])) {
			$this->atts = json_decode(decrypt($_POST['mk-atts'],ENCRYPTION_KEY), true);
		}
		
		// fill recipient with admin email if no email provided
		$this->to = !empty($this->atts['to'])?$this->atts['to']:get_option('admin_email');
		
		// fill subject with default message
		$this->subject = !empty($this->atts['subject'])?$this->atts['subject']:'Message from '.get_option('blogname');
		
		// fill success url
		$this->success = !empty($this->atts['success'])?$this->atts['success']:currentURL();
		
		// send email
		$this->send_email();
	}
	
	function validate() {
		// check name
		if(empty($_POST['mk-name'])) {
			$this->errors['mk-name'] = 'Please enter your name';
		}
		// check email
		if(!filter_var($_POST['mk-email'], FILTER_VALIDATE_EMAIL)) {
			$this->errors['mk-email'] = 'Please enter a valid email address';
		}
		// check message
		if(empty($_POST['mk-message'])) {
			$this->errors['mk-message'] = 'Please fill the message field';
		}
		// check spam bait field, if field is SPAM
		if(!empty($_POST['url'])) {
			$this->is_spam = true;
		}
	}
	
	function send_email() {
		if(!$this->is_spam) {
			$content = "From: ".$_POST['mk-name']."\r\nEmail: ".$_POST['mk-email']."\r\n\r\n".$_POST['mk-message'];
			$headers = 'From: ' . $_POST['mk-name'] . ' <' . $_POST['mk-email'] . '>' . "\r\n" . 'Reply-To: ' . $_POST['mk-email'];
			// send email
			mail($this->to, $this->subject, $content, $headers);
		}
		// redirect
		header('Location: '.$this->success);
		
	}
}

if(isset($_POST['mk-contact'])) {
	new MinikitContact();
}

?>