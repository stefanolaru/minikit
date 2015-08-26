<?php 

class MinikitCustomPosts extends Minikit {
	
	function __construct() {
		add_action('init', array($this, 'create_custom_posts'));
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
	
}

new MinikitCustomPosts;