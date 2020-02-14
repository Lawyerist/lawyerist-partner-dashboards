<?php

require_once( plugin_dir_path( __FILE__ ) . 'lpd-dashboard-functions.php' );


/**
* Adds .partner-dashboard to <body>.
*/
function lpd_body_class( $classes ) {

	if ( is_page( LPD_PAGE_SLUG ) ) {
    $classes[] = 'partner-dashboard';
	}

	if ( $_GET[ 'date_filter' ] ) {
		$classes[] = 'date_filtered';
	}

	foreach ( $_GET as $key => $val ) {
		$classes[] = $key . '-' . $val;
	}

  return $classes;

}

add_filter( 'body_class', 'lpd_body_class' );


/**
* Selects the partner dashboard single post template.
*/
function lpd_template( $template ) {

  if ( is_page( LPD_PAGE_SLUG ) ) {
    $template = plugin_dir_path( __FILE__ ) . 'lpd-dashboard-template.php';
  }

  return $template;

}

add_filter( 'template_include', 'lpd_template' );


function lpd_get_partners_by_user( $user_id ) {

	if ( !$user_id ) {
		return null;
	}

	$args = array(
		'fields'					=> 'ids',
		'meta_compare'		=> 'LIKE',
		'meta_key'				=> 'authorized_users',
		'meta_value'			=> $user_id,
		'order'						=> 'asc',
		'orderby'					=> 'title',
		'post_type'				=> 'partner',
		'posts_per_page'	=> -1,
	);

	$partner_ids = get_posts( $args );

	return $partner_ids;

}


function lpd_dashboard( $partner_id ) {

	// Get post objects.
	$page					= sanitize_text_field( $_GET[ 'page' ] );
	$date_filter	= sanitize_text_field( $_GET[ 'date_filter' ] );
	$partner			= get_post( $partner_id );
	$product_page = get_field( 'product_page', $partner_id ) ? get_post( get_field( 'product_page', $partner_id ) ) : null;
	$portal       = $product_page ? get_post( $product_page->post_parent ) : null;

	echo lpd_get_dashboard_title( $partner->ID, $product_page->ID, $partner->post_title );
	echo lpd_get_nav( $product_page, $page );

	if ( $page == 'affinity_claims' ) {

		?>

		<h2>Affinity Benefit Claims</h2>

		<?php

		echo lpd_get_affinity_claims( $product_page->post_name );

	} else {

		echo lpd_get_performance_report( $partner->ID, $product_page, $portal, $date_filter );

	}

	echo lpd_get_authorized_users_list( $partner->ID );

}
