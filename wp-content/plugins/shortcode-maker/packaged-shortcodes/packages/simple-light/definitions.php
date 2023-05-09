<?php
$items = Smps_Simple_Light::settings()['items'];
foreach ( $items as $item => $label ) {
    add_shortcode( 'smps_sl_'.$item, array( 'Smps_Simple_Light_Shortcodes', 'render_'.$item ) );
}

class Smps_Simple_Light_Shortcodes {

    public static function render_tabs( $atts, $content, $tag ) {

        $atts = shortcode_atts( array(
            'data' => '{}'
        ), $atts, $tag );

        $data = json_decode( base64_decode( $atts['data'] ),true );
        if( !is_array( $data ) ) {
            $data = json_decode(stripslashes(urldecode($atts['data'])),true);
        }

        ?>
        <div class="bs-container">
            <!-- Nav tabs -->
            <ul class="nav nav-<?php echo $data['type']; ?>">
                <?php
                $output = '';
                $i = 0;
                foreach ( $data['tab_data'] as $tab_key => $tab ) : $i++; ?>
                    <li class="nav-item">
                        <a href="#<?php echo $tab_key ; ?>" class="<?php echo $i == 1? 'active show':''; ?> nav-link" data-toggle="tab"><?php echo $tab['title']; ?></a>
                    </li>
                    <?php
                    ob_start();
                    ?>
                    <div class="tab-pane fade <?php echo $i == 1? 'in active show':''; ?>" id="<?php echo $tab_key; ?>">
                        <?php echo nl2br($tab['content']); ?>
                    </div>
                    <?php
                    $output .= ob_get_contents();
                    ob_end_clean();
                    ?>
                <?php endforeach; ?>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <?php echo $output; ?>
            </div>
        </div>
<?php
    }

    public static function render_accordion( $atts, $content, $tag ) {
        $atts = shortcode_atts( array(
            'data' => '{}'
        ), $atts, $tag );

        $data = json_decode( base64_decode( $atts['data'] ),true );
        if( !is_array( $data ) ) {
            $data = json_decode(stripslashes(urldecode($atts['data'])),true);
        } ?>
        <div class="bs-container">
            <div id="accordion">
                <?php
                $i = 0;
                foreach ( $data['acc_data'] as $acc_key => $accordion ) : $i++; ?>
                    <div class="card">
                        <div class="card-header" id="heading-<?php echo $acc_key; ?>">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse-<?php echo $acc_key; ?>" aria-expanded="false" aria-controls="collapse-<?php echo $accordion['content']; ?>">
                                    <?php echo $accordion['title']; ?>
                                </button>
                            </h5>
                        </div>
                        <div id="collapse-<?php echo $acc_key; ?>" class="collapse <?php echo $i == 1 ? 'show' : ''; ?>" aria-labelledby="heading-<?php echo $acc_key; ?>" data-parent="#accordion">
                            <div class="card-body">
                                <?php echo $accordion['content']; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
<?php
    }

    /**
     * Render table
     * @param $atts
     * @param $content
     * @param $tag
     */
    public static function render_table( $atts, $content, $tag ) {

        $atts = shortcode_atts( array(
            'data' => '{}'
        ), $atts, $tag );

        $data = json_decode( base64_decode( $atts['data'] ),true );

        if( !is_array( $data ) ) {
            $data = json_decode(stripslashes(urldecode($atts['data'])),true);
        }

        ?>
        <div class="bs-container">
            <table class="table table-striped">
                <tbody>
                <?php foreach ( $data['table_data']/*['tbody']*/ as $key => $tr ) : ?>
                    <tr>
                        <?php foreach ( $tr as $k => $td ) : ?>
                            <td><?php echo $td; ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        </div>
    <?php
    }

    public static function render_alert( $atts, $content, $tag ) {

        $atts = shortcode_atts( array(
            'data' => '{}'
        ), $atts, $tag );

        $data = json_decode( base64_decode( $atts['data'] ),true );
        if( !is_array( $data ) ) {
            $data = json_decode(stripslashes(urldecode($atts['data'])),true);
        }
        ?>
        <div class="bs-container">
            <div class="alert alert-<?php echo $data['type']; ?> alert-<?php echo $data['dismissable'] == 'true' ? 'dismissible' : ''; ?> fade show" role="alert">
                <?php echo $data['content']; ?>
                <?php if( $data['dismissable'] == 'true' ) : ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    <?php
    }


    public static function render_heading( $atts, $content, $tag ) {

        //
        $atts = shortcode_atts( array(
            'data' => '{}'
        ), $atts, $tag );

        $data = json_decode( base64_decode( $atts['data'] ),true );
        if( !is_array( $data ) ) {
            $data = json_decode(stripslashes(urldecode($atts['data'])),true);
        }
        ?>
        <div class="bs-container">
            <<?php echo $data['type'] ; ?> class="text-<?php echo $data['text_align'];?>"><?php echo $data['text'];?></<?php echo $data['type'];?>>
        </div>
    <?php
    }

    /**
     * quote
     * @param $atts
     * @param $content
     * @param $tag
     */
    public static function render_quote( $atts, $content, $tag ) {
        $atts = shortcode_atts( array(
            'data' => '{}'
        ), $atts, $tag );

        $data = json_decode( base64_decode( $atts['data'] ),true );
        if( !is_array( $data ) ) {
            $data = json_decode(stripslashes(urldecode($atts['data'])),true);
        }
        ?>
        <div class="bs-container">
            <blockquote class="blockquote mb-0 pull-<?php echo $data['alignment'];?>">
                <p><?php echo $data['quote']; ?></p>
                <?php if( $data['author'] ) : ?>
                    <footer class="blockquote-footer"><?php echo $data['author']; ?></footer>
                <?php endif; ?>

            </blockquote>
        </div>
    <?php
    }

    /**
     * button
     * @param $atts
     * @param $content
     * @param $tag
     */
    public static function render_button( $atts, $content, $tag ) {
        $atts = shortcode_atts( array(
            'data' => '{}'
        ), $atts, $tag );

        $data = json_decode( base64_decode( $atts['data'] ),true );
        if( !is_array( $data ) ) {
            $data = json_decode(stripslashes(urldecode($atts['data'])),true);
        }
        ?>
        <div class="bs-container" style="display: inline;">
            <?php
            switch ( $data['redirection_type'] ) {
                case 'url' :
                    $redirect_to = $data['url'];
                    break;
                case 'same_page' :
                    $redirect_to = '';
                    break;
                case 'to_page' :
                    $redirect_to = get_permalink($data['page']);
                    break;
            }
            ?>
            <a href="<?php echo $redirect_to; ?>" <?php echo $data['open_newtab'] == 'true' ? 'target="_blank"' : '' ;?> class="btn btn-<?php echo $data['type']; ?> btn-<?php echo $data['size']; ?> <?php echo $data['shape'] == 'normal' ? 'br0' : ''; ?>">
                <?php
                if( $data['icon'] == 'true' ) :
                    ?>
                    <i class="glyphicon glyphicon-<?php echo $data['icon']; ?>"></i>
                    <?php
                    endif;
                ?>
                <?php echo $data['enable_text'] == 'true' ? $data['text'] : ''; ?>
            </a>
        </div>
        <?php
    }


    /**
     * Spoiler
     * @param $atts
     * @param $content
     * @param $tag
     */
    public static function render_spoiler( $atts, $content, $tag ) {}

    /**
     * list
     * @param $atts
     * @param $content
     * @param $tag
     */
    public static function render_list( $atts, $content, $tag ) {
        $atts = shortcode_atts( array(
            'data' => '{}'
        ), $atts, $tag );

        $data = json_decode( base64_decode( $atts['data'] ),true );
        if( !is_array( $data ) ) {
            $data = json_decode(stripslashes(urldecode($atts['data'])),true);
        }


        echo '<'.$data['list_type'].' class="'.$data['class'].'" id="'.$data['id'].'">';
        foreach ( $data['items'] as $item ) {
            echo '<li>'.$item['label'].'</li>';
        }
        echo '</'.$data['list_type'].'>';

        /*list shortcode definition goes here*/

    }

    /**
     * Highlight
     * @param $atts
     * @param $content
     * @param $tag
     */
    public static function render_highlight( $atts, $content, $tag ) {

        $atts = shortcode_atts( array(
            'data' => '{}'
        ), $atts, $tag );

        $data = json_decode( base64_decode( $atts['data'] ),true );
        if( !is_array( $data ) ) {
            $data = json_decode(stripslashes(urldecode($atts['data'])),true);
        }

        /*highlight shortcode definition goes here*/
        echo '<span class="'.$data['class'].'" id="'.$data['id'].'" style="background:'. $data['background'] .';color:'.$data['text_color'].';">' . $data['content'] . '</span>';
    }

    /**
     * Member content (member_content)
     * @param $atts
     * @param $content
     * @param $tag
     */
    public static function render_restricted_content( $atts, $content, $tag ) {

        $atts = shortcode_atts( array(
            'data' => '{}'
        ), $atts, $tag );

        $data = json_decode( base64_decode( $atts['data'] ),true );
        if( !is_array( $data ) ) {
            $data = json_decode(stripslashes(urldecode($atts['data'])),true);
        }

        /*memberContent shortcode definition goes here*/

        if( is_user_logged_in() ) {

            echo nl2br($data['restricted_content']);
        } else {
            $data['login_text'] = str_replace( '%login%', '<a href="'.wp_login_url().'">login</a>',$data['login_text']);
            echo '<span style="background:'. $data['bg_color'] .';">'. $data['login_text'] .'</span>';
        }
    }

    /**
     * Youtube (youtube)
     * @param $atts
     * @param $content
     * @param $tag
     */
    public static function render_youtube( $atts, $content, $tag ) {
        $atts = shortcode_atts( array(
            'data' => '{}'
        ), $atts, $tag );

        $data = json_decode( base64_decode( $atts['data'] ),true );
        if( !is_array( $data ) ) {
            $data = json_decode(stripslashes(urldecode($atts['data'])),true);
        }

        /*Youtube shortcode definition goes here*/
        $video_id = ( preg_match( '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $data['url'], $match ) ) ? $match[1] : false;

        if( !$video_id ) return;
        ?>
        <iframe id="ytplayer" class="<?php echo $data['class']; ?>"
                type="text/html"
                width="<?php echo $data['width']; ?>"
                height="<?php echo $data['height']; ?>"
                src="https://www.youtube.com/embed/<?php echo $video_id; ?>?autoplay=<?php echo $data['autoplay'] == 'no' ? 0 : 1; ?>&controls=<?php echo $data['controls']; ?>&autohide=<?php echo $data['autohide']; ?>&loop=<?php echo $data['loop']; ?>&rel=<?php echo $data['related_videos'] == 'no' ? 0 : 1; ?>&fs=<?php echo $data['full_screen_button'] == 'no' ? 0 : 1; ?>&modestbranding=<?php echo $data['modestbranding'] == 'no' ? 0 : 1; ?>"
                frameborder="0"></iframe>
<?php
    }

    /**
     * Vimeo (vimeo)
     * @param $atts
     * @param $content
     * @param $tag
     */
    public static function render_vimeo( $atts, $content, $tag ) {
        $atts = shortcode_atts( array(
            'data' => '{}'
        ), $atts, $tag );

        $data = json_decode( base64_decode( $atts['data'] ),true );
        if( !is_array( $data ) ) {
            $data = json_decode(stripslashes(urldecode($atts['data'])),true);
        }

        /*Vimeo shortcode definition goes here*/
        ?>
        <iframe src="https://player.vimeo.com/video/225888984?autoplay=<?php echo $data['autoplay'] == 'no' ? 0 : 1; ?>&loop=<?php echo $data['loop'] == 'no' ? 0 : 1; ?>"
                id="<?php echo $data['Id'];?>"
                class="<?php echo $data['class'];?>"
                width="<?php echo $data['width']; ?>"
                height="<?php echo $data['height']; ?>"
                frameborder="0"
                webkitallowfullscreen
                mozallowfullscreen
                allowfullscreen></iframe>
<?php
    }

    /**
     * Image (image)
     * @param $atts
     * @param $content
     * @param $tag
     */
    public static function render_image( $atts, $content, $tag ) {
        $atts = shortcode_atts( array(
            'data' => '{}'
        ), $atts, $tag );

        $data = json_decode( base64_decode( $atts['data'] ),true );
        if( !is_array( $data ) ) {
            $data = json_decode(stripslashes(urldecode($atts['data'])),true);
        }
        ?>
        <img src="<?php echo $data['src'];?>" width="<?php echo $data['width']?$data['width'] : '';?>" height="<?php echo $data['height']?$data['height']:'';?>"
             class="<?php echo $data['class'];?> <?php echo $data['responsive'] == 'yes' ? 'img-responsive':''; ?>"
             id="<?php echo $data['Id'];?>"
             alt="">
<?php
        /*Image shortcode definition goes here*/
    }

    /**
     * Scheduler (scheduler)
     * @param $atts
     * @param $content
     * @param $tag
     */
    public static function render_scheduler( $atts, $content, $tag ) {
        $atts = shortcode_atts( array(
            'data' => '{}'
        ), $atts, $tag );

        $data = json_decode( base64_decode( $atts['data'] ),true );
        if( !is_array( $data ) ) {
            $data = json_decode(stripslashes(urldecode($atts['data'])),true);
        }

        $time = time();
        $viewable = 0;

        foreach ( $data['timespans'] as $key => $timespan ) {
            if( $time >= strtotime($timespan['from'] ) && $time <= strtotime($timespan['to']) ) {
                $viewable = 1;
                break;
            }
        }

        if( $viewable == 1 ) {
            echo $data['content'];
        } else {
            echo $data['alternative_text'];
        }

        /*Image shortcode definition goes here*/
    }


    public static function render_post_loop( $atts, $content, $tag )
    {

        $atts = shortcode_atts(array(
            'data' => '{}'
        ), $atts, $tag);

        $data = json_decode( base64_decode( $atts['data'] ),true );
        if( !is_array( $data ) ) {
            $data = json_decode(stripslashes(urldecode($atts['data'])),true);
        }


        $args = array();

        if ($data['category__in']) {
            $args['category__in'] = $data['category__in'];
        }

        if ($data['author']) {
            $args['author'] = $data['author'];
        }


        if ($data['posts_per_page']) {
            $args['posts_per_page'] = $data['posts_per_page'];
        }

        if ($data['orderby']) {
            $args['orderby'] = $data['orderby'];
        }

        /*if( $data['post_type'] ) {
            $args['post_type'] = $data['post_type'];
        }*/

        if ($data['post_status']) {
            $args['post_status'] = $data['post_status'];
        }

        if ($data['tag']) {
            $args['tag'] = $data['tag'];
        }

        if ($data['order']) {
            $args['order'] = $data['order'];
        }

        if ($data['nopaging']) {
            $args['nopaging'] = $data['nopaging'];
        }

        $the_query = new WP_Query($args);


        // run the loop based on the query
        if ($the_query->have_posts()) { ?>
            <div id="<?php echo $data['Id']; ?>" class="sm_post_listing <?php echo $data['class']; ?>">
            <?php while ($the_query->have_posts()) : $the_query->the_post(); ?>
                <div class="sm_featured_img">
                    <?php the_post_thumbnail();?>
                </div>
                <div class="sm_title"><h2><?php the_title(); ?></h2></div>
                <div class="sm_excerpt">
                    <?php the_excerpt();?>
                </div>
                <?php
            endwhile;
            ?>
            </div><!--sm_post_listing-->
            <?php
            $postContent = ob_get_clean();
            return $postContent;

        }
    }


    /**
     * render page list
     * @param $atts
     * @param $content
     * @param $tag
     * @return string
     */
    public static function render_page_loop( $atts, $content, $tag )
    {

        $atts = shortcode_atts(array(
            'data' => '{}'
        ), $atts, $tag);

        $data = json_decode( base64_decode( $atts['data'] ),true );
        if( !is_array( $data ) ) {
            $data = json_decode(stripslashes(urldecode($atts['data'])),true);
        }


        $args = array();


        if ($data['posts_per_page']) {
            $args['posts_per_page'] = $data['posts_per_page'];
        }

        if ($data['orderby']) {
            $args['orderby'] = $data['orderby'];
        }

        /*if( $data['post_type'] ) {
            $args['post_type'] = $data['post_type'];
        }*/

        if ($data['post_status']) {
            $args['post_status'] = $data['post_status'];
        }

        if ($data['order']) {
            $args['order'] = $data['order'];
        }

        if ($data['nopaging']) {
            $args['nopaging'] = $data['nopaging'];
        }

        $args['post_type'] = 'page';

        $the_query = new WP_Query($args);


        // run the loop based on the query
        if ($the_query->have_posts()) { ?>
            <div id="<?php echo $data['Id']; ?>" class="sm_post_listing <?php echo $data['class']; ?>">
                <?php while ($the_query->have_posts()) : $the_query->the_post(); ?>
                    <div class="sm_title"><h2><?php the_title(); ?></h2></div>
                    <div class="sm_excerpt">
                        <?php the_excerpt();?>
                    </div>
                    <?php
                endwhile;
                ?>
            </div><!--sm_post_listing-->
            <?php
            $postContent = ob_get_clean();
            return $postContent;

        }
    }


    /**
     * Post meta
     *
     * @param $atts
     * @param $content
     * @param $tag
     */
    public static function render_post_meta( $atts, $content, $tag ) {

        global $post;

        $atts = shortcode_atts( array(
            'data' => '{}'
        ), $atts, $tag );

        $data = json_decode( base64_decode( $atts['data'] ),true );
        if( !is_array( $data ) ) {
            $data = json_decode(stripslashes(urldecode($atts['data'])),true);
        }

        $value = '';

        if( $data['key'] ) {
            if( $data['id'] ) {
                $value = get_post_meta($data['id'],$data['key'], true );
            } else {
                if ( isset( $post->ID ) ) {
                    $value = get_post_meta( $post->ID, $data['key'], true );
                }
            }
        }

        echo '<span class="'.$data['class'].'" id="'.$data['Id'].'">';
        if( $value ) {
            echo $value;
        } else {
            echo $data['default_value'];
        }
        echo '</span>';

    }

    /**
     * Render option
     * @param $atts
     * @param $content
     * @param $tag
     */
    public static function render_option( $atts, $content, $tag ) {

        $atts = shortcode_atts( array(
            'data' => '{}'
        ), $atts, $tag );

        $data = json_decode( base64_decode( $atts['data'] ),true );
        if( !is_array( $data ) ) {
            $data = json_decode(stripslashes(urldecode($atts['data'])),true);
        }

        echo '<span class="'.$data['class'].'" id="'.$data['Id'].'">';
        echo get_option( $data['name'],$data['value']);
        echo '</span>';
    }

    /**
     * Category list
     * @param $atts
     * @param $content
     * @param $tag
     */
    public static function render_category_list( $atts, $content, $tag ) {

        $atts = shortcode_atts( array(
            'data' => '{}'
        ), $atts, $tag );

        $data = json_decode( base64_decode( $atts['data'] ),true );
        if( !is_array( $data ) ) {
            $data = json_decode(stripslashes(urldecode($atts['data'])),true);
        }

        $args = array();

        if( $data['parent_id'] ) {
            $args['child_of'] = $data['parent_id'];
        }

        if( $data['exclude'] ) {
            $args['exclude'] = $data['exclude'];
        }

        $args['title_li'] = $data['title_li'];
        $args['hide_empty'] = $data['hide_empty'];
        $args['hierarchical'] = $data['hierarchical'];
        $args['order'] = $data['order'];
        $args['separator'] = $data['separator'];
        $args['show_count'] = $data['show_count'];
        $args['show_option_all'] = $data['show_option_all'];
        $args['show_option_none'] = $data['show_option_none'];

        wp_list_categories($args);
    }

    public static function render_menu( $atts, $content, $tag ) {

        $atts = shortcode_atts( array(
            'data' => '{}'
        ), $atts, $tag );

        $data = json_decode( base64_decode( $atts['data'] ),true );
        if( !is_array( $data ) ) {
            $data = json_decode(stripslashes(urldecode($atts['data'])),true);
        }

        ?>
        <div class="<?php echo $data['class']; ?>" id="<?php echo $data['Id']; ?>">
            <?php wp_nav_menu( array(
                'menu' => $data['name']
            ) ); ?>
        </div>
<?php

    }

    public static function __callStatic ($method, $args) {
        do_action( 'smps_reder_shortcode', $method, $args);
        return false;
    }
}

