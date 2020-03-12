<?php

	$columns = count( $settings->pricing_columns );

?>

<div class="fl-pricing-table fl-pricing-table-spacing-<?php echo $settings->spacing; ?> fl-pricing-table-border-<?php echo $settings->border_size; ?> fl-pricing-table-<?php echo $settings->border_radius; ?>">
	<?php

	for ( $i = 0; $i < count( $settings->pricing_columns ); $i++ ) :

		if ( ! is_object( $settings->pricing_columns[ $i ] ) ) {
			continue;
		}

		$pricing_column = $settings->pricing_columns[ $i ];

		?>
	<div class="fl-pricing-table-col-<?php echo $columns; ?>">
		<div class="fl-pricing-table-column fl-pricing-table-column-<?php echo $i; ?>">
			<div class="fl-pricing-table-inner-wrap">
				<h2 class="fl-pricing-table-title"><?php echo $pricing_column->title; ?></h2>
				<div class="fl-pricing-table-price">
					<?php echo $pricing_column->price; ?>
					<span class="fl-pricing-table-duration"><?php echo $pricing_column->duration; ?></span>
				</div>
				<ul class="fl-pricing-table-features">
					<?php
					if ( ! empty( $pricing_column->features ) ) {
						foreach ( $pricing_column->features as $feature ) :
							?>
					<li><?php echo trim( $feature ); ?></li>
											<?php
					endforeach;
					};
					?>
				</ul>

				<?php $module->render_button( $i ); ?>

				<br />

			</div>
		</div>
	</div>
		<?php

	endfor;

	?>
</div>
