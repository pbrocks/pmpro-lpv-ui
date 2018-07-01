<?php
/**
 * Plugin Name: PMPro LPV User Interface
 * Author: Stranger Studios && pbrocks
 * Version: 0.9.5
 */

require_once( plugin_dir_path( __FILE__ ) . 'inc/admin.php' );

// include( 'inc/pmpro-lpv-notification-bar.php' );
include( 'inc/pmpro-lpv-detritus.php' );
include( 'inc/classes/class-pmpro-lpv-init.php' );

PMPro_LPV_Init::init();
