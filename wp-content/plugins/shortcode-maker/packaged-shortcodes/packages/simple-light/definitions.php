<?php
$items = Smps_Simple_Light::settings()['items'];
foreach ( $items as $item => $label ) {
    add_shortcode( 'smps_sl_'.$item, array( 'Smps_Simple_Light_Shortcodes', 'render_'.$item ) );
}

class Smps_Simple_Light_Shortcodes {

    public static function render_tabs( $atts, $content, $tag ) {

        if( isset( $atts['tab_data'] ) ) {
            $atts['tab_data'] = json_decode(stripslashes($atts['tab_data']),true);
        }


        $atts = shortcode_atts( array(
            'type' => 'tabs', //tabs ,pills
            'tab_data' => array(
                'tab1' => array(
                    'title' => 'Tab 1 Title',
                    'content' => 'Tab 1 content'
                ),
                'tab2' => array(
                    'title' => 'Tab 2 Title',
                    'content' => 'Tab 2 content'
                ),
                'tab3' => array(
                    'title' => 'Tab 3 Title',
                    'content' => 'Tab 3 content'
                )
            )
        ), $atts, $tag );
        ?>
        <div class="bs-container">
            <!-- Nav tabs -->
            <ul class="nav nav-<?php echo $atts['type']; ?>">
                <?php
                $output = '';
                $i = 0;
                foreach ( $atts['tab_data'] as $tab_key => $tab ) : $i++; ?>
                    <li class="<?php echo $i == 1? 'active':''; ?>"><a href="#<?php echo $tab_key ; ?>" data-toggle="tab"><?php echo $tab['title']; ?></a>
                    </li>
                    <?php
                    ob_start();
                    ?>
                    <div class="tab-pane fade <?php echo $i == 1? 'in active':''; ?>" id="<?php echo $tab_key; ?>">
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

        if( isset( $atts['acc_data'] ) ) {
            $atts['acc_data'] = json_decode(stripslashes($atts['acc_data']),true);
        }

        $atts = shortcode_atts( array(
            'opened_tab' => 1,
            'acc_data' => array(
                'acc1' => array(
                    'title' => 'Accordion 1 Title',
                    'content' => 'Accordion 1 content'
                ),
                'acc2' => array(
                    'title' => 'Accordion 2 Title',
                    'content' => 'Accordion 2 content'
                ),
                'acc3' => array(
                    'title' => 'Accordion 3 Title',
                    'content' => 'Accordion 3 content'
                )
            )
        ), $atts, $tag );
        ?>
        <div class="bs-container">
            <div class="panel-group" id="accordion">
        <?php
        $output = '';
        $i = 0;
        foreach ( $atts['acc_data'] as $acc_key => $accordion ) : $i++; ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#<?php echo $acc_key; ?>"><?php echo $accordion['title']; ?></a>
                    </h4>
                </div>
                <div id="<?php echo $acc_key; ?>" class="panel-collapse collapse <?php echo $i == $atts['opened_tab'] ? 'in' : ''; ?>">
                    <div class="panel-body">
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
        $atts['table_data'] = json_decode(stripslashes($atts['table_data']),true);
        $atts = shortcode_atts( array(
            'table_data' => array(
                'thead' => array( 'Name', 'Email' ),
                'tbody' => array(
                    array( 'John', 'john@doe.com' ),
                    array( 'Doe', 'doe@john.com' ),
                    array( 'Max', 'max@role.com' )
                )
            )
        ), $atts, $tag );
        ?>
        <div class="bs-container">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <!--<thead>
                    <tr>
                        <?php /*foreach ( $atts['table_data']['thead'] as $key => $label ) : */?>
                        <th><?php /*echo $label; */?></th>
                        <?php /*endforeach;*/?>
                    </tr>
                    </thead>-->
                    <tbody>
                    <?php foreach ( $atts['table_data']/*['tbody']*/ as $key => $tr ) : ?>
                        <tr>
                            <?php foreach ( $tr as $k => $td ) : ?>
                                <td><?php echo $td; ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php
    }


    public static function render_panel( $atts, $content, $tag ) {
        $atts = shortcode_atts( array(
            'type' => 'primary', //success,info,warning, danger, default
            'header' => 'Panel Title',
            'header_alignment' => 'left', //center, right
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum tincidunt est vitae ultrices accumsan. Aliquam ornare lacus adipiscing, posuere lectus et, fringilla augue.',
            'footer' => 'Panel Footer',
            'footer_alignment' => 'left', //center, right
        ), $atts, $tag );
        ?>
        <div class="bs-container">
            <div class="panel panel-<?php echo $atts['type']; ?>">
                <?php if( !empty( $atts['header'] ) ) : ?>
                    <div class="panel-heading text-<?php echo $atts['header_alignment']; ?>">
                        <?php echo $atts['header']; ?>
                    </div>
                <?php endif; ?>
                <?php if( !empty( $atts['body'] ) ) : ?>
                    <div class="panel-body">
                        <?php echo nl2br($atts['body']); ?>
                    </div>
                <?php endif; ?>
                <?php if( !empty( $atts['footer'] ) ) : ?>
                    <div class="panel-footer text-<?php echo $atts['footer_alignment']; ?>">
                        <?php echo $atts['footer']; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php
    }

    public static function render_alert( $atts, $content, $tag ) {
        $atts = shortcode_atts( array(
            'type' => 'success', //primary,info,warning, danger, default
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit',
            'dismissable' => true
        ), $atts, $tag );
        ?>
        <div class="bs-container">
            <div class="alert alert-<?php echo $atts['type']; ?> alert-<?php echo $atts['dismissable'] == 'true' ? 'dismissable' : ''; ?>">
                <?php if( $atts['dismissable'] == 'true' ) : ?>
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?php endif; ?>
                <?php echo $atts['content']; ?>
            </div>
        </div>
    <?php
    }


    public static function render_heading( $atts, $content, $tag ) {
        $atts = shortcode_atts( array(
            'text_align' => 'right', //right, left
            'text' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit',
            'type' => 'h2', // h2,h3,h4,h5,h6
        ), $atts, $tag );
        ?>
        <div class="bs-container">
            <<?php echo $atts['type'] ; ?> class="text-<?php echo $atts['text_align'];?>"><?php echo $atts['text'];?></<?php echo $atts['type'];?>>
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
            'alignment' => 'left', //right, left
            'quote' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit',
            'author' => 'John Doe', // h2,h3,h4,h5,h6
        ), $atts, $tag );
        ?>
        <div class="bs-container">
            <blockquote class="pull-<?php echo $atts['alignment'];?>">
                <p><?php echo $atts['quote']; ?></p>
                <?php if( $atts['author'] ) : ?>
                    <small><?php echo $atts['author']; ?>
                        <!--<cite title="Source Title">Source Title</cite>-->
                    </small>
                <?php endif; ?>
            </blockquote>
        </div>
    <?php
    }

    public static function render_button( $atts, $content, $tag ) {
        $atts = shortcode_atts( array(
            'type' => 'default', //right, left
            'size' => '',
            'enable_text' => 'true',
            'text' => 'Click me',
            'enable_icon' => 'false',
            'icon' => '', // h2,h3,h4,h5,h6
            'shape' => 'rounded',
            'redirection_type' => 'same_page',
            'url' => '',
            'page' => '',
            'open_newtab' => 'false'
        ), $atts, $tag );
        ?>
        <div class="bs-container" style="display: inline;">
            <?php
            if( $atts['redirection_type'] == 'url' ) {
                $redirect_to = $atts['url'];
            } else if( $atts['redirection_type'] == 'page' ) {
                $redirect_to = get_page_url( $atts['page'] );
            }
            switch ( $atts['redirection_type'] ) {
                case 'url' :
                    $redirect_to = $atts['url'];
                    break;
                case 'same_page' :
                    $redirect_to = '';
                    break;
                case 'page' :
                    $redirect_to = get_page_url($atts['page']);
                    break;
            }
            ?>
            <a href="<?php echo $redirect_to; ?>" <?php echo $atts['open_newtab'] == 'true' ? 'target="_blank"' : '' ;?> class="btn btn-<?php echo $atts['type']; ?> btn-<?php echo $atts['size']; ?> <?php echo $atts['shape'] == 'normal' ? 'br0' : ''; ?>">
                <?php
                if( $atts['icon'] == 'true' ) :
                    ?>
                    <i class="glyphicon glyphicon-<?php echo $atts['icon']; ?>"></i>
                    <?php
                    endif;
                ?>
                <?php echo $atts['enable_text'] == 'true' ? $atts['text'] : ''; ?>
            </a>
        </div>
        <?php
    }

    public static function __callStatic ($method, $args) {
        do_action( 'smps_reder_shortcode', $method, $args);
        return false;
    }
}
