<?php

require_once get_template_directory() . '/dw-importer/lib/activation.php';
if( ! class_exists( 'Widget_Data' ) ) {
	require_once get_template_directory(). '/dw-importer/lib/class-widget-data.php';
}

class class_widget extends Widget_Data {
	function checkout(){

	}
}

/**
* 
*/
// class ClassName extends AnotherClass
// {
	
// 	function __construct(argument)
// 	{
// 		# code...
// 	}
// }
// add_action( 'admin_init', 'dw_theme_check_clean_installation' );
// function dw_theme_check_clean_installation(){
//     add_action( 'admin_notices', 'dw_theme_reminder' );
// }

// if ( ! function_exists( 'dw_theme_reminder' ) ){
//     function dw_theme_reminder(){
//         global $pagenow;
//         $url = admin_url('images/wpspin_light.gif');
//         $check_import = get_option( 'dw_imported_xml' );
//         if ( 'themes.php' === $pagenow && $check_import != 'true' ){
//             printf( '<div class="updated"><div class="notice_import"><p>This is a fresh installation of %1$s theme. Don\'t forget to go to <a class="dw_import" href="#">import demo content</a> to set it up. If you don\'t want import demo content <a href="#" class="check_import">click here</a>  </p></div><div class="spinner" style="display:none;background: url(\'%2$s\') no-repeat;background-size: 16px 16px;float:left"></div><div class="import_message"></div></div>', get_current_theme(), $url );
//         }
//     }
// } 

if ( ! function_exists( 'dw_get_image_size' ) ){
    function dw_get_image_size(){
    	global $_wp_additional_image_sizes;
    	
    	if( is_array( $_wp_additional_image_sizes ) ) {
    		foreach( $_wp_additional_image_sizes as $key => $value ) {
				$image_size[$key]['width'] = $value['width'];
				$image_size[$key]['height'] = $value['height'];
			}
    	}
       	
		$image_size['large']['width'] = get_option('large_size_w');
		$image_size['large']['height'] = get_option('large_size_h');	
		$image_size['medium']['width'] = get_option('medium_size_w');
		$image_size['medium']['height'] = get_option('medium_size_h');
		$image_size['thumbnail']['width'] = get_option('thumbnail_size_w');
		$image_size['thumbnail']['height'] = get_option('thumbnail_size_h');

		return $image_size;
    }
} 




if( ! function_exists('dw_timeline_pro_admin_scripts' ) ){
    // Enqueue scripts and styles for back-end panel
    function dw_timeline_pro_admin_scripts() {
        global $pagenow;
        if( 'themes.php' == $pagenow ) {
             wp_enqueue_script('jquery');
             wp_enqueue_script('dw-import', get_template_directory_uri() . '/dw-importer/js/dw-import.js', array('jquery') );
        }
    }
    add_action( 'admin_enqueue_scripts', 'dw_timeline_pro_admin_scripts' );
}

add_action( 'wp_ajax_dw_check_import', 'dw_check_import' );
function dw_check_import() {
	add_option( 'dw_imported_xml', 'true');
	wp_send_json_success( $data );
}


add_action( 'wp_ajax_dw_import_data', 'dw_import_data' );
function dw_import_data() {
	global $wpdb, $pagenow; 
	
	$class_widget_data = new class_widget();
	// $class_widget_data->getMethod('clear_widgets')->setAccessible(true);
	
	$widgets_filepath = get_template_directory() ."/dummy-content/dummy_widget.json" ;
	$file_contents = file_get_contents( $widgets_filepath );
	$json_data = json_decode( $file_contents, true );

	foreach ($json_data[0] as $sidebar_name => $widget_list ){
		foreach ( $widget_list as $widget ) {
			$widget_type = trim( substr( $widget, 0, strrpos( $widget, '-' ) ) );
			$widget_type_index = trim( substr( $widget, strrpos( $widget, '-' ) + 1 ) );
			$widget_options = get_option( 'widget_' . $widget_type );
			$widget_title = isset( $widget_options[$widget_type_index]['title'] ) ? $widget_options[$widget_type_index]['title'] : $widget_type_index;

			$widgets[$widget_type][$widget_type_index] = 'on';
			
		}
	}

	$import_file = $widgets_filepath;
	if( empty($widgets) || empty($import_file) ){
		wp_send_json_error('No widget data posted to import');
	}

	$class_widget_data->clear_widgets();
	$json_data = file_get_contents( $import_file );
	$json_data = json_decode( $json_data, true );
	$sidebar_data = $json_data[0];
	$widget_data = $json_data[1];
	foreach ( $sidebar_data as $title => $sidebar ) {
		$count = count( $sidebar );
		for ( $i = 0; $i < $count; $i++ ) {
			$widget = array( );
			$widget['type'] = trim( substr( $sidebar[$i], 0, strrpos( $sidebar[$i], '-' ) ) );
			$widget['type-index'] = trim( substr( $sidebar[$i], strrpos( $sidebar[$i], '-' ) + 1 ) );
			if ( !isset( $widgets[$widget['type']][$widget['type-index']] ) ) {
				unset( $sidebar_data[$title][$i] );
			}
		}
		$sidebar_data[$title] = array_values( $sidebar_data[$title] );
	}

	foreach ( $widgets as $widget_title => $widget_value ) {
		foreach ( $widget_value as $widget_key => $widget_value ) {
			$widgets[$widget_title][$widget_key] = $widget_data[$widget_title][$widget_key];
		}
	}
	$sidebar_data = array( array_filter( $sidebar_data ), $widgets );
	$response['id'] = ( $class_widget_data->parse_import_data( $sidebar_data ) ) ? true : new WP_Error( 'widget_import_submit', 'Unknown Error' );
	// $response = new WP_Ajax_Response( $response );
	// $response->send();



	require_once ABSPATH . 'wp-admin/includes/import.php';

	if ( ! class_exists( 'WP_Importer' ) ) {
		$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
		if ( file_exists( $class_wp_importer ) )
		{
			require $class_wp_importer;
		}
	}

	if ( ! class_exists( 'WP_Import' ) ) {
		if ( ! defined( 'WP_LOAD_IMPORTERS' ) && 'admin.php' != $pagenow ) {
			$class_wp_importer = get_template_directory() ."/dw-importer/lib/wordpress-importer.php";
			if ( file_exists( $class_wp_importer ) )
				require $class_wp_importer;
		}
	}

	// var_dump(class_exists( 'WP_Import' ));die();
	if ( class_exists( 'WP_Import' ) ) { 
		add_option( 'dw_imported_xml', 'true'); 
		$import_filepath = get_template_directory() ."/dummy-content/dummy_content.xml" ; // Get the xml file from directory 
		include_once('lib/dw-import.php');

		$wp_import = new dw_import();
		$wp_import->image_size = dw_get_image_size();
		$wp_import->fetch_attachments = true;
		$wp_import->import($import_filepath);

		$wp_import->check();

	}
		die(); // this is required to return a proper result
}
?>