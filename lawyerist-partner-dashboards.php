<?php

/*
Plugin Name: Lawyerist Partner Dashboards
Plugin URI: https://lawyerist.com
Description: Performance dashboards for Lawyerist's advertising partners.
Author: Sam Glover
Version: 0.1.0
Author URI: https://lawyerist.com
*/

if ( !defined( 'ABSPATH' ) ) exit;

define( 'PARTNER_DASHBOARDS_PLUGIN_VERSION', '0.1.0' );
// define( 'REST_AUTH_TOKEN', '' );

require_once( plugin_dir_path( __FILE__ ) . 'common/partner-dashboards-common.php' );
require_once( plugin_dir_path( __FILE__ ) . 'frontend/partner-dashboards-frontend.php' );

if ( is_admin() ) {
	require_once( plugin_dir_path( __FILE__ ) . 'admin/partner-dashboards-admin.php' );
}

function frontend_stylesheet() {
	wp_enqueue_style( 'partner-dashboards-frontend-css', plugins_url( 'frontend/partner-dashboards-frontend.css', __FILE__ ) );
}

add_action( 'wp_enqueue_scripts', 'frontend_stylesheet' );

/**
* Includes the activation functions to create a Partner post type and
* Partner Dashboards page if one doesn't already exist.
*/
include_once dirname( __FILE__ ) . '/activation.php';
