<?php

function aces_casinos_shortcode_9($atts) {

	ob_start();

	// Define attributes and their defaults

	extract( shortcode_atts( array (
	    'items_number' => 5,
	    'show_items' => 5,
	    'external_link' => '',
	    'category' => '',
	    'items_id' => '',
	    'exclude_id' => '',
	    'game_id' => '',
	    'order' => '',
	    'orderby' => '',
	    'load_more_button' => '',
	    'title' => ''
	), $atts ) );

	if ( $orderby == 'rating') {
		$orderby = 'meta_value_num';
	}

	$exclude_id_array = '';

	if ($exclude_id) {
		$exclude_id_array = explode( ',', $exclude_id );
	}

	if ($game_id) {
		
		$casino_ids = get_post_meta( $game_id, 'parent_casino', true );
		
		$args = array(
			'posts_per_page' => $items_number,
			'post_type'      => 'casino',
			'post__in'       => $casino_ids,
			'post__not_in'   => $exclude_id_array,
			'no_found_rows'  => true,
			'post_status'    => 'publish',
			'meta_key'       => 'casino_overall_rating',
			'orderby'        => 'meta_value_num',
			'order'          => $order
		);

	} else {

		if ( !empty( $category ) ) {

			$categories_id_array = explode( ',', $category );

			$args = array(
				'posts_per_page' => $items_number,
				'post_type'      => 'casino',
				'post__not_in'   => $exclude_id_array,
				'no_found_rows'  => true,
				'post_status'    => 'publish',
				'tax_query' => array(
					array(
						'taxonomy' => 'casino-category',
						'field'    => 'id',
						'terms'    => $categories_id_array
					)
				),
				'meta_key' => 'casino_overall_rating',
				'orderby'  => $orderby,
				'order'    => $order
			);

		} else if ( !empty( $items_id ) ) {

			$items_id_array = explode( ',', $items_id );

			$args = array(
				'posts_per_page' => $items_number,
				'post_type'      => 'casino',
				'post__in'       => $items_id_array,
				'orderby'        => 'post__in',
				'no_found_rows'  => true,
				'post_status'    => 'publish'
			);

		} else {

			$args = array(
				'posts_per_page' => $items_number,
				'post_type'      => 'casino',
				'post__not_in'   => $exclude_id_array,
				'no_found_rows'  => true,
				'post_status'    => 'publish',
				'meta_key'       => 'casino_overall_rating',
				'orderby'        => $orderby,
				'order'          => $order
			);

		}

	}

	$show_items_double = $show_items*2;

	$casino_query = new WP_Query( $args );

	if ( $casino_query->have_posts() ) {

		if ( get_option( 'aces_rating_stars_number' ) ) {
			$casino_rating_stars_number_value = get_option( 'aces_rating_stars_number' );
		} else {
			$casino_rating_stars_number_value = '5';
		}

	?>

	<?php if ( $load_more_button ) { ?>

		<style type="text/css">
			.space-organizations-8-archive-items .space-organizations-8-archive-item:nth-child(<?php echo esc_attr( $show_items ); ?>) ~ .space-organizations-8-archive-item {
			  display: none;
			}
		</style>
		<script type="text/javascript">
			jQuery(document).ready( function($){
				'use strict';
				const list = document.querySelector(".space-organizations-8-archive-items");
				const list_items = list.querySelectorAll(".space-organizations-8-archive-item");
				const ajax_load_more = document.querySelector(".shortcode-load-more-9");
				let count = <?php echo esc_attr( $show_items ); ?>;
				let double_count = <?php echo esc_attr( $show_items_double ); ?>;
				ajax_load_more.addEventListener("click", function () {
					'use strict';
					let range = `.space-organizations-8-archive-item:nth-child(n+${count}):nth-child(-n+${double_count})`;
					list
					    .querySelectorAll(range)
					    .forEach((elem) => (elem.style.display = "block"));
					if (list_items.length <= double_count) {
				    	this.remove();
					} else {
					    count += <?php echo esc_attr( $show_items ); ?>;
					    double_count += <?php echo esc_attr( $show_items ); ?>;
					}
				});
			});
		</script>

	<?php } ?>

	<div class="space-shortcode-wrap space-shortcode-11 relative">
		<div class="space-shortcode-wrap-ins relative">

			<?php if ( $title ) { ?>
			<div class="space-block-title relative">
				<span><?php echo esc_html($title); ?></span>
			</div>
			<?php } ?>

			<div class="space-organizations-8-archive-items box-100 relative">

				<?php while ( $casino_query->have_posts() ) : $casino_query->the_post();
					global $post;
					$casino_allowed_html = array(
						'a' => array(
							'href' => true,
							'title' => true,
							'target' => true,
							'rel' => true
						),
						'br' => array(),
						'em' => array(),
						'strong' => array(),
						'span' => array(
							'class' => true
						),
						'div' => array(
							'class' => true
						),
						'p' => array()
					);

					$casino_external_link = esc_url( get_post_meta( get_the_ID(), 'casino_external_link', true ) );
					$casino_button_title = esc_html( get_post_meta( get_the_ID(), 'casino_button_title', true ) );
					$casino_terms_desc = wp_kses( get_post_meta( get_the_ID(), 'casino_terms_desc', true ), $casino_allowed_html );
					$casino_overall_rating = esc_html( get_post_meta( get_the_ID(), 'casino_overall_rating', true ) );
					$casino_button_notice = wp_kses( get_post_meta( get_the_ID(), 'casino_button_notice', true ), $casino_allowed_html );
					$casino_permalink_button_title = esc_html( get_post_meta( get_the_ID(), 'casino_permalink_button_title', true ) );
					$casino_button_notice = wp_kses( get_post_meta( get_the_ID(), 'casino_button_notice', true ), $casino_allowed_html );

					$organization_detailed_tc = wp_kses( get_post_meta( get_the_ID(), 'casino_detailed_tc', true ), $casino_allowed_html );
					$organization_popup_hide = esc_attr( get_post_meta( get_the_ID(), 'aces_organization_popup_hide', true ) );
					$organization_popup_title = esc_html( get_post_meta( get_the_ID(), 'aces_organization_popup_title', true ) );

					if ($external_link) {
						if ($casino_external_link) {
							$external_link_url = $casino_external_link;
						} else {
							$external_link_url = get_the_permalink();
						}
					} else {
						$external_link_url = get_the_permalink();
					}

					if ($casino_button_title) {
						$button_title = $casino_button_title;
					} else {
						if ( get_option( 'casinos_play_now_title') ) {
							$button_title = esc_html( get_option( 'casinos_play_now_title') );
						} else {
							$button_title = esc_html__( 'Play Now', 'aces' );
						}
					}

					if ($organization_popup_title) {
						$custom_popup_title = $organization_popup_title;
					} else {
						$custom_popup_title = esc_html__( 'T&Cs Apply', 'aces' );
					}

					if ($casino_permalink_button_title) {
						$permalink_button_title = $casino_permalink_button_title;
					} else {
						if ( get_option( 'casinos_read_review_title') ) {
							$permalink_button_title = esc_html( get_option( 'casinos_read_review_title') );
						} else {
							$permalink_button_title = esc_html__( 'Read Review', 'aces' );
						}
					}

					$terms = get_the_terms( $post->ID, 'casino-category' );
				?>

				<div class="space-organizations-8-archive-item box-100 relative">
					<div class="space-organizations-8-archive-item-ins relative">
						<div class="space-organizations-8-archive-item-bg box-100 relative">
							<div class="space-organizations-8-archive-item-left box-33 relative">

									<div class="space-organizations-8-archive-item-brand box-100 relative">
										<div class="space-organizations-8-archive-item-brand-logo box-40 text-center relative">
											<div class="space-organizations-8-archive-item-brand-logo-ins relative">

												<?php
												$post_title_attr = the_title_attribute( 'echo=0' );
												if ( wp_get_attachment_image(get_post_thumbnail_id()) ) { ?>
													<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
														<?php echo wp_get_attachment_image( get_post_thumbnail_id(), 'mercury-135-135', "", array( "alt" => $post_title_attr ) ); ?>
													</a>
												<?php } ?>

											</div>
										</div>
										<div class="space-organizations-8-archive-item-brand-name text-center box-60 relative">
											<div class="space-organizations-8-archive-item-brand-name-link box-100 relative">
												<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
											</div>

											<?php if( function_exists('aces_star_rating') ){ ?>
												<div class="space-organizations-8-archive-item-stars-rating relative">
													<?php aces_star_rating(
														array(
															'rating' => $casino_overall_rating,
															'type' => 'rating',
															'stars_number' => $casino_rating_stars_number_value
														)
													); ?>
												</div>
											<?php } ?>

										</div>
									</div>

							</div>
							<div class="space-organizations-8-archive-item-central box-33 relative">
								<div class="space-organizations-8-archive-item-ins-pd relative">
									<div class="space-organizations-8-archive-item-terms box-100 relative">
										<?php if ($casino_terms_desc) {
											echo wp_kses( $casino_terms_desc, $casino_allowed_html );
										} ?>
									</div>
								</div>
							</div>
							<div class="space-organizations-8-archive-item-right box-33 relative">
								
									<div class="space-organizations-8-archive-item-buttons box-100 relative">
										<div class="space-organizations-8-archive-item-buttons-left box-50 text-center relative">

											<div class="space-organizations-8-archive-item-button-one box-100 relative">
												<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( $permalink_button_title ); ?>"><?php echo esc_html( $permalink_button_title ); ?></a>
											</div>

											<?php if ($organization_popup_hide == true ) { ?>
												<div class="space-organization-header-button-notice relative" style="margin-top: 5px;">
													<span class="tc-apply"><?php echo esc_html( $custom_popup_title ); ?></span>
													<div class="tc-desc">
														<?php
															if ($organization_detailed_tc) {
																echo wp_kses( $organization_detailed_tc, $casino_allowed_html );
															}
														?>
													</div>
												</div>
											<?php } ?>

											<?php if ($casino_button_notice) { ?>
												<div class="space-organizations-8-archive-item-button-notice box-100 relative" style="margin-top: 5px;">
													<?php echo wp_kses( $casino_button_notice, $casino_allowed_html ); ?>
												</div>
											<?php } ?>

										</div>
										<div class="space-organizations-8-archive-item-buttons-right box-50 text-center relative">
											<div class="space-organizations-8-archive-item-button-two relative">
												<a href="<?php echo esc_url( $external_link_url ); ?>" title="<?php echo esc_attr( $button_title ); ?>" <?php if ($external_link) { ?>target="_blank" rel="nofollow"<?php } ?>><?php echo esc_html( $button_title ); ?></a>
											</div>
										</div>
									</div>
							
							</div>
						</div>

						<?php
						if ($organization_popup_hide == true ) {

						} else {
							if ($organization_detailed_tc) { ?>
						<div class="space-organizations-archive-item-detailed-tc box-100 relative">
							<div class="space-organizations-archive-item-detailed-tc-ins relative" style="padding-top: 5px;">
								<?php echo wp_kses( $organization_detailed_tc, $casino_allowed_html ); ?>
							</div>
						</div>
						<?php
							}
						}
						?>
						
					</div>
				</div>

				<?php endwhile; ?>

			</div>
			<?php if ( $load_more_button ) { ?>

				<div class="space-load-more-button-wrap text-center box-100 relative">
					<div class="space-load-more-button-wrap-ins relative">
						<button class="space-load-more-button shortcode-load-more-9"><?php echo esc_html__( 'Load More', 'aces' ); ?></button>
					</div>
				</div>

			<?php } ?>		
		</div>
	</div>

<?php
wp_reset_postdata();
$casino_items = ob_get_clean();
return $casino_items;
}

}
 
add_shortcode('aces-casinos-9', 'aces_casinos_shortcode_9');