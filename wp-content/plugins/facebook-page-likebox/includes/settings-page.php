<?php
/**
 * @author http://slakify.com
 * Plugin: Facebook Page Plugin
 */
?>

    <div class="wrap">

        <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
            <input type="hidden" name="info_update" id="info_update" value="true"/>

            <?php wp_nonce_field('fmz-update-setting','fmz-update-setting'); ?>

            <u><h2>Facebook Page Plugin by Slakify.com</h2></u>

            <div id="poststuff" class="metabox-holder has-right-sidebar">


                <div style="float:left;width:70%;">

                    <br>

                    <?php
                    require_once 'setting-page/crunchlr-left-column.php';
                    require_once 'setting-page/crunchlr-right-column.php';
                    require_once 'setting-page/crunchlr-footer.php';

                    ?>
