<?php
/**
 * The header for our theme.
 *
 * @package Minikit
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">	
		<?php wp_head(); ?>
	</head>
	
	<body <?php body_class(); ?>>
	
		<!--HEADER-->
		<header class="site-header" role="banner">
			
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" id="logo" rel="home"><?php bloginfo( 'name' ); ?></a>
			
			<nav id="navigation" class="main-navigation" role="navigation">
				<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false ) ); ?>
			</nav><!-- #navigation -->
		</header>
		