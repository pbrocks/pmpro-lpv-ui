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
		add_action( 'admin_menu', array( __CLASS__, 'pmpro_lpv_demo' ) );
		add_action( 'init', array( __CLASS__, 'lpv_admin_init' ) );
		// add_action( 'admin_footer', array( __CLASS__, 'lpv_diagnostic_message' ) );
	}

	public static function pmpro_lpv_demo() {
		global $lpv_dash;
		$slug = preg_replace( '/_+/', '-', __FUNCTION__ );
		$label = ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) );
		$lpv_dash = add_dashboard_page( __( $label, 'pmpro-lpv' ), __( $label, 'pmpro-lpv' ), 'manage_options', $slug . '.php', array( __CLASS__, 'pmpro_lpv_page_demo' ), 'dashicons-groups', 20 );
		// add_action( "load-{$lpv_dash}", array( __CLASS__, 'lpv_tabbed_settings' ) );
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
	public static function pmpro_lpv_page_demo() {
		global $pmpro_levels;
		echo '<div class="wrap">';
		echo '<h2>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h2>';
		$screen = get_current_screen();
		echo '<h4 style="color:rgba(250,128,114,.7);">Current Screen is <span style="color:rgba(250,128,114,1);">' . $screen->id . '</span></h4>';

		// Let's grab the tabs that we created in the `tabbed-settings.php`
		// lpv_admin_tabs( $current = 'homepage' );
		self::lpv_tabbed_settings();

		echo '<pre>';
		print_r( $pmpro_levels );
		echo '</pre>';
		echo '</div>';
	}
	public static function lpv_admin_init() {
		$lpv_settings = get_option( 'lpv_tabbed_settings' );
		if ( empty( $lpv_settings ) ) {
			$lpv_settings = array(
				'lpv_intro' => 'Some intro text for the home page',
				'lpv_tag_class' => false,
				'lpv_code' => false,
			);
			add_option( 'lpv_tabbed_settings', $lpv_settings, '', 'yes' );
		}
	}


	public static function lpv_save_theme_settings() {
		global $pagenow;
		$lpv_settings = get_option( 'lpv_tabbed_settings' );
		if ( $pagenow == 'index.php' && $_GET['page'] == 'pmpro-lpv-demo.php' ) {
			if ( isset( $_GET['tab'] ) ) {
				$tab = $_GET['tab'];
			} else {
				$tab = 'homepage';
			}

			switch ( $tab ) {
				case 'general':
					$lpv_settings['lpv_tag_class']    = $_POST['lpv_tag_class'];
					break;
				case 'theme':
					$lpv_settings['lpv_code']  = $_POST['lpv_code'];
					break;
				case 'homepage':
					$lpv_settings['lpv_intro']    = $_POST['lpv_intro'];
					break;
			}
		}

		if ( ! current_user_can( 'unfiltered_html' ) ) {
			if ( $lpv_settings['lpv_code'] ) {
				$lpv_settings['lpv_code'] = stripslashes( esc_textarea( wp_filter_post_kses( $lpv_settings['lpv_code'] ) ) );
			}
			if ( $lpv_settings['lpv_intro'] ) {
				$lpv_settings['lpv_intro'] = stripslashes( esc_textarea( wp_filter_post_kses( $lpv_settings['lpv_intro'] ) ) );
			}
		}

		$updated = update_option( 'lpv_tabbed_settings', $lpv_settings );
	}

	public static function lpv_diagnostic_message() {
		global $current_user;
		echo '<div id="lpv-head"><div>$_GET <pre>';
		print_r( $_GET );
		echo '</pre></div>';
		echo '<div>$_REQUEST <pre>';
		print_r( $_REQUEST );
		echo '</pre></div>';
		echo '<div>$_POST <pre>';
		print_r( $_POST );

		echo '</pre></div><div class="full">';
		echo __FUNCTION__;
		echo '<br>Line ' . __LINE__ . '</div></div>';

		// $message = '<h3 id="lpv-head"><span id="lpv_counter"></span>' . $xyz . ' <br> ' . $stg . '</h3>';
		// if ( current_user_can( 'manage_options' ) ) {
		// echo $message;
		// }
	}




	public static function lpv_admin_tabs( $current = 'homepage' ) {
		$tabs = array(
			'homepage' => 'Home',
			'general' => 'General',
			'settings' => 'Settings',
			'theme' => 'Theme',
		);
		$links = array();
		echo '<div id="icon-themes" class="icon32"><br></div>';
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $tabs as $tab => $name ) {
			$class = ( $tab == $current ) ? ' nav-tab-active' : '';
			echo "<a class='nav-tab$class' href='?page=pmpro-lpv-demo.php&tab=$tab'>$name</a>";

		}
		echo '</h2>';
	}

	public static function lpv_tabbed_settings() {
		global $pagenow;
		$lpv_settings = get_option( 'lpv_tabbed_settings' );
		if ( 'true' == esc_attr( $_GET['updated'] ) ) {
			echo '<div class="updated" ><p> Settings updated.</p></div>';
		}

		if ( isset( $_GET['tab'] ) ) {
			self::lpv_admin_tabs( $_GET['tab'] );
		} else {
			self::lpv_admin_tabs( 'homepage' );
		}
		?>
<style type="text/css">
	#lpv-head {
		text-align: center;
	}
</style>
<div id="lpv-post-stuff">
	<div class="tab-content-wrapper">
		<form method="post" action="<?php admin_url( 'index.php?page=pmpro-lpv-demo.php' ); ?>">
			<?php
			// wp_nonce_field( 'lpv-settings-page' );
			if ( $pagenow == 'index.php' && $_GET['page'] == 'pmpro-lpv-demo.php' ) {

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
								<?php echo self::pmpro_lpv_demo_home() . '<br>'; ?>
							<th><label for="lpv_tag_class">Tags with CSS classes:</label></th>
							<td>
								<?php echo self::pmpro_lpv_demo_home() . '<br>'; ?>
							</td>
						</tr>
						<?php
						break;
					case 'settings':
						?>
						<tr>
							<th><label for="lpv_settings">$lpv_settings:</label></th>
							<td>
								<?php echo self::pmpro_lpv_demo_home() . '<br>'; ?>
							<pre>
								<?php
								print_r( $lpv_settings );
								?>
							</pre>
								<br/>
								<span class="description">This shows what has been saved to the database so far.</span>
							</td>
						</tr>
						<?php
						break;
					case 'theme':
						?>
						<tr>
							<th><label for="lpv_code">Insert tracking code:</label></th>
							<td>
								<?php echo self::pmpro_lpv_demo_primer() . '<br>'; ?>
								<?php
									$this_theme = wp_get_theme();
									echo '<h4>Theme is ' . sprintf(
										__( '%1$s and is version %2$s', 'text-domain' ),
										$this_theme->get( 'Name' ),
										$this_theme->get( 'Version' )
									) . '</h4>';
									echo '<h4>Templates found in ' . get_template_directory() . '</h4>';
									echo '<h4>Stylesheet found in ' . get_stylesheet_directory() . '</h4>';

								?>
							</td>
						</tr>
						<?php
						break;
					case 'homepage':
						?>
						<tr>
							<th><label for="lpv_intro"><?php echo $tab; ?></label></th>
							<td>
								<?php echo self::pmpro_lpv_demo_settings() . '<br>'; ?>

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
						<input type="hidden" name="lpv-settings-submit" value="Y" />
					</p>
			</form>
		</div>
	</div>
</div>
		<?php
	}
	public static function set_pmpro_lpv_settings() {
		$lv_settings = new Sidetrack_Settings();
		$lv_settings->prefix = 'pmp_lpv_';
		$lv_settings->menu_title = 'PMPro LPV';

	}

	/**
	 * [pmpro_lpv_demo_primer description]
	 * PMPro_Helpers\inc\classes\PMPro_Primer\pmpro_primer_page();
	 *
	 * @return [type] [description]
	 */
	public static function pmpro_lpv_demo_settings() {
		echo __FUNCTION__;
		$lv_settings = new Sidetrack_Settings();
		$lv_settings->prefix = 'pmp_lpv_';
		$lv_settings->menu_title = 'PMPro LPV';
		$lv_settings->add_field(
			array(
				'id' => 'lpv_field_name',
				'title' => 'PMPro LPV Input',
				'description' => 'What PMPro LPV Input is used for.',
				'input' => 'text',
				'default' => 'PMPro LPV default',
			)
		);
		$lv_settings->add_field(
			array(
				'id' => 'lpv_field_name_1',
				'title' => 'PMPro LPV Input 1',
				'description' => 'What PMPro LPV Input 1 is used for.',
				'input' => 'text',
				'default' => 'PMPro LPV default 1',
			)
		);
		$lv_settings->hook();
		$lv_settings->settings_screen();
	}
	/**
	 * [pmpro_lpv_demo_home description]
	 * PMPro_Helpers\inc\classes\PMPro_Primer\pmpro_primer_page();
	 *
	 * @return [type] [description]
	 */
	public static function pmpro_lpv_demo_home() {
		echo __FUNCTION__;
	}
	/**
	 * [pmpro_lpv_demo_primer description]
	 * PMPro_Helpers\inc\classes\PMPro_Primer\pmpro_primer_page();
	 *
	 * @return [type] [description]
	 */
	public static function pmpro_lpv_demo_primer() {
		echo __FUNCTION__;
	}
}
