<?php

add_action( 'init', 'wptuts_buttons' );
function wptuts_buttons() {
    add_filter( "mce_external_plugins", "wptuts_add_buttons" );
    add_filter( 'mce_buttons', 'wptuts_register_buttons' );
}
function wptuts_add_buttons( $plugin_array ) {
    $plugin_array['wptuts'] = get_template_directory_uri() . '/wptuts-editor-buttons/wptuts-plugin.js';
    return $plugin_array;
}
function wptuts_register_buttons( $buttons ) {
    array_push( $buttons, 'dropcap', 'showrecent' ); // dropcap', 'recentposts
    return $buttons;
}



add_shortcode( 'recent-posts', 'wptuts_recent_posts' );
function wptuts_recent_posts( $atts ) {
    extract( shortcode_atts( array(
        'numbers' => '5',
    ), $atts ) );
    $rposts = new WP_Query( array( 'posts_per_page' => $numbers, 'orderby' => 'date' ) );
    if ( $rposts->have_posts() ) {
        $html = '<h3>Recent Posts</h3><ul class="recent-posts">';
        while( $rposts->have_posts() ) {
            $rposts->the_post();
            $html .= sprintf(
                '<li><a href="%s" title="%s">%s</a></li>',
                get_permalink($rposts->post->ID),
                get_the_title(),
                get_the_title()
            );
        }
        $html .= '</ul>';
    }
    wp_reset_query();
    return $html;
}
?>