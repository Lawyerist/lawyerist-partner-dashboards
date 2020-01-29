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


function lpd_get_product_page_report( $partner_id, $product_page, $portal ) {

  $portal_path          = parse_url( get_permalink( $portal->ID ), PHP_URL_PATH ) ;
  $product_page_path    = parse_url( get_permalink( $product_page->ID ), PHP_URL_PATH ) ;

  $report_data  = array(
    'portal_views'        => lpd_get_pageviews( $portal_path ),
    'product_page_views'  => lpd_get_pageviews( $product_page_path ),
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

        <div id="community-rating">

          <?php

          $our_rating             = lawyerist_get_our_rating( $product_page->ID );
          $rating                 = lawyerist_get_composite_rating( $product_page->ID );
          $community_review_count = lawyerist_get_community_review_count( $product_page->ID );

          if ( !empty( $our_rating ) ) {
            $rating_count = $community_review_count + 1;
          } else {
            $rating_count = $community_review_count;
          }

          ?>

          <div class="report-label">Product Rating</div>
          <div class="report-number"><?php echo $rating; ?><span style="color: #777;">/5</span></div>
          <div class="report-label-detail"><?php echo lawyerist_star_rating ( $rating ) . '<a href="' . get_permalink( $product_page->ID ); ?>#rating">(<?php echo $rating_count . ' ' . _n( 'rating', 'ratings', $rating_count ) . '</a>'; ?>)</div>

        </div>

        <div id="trial-button-clicks">
          <div class="report-label">Trial Button Leads</div>
          <div class="report-number"><?php echo $report_data[ 'tb_unique_clicks' ]; ?></div>
          <div class="report-label-detail"><?php echo $report_data[ 'tb_total_clicks' ]; ?> Total Clicks</div>
        </div>

      </div>
    </div>

    <div class="card">
      <div class="card-label">Affinity Benefit Claims</div>

      <div id="affinity-benefit-claims">
        <div class="report-number"><?php echo lpd_count_affinity_claims( $product_page->ID ); ?></div>
      </div>

      <?php echo lpd_get_affinity_claims( $product_page ); ?>
    </div>

    <?php

    // var_dump( $portal );

  return ob_get_clean();

}


/**
* Gets page views from Google Analytics.
*/
function lpd_get_pageviews( $page_path ) {

  $analytics = initializeAnalytics();
  $response = getReport( $analytics, $page_path );

  return number_format ( lpd_get_results( $response ) );

}

function lpd_get_results( $reports ) {

  $report           = $reports[ 0 ];
  $rows             = $report->getData()->getRows();
  $row              = $rows[ 0 ];
  $metrics          = $row->getMetrics();
  $values           = $metrics[ 0 ]->getValues();

  return $values[ 0 ];

}

/**
 * Initializes an Analytics Reporting API V4 service object.
 *
 * @return An authorized Analytics Reporting API V4 service object.
 */
function initializeAnalytics() {

  require_once( plugin_dir_path( __FILE__ ) . 'google-api-php-client-2.4.0/vendor/autoload.php' );

  // Create and configure a new client object.
  $client = new Google_Client();
  $client->setApplicationName( "Lawyerist Partner Dashboards" );
  $client->setAuthConfig( plugin_dir_path( __FILE__ ) . 'angelic-throne-266121-b88841b572e7.json' );
  $client->setScopes( [ 'https://www.googleapis.com/auth/analytics.readonly' ] );
  $analytics = new Google_Service_AnalyticsReporting( $client );

  return $analytics;

}


/**
 * Queries the Analytics Reporting API V4.
 *
 * @param service An authorized Analytics Reporting API V4 service object.
 * @return The Analytics Reporting API V4 response.
 */
function getReport( $analytics, $page_path ) {

  // Create the DateRange object.
  $dateRange = new Google_Service_AnalyticsReporting_DateRange();
  $dateRange->setStartDate( '7daysAgo' );
  $dateRange->setEndDate( 'today' );

  // Create the Metrics object.
  $pageviews = new Google_Service_AnalyticsReporting_Metric();
  $pageviews->setExpression( 'ga:pageviews' );
  $pageviews->setAlias( 'pageviews' );

  // Create the page path Dimension Filter object.
  $dimensionFilter = new Google_Service_AnalyticsReporting_DimensionFilter();
  $dimensionFilter->setDimensionName( 'ga:pagePath' );
  $dimensionFilter->setOperator( 'BEGINS_WITH' );
  $dimensionFilter->setExpressions( array( $page_path ) );

  $dimensionFilterClause = new Google_Service_AnalyticsReporting_DimensionFilterClause();
  $dimensionFilterClause->setFilters( array( $dimensionFilter ) );

  // Create the ReportRequest object.
  $request = new Google_Service_AnalyticsReporting_ReportRequest();
  $request->setViewId( get_field( 'google_analytics_view_id', 'option' ) );
  $request->setDateRanges( $dateRange );
  $request->setDimensionFilterClauses( array( $dimensionFilterClause ) );
  $request->setMetrics( array( $pageviews ) );

  $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
  $body->setReportRequests( array( $request) );
  return $analytics->reports->batchGet( $body );

}


/**
* Gets affinity benefit claim count.
*/
function lpd_count_affinity_claims( $product_page_id ) {

  if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) :

    $product_page_path  = parse_url( get_permalink( $product_page_id ), PHP_URL_PATH );
    $claim_count        = 0;

    $form_id          = 55;
    $search_criteria  = array();
    $sorting          = array();
    $paging           = array( 'offset' => 0, 'page_size' => 200 );
    $all_claims       = GFAPI::get_entries( $form_id, $search_criteria, $sorting, $paging );

    foreach ( $all_claims as $claim ) {

      $claim_source_url = parse_url( $claim[ 'source_url' ], PHP_URL_PATH );

      if ( $claim_source_url == $product_page_path ) {
        $claim_count++;
      }

    }

    return $claim_count;

  else: return;

  endif;


}


/**
* Gets affinity benefit claims.
*/
function lpd_get_affinity_claims( $product_page_id ) {

  if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) :

    $product_page_path  = parse_url( get_permalink( $product_page_id ), PHP_URL_PATH );
    $claims             = array();

    $form_id          = 55;
    $search_criteria  = array();
    $sorting          = array();
    $paging           = array( 'offset' => 0, 'page_size' => 200 );
    $all_claims       = GFAPI::get_entries( $form_id, $search_criteria, $sorting, $paging );

    foreach ( $all_claims as $claim ) {

      $claim_source_url = parse_url( $claim[ 'source_url' ], PHP_URL_PATH ) ;

      if ( $claim_source_url == $product_page_path ) {
        $claims[] = $claim;
      }

    }

    if ( !empty( $claims ) ) {

      ob_start();

        ?>

        <table id="list-of-claims">
          <thead>
            <tr>
              <td>Claim ID</td>
              <td>Name</td>
              <td>Email Address</td>
            </tr>
          </thead>
          <tbody>

            <?php foreach ( $claims as $claim ) { ?>

              <tr>
                <td><?php echo $claim[ 'id' ]; ?></td>
                <td><?php echo $claim[ '1.3' ] . ' ' . $claim[ '1.6' ]; ?></td>
                <td><?php echo $claim[ 2 ]; ?></td>
              </tr>

            <?php } ?>

          </tbody>
        </table>



        </table>

        <?php

      return ob_get_clean();

    } else {

      return;

    }

  else: return;

  endif;

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
