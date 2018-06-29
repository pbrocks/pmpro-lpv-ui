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
			<div style="text-align:center;background: #d4f1df; bottom: 0; font-size: 2rem; left:0; padding:1rem; position: fixed; width:100%;z-index:333;">
			You have <span style="color: #B00000;"><?php echo esc_html( $article_s ); ?></span> remaining. 
			<a href="<?php echo wp_login_url( get_permalink() ); ?>" title="Log in">Log in</a> or <a href="<?php echo pmpro_url( 'levels' ); ?>" title="Subscribe now">Subscribe</a> now for unlimited online access.
		</div>
		<?php
		}
	}
}
add_action( 'wp_footer' , 'a_notification_bar_for_limit_post_view', 15 );
