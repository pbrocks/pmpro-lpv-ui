<?php
/**
 * Plugin Name: PMPro LPV User Interface
 * Author: Stranger Studios && pbrocks
 * Version: 0.9.7
 */

require_once( plugin_dir_path( __FILE__ ) . 'inc/admin.php' );
// require_once( plugin_dir_path( __FILE__ ) . 'inc/admin2.php' );
// include( 'inc/pmpro-lpv-detritus.php' ); Plugin_or_Theme_Settings
include( 'inc/classes/class-pmpro-lpv-init.php' );
include( 'inc/classes/class-pmpro-lpv-customizer.php' );
include( 'inc/classes/class-pmpro-lpv-settings.php' );
include( 'inc/classes/class-pmpro-tabbed-settings.php' );
include( 'inc/classes/class-sidetrack-settings.php' );
include( 'inc/classes/class-soderland-toggle-control.php' );

PMPro_LPV_Init::init();
PMPro_LPV_Customizer::init();
PMPro_LPV_Settings::init();
PMPro_Tabbed_Settings::init();

include( 'inc/classes/class-plugin-theme-settings.php' );
