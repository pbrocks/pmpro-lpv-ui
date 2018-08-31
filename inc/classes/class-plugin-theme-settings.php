<?php

// namespace PMPro_LPV_UI\inc\classes;
defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class Plugin_or_Theme_Settings {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'plugin_theme_init' ) );
	}
	public function add_admin_menu() {
		add_dashboard_page( 'Plugin or Theme Settings', 'Plugin or Theme Settings', 'manage_options', 'plugin-theme-settings.php', array( $this, 'options_page' ) );
	}

	public function plugin_theme_init() {
		register_setting( 'plugin_theme_settings', 'plugin_theme' );

		add_settings_section(
			'plugin_theme_section',
			__( 'This is the  area given for a settings page', 'plugin-theme-settings' ),
			array( $this, 'plugin_theme_section_callback' ),
			'plugin_theme_settings'
		);

		add_settings_field(
			'select_field_0',
			__( 'Describe the dropdown select in this field', 'plugin-theme-settings' ),
			array( $this, 'select_field_0_render' ),
			'plugin_theme_settings',
			'plugin_theme_section'
		);

		add_settings_field(
			'text_field_0',
			__( 'Describe the text field in this field', 'plugin-theme-settings' ),
			array( $this, 'text_field_0_render' ),
			'plugin_theme_settings',
			'plugin_theme_section'
		);

		add_settings_field(
			'checkbox_field_0',
			__( 'Describe the checkbox in this field', 'plugin-theme-settings' ),
			array( $this, 'checkbox_field_0_render' ),
			'plugin_theme_settings',
			'plugin_theme_section'
		);

		add_settings_field(
			'textarea_field_0',
			__( 'Describe the textarea in this field', 'plugin-theme-settings' ),
			array( $this, 'textarea_field_0_render' ),
			'plugin_theme_settings',
			'plugin_theme_section'
		);

		add_settings_field(
			'radio_field_0',
			__( 'Describe the radio button select in this field', 'plugin-theme-settings' ),
			array( $this, 'radio_field_0_render' ),
			'plugin_theme_settings',
			'plugin_theme_section'
		);
	}

	public function select_field_0_render() {
		$options = get_option( 'plugin_theme' );
		?>
		<select name='plugin_theme[select_field_0]'>
		<option value='1' <?php selected( $options['select_field_0'], 1 ); ?>>Option 1</option>
		<option value='2' <?php selected( $options['select_field_0'], 2 ); ?>>Option 2</option>
		<option value='3' <?php selected( $options['select_field_0'], 3 ); ?>>Option 3</option>
		<option value='4' <?php selected( $options['select_field_0'], 4 ); ?>>Option 4</option>
		</select>

		<?php
	}

	public function text_field_0_render() {
		$options = get_option( 'plugin_theme' );
		?>
		<input type='text' name='plugin_theme[text_field_0]' value='<?php echo $options['text_field_0']; ?>'>
		<?php
	}

	public function checkbox_field_0_render() {
		$options = get_option( 'plugin_theme' );
		?>
		<input type='checkbox' name='plugin_theme[checkbox_field_0]' <?php checked( $options['checkbox_field_0'], 1 ); ?> value='1'>
		<?php
	}

	public function textarea_field_0_render() {
		$options = get_option( 'plugin_theme' );
		?>
		<textarea cols='40' rows='5' name='plugin_theme[textarea_field_0]'
		><?php echo esc_html( $options['textarea_field_0'] ); ?></textarea>
		<?php
	}

	public function radio_field_0_render() {
		$options = get_option( 'plugin_theme' );
		?>
		<label>Radio 1
		<input type='radio' name='plugin_theme[radio_field_0]' <?php checked( $options['radio_field_0'], 1 ); ?> value='1'></label>
		<br>
		<label>Radio 2
		<input type='radio' name='plugin_theme[radio_field_0]' <?php checked( $options['radio_field_0'], 2 ); ?> value='2'></label>
		<?php
	}

	public function plugin_theme_section_callback() {
		echo '<p class="description">' . __( 'This description is found in this function <b>' . __FUNCTION__ . ' </b>and provides an paragraph-type area below the headings and above the individual settings.', 'plugin-theme-settings' ) . '</p>';
	}

	public function options_page() {
		?>
		<div style="wrap">
			<h2>Plugin or Theme Settings title found in: <?php echo __FUNCTION__; ?> function</h2>
			<form action='options.php' method='post'>
				<?php
					settings_fields( 'plugin_theme_settings' );
					do_settings_sections( 'plugin_theme_settings' );
					submit_button();
				?>
			</form>
		</div>
		<?php
		$this->plugin_theme_render();
		$this->plugin_theme_todo();
	}

	public function plugin_theme_render() {
		$options = get_option( 'plugin_theme' );
		echo '<pre>';
		print_r( $options );
		echo '</pre>';
	}

	public function plugin_theme_todo() {
		echo '<h4>' . __FUNCTION__ . '</h4>';
		echo '<li>Should make sure the settings get deleted if uninstalled</li>';
	}
}
new Plugin_or_Theme_Settings();
