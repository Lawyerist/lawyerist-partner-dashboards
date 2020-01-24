<?php

/**
* Cleans up the new/edit partner page.
*/
function remove_meta_boxes() {

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

}

add_action( 'do_meta_boxes', 'remove_meta_boxes' );
