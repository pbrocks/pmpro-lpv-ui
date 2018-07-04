<?php
defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );
if ( class_exists( 'PMPro_LPV_Settings' ) ) {
	new PMPro_LPV_Settings();
}

class PMPro_LPV_Settings {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'pmpro_lpv_settings_init' ) );
	}
	public static function add_admin_menu() {

		add_options_page( 'PMPro LPV Settings', 'PMPro LPV Settings', 'manage_options', 'pmpro-lpv-settings.php', array( __CLASS__, 'options_page' ) );

	}

	public static function pmpro_lpv_settings_init() {

		register_setting( 'pmpro_lpv_settings', 'pmpro_lpv_settings' );

		add_settings_section(
			'pmpro_lpv_section',
			__( 'This is the description area given for a settings page', 'pmpro-lpv-settings' ),
			array( __CLASS__, 'pmpro_lpv_settings_section_callback' ),
			'pmpro_lpv_settings'
		);

		add_settings_field(
			'select_field_0',
			__( 'Describe the dropdown select in this field', 'pmpro-lpv-settings' ),
			array( __CLASS__, 'select_field_0_render' ),
			'pmpro_lpv_settings',
			'pmpro_lpv_section'
		);

		add_settings_field(
			'text_field_0',
			__( 'Describe the text field in this field', 'pmpro-lpv-settings' ),
			array( __CLASS__, 'text_field_0_render' ),
			'pmpro_lpv_settings',
			'pmpro_lpv_section'
		);

		add_settings_field(
			'checkbox_field_0',
			__( 'Describe the checkbox in this field', 'pmpro-lpv-settings' ),
			array( __CLASS__, 'checkbox_field_0_render' ),
			'pmpro_lpv_settings',
			'pmpro_lpv_section'
		);

		add_settings_field(
			'textarea_field_0',
			__( 'Describe the textarea in this field', 'pmpro-lpv-settings' ),
			array( __CLASS__, 'textarea_field_0_render' ),
			'pmpro_lpv_settings',
			'pmpro_lpv_section'
		);

		add_settings_field(
			'radio_field_0',
			__( 'Describe the radio button select in this field', 'pmpro-lpv-settings' ),
			array( __CLASS__, 'radio_field_0_render' ),
			'pmpro_lpv_settings',
			'pmpro_lpv_section'
		);

	}

	public static function select_field_0_render() {
		$options = get_option( 'pmpro_lpv_settings' );
		?>
		<select name='pmpro_lpv_settings[select_field_0]'>
		<option value='1' <?php selected( $options['select_field_0'], 1 ); ?>>Option 1</option>
		<option value='2' <?php selected( $options['select_field_0'], 2 ); ?>>Option 2</option>
		<option value='3' <?php selected( $options['select_field_0'], 3 ); ?>>Option 3</option>
		<option value='4' <?php selected( $options['select_field_0'], 4 ); ?>>Option 4</option>
		</select>

	<?php

	}

	public static function text_field_0_render() {

		$options = get_option( 'pmpro_lpv_settings' );
		?>
		<input type='text' name='pmpro_lpv_settings[text_field_0]' value='<?php echo $options['text_field_0']; ?>'>
		<?php

	}

	public static function checkbox_field_0_render() {

		$options = get_option( 'pmpro_lpv_settings' );
		?>
		<input type='checkbox' name='pmpro_lpv_settings[checkbox_field_0]' <?php checked( $options['checkbox_field_0'], 1 ); ?> value='1'>
		<?php

	}

	public static function textarea_field_0_render() {

		$options = get_option( 'pmpro_lpv_settings' );
		?>
		<textarea cols='40' rows='5' name='pmpro_lpv_settings[textarea_field_0]'> 
		<?php echo $options['textarea_field_0']; ?>
	 </textarea>
		<?php

	}

	public static function radio_field_0_render() {

		$options = get_option( 'pmpro_lpv_settings' );
		?>
		<label>Radio 1
		<input type='radio' name='pmpro_lpv_settings[radio_field_0]' <?php checked( $options['radio_field_0'], 1 ); ?> value='1'></label>
		<br>
		<label>Radio 2
		<input type='radio' name='pmpro_lpv_settings[radio_field_0]' <?php checked( $options['radio_field_0'], 2 ); ?> value='2'></label>
		<?php

	}

	public static function pmpro_lpv_settings_section_callback() {

		echo __( '<em style="padding:1rem;">This description is found in this function <b>' . __FUNCTION__ . ' </b>and provides an paragraph-type area below the headings and above the individual settings.</em>', 'pmpro-lpv-settings' );

	}

	public static function options_page() {
		?>
		<div style="wrap">
		<form action='options.php' method='post'>
			<h2>PMPro LPV Settings title found in: <?php echo  __FUNCTION__; ?> function</h2>
			<?php
				settings_fields( 'pmpro_lpv_settings' );
				do_settings_sections( 'pmpro_lpv_settings' );
				submit_button();
			?>

			</form>
		</div>
		<?php
		self::pmpro_lpv_settings_render();
		self::pmpro_lpv_settings_todo();
	}

	public static function pmpro_lpv_settings_render() {
		$options = get_option( 'pmpro_lpv_settings' );
		echo '<pre>';
		print_r( $options );
		echo '</pre>';
	}

	public static function pmpro_lpv_settings_todo() {
		echo '<h4>' . __FUNCTION__ . '</h4>';
		echo '<li>Should make sure the settings get deleted if uninstalled</li>';
	}
}
