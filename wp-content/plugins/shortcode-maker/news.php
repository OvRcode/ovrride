<?php
class CC_News {
    /**
     * @var Singleton The reference the *Singleton* instance of this class
     */
    private static $instance;

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        add_action( 'admin_menu' , array( $this, 'add_news_page' ) );
        add_action( 'admin_print_scripts' , array( $this, 'admin_print_script_style' ) );
        add_action( 'admin_notices' , array( $this, 'admin_news_notice' ) );
    }

    public function news_html($response,$loop = 0) {
        ?>
        <div class="cc_news_notice_container">
            <div class="thumbnail">

                <?php if( isset( $response['posts'][$loop]['thumbnail'] ) && $response['posts'][$loop]['thumbnail'] ) {
                    ?>
                    <img src="<?php echo $response['posts'][$loop]['thumbnail']; ?>" alt="" width="200">
                    <?php
                }
                ?>
            </div>
            <div class="news_content">
                <h3><?php _e( '<a href="'.$response['posts'][$loop]['url'].'" target="_blank" style="text-decoration:none;color:#444444;">'.$response['posts'][$loop]['title'].'</a>', 'cc' ); ?></h3>
                <div>
                    <?php echo $response['posts'][$loop]['excerpt']; ?>
                </div>
                <a href="<?php echo $response['posts'][$loop]['url']; ?>" target="_blank"><?php _e( 'Read More', 'cc' ); ?></a>
            </div>
        </div>
        <?php
    }


    /**
     * News notice
     */
    public function admin_news_notice() {
        global $pagenow;
        if( !in_array($pagenow, array('post.php','edit.php')) ) return;

        $notices = sm_get_notice('sm_admin_notices' );

        $response = wp_remote_get( 'http://blog.cybercraftit.com/api/get_category_posts?slug=product-feed&count=1' );
        if( is_wp_error($response) ) return;
        $response = $response['body'];
        $response = json_decode($response,true);
        $new_lastest_date = 1;

        if ( !empty( $response['posts'] ) ) {
            $new_lastest_date = strtotime($response['posts'][0]['date']);
        }

        if( $new_lastest_date && $response['count'] ) {
            if( !isset( $notices['news_notice']['is_dismissed'] ) || !$notices['news_notice']['is_dismissed'] ) {
                ?>
                <div class="notice notice-success is-dismissible cc_news_notice">
                    <input type="hidden" value="<?php echo $new_lastest_date; ?>" name="cc_last_news_date">
                    <?php $this->news_html($response,0); ?>
                </div>
                <?php
            } elseif( isset( $notices['news_notice']['is_dismissed'] )
                && isset( $notices['news_notice']['last_news_date'] )
                && $notices['news_notice']['last_news_date'] < $new_lastest_date
            ) {

                ?>
                <div class="notice notice-success is-dismissible cc_news_notice">
                    <input type="hidden" value="<?php echo $new_lastest_date; ?>" name="cc_last_news_date">
                    <?php $this->news_html($response,0); ?>
                </div>
                <?php
            }
        }
    }

    public function add_news_page() {
        add_submenu_page ( 'edit.php?post_type=sm_shortcode', 'News', 'News', 'manage_options', __FILE__, array( $this, 'generate_news_page_content' ) );
    }

    public function generate_news_page_content() {
        $response = wp_remote_get( 'http://blog.cybercraftit.com/api/get_category_posts?slug=news&count=10' );
        $response = $response['body'];
        $response = json_decode($response,true);

        if( $response['status'] == 'ok' ) {

            if( isset($response['posts'] ) ) {
                $contents = $response['posts'];
                ?>
                <div class="cc_news_container">
                    <h1>
                        <?php _e('Latest News','cc'); ?>
                    </h1>
                    <?php foreach ( $contents as $k => $content ) { ?>
                    <div class="each_container">
                        <?php $this->news_html($response,$k); ?>
                    </div>
                        <a class="read_more_news" href="http://blog.cybercraftit.com/category/news/" target="_blank"><?php _e('Read More', 'cc'); ?></a>
                <?php } ?>
                </div>
                <?php
            }
        }
    }

    public function admin_print_script_style() {
        ?>
        <style>
            .cc_news_container .read_more_news{
                display: block;
                padding: 10px;
                text-align: center;
                text-decoration: none;
                color: #000;
                background: #ffffff;
                border: 1px solid #dddddd;
            }
            .each_container{
                background: #ffffff;
                margin-bottom: 10px;
            }
            .cc_news_notice_container{
                overflow: hidden;
                padding:10px;
            }
            .cc_news_notice_container .thumbnail{
                float: left;
            }
            .cc_news_notice_container .news_content{
                overflow: hidden;
                margin-left: 215px;
            }
        </style>
        <script>
            window.onload = function () {
                (function ($) {
                    $(document).on('click','.cc_news_notice .notice-dismiss',function () {
                        $.post(
                            ajaxurl,
                            {
                                action: 'sm_dissmiss_news_notice',
                                dismiss: true,
                                last_news_date: $(':hidden[name="cc_last_news_date"]').val()
                            },
                            function (data) {
                                console.log(data);
                            }
                        )
                    })
                }(jQuery));
            }

        </script>
<?php
    }
}

CC_News::get_instance();

