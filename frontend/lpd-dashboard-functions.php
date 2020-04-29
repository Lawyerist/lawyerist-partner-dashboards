<?php

/**
* Gets the dashboard title block.
*/
function lpd_get_dashboard_title( $partner_name ) {

  ob_start();

    ?>

    <div id="lpd-title">

      <div class="title-container">
        <h1 class="title"><?php echo $partner_name; ?></h1>
        <p class="subtitle">Partner Dashboard</p>
      </div>

    </div>

    <?php

  return ob_get_clean();

}


/**
* Gets the main navigataion tabs (currently Partner Dashboards and Affinity Claims
* Report, if the partner has any claims).
*/
function lpd_get_nav( $product_page_ids, $page ) {

  ob_start();

    ?>

    <div id="lpd-nav">

      <?php

      if ( gettype( $product_page_ids ) == 'integer' ) {

				$product_page	= get_post( $product_page_ids );

        $aff_total_claims = lpd_count_affinity_claims( $product_page->post_name, 'all' );

			} else {

        $aff_total_claims = 0;

				foreach ( $product_page_ids as $product_page_id ) {

					$product_page	= get_post( $product_page_id );

          $aff_total_claims += lpd_count_affinity_claims( $product_page->post_name, 'all' );

				}

			}

      switch ( $page ) {

        case '' :

          ?>

          <div class="tab active">Performance Report</div>

          <?php if ( $aff_total_claims > 0 ) { ?>
            <a href="<?php echo add_query_arg( 'page', 'affinity_claims' ); ?>" class="tab">Affinity Claims Report</a>
          <?php } ?>

          <?php

          break;

        case 'affinity_claims' :

          ?>

          <a href="<?php echo remove_query_arg( 'page' ); ?>" class="tab">Performance Report</a>

          <?php if ( $aff_total_claims > 0 ) { ?>
            <div class="tab active">Affinity Claims Report</div>
          <?php } ?>

          <?php

          break;

      } ?>

    </div>

    <?php

  return ob_get_clean();

}


/**
* Gets the product page performance report.
*
* This functions pulls data from Google Analytics, which requires the Google API
* files and key. (Not included in the Github repo for hopefully obvious
* reasons.)
*/
function lpd_get_product_page_performance_report( $partner_id, $product_page, $portal, $date_filter ) {

  $portal_path          = $portal ? esc_url( parse_url( get_permalink( $portal->ID ), PHP_URL_PATH ) ) : null;
  $product_page_path    = $product_page ? esc_url( parse_url( get_permalink( $product_page->ID ), PHP_URL_PATH ) ) : null;

  switch ( $date_filter ) {

    case 'last_year'  :
      $date_range = array(
        'start' => date( 'Y' ) - 1 . '-01-01',
        'end'   => date( 'Y' ) - 1 . '-12-31',
      );

      break;

    case 'this_year'  :
      $date_range = array(
        'start' => date( 'Y' ) . '-01-01',
        'end'   => date( 'Y-m-d' ),
      );

      break;

    case 'last_month' :
      $date_range = array(
        'start' => date( 'Y-m-d', strtotime( 'first day of previous month' ) ),
        'end'   => date( 'Y-m-d', strtotime( 'last day of previous month' ) ),
      );

      break;

    case 'this_month' :
    default :
      $date_range = array(
        'start' => date( 'Y-m-d', strtotime( 'first day of this month' ) ),
        'end'   => date( 'Y-m-d' ),
      );

      break;

  }

  $product_page_data  = array(
    'portal_views'        => $portal_path ? lpd_get_pageviews( $portal_path, $date_range ) : null,
    'product_page_views'  => $product_page_path ? lpd_get_pageviews( $product_page_path, $date_range ) : null,
    'tb_unique_clicks'    => has_trial_button( $product_page ) ? trial_button_click_count( $product_page->ID, $date_filter, true ) : null,
    'tb_total_clicks'     => has_trial_button( $product_page ) ? trial_button_click_count( $product_page->ID, $date_filter, false ) : null,
    'aff_filtered_claims' => lpd_count_affinity_claims( $product_page->post_name, $date_range ),
    'aff_total_claims'    => lpd_count_affinity_claims( $product_page->post_name, 'all' ),
  );

  foreach ( $product_page_data as $key => $value ) {

    if ( is_null( $value ) || empty( $value ) || $value == 0 ) {
      $product_page_data[ $key ] = '&ndash;';
    } else {
      $product_page_data[ $key ] = number_format( $value );
    }

  }

  ob_start();

    ?>

    <div class="card">
      <div class="card-label"><?php echo $product_page->post_title; ?></div>

      <div class="cols-4" class="lpd-product-page-report">

        <div class="col">
          <div class="report-label">Product Portal Views</div>
          <div class="report-number"><?php echo $product_page_data[ 'portal_views' ]; ?></div>
          <div class="report-label-detail"><a href="<?php echo get_permalink( $portal->ID ); ?>"><?php echo $portal->post_title; ?></a></div>
        </div>

        <div class="col">
          <div class="report-label">Product Page Views</div>
          <div class="report-number"><?php echo $product_page_data[ 'product_page_views' ]; ?></div>
          <div class="report-label-detail"><a href="<?php echo get_permalink( $product_page->ID ); ?>"><?php echo $product_page->post_title; ?></a></div>
        </div>

        <div class="col">
          <div class="report-label">Trial Button Leads</div>
          <div class="report-number"><?php echo $product_page_data[ 'tb_unique_clicks' ]; ?></div>
          <?php if ( $product_page_data[ 'tb_total_clicks' ] > 0 ) { ?>
            <div class="report-label-detail"><?php echo $product_page_data[ 'tb_total_clicks' ]; ?> Total Clicks</div>
          <?php } ?>
        </div>

        <div class="col">
          <div class="report-label">Affinity Benefit Claims</div>
          <div class="report-number"><?php echo $product_page_data[ 'aff_filtered_claims' ]; ?></div>
          <?php if ( $product_page_data[ 'aff_total_claims' ] > 0 ) { ?>
            <div class="report-label-detail"><a href="<?php echo add_query_arg( 'page', 'affinity_claims' ); ?>">See All <?php echo $product_page_data[ 'aff_total_claims' ]; ?> Claims</a></div>
          <?php } ?>
        </div>

      </div>
    </div>

    <?php

  return ob_get_clean();

}


/**
* Gets page views from Google Analytics.
*/
function lpd_get_pageviews( $page_path, $date_range ) {

  $analytics  = initializeAnalytics();
  $response   = getReport( $analytics, $page_path, $date_range );
  $results    = lpd_get_results( $response );

  return $results;

}


function lpd_get_results( $reports ) {

  $report = $reports[ 0 ];
  $rows   = $report->getData()->getRows();
  $row    = $rows[ 0 ];

  if ( !is_null( $row ) ) {

    $metrics  = $row->getMetrics();
    $values   = $metrics[ 0 ]->getValues();

    return $values[ 0 ];

  } else {

    return;

  }

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
function getReport( $analytics, $page_path, $date_range ) {

  // Create the DateRange object.
  $dateRange = new Google_Service_AnalyticsReporting_DateRange();
  $dateRange->setStartDate( $date_range[ 'start' ] );
  $dateRange->setEndDate( $date_range[ 'end' ] );

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
function lpd_count_affinity_claims( $product_page_slug, $date_range ) {

  if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) :

    $form_id = 55;

    $search_criteria[ 'field_filters' ][] = array(
      'key'       => 'source_url',
      'value'     => $product_page_slug,
      'operator'  => 'contains',
    );

    if ( $date_range !== 'all' ) {
      $search_criteria[ 'start_date' ]  = $date_range[ 'start' ];
      $search_criteria[ 'end_date' ]    = $date_range[ 'end' ];
    }

    $sorting          = array();
    $paging           = array( 'offset' => 0, 'page_size' => 200 );

    $claim_count      = GFAPI::count_entries( $form_id, $search_criteria, $sorting, $paging );

    return $claim_count;

  else: return;

  endif;


}


/**
* Gets affinity benefit claims.
*/
function lpd_get_affinity_claims( $product_page_ids ) {

  if ( !is_plugin_active( 'gravityforms/gravityforms.php' ) ) { return; }

  $form_id = 55;

  if ( gettype( $product_page_ids ) == 'integer' ) {

    $product_page = get_post( $product_page_ids );

    $search_criteria[ 'field_filters' ][] = array(
      'key'       => 'source_url',
      'value'     => $product_page->post_name,
      'operator'  => 'contains',
    );

  } else {

    $search_criteria[ 'field_filters' ][ 'mode' ] = 'any';

    foreach ( $product_page_ids as $product_page_id ) {

      $product_page         = get_post( $product_page_id );

      $search_criteria[ 'field_filters' ][] = array(
        'key'       => 'source_url',
        'value'     => $product_page->post_name,
        'operator'  => 'contains',
      );

    }

  }

  $paging             = array( 'offset' => 0, 'page_size' => 200 );
  $all_claims         = GFAPI::get_entries( $form_id, $search_criteria, $sorting, $paging );

  // Creates arrays of claims for this product, sorted by status.
  $in_progress        = array();
  $existing_customers = array();
  $closed_won         = array();
  $closed_lost        = array();

  foreach ( $all_claims as $claim ) {

    $select_name  = 'claim-' . $claim[ 'id' ] . '-status';
    $claim_status = $claim[ 13 ];

    if ( isset( $_POST[ $select_name ] ) && $_POST[ $select_name ] !== $claim_status ) {
      $claim[ 13 ] = $_POST[ $select_name ];
      GFAPI::update_entry( $claim );
    }

    switch ( $claim[ 13 ] ) {

      case 'Existing Customer' :
        $existing_customers[] = $claim;
        break;

      case 'Closed: Won' :
        $closed_won[] = $claim;
        break;

      case 'Closed: Lost' :
        $closed_lost[] = $claim;
        break;

      case 'In Progress' :
      case null :
        $in_progress[] = $claim;
        break;

    }

  }

  ?>

  <div id="lpd-affinity-claims">

    <?php

    if ( count( $in_progress ) > 0 ) {

      ?>

      <div class="table-label">In Progress</div>
      <?php echo lpd_get_affinity_claim_table( $in_progress ); ?>

      <?php

    }

    if ( count( $closed_won ) > 0 ) {

      ?>

      <div class="table-label">Customers Won</div>
      <?php echo lpd_get_affinity_claim_table( $closed_won ); ?>

      <?php

    }

    if ( count( $closed_lost ) > 0 || count( $existing_customers ) > 0 ) {

      ?>

      <button class="graybutton expandthis-click">Show Existing & Lost Customers</button>

      <div class="expandthis-hide">

        <?php

        if ( count( $closed_lost ) > 0 ) {

          ?>

          <div class="table-label">Customers Lost</div>
          <?php echo lpd_get_affinity_claim_table( $closed_lost ); ?>

          <?php

        }

        if ( count( $existing_customers ) > 0 ) {

          ?>

          <div class="table-label">Existing Customers</div>
          <?php echo lpd_get_affinity_claim_table( $existing_customers ); ?>

          <?php

        }

        ?>

      </div>

      <?php

    }

    ?>

  </div>

<?php

}


/**
* Builds the table of affinity claims.
*/
function lpd_get_affinity_claim_table( $claims ) {

  if ( empty( $claims ) ) { return; }

  ob_start();

    $form_id    = 55;
    $claim_form = GFAPI::get_form( $form_id );

    ?>

    <form action="" method="POST">

      <table class="affinity-claims">
        <thead>
          <tr>
            <th>Claim ID</th>
            <th>Date</th>
            <th>Customer</th>
            <th>Product</th>
            <th>Claim Status</th>
          </tr>
        </thead>
        <tbody>

          <?php

          foreach ( $claims as $claim ) {

            $select_name  = 'claim-' . $claim[ 'id' ] . '-status';

            ?>

            <tr>
              <td><?php echo $claim[ 'id' ]; ?></td>
              <td><?php echo date( 'Y-m-d', strtotime( $claim[ 'date_created' ] ) ); ?></td>
              <td>
                <?php echo $claim[ '1.3' ] . ' ' . $claim[ '1.6' ]; ?><br />
                <small><?php echo $claim[ 2 ]; ?></small><br />
                <small><?php echo $claim[ 5 ]; ?></small>
              </td>
              <td><?php echo $claim[ 3 ]; ?></td>
              <td class="claim_status">
                <label class="hidden" for="<?php echo $select_name; ?>-select">Update claim <?php echo $claim[ 'id' ]; ?> status.</label>
                <select name="<?php echo $select_name; ?>" id="<?php echo $select_name; ?>-select">

                  <?php

                  foreach ( $claim_form[ 'fields' ][ 11 ][ 'choices' ] as $option ) {

                    echo '<option value="' . $option[ 'value' ] . '"';

                    if ( $option[ 'value' ] == $claim[ 13 ] ) {
                      echo ' selected';
                    }

                    echo '>' . $option[ 'text' ] . '</option>';

                  }

                  ?>

                </select>
              </td>
            </tr>

            <?php

          }

          ?>

        </tbody>
      </table>

      <p style="text-align: right;"><button type="submit">Update</button></p>
    </form>

    <?php

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

        <p class="card-label">People Authorized to View this Dashboard</p>
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

        <?php

      return ob_get_clean();

    }

}
