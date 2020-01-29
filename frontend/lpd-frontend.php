<?php

require_once( plugin_dir_path( __FILE__ ) . 'lpd-dashboard-functions.php' );


/**
* Adds .partner-dashboard to <body>.
*/
function lpd_body_class( $classes ) {

	if ( is_page( LPD_PAGE_SLUG ) ) {
    $classes[] = 'partner-dashboard';
	}

  return $classes;

}

add_filter( 'body_class', 'lpd_body_class' );


/**
* Selects the partner dashboard single post template.
*/
function lpd_template( $template ) {

  if ( is_page( LPD_PAGE_SLUG ) ) {
    $template = plugin_dir_path( __FILE__ ) . 'lpd-template.php';
  }

  return $template;

}

add_filter( 'template_include', 'lpd_template' );


function lpd_get_partners_by_user( $user_id ) {

	if ( !$user_id ) {
		return null;
	}

	$args = array(
		'fields'				=> 'ids',
		'meta_compare'	=> 'LIKE',
		'meta_key'			=> 'authorized_users',
		'meta_value'		=> $user_id,
		'post_type'			=> 'partner',
	);

	$partner_ids = get_posts( $args );

	return $partner_ids;

}

function lpd_dashboard() {

	if ( !is_page( LPD_PAGE_SLUG ) ) {
		return;
	}

	// Prevents content from loading if the current user is not logged in. Instead
	// it shows a login form.
	if ( is_user_logged_in() ) :

		$user_id		= get_current_user_ID();
		$dashboards	= lpd_get_partners_by_user( $user_id ); // Returns an array.

		if ( !$dashboards ) {

			?>

			<h1>No Dashboards Found</h1>

			<p>It doesn't look like you are authorized to view any partner dashboards.</p>

			<p>If you think this is an error, please <a href="https://lawyerist.local/about/contact/">contact us</a> and we will sort it out as quickly as possible!</p>

			<?php

		} elseif ( count( $dashboards ) == 1 ) {

			// Get post objects.
			$partner			= get_post( $dashboards[0] );
			$product_page = get_post( get_field( 'product_page', $partner->ID ) );
		  $portal       = get_post( $product_page->post_parent );

			echo lpd_get_dashboard_title( $partner->ID, $partner->post_title );

			echo lpd_get_product_page_report( $partner->ID, $product_page, $portal );

		  echo lpd_get_authorized_users_list( $partner->ID );

		} else {

		}

	else :

		?>

		<h1>Sign in to Access Your Partner Dashboard(s)</h1>

		<div class="card" id="lpd-login">
			<?php wp_login_form(); ?>
			<p class="login-lost-password remove_bottom">Forgot your password? <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>">Reset it here.</a></p>
		</div>

		<?php

	endif;

}
