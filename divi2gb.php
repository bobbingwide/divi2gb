<?php

/**
Plugin Name: divi2gb
Plugin URI: https://www.oik-plugins.com/oik-plugins/divi2gb
Description: Help to convert Divi pages to Gutenberg
Version: 0.0.0
Author: bobbingwide
Author URI: https://www.oik-plugins.com/author/bobbingwide
Text Domain: cwiccer
Domain Path: /languages/
License: GPLv2
 */

function divi2gb() {
	
	//add_action( 'admin_ajax_divi2gb', 'divi2gb_convert' );
	add_action( "wp_ajax_divi2gb", "divi2gb_convert" );
	add_action( "wp_ajax_nopriv_divi2gb", "divi2gb_convert"  );
	

}

function divi2gb_convert() {
	

$id = bw_array_get( $_REQUEST, "id", 805 );

echo "Processing post ID";
echo $id;
$post = get_post( $id );
//print_r( $post );
$post_content = $post->post_content;
//echo $post_content;
$shortcodes = explode( '[', $post_content );
foreach ( $shortcodes as $shortcode ) {
	//echo esc_html( $shortcode);
	divi2gb_handle_shortcode( $shortcode );
}
//print_r( $shortcodes );

	//echo "end";

}

/**
 * Handles the start/end shortcode.
 * We assume that the Divi shortcodes are well formed with start and end tags
 * @param $shortcode
 */
function divi2gb_handle_shortcode( $shortcode ) {
	$pos_et =  strpos( $shortcode, 'et_' );
	if ( 0 === $pos_et ) {
		$code = strstr( $shortcode, ' ', true );
		//echo $code;
		divi2gb_handle_rsb( $code, $shortcode );

	} else {
		//echo $shortcode;

	}
	//echo '<br />';
}

/**
 * Handles the right square bracket ']'.
 *
 *
 * @param $code
 * @param $shortcode
 */
function divi2gb_handle_rsb( $code, $shortcode ) {
	$parts = explode( ']', $shortcode);
	$text = $parts[1];


	switch ( $code ) {
		case 'et_pb_section':
		case 'et_pb_row':
		case 'et_pb_column':
		case 'et_pb_fullwidth_slider':
			break;

			/* [et_pb_slide heading="Meet the financial advice team" button_text="Contact Floyd" button_link="/contact-us/" image="https://rathmorefinancial.com/wp-content/uploads/2020/09/Floyd-Fombo-250x269-1.jpg"
			*/
		case 'et_pb_slide':
			$heading = divi2gb_extract_attribute( 'heading', $parts[0] );
			$button = divi2gb_extract_attribute( 'button_text', $parts[0]);
			$url = divi2gb_extract_attribute( 'button_link', $parts[0]);
			$image = divi2gb_extract_attribute( 'image', $parts[0]);
			echo "<h3>$heading</h3>";
			echo $text;
			echo "<a href=\"$url\">$button</a>";
			break;

		case 'et_pb_blurb':
			$title = divi2gb_extract_attribute( 'title', $parts[0] );
			echo "<h3>$title</h3>";
			echo $text;
			break;

		case 'et_pb_testimonial':
			$author = divi2gb_extract_attribute( 'author', $parts[0]);
			$job = divi2gb_extract_attribute( 'job_title', $parts[0]);
			echo $text;
			echo "$author,$job";
			break;

			/* [et_pb_cta button_url="/contact-us/" button_text="Book Now" */
		case 'et_pb_cta':
			echo $text;
			$button = divi2gb_extract_attribute( 'button_text', $parts[0]);
			$url = divi2gb_extract_attribute( 'button_url', $parts[0]);
			echo "<a href=\"$url\">$button</a>";
			break;

		case 'et_pb_text':
			echo $text;
			break;

		default:
			if ( 0 === strpos($code, 'et_' ) ) {
				echo "shortcode not catered for" . $code;
			}
			//if ( '/' === $code[0] )
			echo $text;


	}
	echo PHP_EOL;


}


function divi2gb_extract_attribute( $attribute, $shortcode ) {
	//$shortcode = str_replace( '=', ' ', $shortcode)
	$atts = shortcode_parse_atts( $shortcode );
	//print_r( $atts );
	$attr_value = bw_array_get( $atts, $attribute, null);
	return $attr_value;

}


divi2gb();