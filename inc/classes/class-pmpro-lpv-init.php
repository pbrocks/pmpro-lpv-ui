<?php

// namespace PMPro_LPV_UI\inc\classes;
defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class PMPro_LPV_Init {
	public static function init() {
		// add_action( 'customize_register', array( __CLASS__, 'engage_the_customizer' ) );
		// add_action( 'wp_enqueue_scripts', array( __CLASS__, 'customizer_enqueue' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'lpv_admin_enqueue' ) );
		add_action( 'wp_footer', array( __CLASS__, 'lpv_notification_bar' ) );
		add_action( 'wp_head', array( __CLASS__, 'pbrx_header_message' ) );
	}

	/**
	 * pbrx_modal_header Diagnostic info
	 *
	 * @return [type] [description]
	 */

	public static function pbrx_header_message() {
		global $current_user;
		$stg = '';
		if ( true == PMPRO_LPV_USE_JAVASCRIPT ) {
			$yep_js = 'true';
		} else {
			$yep_js = 'nope';
		}
		?>
		<form id="lpv-diagnostics-form">
			<input type="hidden" name="hidden" value="lpv-diagnostics-test">
		<?php
		$cur_usr_ary = get_pmpro_member_array( 1 );
		$cur_lev = $cur_usr_ary['level_id'];
		$xyz = basename( __FILE__ );
		$xyz = ' Limit ' . PMPRO_LPV_LIMIT . ' per ' . PMPRO_LPV_LIMIT_PERIOD . ' | js ' . $yep_js . ' | Current Level ' . $cur_lev . ' |';
		if ( isset( $_COOKIE['pmpro_lpv_count'] ) ) {
			$button_value = 'Reset Cookie';
			// $button_value = 3600 * 24 * 100 . ' seconds';
			$button = '<input type="hidden" name="token" value="reset">';
			$stg = ' $_COOKIE(\'pmpro_lpv_count\') SET !! ' . $button;
		} else {
			$button_value = 'Set Cookie';
			$button = '<input type="hidden" name="token" value="set">';
			$stg = ' $_COOKIE(\'pmpro_lpv_count\') NOT set ?!?!? ' . $button;
		}
			?>
		</form>
		<?php
		$values = pmpro_lpv_cookie_values();
		// print_r( $values );
		$header = '<h3 style="z-index:1;position:relative;text-align:center;color:tomato;">Count <span id="lpv_counter"></span>' . $xyz . ' <br> ' . $stg . '</h3><div id="data-returned">data-returned here</div><div id="demo">demo</div>';
		// if ( current_user_can( 'manage_options' ) ) {
			echo $header;
		// }
	}

	public static function lpv_admin_enqueue() {
		wp_enqueue_style( 'lpv-admin', plugins_url( '../css/lpv-admin.css', __FILE__ ) );
	}

	/**
	 * Customizer manager demo
	 *
	 * @param  WP_Customizer_Manager $pmpro_manager
	 * @return void
	 */
	public static function pmpro_customizer_manager( $pmpro_manager ) {

	}

	public static function lpv_notification_bar() {
		// return '<h2>Some sort of data.</h2>';
		?>
		<style type="text/css">
			#data-returned {
				background: red;
			}
		</style>
		<?php
	}
}


