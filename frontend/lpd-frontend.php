<?php

require_once( plugin_dir_path( __FILE__ ) . 'lpd-dashboard-functions.php' );


/**
* Adds .partner-dashboard to <body>.
*/
function lpd_body_class( $classes ) {

	if ( is_page( LPD_PAGE_SLUG ) ) {
    $classes[] = 'partner-dashboard';
	}

	if ( isset( $_POST[ 'date_filter' ] ) ) {
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

	$page						= null;
	$partner				= get_post( $partner_id );
	$product_pages	= get_field( 'product_page', $partner_id ) ? get_field( 'product_page', $partner_id ) : null;
	$date_filter		= null;

	if ( isset( $_POST[ 'page' ] ) ) {
		$page = sanitize_text_field( $_GET[ 'page' ] );
	}

	if ( isset( $_POST[ 'date_filter' ] ) ) {
		$date_filter = sanitize_text_field( $_GET[ 'date_filter' ] );
	}

	echo lpd_get_dashboard_title( $partner->post_title );
	echo lpd_get_nav( $product_pages, $page );

	if ( $page == 'affinity_claims' ) {

		?>

		<h2>Affinity Benefit Claims</h2>

		<?php

		echo lpd_get_affinity_claims( $product_pages );

	} else {

		?>

		<div id="lpd-performance-report">

      <p class="nodata-message">If you are not seeing data below, it probably means you do not have that ad product for that time period. Get the <a href="https://lawyerist.com/ad-info" target="_blank">media kit</a> to see what you are missing, or email <a href="mailto:partnerships@lawyerist.com">partnerships@lawyerist.com</a> to expand your campaign.</p>

      <p class="card-label">Date Range</p>
      <div id="date-range">
        <a id="this-month-filter" href="<?php echo add_query_arg( 'date_filter', 'this_month' ); ?>">This Month</a>
        <a id="last-month-filter" href="<?php echo add_query_arg( 'date_filter', 'last_month' ); ?>">Last Month</a>
        <a id="this-year-filter" href="<?php echo add_query_arg( 'date_filter', 'this_year' ); ?>">This Year</a>
        <a id="last-year-filter" href="<?php echo add_query_arg( 'date_filter', 'last_year' ); ?>">Last Year</a>
        <div class="clear"></div>
      </div>

			<?php

			if ( gettype( $product_pages ) == 'integer' ) {

				$product_page	= get_post( $product_pages );
				$portal				= get_post( $product_page->post_parent );

				echo lpd_get_product_page_performance_report( $partner->ID, $product_page, $portal, $date_filter );

			} else {

				foreach ( $product_pages as $product_page_id ) {

					$product_page	= get_post( $product_page_id );
					$portal				= get_post( $product_page->post_parent );

					echo lpd_get_product_page_performance_report( $partner->ID, $product_page, $portal, $date_filter );

				}

			}

			?>

		</div>

		<?php

	}

	echo lpd_get_authorized_users_list( $partner->ID );

}
