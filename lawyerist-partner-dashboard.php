<?php

/*
Plugin Name: Lawyerist Partner Dashboard
Plugin URI: https://lawyerist.com
Description: Enhanced functionality for the Small Firm Scorecard.
Author: Sam Glover
Version: 0.1.0
Author URI: https://lawyerist.com
*/

if ( !defined( 'ABSPATH' ) ) exit;

define( 'PARTNER_DASHBOARDS_PLUGIN_VERSION', '0.1.0' );
// define( 'REST_AUTH_TOKEN', '' );

require_once( plugin_dir_path( __FILE__ ) . 'common/partner-dashboard-common.php' );
require_once( plugin_dir_path( __FILE__ ) . 'frontend/partner-dashboard-frontend.php' );

if ( is_admin() ) {
	require_once( plugin_dir_path( __FILE__ ) . 'admin/partner-dashboard-admin.php' );
}

function frontend_stylesheet() {
	wp_enqueue_style( 'partner-dashboard-frontend-css', plugins_url( 'frontend/partner-dashboard-frontend.css', __FILE__ ) );
}

add_action( 'wp_enqueue_scripts', 'frontend_stylesheet' );
