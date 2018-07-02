<?php

namespace PMPro_LPV_UI\inc\classes;

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class PMPro_LPV_Customizer {

	public static function init() {
		add_action( 'customize_register', array( __CLASS__, 'engage_the_customizer' ) );
		// add_action( 'wp_enqueue_scripts', array( __CLASS__, 'customizer_enqueue' ) );
		// add_action( 'admin_enqueue_scripts', array( __CLASS__, 'customizer_enqueue' ) );
		// add_action( 'customize_controls_init', array( __CLASS__, 'set_customizer_preview_url' ) );
	}

	/**
	 * Customizer manager demo
	 *
	 * @param  WP_Customizer_Manager $pmpro_manager
	 * @return void
	 */
	public static function engage_the_customizer( $pmpro_manager ) {
		// self::pmpro_panel( $pmpro_manager );
		self::pmpro_section( $pmpro_manager );
	}

	public static function customizer_enqueue() {
		wp_enqueue_style( 'customizer-section', plugins_url( '../css/customizer-section.css', __FILE__ ) );
	}


	/**
	 * [engage_customizer description]
	 *
	 * @param [type] $pmpro_manager [description]
	 * @return [type]             [description]
	 */
	private static function pmpro_panel( $pmpro_manager ) {
		$pmpro_manager->add_panel(
			'pmpro_customizer_panel',
			array(
				'priority' => 10,
				'capability' => 'edit_theme_options',
				'description' => 'Wnat to switch pages via javascript',
				'title' => __( 'PMPro Admin Panel', 'pmpro-customizer' ),
			)
		);
	}

	/**
	 * The pmpro_section function adds a new section
	 * to the Customizer to display the settings and
	 * controls that we build.
	 *
	 * @param  [type] $pmpro_manager [description]
	 * @return [type]             [description]
	 */
	private static function pmpro_section( $pmpro_manager ) {
		$pmpro_manager->add_section(
			'pmpro_section',
			array(
				'title'          => 'PMPro Controls',
				'priority'       => 9,
				'panel'          => 'pmpro_customizer_panel',
				'description' => 'This is a description of this text setting in the PMPro Customizer Controls section of the PMPro panel',
			)
		);

		$pmpro_manager->add_setting(
			'page_comment_toggle',
			array(
				'default' => 1,
			)
		);

		$pmpro_manager->add_control(
			new Soderland_Toggle_Control(
				$pmpro_manager,
				'page_comment_toggle',
				array(
					'label'     => __( 'Show PMPro Controls', 'pmpro-customizer' ),
					'section'   => 'pmpro_section',
					'priority'  => 10,
					'type'      => 'ios',
				)
			)
		);

		$pmpro_manager->add_setting(
			'pmpro[the_header]',
			array(
				'default' => 'header-text default text',
				'type' => 'option',
				'transport' => 'refresh', // refresh (default), postMessage
			// 'capability' => 'edit_theme_options',
			// 'sanitize_callback' => 'sanitize_key'
			)
		);

		$pmpro_manager->add_control(
			'pmpro[the_header]',
			array(
				'section'   => 'pmpro_section',
				'type'   => 'text', // text (default), checkbox, radio, select, dropdown-pages
				'label'       => 'Change Header Text',
				'settings'    => 'pmpro[the_header]',
				'description' => 'Description of this text input setting in ' . __FUNCTION__ . ' for Header Text',
			)
		);

		$pmpro_manager->add_setting(
			'pmpro_footer_text',
			array(
				'default' => 'footer-text default text',
				'type' => 'option',
				'transport' => 'refresh', // refresh (default), postMessage
			// 'capability' => 'edit_theme_options',
			// 'sanitize_callback' => 'sanitize_key'
			)
		);

		$pmpro_manager->add_control(
			'pmpro_footer_text',
			array(
				'section'   => 'pmpro_section',
				'type'   => 'text', // text (default), checkbox, radio, select, dropdown-pages
				'label'       => 'Change Footer Text',
				'settings'    => 'pmpro_footer_text',
				'description' => 'Description of this text input setting in ' . __FUNCTION__ . ' for Default Footer Text',
			)
		);

		/**
		 * Radio control
		 */
		$pmpro_manager->add_setting(
			'menu_radio',
			array(
				'default'        => '2',
			)
		);

		$pmpro_manager->add_control(
			'menu_radio',
			array(
				'section'     => 'pmpro_section',
				'type'        => 'radio',
				'label'       => 'Menu Text Radio Buttons',
				'description' => 'Description of this radio setting in ' . __FUNCTION__,
				'choices'     => array(
					'1' => 'left',
					'2' => 'center',
					'3' => 'right',
				),
				'priority'    => 11,
			)
		);
	}
}
