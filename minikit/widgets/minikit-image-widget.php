<?php
// Add function to widgets_init that'll load our widget
add_action('widgets_init', 'minikit_image_widget');

// Register widget
function minikit_image_widget() {
	register_widget('minikit_Image_Widget');
}

// enque scripts & css
add_action( 'admin_enqueue_scripts', 'minikit_enqueue_scripts');
function minikit_enqueue_scripts() {
	// enque media
	wp_enqueue_media();
	
	// enque custom scripts
	wp_register_script('image_widget', get_template_directory_uri(). '/minikit/js/minikit-image-widget.js', array(), null, true);
	wp_enqueue_script('image_widget');
}

// Widget class
class minikit_image_widget extends WP_Widget {

	function minikit_Image_Widget() {

		// Widget settings
		$widget_ops = array(
			'classname' => 'minikit_image_widget',
			'description' => 'A basic widget that allows you to add a sidebar image'
		);

		// Widget control settings
		$control_ops = array(
			'width' => 300,
			'height' => 350,
			'id_base' => 'minikit_image_widget'
		);

		/* Create the widget. */
		$this->WP_Widget('minikit_image_widget', 'Minikit Image Widget', $widget_ops, $control_ops);
		
	}
	
	function widget($args, $instance) {
		
		extract($args);
		
		// Our variables from the widget settings
		$title = apply_filters('widget_title', $instance['title']);

		// Before widget (defined by theme functions file)
		echo $before_widget;

		// Display the widget title if one was input
		if (empty($instance['hide_title'])) {
			echo $before_title . $title . $after_title;
		}
		
		?>
			<?php if(!empty($instance['link'])): ?>
				<a href="<?php echo $instance['link']; ?>"<?php if($instance['link_text']) echo ' title="'.strip_tags($instance['link_text']).'"'; ?>>
			<?php endif; ?>
				<?php echo wp_get_attachment_image($instance['image_id'], $instance['image_size']); ?>
				<?php if($instance['link_text']) echo '<span>'.$instance['link_text'].'</span>'; ?>
			<?php if(!empty($instance['link'])): ?>
				</a>
			<?php endif; ?>
		
		<?php
		// After widget (defined by theme functions file)
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
//		debug($new_instance);
		$instance = $old_instance;

		// Strip tags to remove HTML (important for text inputs)
		$instance['title'] = strip_tags($new_instance['title']);
		
		// No need to strip tags

		return $new_instance;
	}

	function form($instance) {
		global $_wp_additional_image_sizes;

		// Set up some default widget settings
		$defaults = array();

		$instance = wp_parse_args((array) $instance, $defaults);
		?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Widget Title</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<div style="width: 100%; float: left; border: 1px dashed #CCC; margin-bottom: 10px;">
			<div class="image-container" style="float: left; padding: 10px 10px 0 10px;">
				<?php echo wp_get_attachment_image($instance['image_id'], 'full', null, array(
					'style' =>  'margin: 0 0 10px 0; float: left; max-width: 100%; height: auto;'
				)); ?>
			</div>
			<div style="clear: both;"></div>
			<input type="button" class="button select_minikit_image" value="Select Image" style="margin: 0 10px 10px; float: left;">
			<input type="hidden" class="widefat minikit_image_id" id="<?php echo $this->get_field_id('image_id'); ?>" name="<?php echo $this->get_field_name('image_id'); ?>" value="<?php echo $instance['image_id']; ?>" />
		</div>
		<p>
			<?php $image_sizes = get_intermediate_image_sizes(); ?>
			<label for="<?php echo $this->get_field_id('image_size'); ?>">Display Size</label>
			<select class="widefat" id="<?php echo $this->get_field_id('image_size'); ?>" name="<?php echo $this->get_field_name('image_size'); ?>">
				<?php foreach($image_sizes as $v): ?>
					<option value="<?php echo $v; ?>"<?php if($instance['image_size'] == $v) echo ' selected="selected"'; ?>>
						<?php echo $v; ?>
						<?php if(!empty($_wp_additional_image_sizes[$v])): ?>
							(<?php echo $_wp_additional_image_sizes[$v]['width']; ?>x<?php echo $_wp_additional_image_sizes[$v]['height']; ?>px)
						<?php endif; ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('link'); ?>">Link</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" value="<?php echo $instance['link']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('link_text'); ?>">Link Text</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('link_text'); ?>" name="<?php echo $this->get_field_name('link_text'); ?>" value="<?php echo $instance['link_text']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('hide_title'); ?>"><input type="checkbox" id="<?php echo $this->get_field_id('hide_title'); ?>" name="<?php echo $this->get_field_name('hide_title'); ?>" value="1"<?php if($instance['hide_title'] == 1) echo ' checked="checked"'; ?> /> Hide Widget Title</label>
		</p>
		
		
		<?php
	}

}
?>