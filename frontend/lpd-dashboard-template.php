<?php /* Template Name: Partner Dashboard */ ?>

<?php get_header(); ?>

<div id="column_container">

	<div id="content_column">

		<?php

		if ( is_user_logged_in() ) {

			$partner_id = sanitize_text_field( $_GET[ 'partner' ] );
			$user_id		= get_current_user_ID();
			$dashboards	= lpd_get_partners_by_user( $user_id ); // Returns an array.

			if ( $partner_id && in_array( $partner_id, $dashboards ) ) {

				lpd_dashboard( $partner_id );

			} elseif ( $partner_id && !in_array( $partner_id, $dashboards ) ) {

				?>

				<h1>Access Denied</h1>

				<p>It doesn't look like you are authorized to view this partner dashboard.</p>

				<p>If you think this is an error, please <a href="https://lawyerist.local/about/contact/">contact us</a> and we will sort it out as quickly as possible!</p>

				<a class="button" href="/partner-dashboard/">Back</a>

				<?php

			} elseif ( $dashboards ) {

				if ( count( $dashboards ) > 1 ) {

					?>

					<h1>Your Partner Dashboards</h1>

					<div id="lpd-dashboard-list" class="cards">

						<?php

						foreach ( $dashboards as $partner_id ) {

							$partner = get_post( $partner_id );

							?>

							<a class="card" href="?partner=<?php echo $partner_id; ?>">
								<?php if ( has_post_thumbnail( $partner_id ) ) { echo '<div itemprop="image">' . get_the_post_thumbnail( $partner_id, 'thumbnail' ) . '</div>'; } ?>
								<h2><?php echo $partner->post_title; ?></h2>
							</a>

							<?php

						}

						?>

					</div>

					<?php

				} else {

					lpd_dashboard( $dashboards[0] );

				}

			} else {

				?>

				<h1>No Dashboards Found</h1>

				<p>It doesn't look like you are authorized to view any partner dashboards.</p>

				<p>If you think this is an error, please <a href="https://lawyerist.local/about/contact/">contact us</a> and we will sort it out as quickly as possible!</p>

				<?php

			}

		} else {

			?>

			<h1>Sign in to Access Your Partner Dashboard(s)</h1>

			<div class="card" id="lpd-login">
				<?php wp_login_form(); ?>
				<p class="login-lost-password remove_bottom">Forgot your password? <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>">Reset it here.</a></p>
			</div>

			<?php

		}

		?>

	</div><!-- end #content_column -->

</div><!--end #column_container-->

<?php get_footer(); ?>
