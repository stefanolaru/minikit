<?php 

class MinikitCustomPosts extends Minikit {
	
	function __construct() {
		add_action('init', array($this, 'create_custom_posts'));
		
		// rename admin Posts into News (or whatever)
//		add_action( 'init', array($this, 'change_post_object_label') );
//		add_action( 'admin_menu', array($this, 'change_post_menu_label') );
	}
	
	function create_custom_posts() {
		register_post_type('post_type', array(
			'labels' => $this->labels('Post Type Name', 'Post Type', 'Post Types'),
			'public' => true,
			'has_archive' => false,
			'show_ui' => true,
			'supports' => array('title', 'editor', 'thumbnail', 'page-attributes'),
			'hierarchical' => false
		));
	}
	
	function labels($name, $singular, $plural) {
	
		return array(
			'name' => _x($name, 'post type general name'),
			'singular_name' => _x($singular, 'post type singular name'),
			'add_new' => _x('Add New', $singular),
			'add_new_item' => __('Add New '.$singular),
			'edit_item' => __('Edit '.$singular),
			'new_item' => __('New '.$singular),
			'view_item' => __('View '.$singular),
			'search_items' => __('Search '.$plural),
			'not_found' => __('No '.strtolower($singular).' found'),
			'not_found_in_trash' => __('No '.strtolower($singular).' found in Trash'),
			'parent_item_colon' => ''
		);
		
	}
	
	function change_post_menu_label() {
		global $menu;
		global $submenu;
		$menu[5][0] = 'News';
		$submenu['edit.php'][5][0] = 'News';
		$submenu['edit.php'][10][0] = 'Add News';
		$submenu['edit.php'][16][0] = 'News Tags';
		echo '';
	}
	function change_post_object_label() {
		global $wp_post_types;
		$labels = &$wp_post_types['post']->labels;
		$labels->name = 'News';
		$labels->singular_name = 'News';
		$labels->add_new = 'Add News';
		$labels->add_new_item = 'Add News';
		$labels->edit_item = 'Edit News';
		$labels->new_item = 'News';
		$labels->view_item = 'View News';
		$labels->search_items = 'Search News';
		$labels->not_found = 'No News found';
		$labels->not_found_in_trash = 'No News found in Trash';
	}
	
}

new MinikitCustomPosts;