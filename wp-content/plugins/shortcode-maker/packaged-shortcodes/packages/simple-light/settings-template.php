<template id="smps_simple_light_tabs_settings">
    <form class="shortcode_settings_form">
        <div class="bs-container">
            <div class="form-group">
                <select v-model="type" class="form-control">
                    <option v-for="(name,label) in types" :value="name">{{ label }}</option>
                </select>
            </div>
            <div class="mb10">
                <a class="btn btn-default" href="javascript:" @click="add_tab()"><?php _e( 'Add Tab', 'sm' ); ?></a>
            </div>
            <!-- Nav tabs -->
            <ul class="nav nav-{{ type }}">
                <li v-for="(tab_key, tab_object) in tab_data">
                    <a href="#{{ tab_key }}" data-toggle="tab">
                        <template v-if="tab_target != tab_key">
                            {{ tab_object.title }}
                            <a href="javascript:" class="btn btn-xs" @click="tab_target = tab_key"><i class="fa fa-edit"></i></a>
                            <a href="javascript:" class="btn btn-xs br0" @click="remove_tab(tab_key)"><i class="fa fa-remove"></i></a>
                        </template>
                        <input type="text" v-model="tab_object.title" v-if="tab_target == tab_key">
                        <a href="javascript:" class="btn br0 btn-xs" v-if="tab_target == tab_key" @click="tab_target = ''"><strong><?php _e( 'Save', 'sm' ); ?></strong></a>
                    </a>
                </li>
                <li><a href="javascript:" @click="add_tab()"><i class="fa fa-plus"></i></a></li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content mt20 mb20">
                <div v-for="(tab_key, tab_object) in tab_data" class="tab-pane fade" :id="tab_key">
                    <template v-if="content_target != tab_key">
                        {{ tab_object.content }}
                        <a href="javascript:" class="btn pull-right btn-default" @click="content_target = tab_key"><i class="fa fa-edit"></i></a>
                    </template>
                    <textarea class="form-control" v-model="tab_object.content" cols="30" rows="10" v-if="content_target == tab_key"></textarea>
                    <a href="javascript:" class="btn btn-default br3 mt20" v-if="content_target == tab_key" @click="content_target = ''"><?php _e( 'Save', 'sm' ); ?></a>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-primary" data-dismiss="modal" @click="insert_shortcode()"> <?php _e('Insert','sm'); ?></button>
    </form>
</template>
<template id="smps_simple_light_accordion_settings">
    <form class="shortcode_settings_form">
        <div class="bs-container mb10">
            <div class="mb10">
                <a class="btn btn-default" href="javascript:" @click="add_item()"><?php _e( 'Add Item', 'sm' ); ?></a>
            </div>
            <div class="panel-group" id="accordion">
                <div class="panel panel-default"  v-for="(key, each_acc) in acc_data">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <template v-if="target_acc != key">
                                <a data-toggle="collapse" data-parent="#accordion" href="#{{ key }}">{{ each_acc.title }}</a>
                                <a href="javascript:" class="btn btn-xs btn-default br0 " @click="target_acc = key"><i class="fa fa-edit"></i></a>
                                <a href="javascript:" class="btn btn-xs btn-default br0 " @click="remove_accordion(key)"><i class="fa fa-remove"></i></a>
                            </template>
                            <input type="text" v-model="each_acc.title" v-if="target_acc == key" class="form-control">
                            <a href="javascript:" class="btn btn-default br3 mt10" v-if="target_acc == key" @click="target_acc = ''"><?php _e( 'Save', 'sm' ); ?></a>
                        </h4>
                    </div>
                    <div :id="key" class="panel-collapse collapse">
                        <div class="panel-body" @dblclick="target_content = key">
                            <template v-if="target_content != key">
                                {{ each_acc.content }}
                                <a href="javascript:" class="btn btn-default br3 mt10 pull-right" @click="target_content = key"><i class="fa fa-edit"></i></a>
                            </template>
                            <textarea v-model="each_acc.content" cols="30" rows="10" class="form-control" v-if="target_content == key"></textarea>

                            <a href="javascript:" class="btn btn-default br3 mt10" v-if="target_content == key" @click="target_content = ''"><?php _e( 'Save', 'sm' ); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-primary" data-dismiss="modal" @click="insert_shortcode()"> <?php _e('Insert','sm'); ?></button>
    </form>
</template>
<template id="smps_simple_light_table_settings">
    <form class="shortcode_settings_form">
        <div class="mb10">
            <a class="btn btn-default" href="javascript:" @click="add_row()"><?php _e( 'Add Row', 'sm' ); ?></a>
            <a class="btn btn-default" href="javascript:" @click="add_col()"><?php _e( 'Add Column', 'sm' ); ?></a>
        </div>
        <div class="form-group">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <tr>
                        <td v-for="col_number in col_tracker">
                            <a href="javascript:" class="btn btn-xs btn-default br3 pull-right" @click="remove_col(col_number)"><i class="fa fa-remove"></i></a>
                        </td>
                    </tr>
                    <tr v-for="( t_key, t_val ) in table_data">
                        <td v-for="( c_key, c_val) in t_val ">
                            <input type="text" class="form-control" v-model="c_val">
                            <!--<a href="javascript:" class="btn btn-danger br0" @click="remove_td(t_key, c_key)"><i class="glyphicon glyphicon-minus"></i></a>-->
                        </td>
                        <td><a href="javascript:" class="btn btn-default pull-right btn-xs" @click="remove_row(t_key)" data-val="{{ t_key }}"><i class="fa fa-remove"></i></a></td>
                    </tr>
                </table>
            </div>
        </div>
        <button type="button" class="btn btn-primary" data-dismiss="modal" @click="insert_shortcode()"> <?php _e('Insert','sm'); ?></button>
    </form>
</template>
<!--panel-->
<template id="smps_simple_light_panel_settings">
    <form class="shortcode_settings_form">
        <div class="form-group">
            <label><?php _e('Type','sm'); ?></label>
            <select v-model="type" class="form-control">
                <option v-for="(name,label) in types" :value="name">{{ label }}</option>
            </select>
        </div>
        <div class="form-group">
            <label><?php _e('Title','sm'); ?></label>
            <input type="text" v-model="header" class="form-control">
        </div>
        <div class="form-group">
            <label><?php _e('Title Alignment','sm'); ?></label>
            <select v-model="header_alignment" class="form-control">
                <option v-for="(name,label) in header_alignments" :value="name">{{ label }}</option>
            </select>
        </div>
        <div class="form-group">
            <label><?php _e('Content','sm'); ?></label>
            <textarea v-model="body" cols="30" rows="10" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label><?php _e('Footer','sm'); ?></label>
            <input type="text" v-model="footer" class="form-control">
        </div>
        <div class="form-group">
            <label><?php _e('Footer Alignment','sm'); ?></label>
            <select v-model="footer_alignment" class="form-control">
                <option v-for="(name,label) in footer_alignments" :value="name">{{ label }}</option>
            </select>
        </div>
        <button type="button" class="btn btn-primary" data-dismiss="modal" @click="insert_shortcode()"> <?php _e('Insert','sm'); ?></button>
    </form>
</template>
<template id="smps_simple_light_alert_settings">
    <form class="shortcode_settings_form">
        <div class="form-group">
            <label><?php _e('Type','sm'); ?></label>
            <select v-model="type" class="form-control">
                <option v-for="(name,label) in types" :value="name">{{ label }}</option>
            </select>
        </div>
        <div class="form-group">
            <label><?php _e('Text','sm'); ?></label>
            <textarea v-model="content" cols="30" rows="10" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label><input type="checkbox" v-model="dismissable" > <?php _e('Dismissable','sm'); ?></label>
        </div>
        <button type="button" class="btn btn-primary" data-dismiss="modal" @click="insert_shortcode()"> <?php _e('Insert','sm'); ?></button>
    </form>
</template>
<template id="smps_simple_light_heading_settings">
    <form class="shortcode_settings_form">
        <div class="form-group">
            <label><?php _e('Text Align','sm'); ?></label>
            <select v-model="text_align" class="form-control">
                <option v-for="(name,label) in text_aligns" :value="name">{{ label }}</option>
            </select>
        </div>
        <div class="form-group">
            <label><?php _e('Heading','sm'); ?></label>
            <textarea v-model="text" cols="30" rows="10" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label><?php _e('Type','sm'); ?></label>
            <select v-model="type" class="form-control">
                <option v-for="(name,label) in types" :value="name">{{ label }}</option>
            </select>
        </div>
        <button type="button" class="btn btn-primary" data-dismiss="modal" @click="insert_shortcode()"> <?php _e('Insert','sm'); ?></button>
    </form>
</template>
<template id="smps_simple_light_quote_settings">
    <form class="shortcode_settings_form">
        <div class="form-group">
            <label><?php _e('Text Align','sm'); ?></label>
            <select v-model="alignment" class="form-control">
                <option v-for="(name,label) in alignments" :value="name">{{ label }}</option>
            </select>
        </div>
        <div class="form-group">
            <label><?php _e('Quote','sm'); ?></label>
            <textarea v-model="quote" cols="30" rows="10" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label><?php _e('Author','sm'); ?></label>
            <input type="text" v-model="author" class="form-control">
        </div>
        <button type="button" class="btn btn-primary" data-dismiss="modal" @click="insert_shortcode()"> <?php _e('Insert','sm'); ?></button>
    </form>
</template>
<template id="smps_simple_light_button_settings">
    <?php
    $pages = get_posts(array('post_type' => 'page'));
    ?>
    <form class="shortcode_settings_form">
        <!--types-->
        <div class="form-group">
            <label><?php _e( 'Type', 'sm' ); ?></label>
            <select v-model="type" class="form-control">
                <option v-for="(name,label) in types" :value="name">{{ label }}</option>
            </select>
        </div>
        <div class="form-group">
            <label><?php _e( 'Shape', 'sm' ); ?></label>
            <select v-model="shape" class="form-control">
                <option v-for="(name,label) in shapes" :value="name">{{ label }}</option>
            </select>
        </div>
        <!--size-->
        <div class="form-group">
            <label><?php _e( 'Size', 'sm' ); ?></label>
            <select v-model="size" class="form-control">
                <option v-for="(name,label) in sizes" :value="name">{{ label }}</option>
            </select>
        </div>
        <!--text-->
        <div class="form-group">
            <label><input type="checkbox" v-model="enable_text"> <?php _e( 'Enable Text', 'sm' ); ?></label>
        </div>
        <div class="form-group" v-if="enable_text">
            <input type="text" v-model="text" class="form-control">
        </div>
        <!--icon-->
        <div class="form-group">
            <label><input type="checkbox" v-model="enable_icon"> <?php _e( 'Enable Icon', 'sm' ); ?></label>
        </div>
        <div class="form-group" v-if="enable_icon">
            <input type="text" v-model="icon" class="form-control">
        </div>
        <!--redirection-->
        <div class="form-group">
            <label><?php _e( 'Redirect to', 'sm' ); ?></label>
            <select v-model="redirection_type" class="form-control">
                <option v-for="(name,label) in redirection_types" :value="name">{{ label }}</option>
            </select>
        </div>
        <!--if redirection = page-->
        <div class="form-group" v-if="redirection_type == 'to_page'">
            <label><?php _e( 'Select redirection page', 'sm' ); ?></label>
            <select v-model="page" class="form-control">
                <?php foreach ( $pages as $page ) :?>
                <option value="<?php echo $page->ID; ?>"><?php echo get_the_title($page->ID); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <!--if redirection = url -->
        <div class="form-group" v-if="redirection_type == 'url'">
            <label><?php _e( 'Redirect URL', 'sm' ); ?></label>
            <input type="text" v-model="url" class="form-control">
        </div>
        <!--open in newtab-->
        <div class="form-group">
            <label><input type="checkbox" v-model="open_newtab"> <?php _e( 'Open in New Tab', 'sm' ); ?></label>
        </div>
        <button type="button" class="btn btn-primary" data-dismiss="modal" @click="insert_shortcode()"> <?php _e('Insert','sm'); ?></button>
    </form>
</template>