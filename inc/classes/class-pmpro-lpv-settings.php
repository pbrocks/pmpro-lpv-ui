<?php
defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class PMPro_LPV_Settings {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'pmpro_lpv_settings_init' ) );
	}
	public static function add_admin_menu() {
		add_submenu_page(
			'pmpro-membershiplevels',
			__( 'PMPro Limit Post Views', 'pmpro-lpv-ui' ),
			__( 'PMPro LPV', 'pmpro-lpv-ui' ),
			'manage_options',
			'pmpro-lpv-ui-settings.php',
			array( __CLASS__, 'lpv_options_page' )
		);

	}

	public static function pmpro_lpv_settings_init() {
		register_setting( 'pmpro_lpv_settings', 'pmpro_lpv_settings' );

		add_settings_section(
			'pmpro_lpv_section',
			__( 'This is the description area given for a settings page', 'pmpro-lpv-ui' ),
			array( __CLASS__, 'pmpro_lpv_settings_section_callback' ),
			'pmpro_lpv_settings'
		);

		add_settings_field(
			'limit',
			__( 'Non-members', 'pmpro-lpv-ui' ),
			array( __CLASS__, 'select_lpv_limit_render' ),
			'pmpro_lpv_settings',
			'pmpro_lpv_section'
		);

		add_settings_field(
			'lpv_limit_response',
			__( 'Describe the radio button select in this field', 'pmpro-lpv-ui' ),
			array( __CLASS__, 'lpv_limit_response_render' ),
			'pmpro_lpv_settings',
			'pmpro_lpv_section'
		);
		add_settings_field(
			'text_field_0',
			__( 'Describe the text field in this field', 'pmpro-lpv-ui' ),
			array( __CLASS__, 'text_field_0_render' ),
			'pmpro_lpv_settings',
			'pmpro_lpv_section'
		);

		add_settings_field(
			'checkbox_field_0',
			__( 'Describe the checkbox in this field', 'pmpro-lpv-ui' ),
			array( __CLASS__, 'checkbox_field_0_render' ),
			'pmpro_lpv_settings',
			'pmpro_lpv_section'
		);

		add_settings_field(
			'textarea_field_0',
			__( 'Describe the textarea in this field', 'pmpro-lpv-ui' ),
			array( __CLASS__, 'textarea_field_0_render' ),
			'pmpro_lpv_settings',
			'pmpro_lpv_section'
		);

		echo '<div style="float:right;">' . self::pmprolpv_settings_field_limits() . '</div>';
	}

	public static function pmprolpv_settings_field_limits() {
		$level_id = PMPro_LPV_Init::pmpro_get_user_level();
		$limit = get_option( 'pmprolpv_limit_' . $level_id );
		?><div class="wrap" style="float:right;">
		<input size="2" type="text" id="level_<?php echo $level_id; ?>_views"
			   name="pmprolpv_limit_<?php echo $level_id; ?>[views]" value="<?php echo $limit['views']; ?>">
		<?php _e( ' views per ', 'pmprolpv' ); ?>
		<select name="pmprolpv_limit_<?php echo $level_id; ?>[period]" id="level_<?php echo $level_id; ?>_period">
			<option
				value="hour" <?php selected( $limit['period'], 'hour' ); ?>><?php _e( 'Hour', 'pmprolpv' ); ?></option>
			<option
				value="day" <?php selected( $limit['period'], 'day' ); ?>><?php _e( 'Day', 'pmprolpv' ); ?></option>
			<option
				value="week" <?php selected( $limit['period'], 'week' ); ?>><?php _e( 'Week', 'pmprolpv' ); ?></option>
			<option
				value="month" <?php selected( $limit['period'], 'month' ); ?>><?php _e( 'Month', 'pmprolpv' ); ?></option>
		</select>
	</div>
		<?php
	}

	public static function select_lpv_limit_render() {
		$level_id = PMPro_LPV_Init::pmpro_get_user_level();
		$limit = get_option( 'pmprolpv_limit_' . $level_id );
		$lpv_options = get_option( 'pmpro_lpv_settings' );
		?>
		<input size="2" type="text" id="level_<?php echo $level_id; ?>_views"
			  name="pmprolpv_limit_<?php echo $level_id; ?>[views]" value="<?php echo $limit['views']; ?>">
		<?php _e( ' views per ', 'pmprolpv' ); ?>
		<select name='pmpro_lpv_settings[limit]'>
		<option value='hour' <?php selected( $lpv_options['limit'], 'hour' ); ?>><?php _e( 'Hour', 'pmpro-lpv-ui' ); ?></option>
		<option value='day' <?php selected( $lpv_options['limit'], 'day' ); ?>><?php _e( 'Day', 'pmpro-lpv-ui' ); ?></option>
		<option value='week' <?php selected( $lpv_options['limit'], 'week' ); ?>><?php _e( 'Week', 'pmpro-lpv-ui' ); ?></option>
		<option value='month' <?php selected( $lpv_options['limit'], 'month' ); ?>><?php _e( 'Month', 'pmpro-lpv-ui' ); ?></option>
		</select>

	<?php
	}

	public static function lpv_limit_response_render() {
		$lpv_options = get_option( 'pmpro_lpv_settings' );
		$lpv_options['lpv_limit_response'] = get_option( 'lpv_response_radio' );
		?>
		<label>Radio Footer
		<input type='radio' name='pmpro_lpv_settings[lpv_limit_response]' <?php checked( $lpv_options['lpv_limit_response'], 'footer' ); ?> value='footer'></label>
		<br>
		<label>Radio Popup
		<input type='radio' name='pmpro_lpv_settings[lpv_limit_response]' <?php checked( $lpv_options['lpv_limit_response'], 'popup' ); ?> value='popup'></label>
		<br>
		<label>Radio Redirect
		<input type='radio' name='pmpro_lpv_settings[lpv_limit_response]' <?php checked( $lpv_options['lpv_limit_response'], 'redirect' ); ?> value='redirect'></label>
		<?php
	}

	public static function text_field_0_render() {

		$lpv_options = get_option( 'pmpro_lpv_settings' );
		?>
		<input type='text' name='pmpro_lpv_settings[text_field_0]' value='<?php echo $lpv_options['text_field_0']; ?>'>
		<?php

	}

	public static function checkbox_field_0_render() {

		$lpv_options = get_option( 'pmpro_lpv_settings' );
		?>
		<input type='checkbox' name='pmpro_lpv_settings[checkbox_field_0]' <?php checked( $lpv_options['checkbox_field_0'], 1 ); ?> value='1'>
		<?php

	}

	public static function textarea_field_0_render() {

		$lpv_options = get_option( 'pmpro_lpv_settings' );
		?>
		<textarea cols='40' rows='5' name='pmpro_lpv_settings[textarea_field_0]'> 
		<?php echo $lpv_options['textarea_field_0']; ?>
	 </textarea>
		<?php

	}

	public static function pmpro_lpv_settings_section_callback() {

		echo __( '<em style="padding:1rem;">This description is found in this function <b>' . __FUNCTION__ . ' </b>and provides an paragraph-type area below the headings and above the individual settings.</em>', 'pmpro-lpv-ui' );

	}

	public static function lpv_options_page() {
		?>
		<div style="wrap">
		<form action='options.php' method='post'>
			<h2>PMPro LPV Settings title found in: <?php echo __FUNCTION__; ?> function</h2>
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
		$lpv_options = get_option( 'pmpro_lpv_settings' );
		echo '<pre>';
		print_r( $lpv_options );
		echo '</pre>';
	}

	public static function pmpro_lpv_settings_todo() {
		echo '<h4>' . __FUNCTION__ . '</h4>';
		echo '<li>Should make sure the settings get deleted if uninstalled</li>';
	}
}
