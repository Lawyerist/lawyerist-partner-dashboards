<?php

/**
* Gets the dashboard title block.
*/
function lpd_get_dashboard_title( $partner_id, $partner_name ) {

  ob_start();

  ?>

  <div id="lpd-title">

    <?php if ( has_post_thumbnail( $partner_id ) ) { ?>
      <div itemprop="image"><?php echo get_the_post_thumbnail( $partner_id, 'thumbnail' ); ?></div>
    <?php } ?>

    <div class="title-container">
      <h1 class="title"><?php echo $partner_name; ?></h1>
      <p class="subtitle">Performance Dashboard</p>
    </div>

  </div>

  <?php

  return ob_get_clean();

}


function lpd_get_product_page_report( $partner_id ) {

  $product_page = get_post( get_field( 'product_page', $partner_id ) );
  $portal       = get_post( $product_page->post_parent );

  $report_data  = array(
    'portal_views'        => 456,
    'product_page_views'  => 0,
    'tb_unique_clicks'    => trial_button_click_count( $product_page->ID, 'current', true ),
    'tb_total_clicks'     => trial_button_click_count( $product_page->ID, 'current', false ),
  );

  foreach ( $report_data as $key => $value ) {

    if ( is_null( $value ) || empty( $value ) || $value == 0 ) {
      $report_data[ $key ] = '-';
    }

  }

  ob_start();

    ?>

    <p class="nodata-message">If you are not seeing data below, it probably means you do not have that ad product for that time period. Email <a href="mailto:partnerships@lawyerist.com">partnerships@lawyerist.com</a> to expand your campaign.</p>

    <div class="card">
      <div class="card-label">Product Page</div>
      <div class="cols-4" id="lpd-product-page-report">

        <div id="product-portal-views">
          <div class="report-label">Product Portal Views</div>
          <div class="report-number"><?php echo $report_data[ 'portal_views' ]; ?></div>
          <div class="report-label-detail"><a href="<?php echo get_permalink( $portal->ID ); ?>"><?php echo $portal->post_title; ?></a></div>
        </div>

        <div id="product-page-views">
          <div class="report-label">Product Page Views</div>
          <div class="report-number"><?php echo $report_data[ 'product_page_views' ]; ?></div>
          <div class="report-label-detail"><a href="<?php echo get_permalink( $product_page->ID ); ?>"><?php echo $product_page->post_title; ?></a></div>
        </div>

        <div id="trial-button-clicks">
          <div class="report-label">Trial Button Unique Clicks</div>
          <div class="report-number"><?php echo $report_data[ 'tb_unique_clicks' ]; ?></div>
          <div class="report-label-detail"><?php echo $report_data[ 'tb_total_clicks' ]; ?> Total</div>
        </div>

        <div id="affinity-benefit-claims">
          <div class="report-label">Affinity Benefit Claims</div>
          <div class="report-number">3</div>
          <div class="report-label-detail"><a href="">See Claims</a></div>
        </div>

      </div>
    </div>

    <?php

    // var_dump( $portal );

  return ob_get_clean();

}


/**
* Gets a list of authorized users.
*/
function lpd_get_authorized_users_list( $partner_id ) {

    $authorized_users = get_field( 'authorized_users', $partner_id );

    if ( $authorized_users ) {

      ob_start();

      ?>

      <div class="card">
        <div class="card-label">People Authorized to View this Dashboard</div>
        <div class="cols-2" id="lpd-authorized-users">

          <?php

          foreach ( $authorized_users as $user_id ) {

            $user = get_userdata( $user_id );

            ?>

            <div class="authorized-user">
              <?php echo get_avatar( $user_id, 90 ); ?>
              <div class="user-details">
                <div class="user-name"><?php echo $user->display_name; ?></div>
                <div class="user-email"><?php echo $user->user_email; ?></div>
              </div>
            </div>

            <?php

          }

          ?>

        </div>
      </div>

      <?php

      return ob_get_clean();

    }

}
