<?php

namespace PMPro_LPV_UI\inc\classes;

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class PMPro_LPV_Customizer {
	public static function init() {
		add_action( 'customize_register', array( __CLASS__, 'engage_the_customizer' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'customizer_enqueue' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'customizer_enqueue' ) );
		// add_action( 'customize_controls_init', array( __CLASS__, 'set_customizer_preview_url' ) );
	}

	/**
	 * Customizer manager demo
	 *
	 * @param  WP_Customizer_Manager $pmpro_manager
	 * @return void
	 */
	public static function engage_the_customizer( $pmpro_manager ) {
		self::pmpro_panel( $pmpro_manager );
		self::pmpro_section( $pmpro_manager );
		self::pmpro_text_changes( $pmpro_manager );
		self::pmpro_customizer_manager( $pmpro_manager );
		// self::create_customizer_dev_page( $pmpro_manager );
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
			'pmpro_customizer_panel', array(
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
			'pmpro_section', array(
				'title'          => 'PMPro Controls',
				'priority'       => 9,
				'panel'          => 'pmpro_customizer_panel',
				'description' => 'This is a description of this text setting in the PMPro Customizer Controls section of the PMPro panel',
			)
		);

		$pmpro_manager->add_setting(
			'page_comment_toggle', array(
				'default' => 1,
			)
		);

		$pmpro_manager->add_control(
			new Soderland_Toggle_Control(
				$pmpro_manager,
				'page_comment_toggle', array(
					'label'     => __( 'Show PMPro Controls', 'pmpro-customizer' ),
					'section'   => 'pmpro_section',
					'priority'  => 10,
					'type'      => 'ios',
				)
			)
		);

		$pmpro_manager->add_setting(
			'pmpro[the_header]', array(
				'default' => 'header-text default text',
				'type' => 'option',
				'transport' => 'refresh', // refresh (default), postMessage
			// 'capability' => 'edit_theme_options',
			// 'sanitize_callback' => 'sanitize_key'
			)
		);

		$pmpro_manager->add_control(
			'pmpro[the_header]', array(
				'section'   => 'pmpro_section',
				'type'   => 'text', // text (default), checkbox, radio, select, dropdown-pages
			'label'       => 'Change Header Text',
			'settings'    => 'pmpro[the_header]',
			'description' => 'Description of this text input setting in ' . __FUNCTION__ . ' for Header Text',
			)
		);

		$pmpro_manager->add_setting(
			'pmpro_footer_text', array(
				'default' => 'footer-text default text',
				'type' => 'option',
				'transport' => 'refresh', // refresh (default), postMessage
			// 'capability' => 'edit_theme_options',
			// 'sanitize_callback' => 'sanitize_key'
			)
		);

		$pmpro_manager->add_control(
			'pmpro_footer_text', array(
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
			'menu_radio', array(
				'default'        => '2',
			)
		);

		$pmpro_manager->add_control(
			'menu_radio', array(
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

	/**
	 * The pmpro_section function adds a new section
	 * to the Customizer to display the settings and
	 * controls that we build.
	 *
	 * @param  [type] $pmpro_manager [description]
	 * @return [type]             [description]
	 */
	private static function pmpro_text_changes( $pmpro_manager ) {
		$pmpro_manager->add_section(
			'pmpro_text_changes', array(
				'title'          => 'PMPro Text Changes',
				'priority'       => 19,
				'panel'          => 'pmpro_customizer_panel',
				'description' => 'PMPro Customizer Text Changes makles it easier for you to customize the text from the plugin and add ons.',
			)
		);

		$pmpro_manager->add_setting(
			'page_text_toggle', array(
				'default' => 1,
			)
		);

		$pmpro_manager->add_control(
			new Soderland_Toggle_Control(
				$pmpro_manager,
				'page_text_toggle', array(
					'label'     => __( 'Make PMPro Changes', 'pmpro-customizer' ),
					'section'   => 'pmpro_text_changes',
					'priority'  => 10,
					'type'      => 'ios',
				)
			)
		);
		$pmpro_manager->add_setting(
			'pmpro_text_change_num', array(
				'default' => 4,
				'type' => 'option',
				'transport' => 'refresh', // refresh (default), postMessage
			// 'capability' => 'edit_theme_options',
			// 'sanitize_callback' => 'sanitize_key'
			)
		);

		$pmpro_manager->add_control(
			'pmpro_text_change_num', array(
				'section'   => 'pmpro_text_changes',
				'type'   => 'number', // text (default), checkbox, radio, select, dropdown-pages
			'label'       => 'How many text changes?',
			'settings'    => 'pmpro_text_change_num',
			'description' => ' We\'ll start you off with four.',
			)
		);

		$pmpro_manager->add_setting(
			'pmpro_text_input_1', array(
				'default' => 'What the plugin says',
				'type' => 'option',
				'transport' => 'refresh', // refresh (default), postMessage
			// 'capability' => 'edit_theme_options',
			// 'sanitize_callback' => 'sanitize_key'
			)
		);

		$pmpro_manager->add_control(
			'pmpro_text_input_1', array(
				'section'   => 'pmpro_text_changes',
				'type'   => 'text', // text (default), checkbox, radio, select, dropdown-pages
			'label'       => 'First Text Change Input',
			'settings'    => 'pmpro_text_input_1',
			'description' => 'Text to change in the plugin or an add on',
			)
		);
		$pmpro_manager->add_setting(
			'pmpro_text_output_1', array(
				'default' => 'What you want to say',
				'type' => 'option',
				'transport' => 'refresh', // refresh (default), postMessage
			// 'capability' => 'edit_theme_options',
			// 'sanitize_callback' => 'sanitize_key'
			)
		);

		$pmpro_manager->add_control(
			'pmpro_text_output_1', array(
				'section'   => 'pmpro_text_changes',
				'type'   => 'text', // text (default), checkbox, radio, select, dropdown-pages
			'label'       => 'First Text Change Output',
			'settings'    => 'pmpro_text_output_1',
			'description' => 'What you want the text to say:',
			)
		);
		$pmpro_manager->add_setting(
			'pmpro_text_domain_1', array(
				'default' => 'paid-memberships-pro',
				'type' => 'option',
				'transport' => 'refresh', // refresh (default), postMessage
			// 'capability' => 'edit_theme_options',
			// 'sanitize_callback' => 'sanitize_key'
			)
		);

		$pmpro_manager->add_control(
			'pmpro_text_domain_1', array(
				'section'   => 'pmpro_text_changes',
				'type'   => 'text', // text (default), checkbox, radio, select, dropdown-pages
			'label'       => 'Where is the translatable text?',
			'settings'    => 'pmpro_text_domain_1',
			'description' => 'Domain of this text change, either the main plugin or an add on:',
			)
		);

		$pmpro_manager->add_setting(
			'pmpro_text_input_2', array(
				'default' => 'What the plugin says',
				'type' => 'option',
				'transport' => 'refresh', // refresh (default), postMessage
			// 'capability' => 'edit_theme_options',
			// 'sanitize_callback' => 'sanitize_key'
			)
		);

		$pmpro_manager->add_control(
			'pmpro_text_input_2', array(
				'section'   => 'pmpro_text_changes',
				'type'   => 'text', // text (default), checkbox, radio, select, dropdown-pages
			'label'       => 'Second Text Change Input',
			'settings'    => 'pmpro_text_input_2',
			'description' => 'Text to change in the plugin or an add on',
			)
		);
		$pmpro_manager->add_setting(
			'pmpro_text_output_2', array(
				'default' => 'What you want to say',
				'type' => 'option',
				'transport' => 'refresh', // refresh (default), postMessage
			// 'capability' => 'edit_theme_options',
			// 'sanitize_callback' => 'sanitize_key'
			)
		);

		$pmpro_manager->add_control(
			'pmpro_text_output_2', array(
				'section'   => 'pmpro_text_changes',
				'type'   => 'text', // text (default), checkbox, radio, select, dropdown-pages
			'label'       => 'Second Text Change Output',
			'settings'    => 'pmpro_text_output_2',
			'description' => 'What you want the text to say:',
			)
		);
		$pmpro_manager->add_setting(
			'pmpro_text_domain_2', array(
				'default' => 'paid-memberships-pro',
				'type' => 'option',
				'transport' => 'refresh', // refresh (default), postMessage
			// 'capability' => 'edit_theme_options',
			// 'sanitize_callback' => 'sanitize_key'
			)
		);

		$pmpro_manager->add_control(
			'pmpro_text_domain_2', array(
				'section'   => 'pmpro_text_changes',
				'type'   => 'text', // text (default), checkbox, radio, select, dropdown-pages
			'label'       => 'Where is the translatable text?',
			'settings'    => 'pmpro_text_domain_2',
			'description' => 'Domain of this text change, either the main plugin or an add on:',
			)
		);
	}

	/**
	 * Customizer manager demo
	 *
	 * @param  WP_Customizer_Manager $pmpro_manager
	 * @return void
	 */
	public static function pmpro_customizer_manager( $pmpro_manager ) {

		$pmpro_manager->add_setting(
			'toggle_setting_1',
			array(
				'default'        => false,
			)
		);

		$pmpro_manager->add_control(
			new Soderland_Toggle_Control(
				$pmpro_manager,
				'toggle_setting_1',
				array(
					'settings'    => 'toggle_setting_1',
					'label'       => __( 'Toggle Setting One', 'pmpro-customizer' ),
					'section'     => 'content_options_section',
					'type'        => 'ios',
					'description' => __( 'Configure advanced settings in ' . __FILE__ , 'pmpro-customizer' ),
				)
			)
		);

		$pmpro_manager->add_section(
			'content_options_section' , array(
				'title'      => __( 'PMPro Options', 'pmpro-customizer' ),
				'priority'   => 10,
				'panel'          => 'pmpro_customizer_panel',
				'description'       => __( 'Configure PMPro options for ' . esc_url_raw( home_url() ), 'pmpro-customizer' ),
			)
		);

		$pmpro_manager->add_section(
			'pmpro_redirects_section', array(
				'priority' => 10,
				'capability' => 'edit_theme_options',
				'title' => __( 'PMPro Redirects', 'pmpro-customizer' ),
				'description' => __( '<h4>Turn on Redirect information.</h4>', 'pmpro-customizer' ),
				'panel' => 'pmpro_customizer_panel',
			)
		);

		$pmpro_manager->add_setting(
			'pmpro_redirects_setting',
			array(
				'default'    => false,
			)
		);
		$pmpro_manager->add_control(
			new Soderland_Toggle_Control(
				$pmpro_manager,
				'pmpro_redirects_setting', array(
					'settings'    => 'pmpro_redirects_setting',
					'label'       => __( 'PMPro Redirects' ),
					'description' => 'Adds a button in upper right corner of front end pages to toggle diagnostic infomation.',
					'section'     => 'pmpro_redirects_section',
					'type'        => 'ios',
				)
			)
		);
	}

	public static function return_something() {
		return 'Some sort of data.';
	}

	public static function create_customizer_dev_page( $pmpro_manager ) {

		$customizer_dev_page = 'Customizer Dev Page';
		$customizer_dev_page_content = self::return_something();
		$author_id = get_current_user();

		$check_page = get_page_by_title( $customizer_dev_page );
		if ( null == $check_page ) {
			$my_post = array(
				'post_title'    => wp_strip_all_tags( $customizer_dev_page ),
				// $slug = 'wordpress-post-created-with-code';
				'post_content'  => $customizer_dev_page_content,
				'post_status'   => 'publish',
				'post_type'     => 'page',
				'post_author'   => $author_id,
			// 'post_category' => array( 8,39 ),
			);

			wp_insert_post( $my_post );
		}
	}


	public static function set_customizer_preview_url() {
		global $wp_customize;
		// if ( ! isset( $_GET['url'] ) ) {
			// $wp_customize->set_preview_url( get_permalink( get_page_by_title( 'Launchpad' ) ) );
			$wp_customize->set_preview_url( '/customizer-dev-page/' );
		// }
	}

	/**
	 * A section to show how you use the default customizer controls in WordPress
	 *
	 * @param  Obj $pmpro_manager - WP Manager
	 *
	 * @return Void
	 */
	private static function sanitize_select_slug( $input, $setting ) {

		// Ensure input is a slug.
		$input = sanitize_key( $input );

		// Get list of choices from the control associated with the setting.
		$choices = $setting->manager->get_control( $setting->id )->choices;

		// If the input is a valid key, return it; otherwise, return the default.
		return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
	}
}
