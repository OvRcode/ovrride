<?php

class SM_Vote {

    public function __construct() {
        add_action( 'load-plugins.php', array( __CLASS__, 'vote_init' ) );
        add_action( 'wp_ajax_sm_vote',  array( __CLASS__, 'vote' ) );
    }

    public static function vote_init() {
        /*$sm_votes = get_option( 'sm_vote' );
        !is_array($sm_votes) ? $sm_votes = array() : '';*/

        //if ( in_array( $vote, array( 'yes', 'tweet' , 'facebook', 'suggest', 'no' ) ) || !$timein ) return;
        add_action( 'admin_notices', array( __CLASS__, 'message' ) );
        add_action( 'admin_head',      array( __CLASS__, 'register' ) );
        add_action( 'admin_footer',    array( __CLASS__, 'enqueue' ) );
    }

    public static function register() {
        wp_register_style( 'sm-vote', SHORTCODE_MAKER_ASSET_PATH.'/css/vote.css', false );
        wp_register_script( 'sm-vote', SHORTCODE_MAKER_ASSET_PATH.'/js/vote.js', array( 'jquery' ), false, true );
    }

    public static function enqueue() {
        wp_enqueue_style( 'sm-vote' );
        wp_enqueue_script( 'sm-vote' );
    }

    public static function message() {
        $timein = time() > ( get_option( 'sm_later' ) );
        if ( !$timein ) return;

        $sm_votes = get_option( 'sm_vote' );
        !is_array($sm_votes) ? $sm_votes = array() : '';

        if( isset( $sm_votes['no'] ) ){
            return;
        } else {

            $btn_str = '';
            if( !isset( $sm_votes['yes'] ) ) {
                $btn_str .= '<a href="'.admin_url( 'admin-ajax.php' ).'?action=sm_vote&amp;vote=yes" class="sm-vote-action sm-vote-button button button-small button-primary" data-action="http://wordpress.org/support/view/plugin-reviews/shortcode-maker?rate=5#postform">'.__( 'Rate us', 'sm' ).'</a>';
            }
            if( !isset( $sm_votes['tweet'] ) ) {
                $btn_str .= '<a href="'.admin_url( 'admin-ajax.php' ).'?action=sm_vote&amp;vote=tweet" class="sm-vote-action sm-vote-button button button-small" data-action="http://twitter.com/share?url=http://bit.ly/2mXDU5t&amp;text='.urlencode( __( 'Shortcode Maker - must have WordPress plugin #shortcodemaker', 'sm' ) ).'">'.__( 'Tweet', 'sm' ).'</a>';
            }
            if( !isset( $sm_votes['facebook'] ) ) {
                $btn_str .= '<a href="'.admin_url( 'admin-ajax.php' ).'?action=sm_vote&amp;vote=facebook" class="sm-vote-action sm-vote-button button button-small" data-action="http://facebook.com/sharer?u=http://bit.ly/2mXDU5t&amp;text='.urlencode( __( 'Shortcode Maker - must have WordPress plugin #shortcodemaker', 'sm' ) ).'">'.__( 'Share on facebook', 'sm' ).'</a>';
            }
            if( !isset( $sm_votes['no'] ) ) {
                $btn_str .= '<a href="'.admin_url( 'admin-ajax.php' ).'?action=sm_vote&amp;vote=no" class="sm-vote-action sm-vote-button sm-cancel-button button button-small">'.__( 'No, thanks', 'sm' ).'</a>';
            }

            $btn_str .= '<a href="'.admin_url( 'admin-ajax.php' ).'?action=sm_vote&amp;vote=suggest" class="sm-vote-action sm-vote-button sugget-button button button-small" data-action="http://cybercraftit.com/contact/">'.__( 'Suggest us', 'sm' ).'</a>';

            if( !isset( $sm_votes['later'] ) ) {
                $btn_str .= '<a href="'.admin_url( 'admin-ajax.php' ).'?action=sm_vote&amp;vote=later" class="sm-vote-action sm-vote-button button button-small">'.__( 'Remind me later', 'sm' ).'</a>';
            }

        }



        if( !empty( $btn_str ) ) :
        ?>
        <div class="sm-vote">
            <div class="sm-vote-wrap">
                <div class="sm-vote-gravatar">
                    <a href="http://cybercraftit.com/" target="_blank"><img src="http://2.gravatar.com/avatar/b81a0fdd8fafcb4148aa8c5b41e56431?s=64&d=mm&r=g" alt="<?php _e( 'Mithu A Quayium', 'sm' ); ?>" width="50" height="50"></a>
                </div>
                <div class="sm-vote-message">
                    <p><?php _e( '<h3>We Need Your Support</h3>Thanks for using <strong>Shortcode Maker</strong>.<br>If you find this plugin useful, please rate us, share and tweet to let 
others know about it, and help us improving it by your valuable suggestions .<br><b>Thank you!</b>', 'sm' ); ?></p>
                    <p>
                        <?php echo $btn_str; ?>
                    </p>
                </div>
                <div class="sm-vote-clear"></div>
            </div>
        </div>
<?php
endif;
    }

    public static function vote() {
        $vote = sanitize_key( $_GET['vote'] );

        if ( !is_user_logged_in() || !in_array( $vote, array( 'yes', 'tweet' , 'facebook', 'no', 'suggest', 'later'  ) ) ) die( 'error' );

        $sm_votes = get_option( 'sm_vote' );
        !is_array($sm_votes)?$sm_votes = array() : '';
        $sm_votes[$vote] = $vote;
        update_option( 'sm_vote', $sm_votes );

        if ( $vote === 'later' ) update_option( 'sm_later', time() + 60*60*24*3 );
        die( 'OK: ' . $vote );
    }
}

new SM_Vote();