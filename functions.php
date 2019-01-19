<?php 
/* require minikit */
require_once('inc/minikit.php');
require_once('inc/shortcodes.php');
//require_once('inc/custom_posts.php');
require_once('inc/contact_form.php');

class MinikitTheme extends Minikit {

	public $jquery_ver = '1.11.3';
	
	function __construct() {
		
		/* hide admin bar */
		add_filter('show_admin_bar', '__return_false');
		
		// initial stuff
		add_action('after_setup_theme', array($this, 'theme_support'), 25);
		
		// register & enqueue jquery from google CDN
		add_action('wp_enqueue_scripts', array($this, 'load_jquery'), 999);
		
		// load local jquery fallback
//		add_action('wp_footer', array($this, 'load_jquery_fallback'), 1000);
		
		// register & enqueue other scripts
		add_action('wp_enqueue_scripts', array($this, 'register_scripts'), 1001);
		
		// register & enqueue other scripts
		add_action('wp_enqueue_scripts', array($this, 'register_styles'), 1001);
		
		// remove admin menu items
		add_action('admin_menu', array($this, 'remove_admin_menus'));
		
		// register menus
		$this->register_menus();
		
		// register sidebars
		$this->register_sidebars();
		
		// image sizes
		$this->image_sizes();
		
	}
	
	function image_sizes() {
	
		// default thumb size
		set_post_thumbnail_size(125, 125, true);
	
		/* set a primary crop size */
		add_image_size('300x200', 300, 200, true);
		
	}
	
	function theme_support() {
		add_theme_support( 'custom-header', array(
			'width' => 1920,
			'height' => 1280
		) );
	}
	
	function load_jquery() {
		if(!is_admin()) {
			// deregister wp jquery first
			wp_deregister_script('jquery');
			// register new script
			wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/'.$this->jquery_ver.'/jquery.min.js', array(), null, true);
			// enqueue script
			wp_enqueue_script('jquery');
		}
	}
	
	function load_jquery_fallback() {
		if(wp_script_is('jquery', 'done')) {
			echo '<script>window.jQuery || document.write(\'<script src="'.THEME_URI.'/js/vendor/jquery-'.$this->jquery_ver.'.min.js"><\/script>\')</script>';
		}
	}
	
	function register_scripts() {
		if(!is_admin()) {
			
			// register jquery, modernizr & main.js
			wp_register_script('modernizr', THEME_URI. '/js/vendor/modernizr.js', array(), null, false);
			wp_register_script('main', $this->get_versioned_file('/js/main.js'), array('jquery'), null, true);
	
			// enque scripts		
			wp_enqueue_script('modernizr');
			wp_enqueue_script('main');
		}	
	}
	
	function register_styles() {
		if(!is_admin()) {
		
			// register normalize css and main css
			wp_register_style('normalize', get_template_directory_uri().'/css/normalize.css', array(), '', 'all');
			wp_register_style('foundation', get_template_directory_uri().'/css/foundation.min.css', array('normalize'), '', 'all');
			wp_register_style('style', $this->get_versioned_file('/css/style.css'), array(), '', 'all');
			
			// enque styles
			wp_enqueue_style('normalize');
			wp_enqueue_style('foundation');
			wp_enqueue_style('style');
		}
	}

	function get_versioned_file($path) {

		/*
		Add the following line in .htaccess
		
		# rewrite rule for versioned css/js
		RewriteRule ^(.*)/(css|js)/(.*).([0-9]{12}).(css|js)$ /$1/$2/$3.$5 [L,NC]
		
		*/

		// get URI
		$uri = THEME_DIR . $path;
		if (file_exists($uri)) {

			// get pathinfo
			$pi = pathinfo($path);

			// get timestamp
			$timestamp = date('ymdHis',filemtime($uri));

			// set new versioned path
			$path = $pi['dirname'].'/'.$pi['filename'].'.'.$timestamp.'.'.$pi['extension'];
		}

		return THEME_URI.$path;
	}
	
	function remove_admin_menus() {
	
		global $menu;
		
		$restricted = array(__('Dashboard'), __('Media'), __('Links'), __('Comments'));
		
		end ($menu);
		
		while (prev($menu)){
			$value = explode(' ',$menu[key($menu)][0]);
			if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){unset($menu[key($menu)]);}
		}
	}
	
	function register_menus() {
	
		register_nav_menus(
			array(
				'primary' => __( 'Primary'),
				'footer' => __( 'Footer')
			)
		);
		
	}
	
	function register_sidebars() {
	
		register_sidebar(array(
			'id' => 'sidebar',
			'name' => 'Sidebar',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));
		
	}
	
}

new MinikitTheme();


/* OPTIONAL */

/* load basic widgets */
// require_once('minikit/widgets/minikit-image-widget.php');