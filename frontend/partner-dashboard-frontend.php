<?php

/**
* Adds .partner-dashboard to <body>.
*/
function lpd_body_class( $classes ) {

	if ( is_singular( 'partner' ) ) {
    $classes[] = 'partner-dashboard';
	}

  return $classes;

}

add_filter( 'body_class', 'lpd_body_class' );


/**
* Selects the partner dashboard single post template.
*/
function lpd_template( $template ) {

  if ( is_singular( 'partner' ) && locate_template( array( 'single-partner.php' ) ) !== $template ) {
      $template = plugin_dir_path( __FILE__ ) . 'partner-dashboard-template.php';
  }

  return $template;

}

add_filter( 'template_include', 'lpd_template' );


/**
* Gets the product rating and information about the rating.
*/
function lpd_product_rating( $page_id ) {

}

/**
* Gets a list of authorized users.
*/
function lpd_list_authorized_users() {

  ob_start();

      $args = array(
        'fields'  => array(
          'ID',
          'display_name',
          'user_email',
        ),
        'include' => get_field( 'authorized_users' ),
      );

      $authorized_users = get_users( $args );

      if ( $authorized_users ) {

        foreach ( $authorized_users as $authorized_user ) {
          echo '<div class="authorized-user">';
            echo get_avatar( $authorized_user->ID, 90 );
            echo '<div class="user-details">';
              echo '<div class="user-name">' . $authorized_user->display_name . '</div>';
              echo '<div class="user-email">' . $authorized_user->user_email . '</div>';
            echo '</div>';
          echo '</div>';
        }

      }

  return ob_get_clean();

}
