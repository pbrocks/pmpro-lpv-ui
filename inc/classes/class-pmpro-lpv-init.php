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
		add_action( 'wp_head', array( __CLASS__, 'lpv_header_admin_head' ) );
		add_action( 'admin_menu', array( __CLASS__, 'lpv_admin_menu' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'lpv_header_enqueue' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'lpv_admin_enqueue' ) );
		add_action( 'wp_footer', array( __CLASS__, 'lpv_notification_bar' ) );
		add_action( 'wp_head', array( __CLASS__, 'lpv_cookie_form' ) );
		add_filter( 'lpv_open_todo', array( __CLASS__, 'lpv_open_todo_message' ) );
		add_action( 'wp_head', array( __CLASS__, 'pmpro_lpv_modal' ),15 );

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'clear_button_enqueue' ) );
		add_shortcode( 'clear-button', array( __CLASS__, 'clear_button_shortcode' ) );
		add_action( 'wp_ajax_two_responses_action', array( __CLASS__, 'two_responses_function' ) );
		add_action( 'wp_ajax_tie_into_lpv_cookie', array( __CLASS__, 'lpv_header_set_cookie' ) );
		add_action( 'wp_ajax_nopriv_tie_into_lpv_cookie', array( __CLASS__, 'lpv_header_set_cookie' ) );
	}
	public static function lpv_cookie_check() {
		if ( isset( $_COOKIE['pmpro_lpv_count'] ) ) {
			$return = 'Yep, $_COOKIE[\'pmpro_lpv_count\'] is set!!';
		} else {
			$return = 'WTF';
		}
		return $return;
	}
	public static function lpv_header_admin_head() {
		?>
		<?php if ( get_option( 'lpv_diagnostic_header' ) ) { ?>
			<style type="text/css">
			#lpv-foter {
				background: rgba(250,128,114,.8);
				text-align: center;
			}
			</style>
			<div id="lpv-foter" style="z-index:333;">
				<span id="foter-text">MMMmmmmmkkay</span><br>
				<?php echo self::lpv_cookie_check(); ?>
			</div>
			<?php } ?>
		<?php
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
			array( __CLASS__, 'pmpro_lpv_settings_page' )
		);
	}
	public static function pmpro_lpv_settings_page() {
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
		echo '<pre>pmpro_lpv_settings ';
		print_r( self::pmpro_lpv_settings( 1 ) );
		echo 'get_pmpro_lpv_limit() ';
		print_r( self::get_pmpro_lpv_limit() );
		echo '</pre>';
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

	public static function pmpro_lpv_settings( $level_id ) {
		$lpv['limit'] = get_option( 'pmprolpv_limit_' . $level_id );
		$lpv['use_js'] = get_option( 'pmprolpv_use_js' );
		return $lpv;
	}
	/**
	 * [lpv_cookie_form description]
	 *
	 * @param  [type] $example [description]
	 * @return [type]          [description]
	 */
	public static function lpv_cookie_form() {
		$limitt = 'need limit';
		$periodd = 'need period';
		?>
		<form id="lpv-cookie-form">
		<input type="hidden" name="hidden" value="lpv-cookie-test">
		<?php
		$cur_usr_ary = self::get_pmpro_member_array( 1 );
		$cur_lev = $cur_usr_ary['level_id'];
		$xyz = ' | Current Level ' . $cur_lev . ' | Limit ' . $limitt . ' per ' . $periodd;
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
	 * [clear_button_enqueue description]
	 *
	 * @return [type] [description]
	 */
	public static function clear_button_enqueue() {
		wp_register_script( 'two-responses', plugins_url( '/js/two-responses.js', dirname( __FILE__ ) ), array( 'jquery' ), false, false );
		wp_localize_script(
			'two-responses',
			'two_responses_object',
			array(
				'two_responses_ajaxurl' => admin_url( 'admin-ajax.php' ),
				'two_responses_nonce'   => wp_create_nonce( 'two-responses-nonce' ),
				'two_responses_user_level' => self::pmpro_get_user_level(),
				'two_responses_limit'      => self::get_pmpro_lpv_limit(),
				'two_responses_redirect'   => self::get_pmpro_lpv_redirect(),
				'two_responses_response'   => self::get_pmpro_lpv_limit_response(),
				'two_responses_php_expire' => self::get_pmpro_lpv_period(),
			)
		);
	}
	public static function clear_button_shortcode() {
		wp_enqueue_script( 'two-responses' );
		?>
		<form id="two-responses-form">
		<input type="hidden" id="two-responses" name="two-responses" class="two-responses" value="two-responses" />
		<input type="submit" id="two-responses-submit" name="two-responses-submit" class="two-responses-submit" value="LPV Cookies" />
		</form>
		<?php

	}
	public static function two_responses_function() {
		$variables = $_POST;
		echo '<pre>AJAX $variables ';
		print_r( $variables );
		echo '</pre>';
		exit();
	}

	/**
	 * [lpv_header_enqueue description]
	 *
	 * @return [type] [description]
	 */
	public static function lpv_header_enqueue() {
		wp_register_style( 'lpv-head', plugins_url( 'css/lpv-head.css', dirname( __FILE__ ) ) );
		wp_enqueue_style( 'lpv-head' );
		wp_register_style( 'modal-popup', plugins_url( 'css/modal-popup.css', dirname( __FILE__ ) ) );
		wp_enqueue_style( 'modal-popup' );
		wp_register_script( 'lpv-cookie', plugins_url( '/js/lpv-set-cookie.js', dirname( __FILE__ ) ), array( 'jquery' ), false, false );
		wp_localize_script(
			'lpv-cookie',
			'lpv_cookie_object',
			array(
				'lpv_cookie_ajaxurl'    => admin_url( 'admin-ajax.php' ),
				'lpv_cookie_nonce'      => wp_create_nonce( 'lpv-cookie-nonce' ),
				'lpv_cookie_user_level' => self::pmpro_get_user_level(),
				'lpv_cookie_lpv_limit'  => self::get_pmpro_lpv_limit(),
				'lpv_cookie_redirect'   => self::get_pmpro_lpv_redirect(),
				'lpv_cookie_response'   => self::get_pmpro_lpv_limit_response(),
				'lpv_cookie_php_expire' => self::get_pmpro_lpv_period(),
			)
		);
		wp_enqueue_script( 'lpv-cookie' );
	}
	public static function get_pmpro_member_array( $user_id ) {
		$user_object = new WP_User( $user_id );
		;
		$member_data = get_userdata( $user_object->ID );
		$member_object = pmpro_getMembershipLevelForUser( $member_data->ID );
		$member_level['level_id'] = $member_object->id;
		$member_level['level_name'] = $member_object->name;
		$member_level['level_description'] = $member_object->description;
		$member_level['subscription_id'] = $member_object->subscription_id;
		return $member_level;
	}
	/**
	 * [pmpro_get_user_level Return the member's level
	 *
	 * @return [type] If not a member, level = 0
	 */
	public static function pmpro_get_user_level() {
		global $current_user;
		if ( ! empty( $current_user->membership_level ) ) {
			$level_id = $current_user->membership_level->id;
		} else {
			$level_id = 0;
		}
		return $level_id;
	}

	/**
	 * Redirect to the configured page or the default levels page
	 */
	public static function get_pmpro_lpv_limit() {
		$level_id = self::pmpro_get_user_level();
		$limit = get_option( 'pmprolpv_limit_' . $level_id );
		$limit['level_id'] = $level_id;
		return $limit;
	}

	/**
	 * Redirect to the configured page or the default levels page
	 */
	public static function get_pmpro_lpv_redirect() {
		$page_id = get_option( 'pmprolpv_redirect_page' );
		if ( empty( $page_id ) ) {
			$redirect_url = pmpro_url( 'levels' );
		} else {
			$redirect_url = get_the_permalink( $page_id );
		}
		return $redirect_url;
	}

	public static function get_pmpro_lpv_limit_response() {
		$lpv_options = get_option( 'pmpro_lpv_settings' );
		$lpv_response = get_option( 'lpv_response_radio' );
		// if ( empty( $lpv_response ) ) {
		// $redirect_url = pmpro_url( 'levels' );
		// } else {
		// $redirect_url = get_the_permalink( $lpv_response );
		// }
		return $lpv_response;
	}

	public static function get_pmpro_lpv_period() {
		$lpv_options = get_option( 'pmpro_lpv_settings' );
		$lpv_response = get_option( 'lpv_response_radio' );
		$lpv_period = date( 'Y-m-d H:i:s', strtotime( 'now + 1 hour' ) );
		return $lpv_period;
	}

	/**
	 * [lpv_header_set_cookie This AJAX is going to run on page load
	 *                  It'll be too late for php to set a cookie, but
	 *                  we can do so with Javascript
	 *
	 * @return [type] [description]
	 */
	public static function lpv_header_set_cookie() {
		$ajax_data = $_POST;
		$month = date( 'n', current_time( 'timestamp' ) );
		if ( ! empty( $_COOKIE['pmpro_lpv_count'] ) ) {
			global $current_user;
			$parts = explode( ';', $_COOKIE['pmpro_lpv_count'] );
			$splitparts = explode( '|', $parts[0] );
			$ajax_data['parts'] = $parts;
			$ajax_data['splitparts'] = $splitparts;
			$ajax_data['cookie_level'] = $splitparts[0];
			$ajax_data['cookie_views'] = $splitparts[1];
			$ajax_data['cookie_limit'] = $splitparts[2];
		}

		$curlev = $ajax_data['userlevel'];
		$curlev = $ajax_data['limit']['level_id'];
		$curviews = $splitparts[1];
		$ajax_data['lpv_limit'] = $ajax_data['limit']['views'];
		$ajax_data['lpv_period'] = $ajax_data['limit']['period'];
		$expires = date( 'Y-m-d', strtotime( '+1' . $ajax_data['lpv_period'] ) );
		$cookiestr = "$curlev,$curviews";
		echo json_encode( $ajax_data );
		// echo '<pre>';
		// print_r( $ajax_data );
		// echo '</pre>';
		exit();
	}

	/**
	 * pmpro_lpv_modal This AJAX is going to run on page load
	 *                  It'll be too late for php to set a cookie, but
	 *                  we can do so with Javascript
	 *
	 * @return [type] [description]
	 */
	public static function pmpro_lpv_modal() {
		// if ( 'popup' === get_option( 'lpv_response_radio' ) ) {
		?>
		<div id="lpv-modal" class="popup-modal">
		<!-- Modal content -->
		<div class="modal-content">
			<div class="modal-header">
				<h2>Modal Header</h2>
			</div>
			<div class="modal-body">
				<h2>Levels Shortcode below</h2>
<!-- 				 <img src="https://placekitten.com/150/200"> 
				<img src="https://placekitten.com/150/200">
				<img src="https://placekitten.com/150/200"> -->
				<p><?php echo do_shortcode( '[pmpro_levels]' ); ?></p>
				<p>Levels Shortcode above</p>
			</div>
			<div class="modal-footer">
				<h3>Modal Footer</h3>
			</div>
			</div>
		</div>
		<?php
		// }
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

			// $article_s = sprintf( _n( '%s free article', '%s free articles', $formatted, 'paid-memberships-pro' ), number_format_i18n( $formatted ) );
			?>
			<div id="lpv-footer" style="z-index:333;">
			You have <span style="color: #B00000;"> <span id="footer-text"><span id="lpv_count"><img src="<?php echo esc_html( admin_url( '/images/spinner.gif' ) ); ?>" /></span> of <span id="lpv_limit"><img src="<?php echo esc_html( admin_url( '/images/spinner.gif' ) ); ?>" /></span> </span> remaining. 
			<a href="<?php echo wp_login_url( get_permalink() ); ?>" title="Log in">Log in</a> or <span id="footer-break" style="display:none;"><br><br></span><a href="<?php echo pmpro_url( 'levels' ); ?>" title="Subscribe now">Subscribe</a> for unlimited access.</span><?php echo do_shortcode( '[clear-button]' ); ?>
			</div>
			<?php
		} else {
			?>
			<div id="lpv-footer" style="z-index:333;">
				<span id="footer-text"><h2>Nada</h2></span>
			</div>
			<?php
		}
	}
}
