<?php
/*
* remove Customize menu
*/ 

add_filter('admin_menu', 'admin_menu_filter',500);

function admin_menu_filter(){
    remove_submenu_page( 'themes.php', 'customize.php' );//widget
}



/*
 * Add Theme Options menu
 */

function mk_add_admin() {
	add_submenu_page('themes.php', 'Website Settings', 'Theme Options', 'edit_theme_options', 'mk', 'mk_options_page');
}

add_action('admin_menu', 'mk_add_admin');

function mk_options_page() {
	$prefix = 'mk_';

	$options = array(
		'company_name' => 'Company Name',
		'address' => 'Address',
		'email' => 'Email Address',
		'address' => 'Address',
		'phone' => 'Phone',
		'fax' => 'Fax'
	);
	// save options
	if (!empty($_POST['save'])) {
		foreach ($options as $k => $v) {
			update_option($prefix . $k, isset($_POST[$prefix.$k]) ? $_POST[$prefix.$k] : '');
		}
		// set flash session
		$_SESSION['flash'] = array('type' => 'success', 'message' => 'The options have been updated.');
	}

	// get pages
	$pages = get_pages();
	// 
	$pages_select = array();
	foreach ($pages as $v) {
		$pages_select[$v->ID] = (!empty($v->post_parent) ? '- ' : '') . $v->post_title;
	}
	?>
	<div class="wrap">
		<h2>Website Settings</h2>
		<?php if (!empty($_SESSION['flash'])): ?>
			<div id="message" class="updated below-h2"><p><?php echo $_SESSION['flash']['message']; ?></p></div>
			<?php
			// clear session
			unset($_SESSION['flash']);
		endif;
		?>
		<form method="post">
			<?php foreach ($options as $k => $v): ?>
				<div class="input">
					<label for="<?php echo $prefix . $k ?>" style="display: block;"><?php echo $v; ?></label>
					<input type="text" style="width: 400px;" name="<?php echo $prefix . $k ?>" value="<?php echo get_option($prefix . $k); ?>" />
				</div>
			<?php endforeach; ?>
			<div class="submit">
				<input type="submit" name="save" class="button button-primary button-large" value="Save settings" />
			</div>
		</form>
	</div>
	<?php
}
?>
