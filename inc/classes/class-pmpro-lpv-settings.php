<?php
defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class PMPro_LPV_Settings {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'pmpro_lpv_settings_init' ) );
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
				'level_' . $level->id . '_views',
				$title,
				array( __CLASS__, 'lpv_limit_select_render' ),
				'pmpro_lpv_settings',
				'pmpro_lpv_section',
				$id
			);

		}
		add_settings_field(
			'post_limit_action',
			__( 'Post limit action', 'pmpro-lpv-settings' ),
			array( __CLASS__, 'post_limit_action_render' ),
			'pmpro_lpv_settings',
			'pmpro_lpv_section'
		);
	}

	public static function print_all_pmpro_lpv_levels() {
		$levels = self::get_all_pmpro_lpv_levels();
		echo '<div class="wrap">';
		echo '<pre>';
		print_r( $levels );
		echo '</pre>';
		echo '</div>';
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
		$limit = get_option( 'pmpro_lpv_settings' );
		?>
	<input size="2" type="text" id="level_<?php echo $level_id; ?>_views"
		   name="pmpro_lpv_settings[level_<?php echo $level_id; ?>_views]" value="<?php echo $limit[ 'level_' . $level_id . '_views' ]; ?>">
		<?php _e( ' views per ', 'pmprolpv' ); ?>
	<select name="pmpro_lpv_settings[level_<?php echo $level_id; ?>_period]" id="level_<?php echo $level_id; ?>_period" >
		<option
			value="hour" <?php selected( $limit[ 'level_' . $level_id . '_period' ], 'hour' ); ?>><?php _e( 'Hour', 'pmprolpv' ); ?></option>
		<option
			value="day" <?php selected( $limit[ 'level_' . $level_id . '_period' ], 'day' ); ?>><?php _e( 'Day', 'pmprolpv' ); ?></option>
		<option
			value="week" <?php selected( $limit[ 'level_' . $level_id . '_period' ], 'week' ); ?>><?php _e( 'Week', 'pmprolpv' ); ?></option>
		<option
			value="month" <?php selected( $limit[ 'level_' . $level_id . '_period' ], 'month' ); ?>><?php _e( 'Month', 'pmprolpv' ); ?></option>
	</select>
		<?php
	}

	public static function post_limit_action_render() {
		$options = get_option( 'pmpro_lpv_settings' );
		?>
		<label>
		<input type='radio' name='pmpro_lpv_settings[post_limit_action]' <?php checked( $options['post_limit_action'], 'redirect' ); ?> value='redirect'> Redirect</label>
		<br>
		<label>
		<input type='radio' name='pmpro_lpv_settings[post_limit_action]' <?php checked( $options['post_limit_action'], 'footer' ); ?> value='footer'> Enlarge Footer</label>
		<br>
		<label>
		<input type='radio' name='pmpro_lpv_settings[post_limit_action]' <?php checked( $options['post_limit_action'], 2 ); ?> value='2'> Radio 2</label>
		<?php
	}

	public static function pmpro_lpv_settings_section_callback() {
		echo __( '<em style="padding:1rem;">This description is found in this function <b>' . __FUNCTION__ . ' </b>and provides an paragraph-type area below the headings and above the individual settings.</em>', 'pmpro-lpv-settings' );
	}

	public static function lpv_options_page() {
		?>
		<div style="wrap">
			<h2>PMPro LPV Settings title found in: <?php echo __FUNCTION__; ?> function</h2>
		<form action='options.php' method='post'>
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
