<?php

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

/**
 * An example of how to write code to PEAR's standards
 *
 * Docblock comments start with "/**" at the top.  Notice how the "/"
 * lines up with the normal indenting and the asterisks on subsequent rows
 * are in line with the first asterisk.  The last line of comment text
 * should be immediately followed on the next line by the closing
 *
 * @category   CategoryName
 * @package    PackageName
 * @author     pbrocks <author@example.com>
 */
class PMPro_Tabbed_Settings {
	/**
	 * [init description]
	 *
	 * @return [type] [description]
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'pmpro_beta_demo' ) );
		add_action( 'init', array( __CLASS__, 'beta_admin_init' ) );
		// add_action( 'admin_footer', array( __CLASS__, 'beta_diagnostic_message' ) );
	}

	public static function pmpro_beta_demo() {
		global $beta_dash;
		$slug = preg_replace( '/_+/', '-', __FUNCTION__ );
		$label = ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) );
		$beta_dash = add_dashboard_page( __( $label, 'pmpro-beta' ), __( $label, 'pmpro-beta' ), 'manage_options', $slug . '.php', array( __CLASS__, 'pmpro_beta_page_demo' ), 'dashicons-groups', 20 );
		// add_action( "load-{$beta_dash}", array( __CLASS__, 'beta_tabbed_settings' ) );
	}


	/**
	 * Debug Information
	 *
	 * @since 1.0.0
	 *
	 * @param bool $html Optional. Return as HTML or not
	 *
	 * @return string
	 */
	public static function pmpro_beta_page_demo() {
		global $pmpro_levels;
		echo '<div class="wrap">';
		echo '<h2>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h2>';
		$screen = get_current_screen();
		echo '<h4 style="color:rgba(250,128,114,.7);">Current Screen is <span style="color:rgba(250,128,114,1);">' . $screen->id . '</span></h4>';

		// Let's grab the tabs that we created in the `tabbed-settings.php`
		// beta_admin_tabs( $current = 'homepage' );
		self::beta_tabbed_settings();
		$this_theme = wp_get_theme();
		echo '<h4>Theme is ' . sprintf(
			__( '%1$s and is version %2$s', 'text-domain' ),
			$this_theme->get( 'Name' ),
			$this_theme->get( 'Version' )
		) . '</h4>';
		echo '<h4>Templates found in ' . get_template_directory() . '</h4>';
		echo '<h4>Stylesheet found in ' . get_stylesheet_directory() . '</h4>';

		echo '<pre>';
		print_r( $pmpro_levels );
		echo '</pre>';
		echo '</div>';
	}
	public static function beta_admin_init() {
		$beta_settings = get_option( 'beta_tabbed_settings' );
		if ( empty( $beta_settings ) ) {
			$beta_settings = array(
				'beta_intro' => 'Some intro text for the home page',
				'beta_tag_class' => false,
				'beta_code' => false,
			);
			add_option( 'beta_tabbed_settings', $beta_settings, '', 'yes' );
		}
	}


	public static function beta_save_theme_settings() {
		global $pagenow;
		$beta_settings = get_option( 'beta_tabbed_settings' );
		if ( $pagenow == 'index.php' && $_GET['page'] == 'pmpro-beta-demo.php' ) {
			if ( isset( $_GET['tab'] ) ) {
				$tab = $_GET['tab'];
			} else {
				$tab = 'homepage';
			}

			switch ( $tab ) {
				case 'general':
					$beta_settings['beta_tag_class']    = $_POST['beta_tag_class'];
					break;
				case 'primer':
					$beta_settings['beta_code']  = $_POST['beta_code'];
					break;
				case 'homepage':
					$beta_settings['beta_intro']    = $_POST['beta_intro'];
					break;
			}
		}

		if ( ! current_user_can( 'unfiltered_html' ) ) {
			if ( $beta_settings['beta_code'] ) {
				$beta_settings['beta_code'] = stripslashes( esc_textarea( wp_filter_post_kses( $beta_settings['beta_code'] ) ) );
			}
			if ( $beta_settings['beta_intro'] ) {
				$beta_settings['beta_intro'] = stripslashes( esc_textarea( wp_filter_post_kses( $beta_settings['beta_intro'] ) ) );
			}
		}

		$updated = update_option( 'beta_tabbed_settings', $beta_settings );
	}

	public static function beta_diagnostic_message() {
		global $current_user;
		echo '<div id="lpv-head"><div>$_GET <pre>';
		print_r( $_GET );
		echo '</pre></div>';
		echo '<div>$_REQUEST <pre>';
		print_r( $_REQUEST );
		echo '</pre></div>';
		echo '<div>$_POST <pre>';
		print_r( $_POST );
		add_action( 'template_redirect', 'pmpromc_processSubscriptions', 1 );
		add_filter( 'wp_redirect', 'pmpromc_processSubscriptions', 99 );
		add_action( 'pmpro_membership_post_membership_expiry', 'pmpromc_processUnsubscriptions' );
		echo '</pre></div><div>';
		echo __FUNCTION__;
		echo '<br>Line ' . __LINE__ . '</div></div>';

		// $message = '<h3 id="lpv-head"><span id="lpv_counter"></span>' . $xyz . ' <br> ' . $stg . '</h3>';
		// if ( current_user_can( 'manage_options' ) ) {
		// echo $message;
		// }
	}




	public static function beta_admin_tabs( $current = 'homepage' ) {
		$tabs = array(
			'homepage' => 'Home',
			'general' => 'General',
			'settings' => 'Settings',
			'primer' => 'Primer',
		);
		$links = array();
		echo '<div id="icon-themes" class="icon32"><br></div>';
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $tabs as $tab => $name ) {
			$class = ( $tab == $current ) ? ' nav-tab-active' : '';
			echo "<a class='nav-tab$class' href='?page=pmpro-beta-demo.php&tab=$tab'>$name</a>";

		}
		echo '</h2>';
	}

	public static function beta_tabbed_settings() {
		global $pagenow;
		$beta_settings = get_option( 'beta_tabbed_settings' );
		if ( 'true' == esc_attr( $_GET['updated'] ) ) {
			echo '<div class="updated" ><p> Settings updated.</p></div>';
		}

		if ( isset( $_GET['tab'] ) ) {
			self::beta_admin_tabs( $_GET['tab'] );
		} else {
			self::beta_admin_tabs( 'homepage' );
		}
		?>
<style type="text/css">
	#lpv-head {
		text-align: center;
	}
</style>
<div id="poststuff">
	<div class="tab-content-wrapper">
		<form method="post" action="<?php admin_url( 'index.php?page=pmpro-beta-demo.php' ); ?>">
			<?php
			// wp_nonce_field( 'beta-settings-page' );
			if ( $pagenow == 'index.php' && $_GET['page'] == 'pmpro-beta-demo.php' ) {

				if ( isset( $_GET['tab'] ) ) {
					$tab = $_GET['tab'];
				} else {
					$tab = 'homepage';
				}

				echo '<table class="form-table">';
				switch ( $tab ) {
					case 'general':
						?>
						<tr>
								<?php echo self::pmpro_beta_demo_home(); ?>
							<th><label for="beta_tag_class">Tags with CSS classes:</label></th>
							<td>
								<?php echo self::pmpro_beta_demo_home(); ?>
							</td>
						</tr>
						<?php
						break;
					case 'settings':
						?>
						<tr>
							<th><label for="beta_settings">$beta_settings:</label></th>
							<td>
								<?php echo self::pmpro_beta_demo_home(); ?>
							<pre>
								<?php
								print_r( $beta_settings );
								?>
							</pre>
								<br/>
								<span class="description">This shows what has been saved to the database so far.</span>
							</td>
						</tr>
						<?php
						break;
					case 'primer':
						?>
						<tr>
							<th><label for="beta_code">Insert tracking code:</label></th>
							<td>
								<?php echo self::pmpro_beta_demo_primer() . '<br>'; ?>
								<?php echo PMPro_Primer\pmpro_primer() . '<br>'; ?>
								<textarea id="beta_code" name="beta_code" cols="60" rows="5"><?php echo esc_html( stripslashes( $beta_settings['beta_code'] ) ); ?></textarea><br/>
								<span class="description">Enter your Google Analytics tracking code:</span>
							</td>
						</tr>
						<?php
						break;
					case 'homepage':
						?>
						<tr>
							<th><label for="beta_intro"><?php echo $tab; ?></label></th>
							<td>
								<?php echo self::pmpro_beta_demo_home(); ?>

								<textarea id="beta_intro" name="beta_intro" cols="60" rows="5" ><?php echo esc_html( stripslashes( $beta_settings['beta_intro'] ) ); ?></textarea><br/>
								<span class="description">Enter the introductory text for the home page:</span>
							</td>
						</tr>
						<?php
						break;
				}
				echo '</table>';
			}
			?>
					<p class="submit" style="clear: both;">
						<input type="submit" name="Submit"  class="button-primary" value="Update Settings" />
						<input type="hidden" name="beta-settings-submit" value="Y" />
					</p>
			</form>
		</div>
	</div>
</div>
		<?php

	}

	/**
	 * [pmpro_beta_demo_home description]
	 * PMPro_Helpers\inc\classes\PMPro_Primer\pmpro_primer_page();
	 *
	 * @return [type] [description]
	 */
	public static function pmpro_beta_demo_home() {
		echo __FUNCTION__;
	}
	/**
	 * [pmpro_beta_demo_primer description]
	 * PMPro_Helpers\inc\classes\PMPro_Primer\pmpro_primer_page();
	 *
	 * @return [type] [description]
	 */
	public static function pmpro_beta_demo_primer() {
		echo __FUNCTION__;
	}
}
