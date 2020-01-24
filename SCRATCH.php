// Start the Loop.
if ( have_posts() ) : while ( have_posts() ) : the_post();

  // Assign post variables.
  $partner    = the_title( '', '', FALSE );
  $partner_id = get_the_ID();

?>

  <div id="partner-dashboard-title">

    <?php if ( has_post_thumbnail() ) { ?>
      <div itemprop="image"><?php the_post_thumbnail( 'thumbnail' ); ?></div>
    <?php } ?>

    <div class="title-container">
      <h1 class="title"><?php echo $partner; ?></h1>
      <p class="subtitle">Performance Dashboard</p>
    </div>

  </div>

  <div class="card">
    <div class="card-label">Product Rating</div>
    <div id="product-rating"></div>
  </div>

  <div class="card">
    <div class="card-label">Authorized Users</div>
    <div id="authorized-users">
      <?php echo lpd_list_authorized_users(); ?>
    </div>
  </div>

<?php endwhile; endif; ?>


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
