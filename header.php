<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title>
			<?php 
					wp_title(' | ' . get_bloginfo('name'), true, 'right');
			        if (is_home() || is_front_page()) {
			        	echo get_bloginfo('name');
			        }
			?>
		</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">	
			
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
	
		<!--HEADER-->
		<header>
			
		</header>
		
		<!--MAIN-->
		<div id="main" role="main">