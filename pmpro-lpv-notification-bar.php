<?php
/**
 * Notification bar when post views are being tracked and restricted by the Limit Post Views Add On
 */
function a_notification_bar_for_limit_post_view() {
	// Check for past views. Needs to check if the post is locked at all by default.
	if ( isset( $_COOKIE['pmpro_lpv_count'] ) ) {
		global $current_user;

		// Check cookie for views value.
		$parts = explode( ';', sanitize_text_field( $_COOKIE['pmpro_lpv_count'] ) );
		$limitparts = explode( ',', $parts[0] );

		// Get the level limit for the current user.
		if ( defined( 'PMPRO_LPV_LIMIT' ) && PMPRO_LPV_LIMIT > 0 ) {
			$limit = intval( PMPRO_LPV_LIMIT );
			$remaining_views = $limit - ( $limitparts[1] + 1 );
			/**
			 * If php-intl isn't enabled on the server,
			 * the NumberFormatter class won't be present,
			 * so we create a backup plan.
			 */
			if ( class_exists( 'NumberFormatter' ) ) {
				$f = new NumberFormatter( 'en', NumberFormatter::SPELLOUT );
				$formatted = $f->format( $remaining_views );
			} else {
				$formatted = number_format( $remaining_views );
			}

			$article_s = sprintf( _n( '%s free article', '%s free articles', $formatted, 'paid-memberships-pro' ), number_format_i18n( $formatted ) );
			?>
			<div style="text-align:center;background: #d4f1df; bottom: 0; font-size: 2rem; left:0; padding:1rem; position: fixed; width:100%;z-index:333;"><span id="lpv_count">lpv_count</span>
			You have <span style="color: #B00000;"><?php echo esc_html( $article_s ); ?></span> remaining. 
			<a href="<?php echo wp_login_url( get_permalink() ); ?>" title="Log in">Log in</a> or <a href="<?php echo pmpro_url( 'levels' ); ?>" title="Subscribe now">Subscribe</a> now for unlimited online access99.
		</div>
		<?php
		}
	}
}
add_action( 'wp_footer' , 'a_notification_bar_for_limit_post_view', 15 );

// add_action( 'init', 'pbrx_header_js_set_cookie');
function pbrx_header_js_set_cookie() {
?>
<style type="text/css">
	#freakin {
		position:absolute;
		top:1rem;
		background:yellow;
		padding: 1rem;
		width: 100%;
		z-index: 222; 
	}
</style>
	<div id="freakin"><h2>freakin</h2>
	<script type="text/javascript">
		/**
		 * Get the value of a cookie
		 * Source: https://gist.github.com/wpsmith/6cf23551dd140fb72ae7
		 * @param  {String} name  The name of the cookie
		 * @return {String}       The cookie value
		 */
		var getCookie = function (name) {
			var value = "; " + document.cookie;
			var parts = value.split("; " + name + "=");
			if (parts.length == 2) return parts.pop().split(";").shift();
		};
	</script>
	<script type="text/javascript">
		var thisCookie = getCookie('lpv_count');
		var myarr = thisCookie.split("|");
		var count = Number(myarr[1])+Number(1);
			// document.write(myarr[0] + "::" + myarr[1]+ "::" + myarr[2]); // value
	</script>
	<script type="text/javascript">
		if ( count > 5 ) {
			count = 0;
			// window.location = 'https://google.com';
			document.cookie = 'lpv_count=level|' + count + '|limit; expires=Fri, 31 Dec 2024 23:59:59 GMT';
		} else {
			document.cookie = 'lpv_count=level|' + count + '|limit; expires=Fri, 31 Dec 2024 23:59:59 GMT';
		}
	</script>
	</div>
<?php
}
