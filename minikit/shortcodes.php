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

?>