<?php

// namespace PMPro_LPV_UI\inc\classes;
defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class PMPro_LPV_Init {

	/**
	 * init     Using static functions for our classes -- seems to be slightly more performant
	 *
	 * @return [type] [description]
	 */
	public static function init() {
		// Adding a subment to hook into other PMPro menus
		add_action( 'admin_menu', array( __CLASS__, 'lpv_admin_menu' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'lpv_header_enqueue' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'lpv_admin_enqueue' ) );
		add_action( 'wp_footer', array( __CLASS__, 'lpv_notification_bar' ) );
		add_action( 'wp_head', array( __CLASS__, 'pbrx_header_message' ) );

		add_filter( 'lpv_open_todo', array( __CLASS__, 'lpv_open_todo_message' ) );

		add_action( 'wp_ajax_tie_into_lpv_diagnostics', array( __CLASS__, 'pbrx_header_set_cookie' ) );
		add_action( 'wp_ajax_nopriv_tie_into_lpv_diagnostics', array( __CLASS__, 'pbrx_header_set_cookie' ) );
	}
	/**
	 * Customizer manager demo
	 *
	 * @param  WP_Customizer_Manager $pmpro_manager
	 * @return void
	 */
	public static function lpv_admin_menu() {
		add_submenu_page(
			'pmpro-membershiplevels',
			__( 'PMPro Limit Post Views', 'pmpro-lpv-ui' ),
			__( 'LPV Class', 'pmpro-lpv-ui' ),
			apply_filters( 'pmpro_edit_member_capability', 'manage_options' ),
			'pmpro-limitpostviews',
			array( __CLASS__, 'pmprolpv_settings_page' )
		);
	}
	public static function pmprolpv_settings_page() {
		echo '<h3>' . __FILE__ . '</h3>';
		echo '<ul>';
		echo '<li> * Cookie is set on landing on Home, no banner shown.li>';
		echo '<li> * User triggers count on single posts.</li>';
		echo '<li> * Banner shows on bottom with counts on first view of single post.</li>';
		echo '<li> * Banner does not show on archive pages only on single posts.</li>';
		echo '<li> * After last view available banner says you have no views remaining, provides link to subcribe/join (levels page) </li>';
		echo '<li> * * NumberFormatter is a problem - removing for now</li>';
		echo '<li> * ' . apply_filters( 'lpv_open_todo', 'lpv_open_todo filter here' ) . '</li>';
		echo '</ul>';
	}

	/**
	 * [lpv_open_todo_message description]
	 *
	 * @param  [type] $example [description]
	 * @return [type]          [description]
	 */
	public static function lpv_open_todo_message( $example ) {
		// Maybe modify $example in some way.
		return $example;
	}

	/**
	 * pbrx_modal_header Diagnostic info
	 *
	 * @return [type] [description]
	 */
	public static function pbrx_header_message() {
		global $current_user;
		if ( true == get_option( 'header_diagnostic_toggle' ) ) {
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
			$xyz = ' | Current Level ' . $cur_lev . ' | Limit ' . PMPRO_LPV_LIMIT . ' per ' . PMPRO_LPV_LIMIT_PERIOD;
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
			$header = '<h3 id="lpv-head">Count <span id="lpv_counter"></span>' . $xyz . ' <br> ' . $stg . '<div id="data-returned">data-returned here</div></h3>';
			// if ( current_user_can( 'manage_options' ) ) {
			echo $header;
			// }
		}
	}

	/**
	 * [lpv_admin_enqueue description]
	 *
	 * @return [type] [description]
	 */
	public static function lpv_admin_enqueue() {
		wp_enqueue_style( 'lpv-admin', plugins_url( '../css/lpv-admin.css', __FILE__ ) );
	}

	/**
	 * [lpv_header_enqueue description]
	 *
	 * @return [type] [description]
	 */
	public static function lpv_header_enqueue() {
		wp_register_style( 'lpv-head', plugins_url( 'css/lpv-head.css', dirname( __FILE__ ) ) );
		wp_enqueue_style( 'lpv-head' );
		wp_register_script( 'lpv-diagnostics', plugins_url( '/js/lpv-diagnostics.js', dirname( __FILE__ ) ), array( 'jquery' ), false, false );
		wp_localize_script(
			'lpv-diagnostics',
			'lpv_diagnostics_object',
			array(
				'lpv_diagnostics_ajaxurl' => admin_url( 'admin-ajax.php' ),
				'lpv_diagnostics_nonce' => wp_create_nonce( 'lpv-diagnostics-nonce' ),
				'lpv_diagnostics_user_level' => pmpro_get_user_level(),
				'lpv_diagnostics_redirect' => get_pmpro_lpv_redirect(),
				'lpv_diagnostics_php_expire' => date( 'Y-m-d H:i:s', strtotime( 'today + 1 week' ) ),
			)
		);
		wp_enqueue_script( 'lpv-diagnostics' );
	}

	/**
	 * [pbrx_header_set_cookie This AJAX is going to run on page load
	 *                  It'll be too late for php to set a cookie, but
	 *                  we can do so with Javascript
	 *
	 * @return [type] [description]
	 */
	public static function pbrx_header_set_cookie() {
		$ajax_data = $_POST;
		$month = date( 'n', current_time( 'timestamp' ) );
		if ( ! empty( $_COOKIE['pmpro_lpv_count'] ) ) {
			global $current_user;
			$parts = explode( ';', $_COOKIE['pmpro_lpv_count'] );
			$limitparts = explode( ',', $parts[0] );
			// Get the level limit for the current user.
			if ( defined( 'PMPRO_LPV_LIMIT' ) && PMPRO_LPV_LIMIT > 0 ) {
				$limit = intval( PMPRO_LPV_LIMIT );
			}
			$ajax_data['parts'] = $parts;
			$ajax_data['limitparts'] = $limitparts;
			$ajax_data['level'] = $limitparts[0];
			$ajax_data['view'] = 13;
			$ajax_data['limit'] = $limit;
		}

		$curlev = 4;
		$curviews = $limitparts[1];
		$expires = date( 'Y-m-d', strtotime( '+30 days' ) );
		$cookiestr .= "$curlev,$curviews";
		echo json_encode( $ajax_data );

		exit();
	}
	/**
	 * [lpv_notification_bar description]
	 *
	 * @return [type] [description]
	 */
	public static function lpv_notification_bar() {
		// Check for past views. Needs to check if the post is locked at all by default.
		if ( isset( $_COOKIE['pmpro_lpv_count'] ) ) {
			global $current_user;

			$article_s = sprintf( _n( '%s free article', '%s free articles', $formatted, 'paid-memberships-pro' ), number_format_i18n( $formatted ) );
				?>
				<div id="lpv-footer" style="z-index:333;">
			You have <span style="color: #B00000;"> <span id="lpv_count"><img src="<?php echo esc_html( admin_url( '/images/spinner.gif' ) ); ?>" /></span> of <span id="lpv_limit"><img src="<?php echo esc_html( admin_url( '/images/spinner.gif' ) ); ?>" /></span> </span> remaining. 
			<a href="<?php echo wp_login_url( get_permalink() ); ?>" title="Log in">Log in</a> or <a href="<?php echo pmpro_url( 'levels' ); ?>" title="Subscribe now">Subscribe</a> for unlimited access. 
			</div>
			<?php
		}
	}
}
