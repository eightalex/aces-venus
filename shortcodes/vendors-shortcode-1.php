<?php

function aces_vendors_shortcode_1($atts) {

	ob_start();

	// Define attributes and their defaults

	extract( shortcode_atts( array (
	    'items_number' => 4,
	    'columns' => 4,
	    'order' => '',
	    'orderby' => '',
	    'title' => ''
	), $atts ) );

	$args = array(
		'hide_empty'=> 1,
		'type' => 'game',
		'taxonomy' => 'vendor',
		'orderby' => $orderby,
		'order' => $order
	);
	$vendors = get_categories($args);

	if( $vendors ){
	?>

		<div class="space-shortcode-wrap space-units-shortcode-1 relative relative">
			<div class="space-shortcode-wrap-ins relative">

				<?php if ( $title ) { ?>
				<div class="space-block-title relative">
					<span><?php echo esc_html($title); ?></span>
				</div>
				<?php } ?>

				<div class="space-units-archive-items vendor-list-items box-100 relative">

					<?php foreach($vendors as $vendor) { ?>

					<div class="space-units-archive-item vendor-list-item <?php if ($columns == 1) { ?>box-100<?php } else if ($columns == 2) { ?>box-50<?php } else if ($columns == 3) { ?>box-33<?php } else { ?>box-25<?php } ?> relative">
						<div class="space-units-archive-item-ins relative">
							<?php
							$vendor_logo = get_term_meta($vendor->term_id, 'taxonomy-image-id', true);
							if ($vendor_logo) { ?>
								<div class="space-units-archive-item-img relative">
									<a href="<?php echo esc_url( get_term_link($vendor->slug, 'vendor') ); ?>" title="<?php echo esc_attr($vendor->name); ?>">
										<?php echo wp_get_attachment_image( $vendor_logo, 'mercury-450-254', "", array( "class" => "space-vendor-list-logo" ) );  ?>
									</a>
								</div>
								<div class="space-units-archive-item-button text-center relative">
									<a href="<?php echo esc_url( get_term_link($vendor->slug, 'vendor') ); ?>" title="<?php esc_attr_e( 'View Games', 'aces' ); ?>"><?php esc_html_e( 'View Games', 'aces' ); ?></a>
								</div>
							<?php } ?>
							<div class="space-units-archive-item-wrap text-center relative">
								<div class="space-units-archive-item-title relative">
									<a href="<?php echo esc_url( get_term_link($vendor->slug, 'vendor') ); ?>" title="<?php echo esc_attr($vendor->name); ?>"><?php echo esc_html($vendor->name); ?></a>
								</div>							
							</div>
						</div>
					</div>

					<?php } ?>

				</div>
			
			</div>
		</div>

	<?php
	wp_reset_postdata();
	$vendor_items = ob_get_clean();
	return $vendor_items;
}

}
 
add_shortcode('aces-vendors-1', 'aces_vendors_shortcode_1');