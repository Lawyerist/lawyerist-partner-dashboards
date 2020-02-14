<?php

/*
Plugin Name: Lawyerist Partner Dashboards
Plugin URI: https://lawyerist.com
Description: Performance dashboards for Lawyerist's advertising partners.
Author: Sam Glover
Version: 0.3.1
Author URI: https://lawyerist.com
*/

if ( !defined( 'ABSPATH' ) ) exit;

/**
* Constants
*/
define( 'LPD_PLUGIN_VERSION', '0.1.0' );
define( 'LPD_PAGE_SLUG', 'partner-dashboard' );

require_once( plugin_dir_path( __FILE__ ) . 'common/lpd-common.php' );

if ( is_admin() ) {
	require_once( plugin_dir_path( __FILE__ ) . 'admin/lpd-admin.php' );
}

if ( !is_admin() ) {

	require_once( plugin_dir_path( __FILE__ ) . 'frontend/lpd-frontend.php' );

	function lpd_frontend_stylesheet_scripts() {

		wp_enqueue_style( 'lpd-frontend-css', plugins_url( 'frontend/lpd-frontend.css', __FILE__ ) );

	}

	add_action( 'wp_enqueue_scripts', 'lpd_frontend_stylesheet_scripts' );

}

/**
* Includes the activation functions to create a Partner post type and
* Partner Dashboards page if one doesn't already exist.
*/
include_once dirname( __FILE__ ) . '/lpd-activation.php';
