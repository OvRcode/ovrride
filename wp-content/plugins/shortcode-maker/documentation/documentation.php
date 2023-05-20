<div class="bs-container">
    <div class="modal fade" id="sm_doc_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"><?php _e( 'How to', 'sm' ); ?></h4>
                </div>
                <div class="modal-body" style="overflow-y: scroll;height: 350px;">
                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingOne">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        <?php _e( 'Packaged Shortcode', 'sm' ); ?>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                                <div class="panel-body">
                                    Now , you can have numerous shortcodes in packaged shortcode panel.
                                    Just click on the shortcode button from the panel.

                                    <img width="100%" src="<?php echo plugins_url('assets/images/1.png',__FILE__); ?>" alt="">

                                    A popup will appear to modify the values of shortcode before you insert it to the editor.

                                    <img width="100%" src="<?php echo plugins_url('assets/images/2.png',__FILE__); ?>" alt="">

                                    After the modifying the values as you need, click the insert button and the shortcode will be
                                    inserted into the editor.

                                    <img width="100%" src="<?php echo plugins_url('assets/images/3.png',__FILE__); ?>" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingTwo">
                                <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        <?php _e( 'Edit shortcode that is inserted already with easy interface'); ?>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                <div class="panel-body">
                                    Sometimes you may need to edit the shortcode values that has been inserted already.
                                    With shortcode maker, you can do this quite easily with user interface. No need to modify the values manually.
                                    Rather to edit the shortcode, just select the whole inserted shortcode like the image. This way , you will find the
                                    regarding shorcode button in selected state.

                                    <img width="100%" src="<?php echo plugins_url('assets/images/4.png',__FILE__); ?>" alt="">

                                    Click that button, and the popup will appear with populated inserted shortcode data

                                    <img width="100%" src="<?php echo plugins_url('assets/images/5.png',__FILE__); ?>" alt="">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
