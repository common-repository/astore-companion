<?php

add_filter( 'cmb_meta_boxes', 'acmb_page_metaboxes' );
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function acmb_page_metaboxes( array $meta_boxes ) {

	$prefix = '_acmb_';

	/**
	 * Page options
	 */
	$meta_boxes['theme_options_metabox'] = array(
		'id'         => 'theme_options_metabox',
		'title'      => __( 'Page Options', 'astore-companion' ),
		'pages'      => array( 'page', 'post' ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'fields'     => array(
			
			array(
				'name'    => __( 'Background Color', 'astore-companion' ),
				'desc'    => '',
				'id'      => $prefix . 'bg_color',
				'type'    => 'colorpicker',
				'default' => '#ffffff'
			),
			array(
				'name' => __( 'Background Image', 'astore-companion' ),
				'desc' => __( 'Upload an image or enter a URL.', 'astore-companion' ),
				'id'   => $prefix . 'bg_image',
				'type' => 'file',
			),
			array(
				'name'    => __( 'Sidebar', 'astore-companion' ),
				'desc'    => '',
				'id'      => $prefix . 'sidebar',
				'type'    => 'radio',
				'default' => '',
				'options' => array(
					'' => __( 'Default', 'astore-companion' ),
					'left' => __( 'Left Sidebar', 'astore-companion' ),
					'right' => __( 'Right Sidebar', 'astore-companion' ),
					'no' => __( 'No Sidebar', 'astore-companion' ),
				),
			),
			array(
				'name'    => __( 'Content Before Sidebar', 'astore-companion' ),
				'desc'    => '',
				'id'      => $prefix . 'before_sidebar',
				'type'    => 'textarea',
				'default' => ''
			),
			
			
			array(
				'name'    => __( 'Content After Sidebar', 'astore-companion' ),
				'desc'    => '',
				'id'      => $prefix . 'after_sidebar',
				'type'    => 'textarea',
				'default' => ''
			),
			

			
		)
	);

	return $meta_boxes;
}




add_action( 'init', 'acmb_initialize_cmb_meta_boxes', 9999 );
/**
 * Initialize the metabox class.
 */
function acmb_initialize_cmb_meta_boxes() {

	if ( ! class_exists( 'cmb_Meta_Box' ) )
		require_once 'init.php';

}
