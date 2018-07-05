<?php
defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class PMPro_LPV_Settings {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'pmpro_lpv_settings_init' ) );
		// add_action( 'admin_footer', array( __CLASS__, 'print_all_pmpro_lpv_levels' ) );
	}
	public static function add_admin_menu() {

		add_submenu_page( 'pmpro-membershiplevels', __( 'PMPro LPV Settings', 'pmpro-lpv-ui' ), __( 'PMPro LPV Settings', 'pmpro-lpv-ui' ), 'manage_options', 'pmpro-lpv-settings.php', array( __CLASS__, 'lpv_options_page' ) );

	}

	public static function pmpro_lpv_settings_init() {

		register_setting( 'pmpro_lpv_settings', 'pmpro_lpv_settings' );
		$levels = self::get_all_pmpro_lpv_levels();

		add_settings_section(
			'pmpro_lpv_section',
			__( 'This is the description area given for a settings page', 'pmpro-lpv-settings' ),
			array( __CLASS__, 'pmpro_lpv_settings_section_callback' ),
			'pmpro_lpv_settings'
		);

		foreach ( $levels as $id => $level ) {
			$title = $level->name . ' ( Level ' . $level->id . ' )';
			add_settings_field(
				'lpv_limit_id_' . $id,
				$title,
				array( __CLASS__, 'lpv_limit_select_render' ),
				'pmpro_lpv_settings',
				'pmpro_lpv_section',
				$id
			);

			register_setting(
				'pmpro_lpv_settings',
				'lpv_limit_id_' . $id,
				'pmprolpv_sanitize_limit'
			);
		}
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
		// add_settings_field(
		// 'checkbox_field_0',
		// __( 'Describe the checkbox in this field', 'pmpro-lpv-settings' ),
		// array( __CLASS__, 'checkbox_field_0_render' ),
		// 'pmpro_lpv_settings',
		// 'pmpro_lpv_section'
		// );
		add_settings_field(
			'textarea_field_a',
			__( 'Describe the textarea in this field', 'pmpro-lpv-settings' ),
			array( __CLASS__, 'textarea_field_0_render' ),
			'pmpro_lpv_settings',
			'pmpro_lpv_section'
		);
		// add_settings_field(
		// 'radio_field_0',
		// __( 'Describe the radio button select in this field', 'pmpro-lpv-settings' ),
		// array( __CLASS__, 'radio_field_0_render' ),
		// 'pmpro_lpv_settings',
		// 'pmpro_lpv_section'
		// );
	}

	public static function print_all_pmpro_lpv_levels() {
		$levels = self::get_all_pmpro_lpv_levels();
		echo '<pre> <div class="wrap">';
		print_r( $levels );
		echo '</div></pre>';
	}

	public static function get_all_pmpro_lpv_levels() {
		// Register limits settings fields.
		$levels = pmpro_getAllLevels( true, true );
		$levels[0] = new stdClass();
		$levels[0]->name = __( 'Non-members', 'pmpro' );
		$levels[0]->id = 0;
		asort( $levels );
		return $levels;
	}

	public static function lpv_limit_select_render( $level_id ) {
		// $options = get_option( 'pmpro_lpv_settings' );
		// $level_id = PMPro_LPV_Init::pmpro_get_user_level();
		$limit = get_option( 'lpv_limit_id_' . $level_id );
	?>
	<input size="2" type="text" id="level_<?php echo $level_id; ?>_views"
		   name="lpv_limit_id_<?php echo $level_id; ?>[views]" value="<?php echo $limit['views']; ?>">
	<?php _e( ' views per ', 'pmprolpv' ); ?>
	<select name="lpv_limit_id_<?php echo $level_id; ?>[period]" id="level_<?php echo $level_id; ?>_period">
		<option
			value="hour" <?php selected( $limit['period'], 'hour' ); ?>><?php _e( 'Hour', 'pmprolpv' ); ?></option>
		<option
			value="day" <?php selected( $limit['period'], 'day' ); ?>><?php _e( 'Day', 'pmprolpv' ); ?></option>
		<option
			value="week" <?php selected( $limit['period'], 'week' ); ?>><?php _e( 'Week', 'pmprolpv' ); ?></option>
		<option
			value="month" <?php selected( $limit['period'], 'month' ); ?>><?php _e( 'Month', 'pmprolpv' ); ?></option>
	</select>
	<?php
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
		<textarea cols='40' rows='5' name='pmpro_lpv_settings[textarea_field_a]'> 
		<?php echo $options['textarea_field_a']; ?>
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

	public static function lpv_options_page() {
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
			<div>
				<?php
				$levels = self::get_all_pmpro_lpv_levels();

				self::pmpro_lpv_settings_render();
				self::pmpro_lpv_settings_todo();
				?>
			</div>
		<?php
			$lpv_options = get_option( 'pmpro_lpv_settings' );
			echo '<pre>';
			print_r( $lpv_options );
			echo '</pre>';
			echo '<pre>';
			print_r( $levels );
			echo '</pre>';
		echo '</div>';
	}

	/**
	 * Sanitize limit fields
	 *
	 * @since 0.3.0
	 * @param $args
	 *
	 * @return mixed
	 */
	public static function pmprolpv_sanitize_limit( $args ) {

		if ( ! is_numeric( $args['views'] ) ) {
			$args['views'] = '';
			$args['period'] = '';
		}

		return $args;
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
