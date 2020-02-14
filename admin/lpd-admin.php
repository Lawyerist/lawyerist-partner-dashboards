<?php

/**
* Adds an options page.
*/
function my_acf_op_init() {

  // Check function exists.
  if( function_exists( 'acf_add_options_sub_page' ) ) {

    acf_add_options_sub_page(array(
      'page_title'  => __( 'Partner Settings' ),
      'menu_title'  => __( 'Settings' ),
      'parent_slug' => __( 'edit.php?post_type=partner' ),
    ));

  }

}

add_action( 'acf/init', 'my_acf_op_init' );


/**
* Cleans up the new/edit partner page.
*/
function lpd_meta_boxes() {

  $metaboxes_to_remove = array(
    'wc-memberships-post-memberships-data',
    'wp-review-metabox',
    'wp-review-metabox-review',
    'wp-review-metabox-item',
    'wp-review-metabox-desc',
    'wp-review-metabox-reviewLinks',
    'wp-review-metabox-userReview',
    // 'wpseo_meta',
  );

  foreach ( $metaboxes_to_remove as $metabox ) {
    remove_meta_box( $metabox, 'partner', 'normal' );
  }

  add_meta_box(
    'lpd-partner-dashboard-link',
    'Dashboard',
    'lpd_partner_dashboard_link',
    'partner',
    'side',
    'high',
  );

}

add_action( 'do_meta_boxes', 'lpd_meta_boxes' );


function lpd_partner_dashboard_link( $post ) {
  echo '<a class="button" href="/partner-dashboard/?partner=' . $post->ID . '" target="_blank">Open Dashboard</a>';
}


/**
* Sorts partners by title/name.
*/
function lpd_partner_sort_order($query) {

  if( !$query->is_admin ) { return; }

  if ($query->get( 'post_type' ) == 'partner') {

    $query->set( 'orderby', 'title' );
    $query->set( 'order', 'ASC' );

  }

  return $query;

}

add_filter( 'pre_get_posts', 'lpd_partner_sort_order' );
