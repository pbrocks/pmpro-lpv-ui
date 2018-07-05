<?php
defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

// class PMPro_LPV_Settings2 {
// public static function init() {
// add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ) );
// add_action( 'admin_init', array( __CLASS__, 'pmpro_lpv_settings_init' ) );
// add_action( 'admin_footer', array( __CLASS__, 'print_all_pmpro_lpv_levels' ) );
// }
// public static function add_admin_menu() {
// add_submenu_page( 'pmpro-membershiplevels', __( 'PMPro LPV Settings', 'pmpro-lpv-ui' ), __( 'PMPro LPV Settings', 'pmpro-lpv-ui' ), 'manage_options', 'pmpro-lpv-settings.php', array( __CLASS__, 'lpv_options_page' ) );
// }
// public static function pmpro_lpv_settings_init() {
add_action( 'admin_menu', 'lpv_add_admin_menu' );
add_action( 'admin_init', 'lpv_settings_init' );


function lpv_add_admin_menu() {

	add_submenu_page( 'pmpro-membershiplevels', 'pmpro_lpv_settings', 'pmpro_lpv_settings', 'manage_options', 'pmpro_lpv_settings', 'lpv_options_page' );

}


function lpv_settings_init() {

	register_setting( 'plugin_page', 'lpv_settings' );

	add_settings_section(
		'lpv_plugin_page_section',
		__( 'Your section description', 'pmpro-lpv-ui' ),
		'lpv_settings_section_callback',
		'plugin_page'
	);

	add_settings_field(
		'lpv_checkbox_field_0',
		__( 'Settings field description', 'pmpro-lpv-ui' ),
		'lpv_checkbox_field_0_render',
		'plugin_page',
		'lpv_plugin_page_section'
	);

	add_settings_field(
		'lpv_select_field_1',
		__( 'Settings field description', 'pmpro-lpv-ui' ),
		'lpv_select_field_1_render',
		'plugin_page',
		'lpv_plugin_page_section'
	);

	add_settings_field(
		'lpv_select_period_render',
		__( 'Set Period', 'pmpro-lpv-ui' ),
		'lpv_select_period_render',
		'plugin_page',
		'lpv_plugin_page_section'
	);

}


function lpv_checkbox_field_0_render() {
	$options = get_option( 'lpv_settings' );
	?>
	<input type='checkbox' name='lpv_settings[lpv_checkbox_field_0]' <?php checked( $options['lpv_checkbox_field_0'], 1 ); ?> value='1'>
	<?php
}

function lpv_select_field_1_render() {
	$options = get_option( 'lpv_settings' );
	?>
	<select name='lpv_settings[lpv_select_field_1]'>
		<option value='1' <?php selected( $options['lpv_select_field_1'], 1 ); ?>>Option 1</option>
		<option value='2' <?php selected( $options['lpv_select_field_1'], 2 ); ?>>Option 2</option>
		<option value='3' <?php selected( $options['lpv_select_field_1'], 3 ); ?>>Option 3</option>
		<option value='4' <?php selected( $options['lpv_select_field_1'], 4 ); ?>>Option 4</option>
	</select>
<?php
}

function lpv_select_period_render() {
	$options = get_option( 'lpv_settings' );
	?>
	<select name='lpv_settings[lpv_select_period]'>
		<option value='hour' <?php selected( $options['lpv_select_period'], 'hour' ); ?>>Hour</option>
		<option value='day' <?php selected( $options['lpv_select_period'], 'day' ); ?>>Day</option>
		<option value='week' <?php selected( $options['lpv_select_period'], 'week' ); ?>>Week</option>
		<option value='month' <?php selected( $options['lpv_select_period'], 'month' ); ?>>Month</option>
	</select>
<?php
}


function lpv_settings_section_callback() {
	echo __( '<p class="description">This section description</p>', 'pmpro-lpv-ui' );
}


function lpv_options_page() {
	?>
	<form action='options.php' method='post'>

		<h2>pmpro_lpv_settings</h2>

		<?php
		settings_fields( 'plugin_page' );
		do_settings_sections( 'plugin_page' );
		submit_button();
		?>

	</form>
	<?php
	echo '<pre>';
	print_r( return_lpv_settings() );
	echo '</pre>';
}

function return_lpv_settings() {
	$options = get_option( 'lpv_settings' );
	return $options;
}
