<?php /* Template Name: Partner Dashboard */ ?>

<?php get_header(); ?>

<div id="column_container">

	<div id="content_column">

    <?php

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

        <h1 class="partner"><?php echo $partner; ?> Dashboard</h1>

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

	</div><!-- end #content_column -->

</div><!--end #column_container-->

<?php get_footer(); ?>
