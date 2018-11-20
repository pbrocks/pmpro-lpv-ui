<?php
/**
 * Plugin Name: PMPro LPV User Interface
 * Author: Stranger Studios && pbrocks
 * Version: 0.9.7.2
 */

// require_once( plugin_dir_path( __FILE__ ) . 'inc/admin.php' );
// require_once( plugin_dir_path( __FILE__ ) . 'inc/admin2.php' );
// include( 'inc/pmpro-lpv-detritus.php' );
require 'inc/classes/class-pmpro-lpv-init.php';
require 'inc/classes/class-pmpro-lpv-customizer.php';
require 'inc/classes/class-pmpro-lpv-settings.php';
require 'inc/classes/class-pmpro-tabbed-settings.php';
// include( 'inc/classes/class-soderland-toggle-control.php' );
PMPro_LPV_Init::init();
PMPro_LPV_Customizer::init();
PMPro_LPV_Settings::init();
PMPro_Tabbed_Settings::init();
