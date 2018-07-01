<?php
namespace Penn_Sports_Customizer\inc\classes;

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

/**
 * Attempt at a tax solution for Canada
 *
 * This solution assume the tax rate specified in the private class variables.
 *
 * NOTE: Ask your accountant how much tax you must charge!
 *
 * More info: https://sbinfocanada.about.com/od/pst/a/BC-PST.htm
 *
 * Edit as needed, then save this file in your plugins folder and activate it through the plugins page in the WP dashboard.
 *
 * @credit: https://github.com/ideadude
 */
class Eighty20_Canadian_Taxes {

	/**
	 * This class (instance)
	 *
	 * @var null|Canadian_Taxes
	 */
	private static $instance = null;

	/**
	 * The TPS tax rate. Set to null if not applicable
	 *
	 * TODO: Update for your province!
	 *
	 * @var float|null
	 */
	private $tps_rate = null;

	/**
	 * The TVQ tax rate. Set to null if not applicable
	 *
	 * TODO: Update for your province!
	 *
	 * @var float|null
	 */
	private $tvq_rate = null;

	/**
	 * The PST rate to use (set other rates to 'null' if not needed.)
	 *
	 * TODO: Update when needed for your province!
	 *
	 * @var float|null
	 */
	private $pst_rate = null;

	/**
	 * The HST rate to use (set other rates to 'null' if not needed.)
	 *
	 * TODO: Update when needed
	 *
	 * @var float|null
	 */
	private $hst_rate = 6.03;


	/**
	 * Province name
	 *
	 * TODO: Update for your province!
	 *
	 * @var string|null - Null means we're assuming the calculation is for all of Canada
	 */
	private $province = null;

	/**
	 * Province Abbreviation
	 *
	 * TODO: Update for your province!
	 *
	 * @var string|null - Null means we're assuming the calculation is for all of Canada
	 */
	private $province_code = null;

	/**
	 * Canadian_Taxes constructor.
	 */
	public function __construct() {
	}

	/**
	 * Load WordPress action/filters for plugin/functionality
	 */
	public function loadHooks() {

		add_filter( 'pmpro_level_cost_text', array( $this, 'levelCostText' ), 10, 2 );

		add_action( 'pmpro_checkout_boxes', array( $this, 'addToCheckoutPage' ), 10 );
		add_action( 'pmpro_checkout_preheader', array( $this, 'saveToSession' ), 10 );
		add_action( 'pmpro_after_checkout', array( $this, 'removeFromSession' ), 10 );

		// Add custom tax calculation if user indicated they're from "our" province in Canada
		if ( ! empty( $_REQUEST['taxregion'] ) || ! empty( $_SESSION['taxregion'] ) ) {
			add_filter( 'pmpro_tax', array( $this, 'calculateCustomTax' ), 10, 3 );
		} else {

			// They didn't indicate, but we're checking anyway (only if billing info is enabled for gateway!)
			if ( ! empty( $_REQUEST['bstate'] ) && ! empty( $_REQUEST['bcountry'] ) ) {

				$province = trim( strtolower( $_REQUEST['bstate'] ) );
				$country = trim( strtolower( $_REQUEST['bcountry'] ) );

				if ( ( in_array( $province, array( strtolower( $this->province_code ), strtolower( $this->province ) ) ) && 'ca' === $country ) || 'ca' == $country ) {
					add_filter( 'pmpro_tax', array( $this, 'calculateCustomTax' ), 10, 3 );
				}
			}
		}
	}
	/**
	 * Clear the session object on our behalf
	 */
	public function removeFromSession() {

		if ( isset( $_SESSION['taxregion'] ) ) {
			unset( $_SESSION['taxregion'] );
		}
	}

	/**
	 * Handle off-site gateways
	 */
	public function saveToSession() {

		if ( isset( $_REQUEST['tax_region'] ) ) {
			$_SESSION['taxregion'] = $_REQUEST['taxregion'];
		}
	}

	/**
	 * Generate HTML to include on checkout page for Residents of the specified province in Canada
	 */
	public function addToCheckoutPage() {

		$region_selected = ( ( ! empty( $_REQUEST['taxregion'] ) || ! empty( $_SESSION['taxregion'] ) || strtolower( $_REQUEST['bcountry'] ) == 'ca'  ) ? 'checked="checked" ' : null ); ?>
		<table id="pmpro_pricing_fields" class="pmpro_checkout" width="100%" cellpadding="0" cellspacing="0" border="0">
			<thead>
			<tr>
				<th>
					<?php
					printf(
						__( 'Resident of %s', 'paid-memberships-pro' ),
						( ! empty( $this->province ) ? $this->province : __( 'Canada', 'paid-memberships-pro' ) )
					);
					?>
				</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td>
					<div>
						<input id="taxregion" name="taxregion" type="checkbox"
							   value="1" <?php echo $region_selected; ?>/>
							<?php
							if ( ! empty( $this->province ) ) {
								printf( __( 'Check this box if your billing address is in %s, Canada', 'paid-memberships-pro' ), $this->province );
							} else {
								_e( 'Check this box if your billing address is in Canada', 'paid-memberships-pro' ); }
?>
					</div>
				</td>
			</tr>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Conditionally add text to indicate that TPS or TVQ tax will be added
	 *
	 * @param $text
	 * @param $level
	 *
	 * @return string
	 */
	public function levelCostText( $text, $level ) {

		if ( ! empty( $this->tvq_rate ) || ! empty( $this->tps_rate ) || ! empty( $this->pst_rate ) || ! empty( $this->hst_rate ) ) {

			$text .= sprintf(
				__( ' Members in %1$s will be charged a %2$s%3$s%4$s%5$s%6$s', 'paid-memberships-pro' ),
				( ! empty( $this->province ) ? $this->province : __( 'Canada', 'paid-memberships-pro' ) ),
				( ! empty( $this->hst_rate ) ? sprintf( __( '%1$.3f%% HST tax rate', 'paid-memberships-pro' ), $this->hst_rate ) : null ),
				( ! empty( $this->pst_rate ) ? sprintf( __( '%1$.3f%% PST tax rate', 'paid-memberships-pro' ), $this->pst_rate ) : null ),
				( ! empty( $this->tps_rate ) ? sprintf( __( '%1$.3f%% TPS tax rate', 'paid-memberships-pro' ), $this->tps_rate ) : null ),
				( ! empty( $this->tps_rate ) ? __( ' and a ', 'paid-memberships-pro' ) : null ),
				( ! empty( $this->tvq_rate ) ? sprintf( __( '%1$.3f%% TVQ tax rate', 'paid-memberships-pro' ), $this->tvq_rate ) : null )
			);
		}

		return $text;
	}

	/**
	 * Calculate the tax(es) to add to the base price during checkout
	 *
	 * @param $tax_amount
	 * @param $settings
	 * @param $order
	 *
	 * @return float
	 *
	 * @filter pmpro_tax - Tax calculation for Paid Memberships Pro
	 */
	public function calculateCustomTax( $tax_amount, $settings, $order ) {

		$base_cost = (float) $settings['price'];

		// Instantiate the variable w/a value we can do something about/with in a typed language
		if ( empty( $tax_amount ) ) {
			$tax_amount = 0;
		}

		// Apply TPS tax
		if ( ! empty( $this->tps_rate ) ) {
			$tax_amount += (float) $this->calculateTax( $this->tps_rate, $base_cost );
		}

		// Apply TVQ tax
		if ( ! empty( $this->tvq_rate ) ) {
			$tax_amount += (float) $this->calculateTax( $this->tvq_rate, $base_cost );
		}

		// Apply PST tax
		if ( ! empty( $this->pst_rate ) ) {
			$tax_amount += (float) $this->calculateTax( $this->pst_rate, $base_cost );
		}

		// Apply HST tax
		if ( ! empty( $this->hst_rate ) ) {
			$tax_amount += (float) $this->calculateTax( $this->hst_rate, $base_cost );
		}

		return $tax_amount;
	}

	/**
	 * Standard tax calculation
	 *
	 * @param float $rate
	 * @param float $base_amount
	 *
	 * @return float The tax amount (to be added to the total)
	 *
	 * @access private
	 */
	private function calculateTax( $rate, $base_amount ) {

		// Basic percentage calculation
		return round( ( (float) $base_amount * ( (float) $rate ) ) / 100, 2 );
	}


	/**
	 * Return or instantiate the Canadian_Taxes object
	 *
	 * @return Canadian_Taxes|null
	 */
	public static function getInstance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
add_action( 'plugins_loaded', array( Canadian_Taxes::getInstance(), 'loadHooks' ), 10 );
