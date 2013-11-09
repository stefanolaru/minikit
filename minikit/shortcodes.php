<?php 
/*
 * MiniKit is maintained by Stefan Olaru
 * - www.stefanolaru.com -
 */


//Clean Up WordPress Shortcode Formatting - important for nested shortcodes
//adjusted from http://donalmacarthur.com/articles/cleaning-up-wordpress-shortcode-formatting/
function parse_shortcode_content( $content ) {

   /* Parse nested shortcodes and add formatting. */
    $content = trim( do_shortcode( shortcode_unautop( $content ) ) );

    /* Remove '' from the start of the string. */
    if ( substr( $content, 0, 4 ) == '' )
        $content = substr( $content, 4 );

    /* Remove '' from the end of the string. */
    if ( substr( $content, -3, 3 ) == '' )
        $content = substr( $content, 0, -3 );

    /* Remove any instances of ''. */
    $content = str_replace( array( '<p></p>' ), '', $content );
    $content = str_replace( array( '<p>  </p>' ), '', $content );

    return $content;
}

//move wpautop filter to AFTER shortcode is processed
remove_filter( 'the_content', 'wpautop' );
add_filter( 'the_content', 'wpautop' , 99);
add_filter( 'the_content', 'shortcode_unautop',100 );



/* write email address using javascript*/
function email_antispam($atts,$content=null) {
	$link = '<a'.(!empty($atts['class'])?' class="'.$atts['class'].'"':'').' href="mailto:'.do_shortcode($content).'">'.do_shortcode($content).'</a>';
	$html = '<script type="text/javascript">';
	$html .= 'document.write(\''.$link.'\');';
	$html .= '</script>';
	return $html;
}
add_shortcode('email', 'email_antispam');

/* contact form */
function mk_contact_form($atts,$content=null) {
	$html = '<div class="mkcf-wrapper">';
	
	// check for validation errors
	if(!empty($_SESSION['mk-validation-errors'])) {
		$html .= '<div class="alert-box error">';
		$html .= implode('<br />', $_SESSION['mk-validation-errors']);
		$html .= '</div>';
		// unset validation errors
		unset($_SESSION['mk-validation-errors']);
	}
	
	$html .= '<form method="post" action="'.currentURL().'" id="mk-contact-form">';
	// name field
	$html .= '<div class="input">';
	$html .= '<label for="mk-name">Name</label>';
	$html .= '<input type="text" class="input-text" id="mk-name" name="mk-name" value="" />';
	$html .= '</div>';
	
	// email field
	$html .= '<div class="input">';
	$html .= '<label for="mk-email">Email</label>';
	$html .= '<input type="email" class="input-text" id="mk-email" name="mk-email" value="" />';
	$html .= '</div>';
	
	// spam	bait field
	$html .= '<div class="input" style="display:none;">';
	$html .= '<label for="mk-url">URL</label>';
	$html .= '<input type="text" class="input-text" id="mk-url" name="url" value="" />';
	$html .= '</div>';
	
	// message field
	$html .= '<div class="input">';
	$html .= '<label for="mk-message">Message</label>';
	$html .= '<textarea name="mk-message" rows="5" id="mk-message" class="input-text"></textarea>';
	$html .= '</div>';
	
	// submit
	$html .= '<div class="input">';
	$html .= '<input type="hidden" value="'.encrypt(json_encode($atts), ENCRYPTION_KEY).'" name="mk-atts" />';
	$html .= '<input type="submit" value="Send" name="mk-contact" class="button" />';
	$html .= '</div>';
	$html .= '</form>';
	$html .= '</div>';
	return $html;
}
add_shortcode('contact-form', 'mk_contact_form');


?>