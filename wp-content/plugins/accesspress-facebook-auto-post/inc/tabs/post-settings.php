<h4 class="asap-network-title"><?php _e('Post Settings', 'accesspress-facebook-auto-post'); ?></h4>
<div class="asap-network-inner-wrap">
    <div class="asap-network-field-wrap">
        <label><?php _e('Enable Auto Publish For:', 'accesspress-facebook-auto-post'); ?></label>
        <div class="asap-network-field">
            <?php
            $post_types = $this->get_registered_post_types();
            foreach ($post_types as $post_type) {
                $post_type_obj = get_post_type_object($post_type);
                ?>
                <label class="asap-full-width"><input type="checkbox" name="account_details[post_types][]" value="<?php echo $post_type; ?>" <?php echo (isset($account_details['post_types']) && is_array($account_details['post_types']) && in_array($post_type, $account_details['post_types'])) ? 'checked="checked"' : ''; ?>/><?php echo $post_type_obj->labels->name; ?></label>
            <?php }
            ?>
        </div>
    </div>

    <div class="asap-network-field-wrap">
        <label><?php _e('Categories for Auto Post', 'accesspress-facebook-auto-post'); ?></label>
        <div class="asap-network-field">
            <select name="account_details[category][]" multiple="multiple">
                <?php $category = isset($account_details['category']) ? $account_details['category'] : array(); ?>
                <option value="all" <?php echo (in_array('all', $category)) ? 'selected="selected"' : ''; ?>><?php _e('All', 'accesspress-facebook-auto-post'); ?></option>
                <?php
                $taxonomies = get_taxonomies();
                unset($taxonomies['nav_menu']);
                unset($taxonomies['post_format']);
                //$this->print_array($taxonomies);
                foreach ($taxonomies as $taxonomy) {
                    $taxonomy_obj = get_taxonomy($taxonomy);

                    $terms = get_terms($taxonomy, array('hide_empty' => 0));
                    if (count($terms) > 0) {
                        ?>
                        <optgroup label="<?php echo $taxonomy_obj->label; ?>">
                            <?php
                            foreach ($terms as $term) {
                                ?>
                                <option value="<?php echo $term->term_id; ?>" <?php echo (in_array($term->term_id, $category)) ? 'selected="selected"' : ''; ?>><?php echo $term->name; ?></option>
                                <?php
                            }
                            ?>
                        </optgroup>
                        <?php
                    }
                }
                ?>
            </select>
            <div class="asap-field-note">
                <?php _e('Note:Please use command or control key to select multiple options.Not selecting any of the option will be considered as <strong>All</strong> selected.', 'accesspress-facebook-auto-post'); ?>
            </div>
        </div>
    </div>
</div>
<div class="asap-network-field-wrap">
    <div class="asap-network-field">
        <input type="submit" name="edit_submit" value="<?php _e('Save Settings', 'accesspress-facebook-auto-post'); ?>"/>
        <a href="<?php echo admin_url('admin-post.php?action=afap_restore_settings'); ?>" onclick="return confirm('<?php _e('Are you sure you want to restore the default settings?','accesspress-facebook-auto-post');?>')"><input type="button" value="<?php _e('Restore Default Settings', 'accesspress-facebook-auto-post'); ?>"/></a>
    </div>
</div>