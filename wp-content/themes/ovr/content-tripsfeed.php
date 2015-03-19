<?php
	
/* Upcoming Trips ________________________________________________________________ */ ?>

<div id="upcoming-trips">

	<h2>Upcoming Trips</h2> 

	<?php

	$args = array( 'post_type' => 'product', 'order' => 'ASC','posts_per_page' => -1 );   
	$upcoming_product_loop = new WP_Query( $args );

	$trip_dates = [];
	$trips_titles = [];
	$trip_links = [];
	$trip_thumbs = [];
	$spots_left = [];

	while( $upcoming_product_loop->have_posts() ) : $upcoming_product_loop->the_post();
		global $post;

		$trip_date = get_field( 'date_picker' );

		// only add trip to array if date_picker field is set.
		if ( $trip_date ) {
		$alt_thumb = wp_get_attachment_image_src( get_field( 'alternative_thumbnail', $post->ID ), 'full' );

		$trip_dates[] = $trip_date;
		$trip_titles[] = get_the_title();
		$trip_links[] = get_permalink();
		$trip_thumbs[] = $alt_thumb[0];
		$spots_left[] = get_post_meta( $post->ID, '_stock', true);
		}

		array_multisort($trip_dates, $trip_titles, $trip_links, $trip_thumbs, $spots_left);
	endwhile;

	// count how many trips are listed, if less than 8 have date_picker fields set, set to that number
	$max_trips = count($trip_dates) < 8 ? count($trip_dates) : 8;

	// foreach ($trip_dates as $key => $value) {
	for( $i = 0; $i < $max_trips; $i++ ) { ?>
		
		<div class="upcoming" style="overflow:hidden;">
				<div class="upcoming-thumbnail">
					<a href="<?php echo $trip_links[$i]; ?>">
						<img src="<?php echo $trip_thumbs[$i]; ?>" alt="<?php echo esc_attr( $trip_titles[$i] );?>">
					</a>
				</div>
				
				<div class="upcoming-title">
					<a href="<?php echo $trip_links[$i]; ?>"><?php echo $trip_titles[$i]; ?></a>
				</div>
				
				<div class="upcoming-info"> 
					<div class="book-this-btn">
						<a class="book-btn-smll" href="<?php echo $trip_links[$i]; ?>">BOOK THIS TRIP</a>
					</div>
					
					<div class="seats-left">
					   <p><a href="<?php echo $trip_links[$i]; ?>"><?php echo $spots_left[$i]; ?> Seats Left</a></p>
					</div>
				</div>	
			</div> 

		<hr/>

		<?php 
	} ?>

</div> <!-- #upcoming-trips -->
