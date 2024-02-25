<?php
$extimgurl 	= get_post_meta( $this->slide->ID, 'ml-slider_extimgurl', true );
$url 		= get_post_meta( $this->slide->ID, 'ml-slider_url', true );
$target 	= get_post_meta( $this->slide->ID, 'ml-slider_new_window', true ) 
			? true : false;
?>
<div class="thumb-col-settings">
	<div class="metaslider-ui-inner metaslider-slide-thumb">
		<div class="thumb" style="background-image: url(<?php 
		echo esc_url( $extimgurl )  ?>)"></div>
	</div>
	<div>
		<div class="row mb-2">
			<label>
				<?php _e( 'External Image URL', 'ml-slider-pro' ) ?>
			</label>
		</div>
		<div class="row">
			<input class="url extimgurl" type="text" name="attachment[<?php 
			echo esc_attr( $this->slide->ID ); ?>][extimgurl]" placeholder="<?php 
			esc_attr_e( 'Source Image URL', 'ml-slider-pro' ) ?>" value="<?php 
			echo esc_url( $extimgurl ); ?>" />
		</div>
		<div class="row mb-2">
			<label>
				<?php esc_html_e( 'Link URL', 'ml-slider' ) ?>
			</label>
		</div>
		<div class="row has-right-checkbox">
			<div>
				<input class="url" type="text" name="attachment[<?php 
				echo esc_attr($this->slide->ID); ?>][url]" placeholder="<?php 
				esc_attr_e( 'Link to URL', 'ml-slider-pro' ) ?>" value="<?php 
				echo esc_url( $url ); ?>" />
			</div>
			<div class="input-label">
				<label>
					<?php 
					esc_html_e( 'New window', 'ml-slider' );
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $this->info_tooltip( __(
						'Open link in a new window', 
						'ml-slider'
					) );
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $this->switch_button( 
						'attachment[' . esc_attr( $this->slide->ID ) . '][new_window]', 
						(bool) $target,
						array(),
						'mr-0 ml-2'
					);
					?>
				</label>
			</div>
		</div>
	</div>
</div>
