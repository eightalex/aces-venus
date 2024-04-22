<?php

function aces_bonuses_shortcode_2($atts) {

	ob_start();

	// Define attributes and their defaults

	extract( shortcode_atts( array (
	    'items_number' => 3,
	    'external_link' => '',
	    'category' => '',
	    'items_id' => '',
	    'parent_id' => '',
	    'exclude_id' => '',
	    'columns' => 3,
	    'order' => '',
	    'orderby' => '',
	    'title' => ''
	), $atts ) );

	$exclude_id_array = '';

	if ($exclude_id) {
		$exclude_id_array = explode( ',', $exclude_id );
	}

	if ( !empty( $category ) ) {

		$categories_id_array = explode( ',', $category );

		$args = array(
			'posts_per_page' => $items_number,
			'post_type'      => 'bonus',
			'post__not_in'   => $exclude_id_array,
			'no_found_rows'  => true,
			'post_status'    => 'publish',
			'tax_query' => array(
				array(
					'taxonomy' => 'bonus-category',
					'field'    => 'id',
					'terms'    => $categories_id_array
				)
			),
			'orderby'  => $orderby,
			'order'    => $order
		);

	} else if ( !empty( $items_id ) ) {

		$items_id_array = explode( ',', $items_id );

		$args = array(
			'posts_per_page' => $items_number,
			'post_type'      => 'bonus',
			'post__in'       => $items_id_array,
			'orderby'        => 'post__in',
			'no_found_rows'  => true,
			'post_status'    => 'publish'
		);

	} else if ( !empty( $parent_id ) ) {

		$parent_id = '"'.$parent_id.'"';

		$args = array(
			'posts_per_page' => $items_number,
			'post_type'      => 'bonus',
			'post__not_in'   => $exclude_id_array,
			'no_found_rows'  => true,
			'post_status'    => 'publish',
			'meta_query' => array(
		        array(
		            'key' => 'bonus_parent_casino',
		            'value' => $parent_id,
		            'compare' => 'LIKE'
		        )
		    )
		);

	} else {

		$args = array(
			'posts_per_page' => $items_number,
			'post_type'      => 'bonus',
			'post__not_in'   => $exclude_id_array,
			'no_found_rows'  => true,
			'post_status'    => 'publish',
			'orderby'  => $orderby,
			'order'    => $order
		);

	}

	$bonus_query = new WP_Query( $args );

	if ( $bonus_query->have_posts() ) {
	?>

	<div class="space-shortcode-wrap space-shortcode-8 relative">
		<div class="space-shortcode-wrap-ins relative">

			<?php if ( $title ) { ?>
			<div class="space-block-title relative">
				<span><?php echo esc_html($title); ?></span>
			</div>
			<?php } ?>

			<div class="space-offers-archive-items box-100 relative">

				<?php while ( $bonus_query->have_posts() ) : $bonus_query->the_post();
					global $post;
					$bonus_allowed_html = array(
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

					$bonus_external_link = esc_url( get_post_meta( get_the_ID(), 'bonus_external_link', true ) );
					$bonus_button_title = esc_html( get_post_meta( get_the_ID(), 'bonus_button_title', true ) );
					$bonus_button_notice = wp_kses( get_post_meta( get_the_ID(), 'bonus_button_notice', true ), $bonus_allowed_html );
					$bonus_dark_style = esc_attr( get_post_meta( get_the_ID(), 'bonus_dark_style', true ) );

					$offer_detailed_tc = wp_kses( get_post_meta( get_the_ID(), 'offer_detailed_tc', true ), $bonus_allowed_html );
					$offer_popup_hide = esc_attr( get_post_meta( get_the_ID(), 'aces_offer_popup_hide', true ) );
					$offer_popup_title = esc_html( get_post_meta( get_the_ID(), 'aces_offer_popup_title', true ) );

					if ($bonus_button_title) {
						$button_title = $bonus_button_title;
					} else {
						if ( get_option( 'bonuses_get_bonus_title') ) {
							$button_title = esc_html( get_option( 'bonuses_get_bonus_title') );
						} else {
							$button_title = esc_html__( 'Get Bonus', 'aces' );
						}
					}

					if ($offer_popup_title) {
						$custom_popup_title = $offer_popup_title;
					} else {
						$custom_popup_title = esc_html__( 'T&Cs Apply', 'aces' );
					}

					if ($external_link) {
						if ($bonus_external_link) {
							$external_link_url = $bonus_external_link;
						} else {
							$external_link_url = get_the_permalink();
						}
					} else {
						$external_link_url = get_the_permalink();
					}

					$post_title_attr = the_title_attribute( 'echo=0' );
					$terms = get_the_terms( $post->ID, 'bonus-category' );

					$casino_ids = get_post_meta( get_the_ID(), 'bonus_parent_casino', true );
					$casino_id = reset($casino_ids);
				?>

				<div class="space-offers-archive-item <?php if ($columns == 1) { ?>box-100<?php } else if ($columns == 2) { ?>box-50<?php } else { ?>box-33<?php } ?> relative<?php if ($bonus_dark_style == true ) { ?> space-dark-style<?php } ?>">
					<div class="space-offers-archive-item-ins relative" style="flex-wrap: wrap; align-items: start;">
						<div class="space-offers-archive-item-top-elements box-100 relative">
							<div class="space-offers-archive-item-top-left-element box-40 relative">
								<?php
								if( $casino_ids ){
									$casino_logo = wp_get_attachment_image_src(get_post_thumbnail_id($casino_id), 'mercury-135-135');
									if ($casino_logo) { ?>
										<a href="<?php the_permalink($casino_id); ?>" title="<?php echo get_the_title($casino_id); ?>">
											<img src="<?php echo esc_url($casino_logo[0]); ?>" alt="<?php echo get_the_title($casino_id); ?>">
										</a>
									<?php }									
								} elseif ( wp_get_attachment_image(get_post_thumbnail_id()) ) { ?>
									<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
										<?php echo wp_get_attachment_image( get_post_thumbnail_id(), 'mercury-135-135', "", array( "alt" => $post_title_attr ) ); ?>
									</a>
								<?php }
								?>
							</div>
							<div class="space-offers-archive-item-top-right-element box-60 relative">
								<div class="space-offers-archive-item-wrap relative" style="padding: 15px 15px 0;">

									<?php if ($casino_ids) { ?>
										<div class="space-offers-archive-item-cat relative">
										    <a href="<?php the_permalink($casino_id); ?>" title="<?php echo get_the_title($casino_id); ?>" style="font-size: 12px;"><?php echo get_the_title($casino_id); ?></a>
										</div>
									<?php } elseif ($terms) { ?>
										<div class="space-offers-archive-item-cat relative">
											<?php foreach ( $terms as $term ) { ?>
										        <a href="<?php echo esc_url (get_term_link( (int)$term->term_id, $term->taxonomy )); ?>" style="font-size: 12px;" title="<?php echo esc_attr($term->name); ?>"><?php echo esc_html($term->name); ?></a>
										    <?php } ?>
										</div>
									<?php } ?>

									<div class="space-offers-archive-item-title relative" style="margin-bottom: 0;">
										<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php get_the_title() ? the_title() : the_ID(); ?></a>
									</div>

								</div>
							</div>
						</div>
						<div class="space-offers-archive-item-middle-elements text-center box-100 relative">
							<div class="space-offers-archive-item-middle-elements-ins relative">
								<div class="space-offers-archive-item-button relative" style="margin-top: 0;">
									<a href="<?php echo esc_url( $external_link_url ); ?>" title="<?php echo esc_attr( $button_title ); ?>" <?php if ($external_link) { ?>target="_blank" rel="nofollow"<?php } ?>><?php echo esc_html( $button_title ); ?></a>
								</div>

								<?php if ($offer_popup_hide == true ) { ?>
									<div class="space-organization-header-button-notice relative" style="margin-top: 5px;">
										<span class="tc-apply"><?php echo esc_html( $custom_popup_title ); ?></span>
										<div class="tc-desc">
											<?php
												if ($offer_detailed_tc) {
													echo wp_kses( $offer_detailed_tc, $bonus_allowed_html );
												}
											?>
										</div>
									</div>
								<?php } ?>

								<?php if ($bonus_button_notice) { ?>

								<div class="space-offers-archive-item-button-notice relative" style="margin-top: 5px;">
									<?php echo wp_kses( $bonus_button_notice, $bonus_allowed_html ); ?>
								</div>

								<?php } ?>
							</div>
						</div>

						<?php
						if ($offer_popup_hide == true ) {

						} else {
							if ($offer_detailed_tc) { ?>
							<div class="space-organizations-archive-item-detailed-tc box-100 relative">
								<div class="space-organizations-archive-item-detailed-tc-ins relative" style="padding: 0 15px 15px;">
									<?php echo wp_kses( $offer_detailed_tc, $bonus_allowed_html ); ?>
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
		
		</div>
	</div>

	<?php
	wp_reset_postdata();
	$bonus_items = ob_get_clean();
	return $bonus_items;
	}

}
 
add_shortcode('aces-bonuses-2', 'aces_bonuses_shortcode_2');