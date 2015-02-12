<?php

function clear_widgets() {
	$sidebars = wp_get_sidebars_widgets();
	$inactive = isset( $sidebars['wp_inactive_widgets']) ? $sidebars['wp_inactive_widgets'] : array();
	unset( $sidebars['wp_inactive_widgets'] );
	foreach ( $sidebars as $sidebar => $widgets ) {
		if ( is_array( $widgets ) ) {
			$inactive = array_merge( $inactive, $widgets );
		}
		$sidebars[$sidebar] = array();
	}

	$sidebars['wp_inactive_widgets'] = $inactive;
	wp_set_sidebars_widgets( $sidebars );
}

function get_new_widget_name( $widget_name, $widget_index ) {
	$current_sidebars = get_option( 'sidebars_widgets' );
	$all_widget_array = array( );
	foreach ( $current_sidebars as $sidebar => $widgets ) {
		if ( !empty( $widgets ) && is_array( $widgets ) && $sidebar != 'wp_inactive_widgets' ) {
			foreach ( $widgets as $widget ) {
				$all_widget_array[] = $widget;
			}
		}
	}
	while ( in_array( $widget_name . '-' . $widget_index, $all_widget_array ) ) {
		$widget_index++;
	}
	$new_widget_name = $widget_name . '-' . $widget_index;
	return $new_widget_name;
}

function parse_import_data( $import_array ) {
	$sidebars_data = $import_array[0];
	$widget_data = $import_array[1];
	$current_sidebars = get_option( 'sidebars_widgets' );
	$new_widgets = array( );
	foreach ( $sidebars_data as $import_sidebar => $import_widgets ) :

		foreach ( $import_widgets as $import_widget ) :
				//if the sidebar exists
			if ( isset( $current_sidebars[$import_sidebar] ) ) :
				$title = trim( substr( $import_widget, 0, strrpos( $import_widget, '-' ) ) );
			$index = trim( substr( $import_widget, strrpos( $import_widget, '-' ) + 1 ) );
			$current_widget_data = get_option( 'widget_' . $title );
			$new_widget_name = get_new_widget_name( $title, $index );
			$new_index = trim( substr( $new_widget_name, strrpos( $new_widget_name, '-' ) + 1 ) );

			if ( !empty( $new_widgets[ $title ] ) && is_array( $new_widgets[$title] ) ) {
				while ( array_key_exists( $new_index, $new_widgets[$title] ) ) {
					$new_index++;
				}
			}
			$current_sidebars[$import_sidebar][] = $title . '-' . $new_index;
			if ( array_key_exists( $title, $new_widgets ) ) {
				$new_widgets[$title][$new_index] = $widget_data[$title][$index];
				$multiwidget = $new_widgets[$title]['_multiwidget'];
				unset( $new_widgets[$title]['_multiwidget'] );
				$new_widgets[$title]['_multiwidget'] = $multiwidget;
			} else {
				$current_widget_data[$new_index] = $widget_data[$title][$index];
				$current_multiwidget = $current_widget_data['_multiwidget'];
				$new_multiwidget = isset($widget_data[$title]['_multiwidget']) ? $widget_data[$title]['_multiwidget'] : false;
				$multiwidget = ($current_multiwidget != $new_multiwidget) ? $current_multiwidget : 1;
				unset( $current_widget_data['_multiwidget'] );
				$current_widget_data['_multiwidget'] = $multiwidget;
				$new_widgets[$title] = $current_widget_data;
			}

			endif;
			endforeach;
			endforeach;

			if ( isset( $new_widgets ) && isset( $current_sidebars ) ) {
				update_option( 'sidebars_widgets', $current_sidebars );
				foreach ( $new_widgets as $title => $content ) {
					$content = apply_filters( 'widget_data_import', $content, $title );
					update_option( 'widget_' . $title, $content );
				}

				return true;
			}

			return false;
}


add_action( 'wp_ajax_dw_check_import', 'dw_check_import' );
function dw_check_import() {
	add_option( 'dw_imported_xml', 'true');
	wp_send_json_success( 'Done' );
}


add_action( 'wp_ajax_dw_import_data', 'dw_import_data' );
function dw_import_data() {
	global $wpdb, $pagenow; 

	$widgets_filepath = get_template_directory() ."/sample-data/widgets.json" ;
	$widgets = array(
		'dw_focus_recent_news' => array(
			'2' => 'on'
			),
		'dw_focus_accordions' => array(
			'3' => 'on'
			),
		'text' => array(
			'2' => 'on',
			'27' => 'on',
			'4'  => 'on',
			'24' => 'on',
			'21' => 'on',
			'22' => 'on',
			'10' => 'on',
			'3'  => 'on', 
			'14' => 'on',
			'16' => 'on',
			'18' => 'on',
			'19' => 'on',
			'28' => 'on',
			'13' => 'on',
			'29' => 'on',
			),
		'dw_twitter' => array(
			'2' => 'on'
			),
		'dw_focus_popular_news' => array(
			'3' => 'on'
			),
		'dw_focus_tabs' => array(
			'7' => 'on',
			'4' => 'on',
			'6' => 'on',
			),

		'dw_focus_news_slider' => array(
			'2' => 'on',
			),
		'dw_focus_news_by_category' => array(
			'4' => 'on',
			'3' => 'on',
			'2' => 'on',
			'5' => 'on',
			),
		'dw_focus_news_headlines' => array(
			'2' => 'on',
			),
		'dw_focus_news_carousel' => array(
			'2' => 'on',
			),
		'nav_menu' => array(
			'2' => 'on',
			'3' => 'on',
			'4' => 'on',
			'5' => 'on',
			'7' => 'on',
			'6' => 'on',
			),
		);
	$import_file = $widgets_filepath;
	if( empty($widgets) || empty($import_file) ){
		wp_send_json_error('No widget data posted to import');
	}

	clear_widgets();
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
	$response['id'] = ( parse_import_data( $sidebar_data ) ) ? true : new WP_Error( 'widget_import_submit', 'Unknown Error' );
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
			$class_wp_importer = get_template_directory() ."/inc/wordpress-importer.php";
			if ( file_exists( $class_wp_importer ) )
				require $class_wp_importer;
		}
	}

	// var_dump(class_exists( 'WP_Import' ));die();
	if ( class_exists( 'WP_Import' ) ) { 
		add_option( 'dw_imported_xml', 'true'); 
		$import_filepath = get_template_directory() ."/sample-data/demo.xml" ; // Get the xml file from directory 
		include_once('dw-import.php');

		$wp_import = new dw_import();
		$wp_import->fetch_attachments = true;
		$wp_import->import($import_filepath);

		$wp_import->check();

	}
		die(); // this is required to return a proper result
}
?>