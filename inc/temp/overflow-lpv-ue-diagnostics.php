<?php
/**
 * Plugin Na PMPro LPV User Experience
 * 0 %2C 5 %3B 6
 */
require_once( plugin_dir_path( __FILE__ ) . 'includes/admin.php' );

function pmpro_get_user_level() {
	global $current_user;
	if ( ! empty( $current_user->membership_level ) ) {
		$level_id = $current_user->membership_level->id;
	}
	if ( empty( $level_id ) ) {
		$level_id = 0;
	}
	return $level_id;
}
add_action( 'init', 'pmpro_get_user_level' );

add_action( 'wp_ajax_tie_into_lpv_diagnostics', 'pbrx_header_set_cookie' );
add_action( 'wp_ajax_nopriv_tie_into_lpv_diagnostics', 'pbrx_header_set_cookie' );
function pbrx_header_set_cookie() {
	$ajax_data = $_POST;
	$month = date( 'n', current_time( 'timestamp' ) );
	if ( ! empty( $_COOKIE['pmpro_lpv_count'] ) ) {
		global $current_user;
		// Check cookie for views value.
		// $parts = explode( ';', sanitize_text_field( $_COOKIE['pmpro_lpv_count'] ) );
		$parts = explode( ';', $_COOKIE['pmpro_lpv_count'] );
		$limitparts = explode( ',', $parts[0] );
		// Get the level limit for the current user.
		if ( defined( 'PMPRO_LPV_LIMIT' ) && PMPRO_LPV_LIMIT > 0 ) {
			$limit = intval( PMPRO_LPV_LIMIT );
		}
	}
	$ajax_data['parts'] = $parts;
	$ajax_data['limitparts'] = $limitparts;
	$ajax_data['limit'] = $limit;
	$curlev = pmpro_get_user_level();
	$curviews = 1;
	$expires = date( 'Y-m-d', strtotime( '+30 days' ) );
	$cookiestr .= "$curlev,$curviews";
	echo json_encode( $ajax_data );
	// echo '<pre>';
	// print_r( $ajax_data );
	// echo '</pre>';
	// }
	// setcookie( 'pmpro_lpv_co7unt', $curviews . ';' . $cookiestr, $expires, COOKIEPATH, COOKIE_DOMAIN, false );
	// setcookie( 'pmpro_lpv_count', $cookiestr . ';' . $month, $expires, '/' );
	exit();
}
// add_action( 'init', 'pbrx_header_set_cookie' );
/**
 * pbrx_modal_header Diagnostic info
 *
 * @return [type] [description]
 */
function pbrx_modal_banner() {
	$stg = '';
	// $location = plugins_url( '/js/lpv-diagnostics.js', __FILE__ );
	?>
	<form id="lpv-diagnostics-form">
		<input type="hidden" name="hidden" value="lpv-diagnostics-test">
	<?php
	$cur_lev = pmpro_get_user_level();
	$xyz = basename( __FILE__ );
	$xyz = 'Limit ' . PMPRO_LPV_LIMIT . ' | PERIOD ' . PMPRO_LPV_LIMIT_PERIOD . ' | js ' . PMPRO_LPV_USE_JAVASCRIPT;
	if ( isset( $_COOKIE['pmpro_lpv_count'] ) ) {
		$button_value = 'Reset Cookie';
		// $button_value = 3600 * 24 * 100 . ' seconds';
		$button = '<input type="hidden" name="token" value="reset"><input type="submit" class="button-primary" id="lpv_diagnostics_submit" value="' . $button_value . '" />';
		$stg = 'Current Level ' . $cur_lev . ' $_COOKIE(\'pmpro_lpv_count\') SET !! ' . $button;
	} else {
		$button_value = 'Set Cookie';
		$button = '<input type="hidden" name="token" value="set"><input type="submit" class="button-primary" id="lpv_diagnostics_submit" value="' . $button_value . '" />';
		$stg = 'Current Level ' . $cur_lev . ' $_COOKIE(\'pmpro_lpv_count\') NOT set ?!?!? ' . $button;
	}
		?>
	</form>
	<script type="text/javascript">
		insertCounter();
	</script>
	<script type="text/javascript">
		readCookie('lpv_pg_county');
	</script>
	<?php
	$header = '<h3 style="z-index:1;position:relative;text-align:center;color:tomato;">' . $stg . '<br>' . $xyz . ' Count <span id="lpv_counter"></span></h3><div id="data-returned">data-returned here</div><div id="demo">demo</div>';
	// if ( current_user_can( 'manage_options' ) ) {
		echo $header;
	// }
?>

	<script type="text/javascript">
		var demo = Get_Cookie( 'lpv_pg_county' );
		document.getElementById('demo').innerHTML = 'This is the ' +demo;
	</script>
<?php
}
add_action( 'wp_head', 'pbrx_modal_banner' );

function reset_lpv_cookie() {
	if ( ! empty( $_COOKIE['pmpro_lpv_count'] ) ) {
		// setcookie( 'pmpro_lpv_count', 'ns', 1, '/' );
	}
}

/**
 * Limit post views or load JS to do the same.
 * Hooks into wp action: add_action( 'wp', 'pmpro_lpv_wp1' );
 */
function pmpro_lpv_wp1() {
	global $current_user;
	if ( function_exists( 'pmpro_has_membership_access' ) ) {
		/**
		 * If we're viewing a page that the user doesn't have access to...
		 * Could add extra checks here.
		 */
		if ( ! pmpro_has_membership_access() ) {
			/**
			 * Filter which post types should be tracked by LPV
			 *
			 * @since .4
			 */
			$pmprolpv_post_types = apply_filters( 'pmprolpv_post_types', array( 'post' ) );
			$queried_object = get_queried_object();
			if ( empty( $queried_object ) || empty( $queried_object->post_type ) || ! in_array( $queried_object->post_type, $pmprolpv_post_types, true ) ) {
				return;
			}

			$hasaccess = apply_filters( 'pmprolpv_has_membership_access', true, $queried_object );
			if ( false === $hasaccess ) {
				pmpro_lpv_redirect22();
			}

			// if we're using javascript, just give them access and let JS redirect them.
			if ( defined( 'PMPRO_LPV_USE_JAVASCRIPT' ) && PMPRO_LPV_USE_JAVASCRIPT ) {
				wp_enqueue_script( 'wp-utils', includes_url( '/js/utils.js' ) );
				add_action( 'wp_footer', 'pmpro_lpv_wp_footer1' );
				add_filter( 'pmpro_has_membership_access_filter', '__return_true' );

				return;
			}

			// PHP is going to handle cookie check and redirect.
			$level = pmpro_getMembershipLevelForUser( $current_user->ID );
			if ( ! empty( $level->id ) ) {
				$level_id = $level->id;
			} else {
				$level_id = null;
			}

			$cookie_values = pmpro_lpv_cookie_values();

			// if count is above limit, redirect, otherwise update cookie.
			if ( defined( 'PMPRO_LPV_LIMIT' ) && $count > PMPRO_LPV_LIMIT ) {
				pmpro_lpv_redirect22();
			} else {
				// give them access and track the view.
				add_filter( 'pmpro_has_membership_access_filter', '__return_true' );

				if ( defined( 'PMPRO_LPV_LIMIT_PERIOD' ) ) {
					switch ( PMPRO_LPV_LIMIT_PERIOD ) {
						case 'hour':
							$expires = current_time( 'timestamp' ) + HOUR_IN_SECONDS;
							break;
						case 'day':
							$expires = current_time( 'timestamp' ) + DAY_IN_SECONDS;
							break;
						case 'week':
							$expires = current_time( 'timestamp' ) + WEEK_IN_SECONDS;
							break;
						case 'month':
							$expires = current_time( 'timestamp' ) + ( DAY_IN_SECONDS * 30 );
					}
				} else {
					$expires = current_time( 'timestamp' ) + ( DAY_IN_SECONDS * 30 );
				}

				// put the cookie string back together with updated values.
				$cookiestr = '';
				foreach ( $levellimits as $curlev => $curviews ) {
					$cookiestr .= "$curlev,$curviews";
				}
				// setcookie( 'pmpro_lpv_count', $cookiestr . ';' . $month, $expires, '/' );
				set_pmpro_lpv_cookie( $cookiestr );
			}
		}
	}
}

function pmpro_lpv_cookie_values() {
	$thismonth = date( 'n', current_time( 'timestamp' ) );
	$level_id = pmpro_get_user_level();
	// check for past views.
	if ( ! empty( $_COOKIE['pmpro_lpv_count'] ) ) {
		$month = $thismonth;
		$parts = explode( ';', sanitize_text_field( $_COOKIE['pmpro_lpv_count'] ) );
		if ( count( $parts ) > 1 ) { // just in case.
			$month = $parts[1];
		} else { // for one-time cookie format migration.
			$parts[0] = '0,0';
		}
		$limitparts = explode( ',', $parts[0] );
		$levellimits = array();
		$length = count( $limitparts );
		for ( $i = 0; $i < $length; $i++ ) {
			if ( $i % 2 === 1 ) {
				$levellimits[ $limitparts[ $i - 1 ] ] = $limitparts[ $i ];
			}
		}
		if ( $month == $thismonth && array_key_exists( $level_id, $levellimits ) ) {
			$count = $levellimits[ $level_id ] + 1; // same month as other views.
			$levellimits[ $level_id ]++;
		} elseif ( $month == $thismonth ) { // same month, but we haven't ticked yet.
			$count = 1;
			$levellimits[ $level_id ] = 1;
		} else {
			$count = 1;                     // new month.
			$levellimits = array();
			$levellimits[ $level_id ] = 1;
			$month = $thismonth;
		}
	} else {
		// new user.
		$count = 1;
		$levellimits = array();
		$levellimits[ $level_id ] = 1;
		$month = $thismonth;
	}
	$return['count'] = $count;
	$return['month'] = $month;
	return $return;
}

add_action( 'init', 'set_pmpro_lpv_cookie' );
function set_pmpro_lpv_cookie( $cookiestr = '1,3' ) {
	$expires = time() + 3600 * 24 * 100;
	$month = date( 'n', current_time( 'timestamp' ) );
	$curlev = pmpro_get_user_level();
	$curviews = 5;
	$cookiestr .= "$curlev,$curviews";

	if ( ! is_admin() && ! isset( $_COOKIE['pmpro_lpv_count'] ) ) {
		setcookie( 'pmpro_lpv_count', $cookiestr . ';' . $month, $expires, COOKIEPATH, COOKIE_DOMAIN, false );
	}
}
// add_action( 'init', 'reset_pmpro_lpv_cookie' );
function reset_pmpro_lpv_cookie() {
	if ( ! empty( $_COOKIE['pmpro_lpv_count'] ) ) {
		unset( $_COOKIE['pmpro_lpv_count'] );
		setcookie( 'pmpro_lpv_count', 'ns', 1, '/' );
	}
}

/**
 * Redirect to  the configured page or the default levels page
 */
function pmpro_lpv_redirect22() {
	$page_id = get_option( 'pmprolpv_redirect_page' );

	if ( empty( $page_id ) ) {
		$redirect_url = pmpro_url( 'levels' );
	} else {
		$redirect_url = get_the_permalink( $page_id );
	}

	wp_redirect( $redirect_url );    // here is where you can change which page is redirected to.
	exit;
}

/**
 * Function to add links to the plugin row meta
 */
function pmpro_lpv_ue_plugin_row_meta( $links, $file ) {
	if ( strpos( $file, 'pmpro-lpv-user-experience-diagnostics.php' ) !== false ) {
		$new_links = array(
			'<a href="' . esc_url( 'https://www.paidmembershipspro.com/add-ons/plugins-on-github/pmpro-limit-post-views/' ) . '" title="' . esc_attr( __( 'View Documentation', 'pmpro' ) ) . '">' . __( 'Docs', 'pmpro' ) . '</a>',
			'<a href="' . esc_url( 'https://paidmembershipspro.com/support/' ) . '" title="' . esc_attr( __( 'Visit Customer Support Forum', 'pmpro' ) ) . '">' . __( 'Support', 'pmpro' ) . '</a>',
		);
		$links = array_merge( $links, $new_links );
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'pmpro_lpv_ue_plugin_row_meta', 10, 2 );

/**
 * Javascript limit (hooks for these are above)
 * This is only loaded on pages that are locked for members
 */
function pmpro_lpv_wp_footer1() {
	global $current_user;
	// set mylevel to user's current level.
	if ( ! empty( $current_user->membership_level ) ) {
		$level_id = $current_user->membership_level->id;
	}
	if ( empty( $level_id ) ) {
		$level_id = 0;
	}
		?>
	<script>
		//vars
		var pmpro_lpv_count;        //stores cookie
		var parts;                  //cookie convert to array of 2 parts
		var count;                  //part 0 is the view count
		var month;                  //part 1 is the month
		var newticks = [];          // this will hold our usage this month by level
		
		//what is the current month?
		var d = new Date();
		var thismonth = d.getMonth();

		var mylevel = <?php esc_attr_e( $level_id ); ?>;
		
		//get cookie
		pmpro_lpv_count = wpCookies.get('pmpro_lpv_count');

		if (pmpro_lpv_count) {          
			//get values from cookie
			parts = pmpro_lpv_count.split(';');
			month = parts[1];
			if(month === undefined) { month = thismonth; parts[0] = "0,0"; } // just in case, and for cookie format migration
			limitparts = parts[0].split(',');
			var limitarrlength = limitparts.length;
			var curkey = -1;
			for (var i = 0; i < limitarrlength; i ++) {
				if(i % 2 == 0) {
					curkey = parseInt(limitparts[i], 10);
				} else {
					newticks[curkey] = parseInt(limitparts[i], 10);
					curkey = -1;
				}
			}
			if (month == thismonth && newticks[mylevel] !== undefined) {
				count = newticks[mylevel] + 1;  // same month as other views
				newticks[mylevel]++;            // advance it for writing to the cookie
			} else if(month == thismonth) { // it's the current month, but we haven't ticked yet.
				count = 1;
				newticks[mylevel] = 1;
			} else {
				count = 1;                      //new month
				newticks = [];                  // new month, so we don't care about old ticks
				newticks[mylevel] = 1;
				month = thismonth;
			}
		}
		else {
			//defaults
			count = 1;
			newticks[mylevel] = 1;
			month = thismonth;
		}

		// if count is above limit, redirect, otherwise update cookie.
		if ( count > <?php echo intval( PMPRO_LPV_LIMIT ); ?>) {
			<?php
				$page_id = get_option( 'pmprolpv_redirect_page' );
			if ( empty( $page_id ) ) {
				$redirect_url = pmpro_url( 'levels' );
			} else {
				$redirect_url = get_the_permalink( $page_id );
			}
			?>
			window.location.replace('<?php echo $redirect_url; ?>');
		} else {
			<?php
			if ( defined( 'PMPRO_LPV_LIMIT_PERIOD' ) ) {
				switch ( PMPRO_LPV_LIMIT_PERIOD ) {
					case 'hour':
						$expires = HOUR_IN_SECONDS;
						break;
					case 'day':
						$expires = DAY_IN_SECONDS;
						break;
					case 'week':
						$expires = WEEK_IN_SECONDS;
						break;
					case 'month':
						$expires = DAY_IN_SECONDS * 30;
				}
			}

			if ( empty( $expires ) ) {
				$expires = DAY_IN_SECONDS * 30;
			}
			?>
			
			// put the cookie string back together with updated values.
			var arrlen = newticks.length;
			var outstr = "";
			for(var i=0;i<arrlen;i++) {
				if(newticks[i] !== undefined) {
					outstr += "," + i + "," + newticks[i];
				}
			}
			// output the cookie to track the view
			wpCookies.set('pmpro_lpv_count', outstr.slice(1) + ';' + String(month), <?php echo $expires; ?>, '/');
		}
	</script>
	<?php
}

function test_ajax_data() {
	wp_register_script( 'lpv-diagnostics', plugins_url( '/js/lpv-diagnostics.js', __FILE__ ), array( 'jquery' ), false, false );
	wp_localize_script(
		'lpv-diagnostics', 'lpv_diagnostics_object', array(
			'lpv_diagnostics_ajaxurl' => admin_url( 'admin-ajax.php' ),
			'lpv_diagnostics_nonce' => wp_create_nonce( 'lpv-diagnostics-nonce' ),
			'lpv_diagnostics_user_level' => pmpro_get_user_level(),
			'lpv_diagnostics_cookie_values' => pmpro_lpv_cookie_values(),

		)
	);
	wp_enqueue_script( 'lpv-diagnostics' );
	wp_register_script( 'set-get-cookies', plugins_url( '/js/set-get-cookies.js', __FILE__ ), array( 'jquery' ), false, false );
	wp_enqueue_script( 'set-get-cookies' );
	wp_register_script( 'hit-counter', plugins_url( '/js/hit-counter.js', __FILE__ ), array( 'jquery' ), false, false );
	wp_enqueue_script( 'hit-counter' );
	wp_register_script( 'set-cookie-2009', plugins_url( '/js/set-cookie-2009.js', __FILE__ ), array( 'jquery' ), false, false );
	wp_enqueue_script( 'set-cookie-2009' );
}
add_action( 'wp_enqueue_scripts', 'test_ajax_data' );

function get_ajax_url() {
	wp_register_script( 'cookie-handle', plugins_url( '/js/lpv-ajax.js', __FILE__ ), array( 'jquery' ), false, false );
	wp_localize_script(
		'cookie-handle', 'cookie_object', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		)
	);
	wp_enqueue_script( 'cookie-handle' );
}
add_action( 'wp_enqueue_scripts', 'get_ajax_url' );
/*
function ajax_in_head(){
?>
<script type="text/javascript">
jQuery(document).ready(function() {

jQuery('.my-button').click(function(){

var data = {
action: 'my_custom_cookie'
};

jQuery.post('<?php echo admin_url( "admin-ajax.php" ); ?>', data);

});

});
</script>
<?php
}
add_action('wp_head', 'ajax_in_head');

disable add_action('wp_enqueue_scripts', 'get_ajax_url'); if you want to use wp_head action.
*/
add_action( 'wp_ajax_my_custom_cookie', 'my_custom_cookie_callback' );
add_action( 'wp_ajax_nopriv_my_custom_cookie', 'my_custom_cookie_callback' );
function my_custom_cookie_callback() {
	$hour = 24; // 24 = 24 hours, change number if you want!
	$calc = 3600 * $hour;
	setcookie( 'my-custom-cookie', 'my-ajax-cookie', time() + $calc, '/' );
}

