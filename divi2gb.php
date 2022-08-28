<?php

/**
Plugin Name: divi2gb
Plugin URI: https://www.oik-plugins.com/oik-plugins/divi2gb
Description: Help to convert Divi pages to Gutenberg
Version: 0.0.0
Author: bobbingwide
Author URI: https://www.oik-plugins.com/author/bobbingwide
Text Domain: divi2gb
Domain Path: /languages/
License: GPLv2
 */

function divi2gb() {
	
	//add_action( 'admin_ajax_divi2gb', 'divi2gb_convert' );
	add_action( "wp_ajax_divi2gb", "divi2gb_convert" );
	add_action( "wp_ajax_nopriv_divi2gb", "divi2gb_convert"  );
	

}

function divi2gb_convert() {
	

$id = bw_array_get( $_REQUEST, "id", null );

if ( $id ) {
    echo "Processing post ID ";
    echo $id;
    $post = get_post($id);
//print_r( $post );
    $post_content = $post->post_content;
//echo $post_content;
    $shortcodes = explode('[', $post_content);
    foreach ($shortcodes as $shortcode) {
        //echo esc_html( $shortcode);
        divi2gb_handle_shortcode($shortcode);
    }
//print_r( $shortcodes );

    //echo "end";
} else {
    divi2gb_page_list();
}
}

function divi2gb_page_list() {
    $posts = get_posts( [ "post_type" => "page", "numberposts" => -1 ] );
    $admin_url = admin_url( 'admin-ajax.php');
    $edit_url = admin_url( 'post.php');
    echo '<ul>';
    foreach ( $posts as $post ) {

        $url = add_query_arg( [ "action" => "divi2gb", "id" => $post->ID ], $admin_url );
        $link = retlink( null, $url, "Convert " . $post->post_title );
        $post_edit_url = add_query_arg( ['post' => $post->ID, 'action' => 'edit'], $edit_url );
        $post_edit_link = retlink( null, $post_edit_url, "Edit");
        echo "<li> $link $post_edit_link </li>";
    }
    echo '</ul>';
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
			echo "<h2>$heading</h2>";
            echo divi2gb_generate_image( $image, null, null  );
			echo $text;
			echo "<a href=\"$url\">$button</a>";
			break;

		case 'et_pb_blurb':
			$title = divi2gb_extract_attribute( 'title', $parts[0] );
			echo "<h2>$title</h2>";
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

            /*
            [et_pb_image src="https://rathmorefinancial.com/wp-content/uploads/2020/07/Rathmore-Financial-981x300-1.jpg"
            alt="Rathmore Financial Consultants - Personal Financial Planning"
            title_text="Rathmore Financial 981x300"
            align="center" align_tablet="center"
            align_phone="" align_last_edited="on|desktop"
            admin_label="Image - Team" _builder_version="4.17.4"
            animation_style="fade"
            animation_duration="500ms"
            use_border_color="off"
            border_color="#ffffff"
            border_style="solid"
            animation="fade_in"
            sticky="off"
            always_center_on_mobile="on"
            global_colors_info="{}"][/et_pb_image]
            */
        case 'et_pb_image':
            $src = divi2gb_extract_attribute( 'src', $parts[0]);
            $alt = divi2gb_extract_attribute( 'alt', $parts[0]);
            $title_text= divi2gb_extract_attribute( 'title_text', $parts[0]);
            $image = divi2gb_generate_image( $src, $alt, $title_text );
            /*
            <!-- wp:image {"id":136,"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="https://s.b/wp55/frost/wp-content/uploads/sites/23/2022/08/Rathmore-Financial-981x300-1.jpg" alt="" class="wp-image-136"/></figure>
<!-- /wp:image -->
            */
            echo $image;
            break;

        case 'et_pb_contact_form':
            $form = divi2gb_generate_contact_form( $parts[0]);
            echo $form;
            break;

        case 'et_pb_contact_field':
        case 'et_pb_map_pin':
            break;

        case 'et_pb_map':
            $map = divi2gb_generate_map( $parts[0] );
            echo $map;
            break;


        case 'et_pb_team_member':
            $team_member = divi2gb_generate_team_member( $parts[0], $text );
            echo $team_member;
            break;

        /* [et_pb_fullwidth_header background_overlay_color=”rgba(0,0,0,0)” content_max_width=”none” _builder_version=”3.16″ background_color=”rgba(255, 255, 255, 0)” background_image=”https://rathmorefinancial.com/wp-content/uploads/2017/04/Rathmore-0080-desk-1.jpg” background_layout=”light” background_url=”https://rathmorefinancial.com/wp-content/uploads/2017/04/Rathmore-0080-desk-1.jpg” button_one_letter_spacing_hover=”0″ button_two_letter_spacing_hover=”0″ button_one_text_size__hover_enabled=”off” button_one_text_size__hover=”null” button_two_text_size__hover_enabled=”off” button_two_text_size__hover=”null” button_one_text_color__hover_enabled=”off” button_one_text_color__hover=”null” button_two_text_color__hover_enabled=”off” button_two_text_color__hover=”null” button_one_border_width__hover_enabled=”off” button_one_border_width__hover=”null” button_two_border_width__hover_enabled=”off” button_two_border_width__hover=”null” button_one_border_color__hover_enabled=”off” button_one_border_color__hover=”null” button_two_border_color__hover_enabled=”off” button_two_border_color__hover=”null” button_one_border_radius__hover_enabled=”off” button_one_border_radius__hover=”null” button_two_border_radius__hover_enabled=”off” button_two_border_radius__hover=”null” button_one_letter_spacing__hover_enabled=”on” button_one_letter_spacing__hover=”0″ button_two_letter_spacing__hover_enabled=”on” button_two_letter_spacing__hover=”0″ button_one_bg_color__hover_enabled=”off” button_one_bg_color__hover=”null” button_two_bg_color__hover_enabled=”off” button_two_bg_color__hover=”null”][/et_pb_fullwidth_header]
        */
        case 'et_pb_fullwidth_header':
            $fullwidth_header = divi2gb_generate_fullwidth_header( $parts[0]);
            echo $fullwidth_header;
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

function divi2gb_generate_image( $src, $alt, $title_text ) {

    $html = '<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->';
    $html .= '<figure class="wp-block-image size-full"><img src="';
    $html .= $src;
    $html .= '" alt="';
    $html .= $alt;
    $html .= '"/></figure>';
    $html .= '<!-- /wp:image -->';
    return $html;
}

/*
 * [et_pb_contact_form
 * email=”herb@bobbingwide.com”
 * custom_message=”From: %%name%%||et_pb_line_break_holder||Email: %%email%%||et_pb_line_break_holder||Phone: %%phone%%||et_pb_line_break_holder||Message: %%message%%||et_pb_line_break_holder||||et_pb_line_break_holder||Does this work? ”
 * module_id=”et_pb_contact_form_0″
 * _builder_version=”4.16″
 * _unique_id=”e8e14e43-b8fe-45f6-9d25-645101267cf7″
 * global_colors_info=”{}”]
 * [et_pb_contact_field field_id=”Name” field_title=”Name” _builder_version=”4.16″ form_field_font=”||||” use_border_color=”off” global_colors_info=”{}” button_text_size__hover_enabled=”off” button_one_text_size__hover_enabled=”off” button_two_text_size__hover_enabled=”off” button_text_color__hover_enabled=”off” button_one_text_color__hover_enabled=”off” button_two_text_color__hover_enabled=”off” button_border_width__hover_enabled=”off” button_one_border_width__hover_enabled=”off” button_two_border_width__hover_enabled=”off” button_border_color__hover_enabled=”off” button_one_border_color__hover_enabled=”off” button_two_border_color__hover_enabled=”off” button_border_radius__hover_enabled=”off” button_one_border_radius__hover_enabled=”off” button_two_border_radius__hover_enabled=”off” button_letter_spacing__hover_enabled=”off” button_one_letter_spacing__hover_enabled=”off” button_two_letter_spacing__hover_enabled=”off” button_bg_color__hover_enabled=”off” button_one_bg_color__hover_enabled=”off” button_two_bg_color__hover_enabled=”off”] [/et_pb_contact_field][et_pb_contact_field field_id=”Email” field_title=”Email Address” field_type=”email” _builder_version=”4.16″ form_field_font=”||||” use_border_color=”off” global_colors_info=”{}” button_text_size__hover_enabled=”off” button_one_text_size__hover_enabled=”off” button_two_text_size__hover_enabled=”off” button_text_color__hover_enabled=”off” button_one_text_color__hover_enabled=”off” button_two_text_color__hover_enabled=”off” button_border_width__hover_enabled=”off” button_one_border_width__hover_enabled=”off” button_two_border_width__hover_enabled=”off” button_border_color__hover_enabled=”off” button_one_border_color__hover_enabled=”off” button_two_border_color__hover_enabled=”off” button_border_radius__hover_enabled=”off” button_one_border_radius__hover_enabled=”off” button_two_border_radius__hover_enabled=”off” button_letter_spacing__hover_enabled=”off” button_one_letter_spacing__hover_enabled=”off” button_two_letter_spacing__hover_enabled=”off” button_bg_color__hover_enabled=”off” button_one_bg_color__hover_enabled=”off” button_two_bg_color__hover_enabled=”off”] [/et_pb_contact_field][et_pb_contact_field field_id=”Phone” field_title=”Phone No.” fullwidth_field=”on” _builder_version=”4.16″ form_field_font=”||||” use_border_color=”off” global_colors_info=”{}” button_text_size__hover_enabled=”off” button_one_text_size__hover_enabled=”off” button_two_text_size__hover_enabled=”off” button_text_color__hover_enabled=”off” button_one_text_color__hover_enabled=”off” button_two_text_color__hover_enabled=”off” button_border_width__hover_enabled=”off” button_one_border_width__hover_enabled=”off” button_two_border_width__hover_enabled=”off” button_border_color__hover_enabled=”off” button_one_border_color__hover_enabled=”off” button_two_border_color__hover_enabled=”off” button_border_radius__hover_enabled=”off” button_one_border_radius__hover_enabled=”off” button_two_border_radius__hover_enabled=”off” button_letter_spacing__hover_enabled=”off” button_one_letter_spacing__hover_enabled=”off” button_two_letter_spacing__hover_enabled=”off” button_bg_color__hover_enabled=”off” button_one_bg_color__hover_enabled=”off” button_two_bg_color__hover_enabled=”off”] [/et_pb_contact_field][et_pb_contact_field field_id=”Message” field_title=”Message” field_type=”text” _builder_version=”4.16″ global_colors_info=”{}” button_text_size__hover_enabled=”off” button_one_text_size__hover_enabled=”off” button_two_text_size__hover_enabled=”off” button_text_color__hover_enabled=”off” button_one_text_color__hover_enabled=”off” button_two_text_color__hover_enabled=”off” button_border_width__hover_enabled=”off” button_one_border_width__hover_enabled=”off” button_two_border_width__hover_enabled=”off” button_border_color__hover_enabled=”off” button_one_border_color__hover_enabled=”off” button_two_border_color__hover_enabled=”off” button_border_radius__hover_enabled=”off” button_one_border_radius__hover_enabled=”off” button_two_border_radius__hover_enabled=”off” button_letter_spacing__hover_enabled=”off” button_one_letter_spacing__hover_enabled=”off” button_two_letter_spacing__hover_enabled=”off” button_bg_color__hover_enabled=”off” button_one_bg_color__hover_enabled=”off” button_two_bg_color__hover_enabled=”off”]
 * [/et_pb_contact_field]
 * [/et_pb_contact_form]
 */

function divi2gb_generate_contact_form( $attributes ) {
    $html = 'Contact form goes here';
    $html = '<!-- wp:oik/contact-form /-->';
    return $html;
}

/*
 * [et_pb_map address=”Trym Lodge, 1 Henbury Rd, Avon, Bristol BS9 3HQ, UK” zoom_level=”6″ address_lat=”51.16249110113337″ address_lng=”-1.8937478437500177″ mouse_wheel=”off” _builder_version=”4.16″ z_index_tablet=”500″ global_colors_info=”{}”][et_pb_map_pin title=”Rathmore – Bristol” pin_address=”Trym Lodge, 1 Henbury Rd, Avon, Bristol BS9 3HQ, UK” pin_address_lat=”51.4954313″ pin_address_lng=”-2.6188455000000204″ _builder_version=”3.21.4″ global_colors_info=”{}”]

Trym Lodge
1 Henbury Road
Westbury-on-Trym
Bristol
BS9 3HQ

[/et_pb_map_pin][et_pb_map_pin title=”Rathmore – Liphook” pin_address=”The Chase, Chiltlee Manor, Haslemere Road, Liphook GU30 7AZ, United Kingdom” pin_address_lat=”51.076403″ pin_address_lng=”-0.7988780000000588″ _builder_version=”3.21.4″ global_colors_info=”{}”]

The Chase
Chiltlee Manor
Haslemere Road
Liphook
GU30 7AZ

[/et_pb_map_pin][/et_pb_map]

The Google Maps block needs to support markers - et_pb_map_pin
and zoom level and address lat and long.
 */
function divi2gb_generate_map( $attributes ) {
    $html = "Google Maps Map goes here";
    $html = '<!-- wp:oik/googlemap -->';
    $html .= '<div><h3>Map</h3>[bw_show_googlemap]</div>';
    $html .= '<!-- /wp:oik/googlemap -->';
    return $html;
}

/* [et_pb_team_member
            name="Will Molony DipPFS"
            position="Chairman "
            image_url="https://rathmorefinancial.com/wp-content/uploads/2020/08/Will-Molony.jpg"
            admin_label="Will Molony" _builder_version="4.5.6" hover_enabled="0" title_text="Will Molony"]
            */
function divi2gb_generate_team_member( $attributes, $text ) {
    $name = divi2gb_extract_attribute( 'name', $attributes);
    $position = divi2gb_extract_attribute( 'position', $attributes);
    $image_url = divi2gb_extract_attribute( 'image_url', $attributes);
    $alt = divi2gb_extract_attribute( 'alt', $attributes);
    $html = null;
    if ( $image_url ) {
        $html .= divi2gb_generate_image($image_url, $name, $position);
    }
    $html .= "<h3>$name</h3>";
    $html .= "<p><strong>$position</strong></p>";
    $html .= $text;
    return $html;

}

/*
 * [et_pb_fullwidth_header
 * content_max_width="none"
 * admin_label="Fullwidth Header"
 * _builder_version="3.16"
 * background_color="rgba(255, 255, 255, 0)"
 * background_image="https://rathmorefinancial.com/wp-content/uploads/2017/04/Rathmore-0080-desk-1.jpg"
 * background_layout="light"
 * background_url="https://rathmorefinancial.com/wp-content/uploads/2017/04/Rathmore-0080-desk-1.jpg"
 * button_one_letter_spacing_hover="0" button_two_letter_spacing_hover="0" button_one_text_size__hover_enabled="off" button_two_text_size__hover_enabled="off" button_one_text_color__hover_enabled="off" button_two_text_color__hover_enabled="off" button_one_border_width__hover_enabled="off" button_two_border_width__hover_enabled="off" button_one_border_color__hover_enabled="off" button_two_border_color__hover_enabled="off" button_one_border_radius__hover_enabled="off" button_two_border_radius__hover_enabled="off" button_one_letter_spacing__hover_enabled="on" button_one_letter_spacing__hover="0"
 * button_two_letter_spacing__hover_enabled="on" button_two_letter_spacing__hover="0" button_one_bg_color__hover_enabled="off" button_two_bg_color__hover_enabled="off"][/et_pb_fullwidth_header]
 */
function divi2gb_generate_fullwidth_header( $attributes ) {
    $background_image = divi2gb_extract_attribute( 'background_image', $attributes);
    $html = divi2gb_generate_image($background_image, null, null);
    return $html;
}


divi2gb();