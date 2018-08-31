<?php
/**
 * Quick-and-dirty class for managing WordPress options for a plugin.
 * Include this class in your plugin. Create an instance and use as follows:
 *
 * Required Usage:
 * Plugin Name: Sidetrack Settings class
 *   // Create new instance
 *   $settings = new wordpress_settings;
 *
 *   // Configure the prefix for storing and referencing your plugin's settings
 *   $settings->prefix = "plugin_prefix_";
 *
 *   // Menu title for settings screen.
 *   $settings->menu_title = "Name of Plugin";
 *
 *   // Add one or more settings fields.
 *   // Here's a full example. Only required field is `id`
 *   $settings->add_field(array(
 *
 *     # Required. The field identifier.
 *     "id" => "field_name",
 *
 *     # Optional.
 *     # The title of the field. When empty, defaults to a value derived from
 *     # the `id`. Used for labeling field inputs.
 *     "title" => "Field Title",
 *
 *     # Optional.
 *     # Long text description describing the field. Can include HTML tags.
 *     "description" => "What this field is used for.",
 *
 *     # Optional.
 *     # The type of input to present to the user.
 *     # Defaults to "text", e.g., `<input type="text">`
 *     # NOTE: currently "text" is the only supported option.
 *     "input" => "text",
 *
 *     # Optoinal.
 *     # The default value for the field
 *     "default" => "",
 *
 *   ));
 *
 *   //
 *   // Once configured call `hook()` to integrate into WordPress.
 *   //
 *   $settings->hook();
 *
 *
 * Optional Configuration:
 *
 *   // Page title for settings screen. Defaults to the menu title.
 *   $settings->page_title = "Settings for Name of Plugin";
 *
 *   // Width of input text fields. Defaults to "80%".
 *   $settings->input_text_width = "100%";
 */
class Sidetrack_Settings {

	/**
	 * The prefix used when settings are stored in the WordPress database
	 *
	 * @var string
	 */
	public $prefix = '';

	/**
	 * The title of the WordPress Dashboard settings menu item.
	 *
	 * @var string
	 */
	public $menu_title = '';

	/**
	 * The page title of the settings screen in the WordPress Dashboard.
	 *
	 * @var string
	 */
	public $page_title = '';

	/**
	 * The CSS `width` value to assign to text inputs.
	 *
	 * @var string
	 */
	public $input_text_width = '80%';

	/**
	 * Add a settings field.
	 *
	 * @param array $args
	 */
	public function add_field( array $args ) {
		$field = $this->create_field( $args );
		$this->_fields[ $field['id'] ] = $field;
	}

	/**
	 * Internal collection of settings fields.
	 *
	 * @var array
	 */
	protected $_fields = array();

	/**
	 * Create a settings field array, ensuring defaults are populated.
	 *
	 * @param array $args
	 * @return array
	 */
	public function create_field( array $args ) {
		$defaults = array(
			'id' => '',
			'title' => '',
			'description' => '',
			'input' => 'text',  // currently only "text"
			'default' => '',
		);
		$field = array_merge( $defaults, $args );
		if ( empty( $field['title'] ) ) {
			$field['title'] = ucwords( implode( ' ', array_map( 'trim', preg_split( '/[_-]+/', $field['id'] ) ) ) );
		}
		return $field;
	}

	/**
	 * Check if a field exists.
	 *
	 * @param string $k The id of the field to check.
	 * @return boolean|array FALSE when field was not found. Otherwise, returns a field array.
	 */
	public function field_exists( $k ) {
		if ( array_key_exists( $k, $this->_fields ) ) {
			return $this->_fields[ $k ];
		} else {
			return false;
		}
	}

	/**
	 * Format a field id as a unique option key, for storing the field value in the WordPress database.
	 *
	 * @param string $k The field id.
	 * @return string
	 */
	public function option_key( $k ) {
		if ( empty( $this->prefix ) ) {
			return $k;
		} else {
			return $this->prefix . $k;
		}
	}

	/**
	 * Check if the named settings field exists.
	 *
	 * @param string $k The field id.
	 * @return boolean
	 */
	function __isset( $k ) {
		return ! ! $this->field_exists( $k );
	}

	/**
	 * Remove a settings field.
	 *
	 * @param string $k The field id.
	 */
	function __unset( $k ) {
		unset( $this->_fields[ $k ] );
	}

	/**
	 * Get accessor for retrieving settings field values.
	 *
	 * @param string $k The field id
	 * @return mixed NULL when the field was not found. Otherwise, the configured field value. Or the field's default value when a value has not yet been set.
	 */
	function __get( $k ) {
		if ( $field = $this->field_exists( $k ) ) {
			$ok = $this->option_key( $k );
			$v = get_option( $ok, $field['default'] );
			return $v;
		}
		return null;
	}

	/**
	 * Set accessor for configuring a settings field value
	 *
	 * @param string $k The field id
	 * @param string $v The field value, as fed to `update_option`. Can contain any serializable value.
	 */
	function __set( $k, $v ) {
		if ( $this->field_exists( $k ) ) {
			$ok = $this->option_key( $k );
			update_option( $ok, $v );
		}
	}

	/**
	 * Hook into WordPress.
	 * Sets up the settings form screens in WordPress Dashboard.
	 */
	public function hook() {
		if ( is_admin() ) {
			add_action( 'admin_init', array( &$this, 'settings_form_save' ) );
			add_action( 'admin_menu', array( &$this, 'setup_menu' ) );
		}
	}

	/**
	 * Detect and handle form save requests.
	 */
	public function settings_form_save() {
		$save_action = $this->prefix . 'settings_form_save';
		$do_save = isset( $_POST['action'] ) && $_POST['action'] === $save_action;
		if ( $do_save ) {
			if ( check_admin_referer( $save_action ) ) {
				foreach ( $_POST as $fid => $v ) {
					$this->$fid = $v;
				}
				$redirect_url = $_POST['_wp_http_referer'];
				if ( strpos( $redirect_url, '&updated=1' ) === false ) {
					$redirect_url .= '&updated=1';
				}
				wp_safe_redirect( $redirect_url );
			} else {
				wp_die( 'Unable to save settings: nonce verification failed.' );
			}
		}
	}

	/**
	 * Add settings screen to WordPress dashboard.
	 */
	public function setup_menu() {
		// add_submenu_page( 'options-general.php', ( $this->page_title ? $this->page_title : $this->menu_title ), $this->menu_title, 'manage_options', "{$this->prefix}menu", array( &$this, 'settings_screen' ) );
		add_submenu_page( 'pmpro-membershiplevels', ( $this->page_title ? $this->page_title : $this->menu_title ), $this->menu_title, 'manage_options', "{$this->prefix}menu", array( &$this, 'settings_screen' ) );
	}

	/**
	 * Output the settings screen.
	 */
	public function settings_screen() {
		$nonce_name = "{$this->prefix}settings_form_save";
		?>
<div class="wrap">
  <h2><?php echo ( $this->page_title ? $this->page_title : $this->menu_title ); ?></h2>
	<form method="post" action="" class="<?php echo $this->prefix; ?>_settings_form">
		<input type="hidden" name="action" value="<?php echo $nonce_name; ?>">
		<?php wp_nonce_field( $nonce_name ); ?>
		<table class="form-table">
		<?php
		foreach ( $this->_fields as $field_id => $field ) {
			$dom_id = $this->prefix . $field_id;
			?>
	<tr valign="top">
	  <th scope="row">
		<label for="<?php echo $dom_id; ?>"><?php echo $field['title']; ?></label>
	  </th>
			<td>
								<input type="text" id="<?php echo $dom_id; ?>" name="<?php echo $field_id; ?>" value="<?php echo esc_attr( $this->$field_id ); ?>"
												  <?php
													if ( $this->input_text_width ) {
														printf( ' style="width: %s"', $this->input_text_width ); }
													?>
				>
			<?php if ( $field['description'] ) { ?>
				<p class="description"><?php echo $field['description']; ?></p>
				<?php } ?>
			</td>
		</tr>
			<?php
		}
		?>
		</table>
	<p class="submit">
	  <input type="submit" value="Save Settings" class="button-primary">
	</p>
	</form>
</div>
		<?php
	}

}
