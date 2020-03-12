<?php

/**
 * @class FLVideoModule
 */
class FLVideoModule extends FLBuilderModule {

	/**
	 * @property $data
	 */
	public $data = null;

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'            => __( 'Video', 'fl-builder' ),
			'description'     => __( 'Render a WordPress or embedable video.', 'fl-builder' ),
			'category'        => __( 'Basic', 'fl-builder' ),
			'partial_refresh' => true,
			'icon'            => 'format-video.svg',
		));

		$this->add_js( 'jquery-fitvids' );

		add_filter( 'wp_video_shortcode', __CLASS__ . '::mute_video', 10, 4 );
	}

	/**
	 * @method get_data
	 */
	public function get_data() {
		if ( ! $this->data ) {

			$this->data = FLBuilderPhoto::get_attachment_data( $this->settings->video );

			if ( ! $this->data && isset( $this->settings->data ) ) {
				$this->data = $this->settings->data;
			}
			if ( $this->data ) {
				$parts                 = explode( '.', $this->data->filename );
				$this->data->extension = array_pop( $parts );
				$this->data->poster    = isset( $this->settings->poster_src ) ? $this->settings->poster_src : '';
				$this->data->loop      = isset( $this->settings->loop ) && $this->settings->loop ? ' loop="yes"' : '';
				$this->data->autoplay  = isset( $this->settings->autoplay ) && $this->settings->autoplay ? ' autoplay="yes"' : '';

				// WebM format
				$webm_data              = FLBuilderPhoto::get_attachment_data( $this->settings->video_webm );
				$this->data->video_webm = isset( $this->settings->video_webm ) && $webm_data ? ' webm="' . $webm_data->url . '"' : '';

			}
		}

		return $this->data;
	}

	/**
	 * @method update
	 * @param $settings {object}
	 */
	public function update( $settings ) {
		// Cache the attachment data.
		if ( 'media_library' == $settings->video_type ) {

			$video = FLBuilderPhoto::get_attachment_data( $settings->video );

			if ( $video ) {
				$settings->data = $video;
			} else {
				$settings->data = null;
			}
		}

		return $settings;
	}

	/**
	 * Temporary fix for autoplay in Chrome & Safari. Video shortcode doesn't support `muted` parameter.
	 * Bug report: https://core.trac.wordpress.org/ticket/42718.
	 *
	 * @since 2.1.3
	 * @param string $output  Video shortcode HTML output.
	 * @param array  $atts    Array of video shortcode attributes.
	 * @param string $video   Video file.
	 * @param int    $post_id Post ID.
	 * @return string
	 */
	static public function mute_video( $output, $atts, $video, $post_id ) {
		if ( false !== strpos( $output, 'autoplay="1"' ) && FLBuilderModel::get_post_id() == $post_id ) {
			$output = str_replace( '<video', '<video muted', $output );
		}
		return $output;
	}

	/**
	 * Calculate video aspect ratio for style.
	 *
	 * @since 2.2
	 * @return float
	 */
	public function video_aspect_ratio() {
		$data = $this->get_data();
		if ( $data && function_exists( 'bcdiv' ) ) {
			$ratio = ( $data->height / $data->width ) * 100;
			return bcdiv( $ratio, 1, 2 );
		}
	}

	/**
	 * Returns structured data markup.
	 * @since 2.2
	 */
	public function get_structured_data( $module ) {
		$settings = $module->settings;
		$markup   = '';
		if ( 'yes' != $settings->schema_enabled ) {
			return false;
		}
		if ( '' == $settings->name || '' == $settings->description || '' == $settings->thumbnail || '' == $settings->up_date ) {
			return false;
		}
		$markup .= sprintf( '<meta itemprop="name" content="%s" />', esc_attr( $settings->name ) );
		$markup .= sprintf( '<meta itemprop="uploadDate" content="%s" />', esc_attr( $settings->up_date ) );
		$markup .= sprintf( '<meta itemprop="thumbnailUrl" content="%s" />', $settings->thumbnail_src );
		$markup .= sprintf( '<meta itemprop="description" content="%s" />', esc_attr( $settings->description ) );

		return $markup;
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLVideoModule', array(
	'general' => array(
		'title'    => __( 'General', 'fl-builder' ),
		'sections' => array(
			'general' => array(
				'title'  => '',
				'fields' => array(
					'video_type' => array(
						'type'    => 'select',
						'label'   => __( 'Video Type', 'fl-builder' ),
						'default' => 'wordpress',
						'options' => array(
							'media_library' => __( 'Media Library', 'fl-builder' ),
							'embed'         => __( 'Embed', 'fl-builder' ),
						),
						'toggle'  => array(
							'media_library' => array(
								'fields' => array( 'video', 'video_webm', 'poster', 'autoplay', 'loop' ),
							),
							'embed'         => array(
								'fields' => array( 'embed_code' ),
							),
						),
					),
					'video'      => array(
						'type'        => 'video',
						'label'       => __( 'Video (MP4)', 'fl-builder' ),
						'help'        => __( 'A video in the MP4 format. Most modern browsers support this format.', 'fl-builder' ),
						'show_remove' => true,
					),
					'video_webm' => array(
						'type'        => 'video',
						'show_remove' => true,
						'label'       => __( 'Video (WebM)', 'fl-builder' ),
						'help'        => __( 'A video in the WebM format to use as fallback. This format is required to support browsers such as FireFox and Opera.', 'fl-builder' ),
						'preview'     => array(
							'type' => 'none',
						),
					),
					'poster'     => array(
						'type'        => 'photo',
						'show_remove' => true,
						'label'       => _x( 'Poster', 'Video preview/fallback image.', 'fl-builder' ),
					),
					'autoplay'   => array(
						'type'    => 'select',
						'label'   => __( 'Auto Play', 'fl-builder' ),
						'default' => '0',
						'options' => array(
							'0' => __( 'No', 'fl-builder' ),
							'1' => __( 'Yes', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
					'loop'       => array(
						'type'    => 'select',
						'label'   => __( 'Loop', 'fl-builder' ),
						'default' => '0',
						'options' => array(
							'0' => __( 'No', 'fl-builder' ),
							'1' => __( 'Yes', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
					'embed_code' => array(
						'type'        => 'code',
						'wrap'        => true,
						'editor'      => 'html',
						'label'       => '',
						'rows'        => '9',
						'connections' => array( 'custom_field' ),
					),
				),
			),

		),
	),
	'schema'  => array(
		'title'    => 'Structured Data',
		'sections' => array(
			'schema' => array(
				'fields' => array(
					'schema_enabled' => array(
						'type'    => 'select',
						'label'   => __( 'Enable Structured Data?', 'fl-builder' ),
						'default' => 'no',
						'preview' => array(
							'type' => 'none',
						),
						'toggle'  => array(
							'yes' => array(
								'fields' => array( 'name', 'description', 'thumbnail', 'up_date' ),
							),
						),
						'options' => array(
							'yes' => __( 'Yes', 'fl-builder' ),
							'no'  => __( 'No', 'fl-builder' ),
						),
					),
					'name'           => array(
						'type'    => 'text',
						'label'   => __( 'Video Name', 'fl-builder' ),
						'preview' => array(
							'type' => 'none',
						),
					),
					'description'    => array(
						'type'    => 'text',
						'label'   => __( 'Video Description', 'fl-builder' ),
						'preview' => array(
							'type' => 'none',
						),
					),
					'thumbnail'      => array(
						'type'        => 'photo',
						'label'       => __( 'Video Thumbnail', 'fl-builder' ),
						'show_remove' => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
					'up_date'        => array(
						'type'    => 'date',
						'label'   => __( 'Upload Date', 'fl-builder' ),
						'preview' => array(
							'type' => 'none',
						),
					),
				),
			),
		),
	),
));
