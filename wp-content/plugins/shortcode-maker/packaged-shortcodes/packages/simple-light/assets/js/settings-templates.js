(function ($) {
    $(document).ready(function () {

        /**
         * tabs
         */
        Vue.component('smps_simple_light_tabs_settings',{
            template : '#smps_simple_light_tabs_settings',
            data : function () {
                return {
                    component_name : 'tabs',
                    s : {
                        type : 'tabs',
                        tab_data : {}
                    },
                    tab_target : '',
                    content_target : '',
                    types : {
                        'tabs' : 'Tabs',
                        'pills' : 'Pills'
                    },
                    tab_template : {
                        'title' : 'Tab Label',
                        'content' : 'Tab content'
                    }
                }
            },
            methods : {
                add_tab : function () {
                    Vue.set(this.s.tab_data,'tab_' + new Date().getTime(),JSON.parse(JSON.stringify(this.tab_template)));
                },
                insert_shortcode : function () {
                    sm_object.insert_shortcode( this.s, 'smps_sl_tabs' );
                },
                remove_tab : function ( tab_key ) {
                    Vue.delete( this.s.tab_data, tab_key );
                }
            },
            ready : function () {
                if ( smps_app.edit_target_item == this.component_name ) {
                    this.s = smps_app.edit_target_item_data;
                }
            }
        });

        /**
         * accordion
         */
        Vue.component('smps_simple_light_accordion_settings',{
            template : '#smps_simple_light_accordion_settings',
            data : function () {
                return {
                    component_name : 'accordion',
                    s : {
                        acc_data : {},
                    },
                    target_acc : '',
                    target_content : '',
                    acc_template : {
                        'title' : 'Item Label',
                        'content' : 'Item content'
                    },
                }
            },
            methods : {
                add_item : function () {
                    Vue.set(this.s.acc_data,'acc_' + new Date().getTime(),JSON.parse(JSON.stringify(this.acc_template)));
                },
                insert_shortcode : function () {
                    sm_object.insert_shortcode( this.s, 'smps_sl_accordion' );
                },
                remove_accordion : function (key) {
                    Vue.delete(this.s.acc_data,key);
                }
            },
            ready : function () {
                if ( smps_app.edit_target_item == this.component_name ) {
                    this.s = smps_app.edit_target_item_data;
                }
            }
        });

        /**
         * table
         */
        Vue.component('smps_simple_light_table_settings',{
            template : '#smps_simple_light_table_settings',
            data : function () {
                return {
                    component_name : 'table',
                    s : {
                        table_data : {},
                        col_template : {},
                        col_tracker : []
                    },

                }
            },
            methods : {
                add_col : function () {
                    var col_val = '';/*'td_' + new Date().getTime()*/
                    var col_key = new Date().getTime();
                    Vue.set(this.s.col_template, col_key, col_val );

                    for( var k in this.s.table_data ) {
                        Vue.set( this.s.table_data[k],col_key,col_val);
                    }

                    //col nums
                    this.s.col_tracker.push(this.s.col_tracker.length);
                },
                add_row : function () {
                    Vue.set( this.s.table_data, 'tr_' + new Date().getTime(), JSON.parse( JSON.stringify( this.s.col_template ) ) );
                },
                remove_col : function (col_number) {
                    console.log(col_number);
                    //remove col of table data
                    for( var k in this.s.table_data ) {
                        Vue.delete( this.s.table_data[k], Object.keys(this.s.table_data[k])[col_number] );
                    }
                    //remove col of col template
                    Vue.delete( this.s.col_template, Object.keys(this.s.col_template)[col_number]);
                    //remove from col tracker
                    this.s.col_tracker.splice( -1, 1 );
                },
                remove_td : function ( t_key, c_key ) {
                    Vue.delete( this.s.table_data[t_key],c_key);
                },
                remove_row : function ( t_key ) {
                    Vue.delete( this.s.table_data, t_key );
                    if( !Object.keys(this.s.table_data).length ) {
                        this.s.col_template = {};
                        this.s.col_tracker = [];
                    }
                },
                insert_shortcode : function () {
                    sm_object.insert_shortcode( this.s, 'smps_sl_table' );
                }
            },
            ready : function () {
                this.add_col();
                this.add_row();

                if ( smps_app.edit_target_item == this.component_name ) {
                    this.s = smps_app.edit_target_item_data;
                }
            }
        });

        Vue.component( 'smps_simple_light_panel_settings', {
            template : '#smps_simple_light_panel_settings',
            data : function () {
                return {
                    component_name : 'panel',
                    s : {
                        'type' : 'primary',
                        'header' : 'Panel Title',
                        'header_alignment' : 'left',
                        'body' : 'Panel content !',
                        'footer' : 'Footer text',
                        'footer_alignment' : 'left',
                    },
                    'types' : {
                        'primary' : 'Primary',
                        'success' : 'Success',
                        'info' : 'Info',
                        'warning' : 'Warning',
                        'danger' : 'Danger',
                        'default' : 'Default'
                    },
                    'header_alignments' : {
                        'right' : 'Right',
                        'center' : 'Center',
                        'left' : 'Left'
                    },
                    'footer_alignments' : {
                        'right' : 'Right',
                        'center' : 'Center',
                        'left' : 'Left'
                    }
                }
            },
            methods : {
                insert_shortcode : function () {
                    sm_object.insert_shortcode( this.s, 'smps_sl_panel' );
                }
            },
            ready : function () {
                if ( smps_app.edit_target_item == this.component_name ) {
                    this.s = smps_app.edit_target_item_data;
                }
            }
        } );
        //alert
        Vue.component('smps_simple_light_alert_settings',{
            template : '#smps_simple_light_alert_settings',
            data : function () {
                return {
                    component_name : 'alert',
                    s : {
                        type : 'success',
                        content : '',
                        dismissable : true
                    },
                    types : {
                        'primary' : 'Primary',
                        'success' : 'Success',
                        'info' : 'Info',
                        'warning' : 'Warning',
                        'danger' : 'Danger',
                        'default' : 'Default'
                    }
                }
            },
            methods : {
                insert_shortcode : function () {
                    sm_object.insert_shortcode( this.s, 'smps_sl_alert' );
                }
            },
            ready : function () {
                if ( smps_app.edit_target_item == this.component_name ) {
                    this.s = smps_app.edit_target_item_data;
                }
            }
        });

        /**
         * heading
         */
        Vue.component( 'smps_simple_light_heading_settings', {
            template : '#smps_simple_light_heading_settings',
            data : function () {
                return {
                    component_name : 'heading',
                    s : {
                        text_align : 'left',
                        text : 'Lorem ipsum dolor sit amet, consectetur adipisicing elit',
                        type : 'h2',
                    },
                    text_aligns : {
                        'right' : 'Right',
                        'center' : 'Center',
                        'left' : 'Left'
                    },
                    types : {
                        h1 : 'h1',
                        h2 : 'h2',
                        h3 : 'h3',
                        h4 : 'h4',
                        h5 : 'h5',
                        h6 : 'h6'
                    }
                }
            },
            methods : {
                insert_shortcode : function () {
                    sm_object.insert_shortcode( this.s, 'smps_sl_heading' );
                }
            },
            ready : function () {
                if ( smps_app.edit_target_item == this.component_name ) {
                    this.s = smps_app.edit_target_item_data;
                }
            }
        });

        /**
         * Quote
         */
        Vue.component('smps_simple_light_quote_settings',{
            template : '#smps_simple_light_quote_settings',
            data : function () {
                return {
                    component_name : 'quote',
                    s : {
                        alignment : 'left',
                        quote : 'Lorem ipsum dolor sit amet, consectetur adipisicing elit',
                        author : 'John Doe'
                    },
                    alignments : {
                        'right' : 'Right',
                        'left' : 'Left'
                    }
                }
            },
            methods : {
                insert_shortcode : function () {
                    sm_object.insert_shortcode( this.s, 'smps_sl_quote' );
                }
            },
            ready : function () {
                if ( smps_app.edit_target_item == this.component_name ) {
                    this.s = smps_app.edit_target_item_data;
                }
            }
        });

        Vue.component( 'smps_simple_light_button_settings',{
            template : '#smps_simple_light_button_settings',
            data : function () {
                return {
                    component_name : 'button',
                    s : {
                        type : 'default',
                        enable_text : true,
                        text : 'Button',
                        enable_icon : false,
                        icon : '',
                        shape : 'rounded',
                        size : '',
                        redirection_type : 'same_page',
                        open_newtab : false,
                        url : 'http://',
                        page : '',
                    },
                    types : {
                        'primary' : 'Primary',
                        'success' : 'Success',
                        'info' : 'Info',
                        'warning' : 'Warning',
                        'danger' : 'Danger',
                        'default' : 'Default'
                    },
                    shapes : {
                        'rounded' : 'Rounded',
                        'normal' : 'Normal'
                    },
                    sizes : {
                        'lg' : 'Large',
                        '' : 'Default',
                        'sm' : 'Small',
                        'xs' : 'Mini',
                        'block' : 'Block'
                    },
                    redirection_types : {
                        'to_page' : 'To a Page',
                        'same_page' : 'Same Page',
                        'url' : 'Set Manually'
                    },
                    pages : {}
                }
            },
            methods : {

                insert_shortcode : function () {
                    sm_object.insert_shortcode( this.s, 'smps_sl_button' );
                }
            },
            ready : function () {
                if ( smps_app.edit_target_item == this.component_name ) {
                    this.s = smps_app.edit_target_item_data;
                }
            }
        } );

        Vue.component('smps_simple_light_spoiler_settings', {
            template : '#smps_simple_light_spoiler_settings',
            data : function () {
                return {
                    component_name : 'spoiler',
                    s : {
                        title : 'Spoiler Title',
                        is_open : 'yes',
                        style : 'default',
                        class : '',
                        content : 'Spoiler content',
                    },
                    open_opts : { 'yes' : 'Yes', 'no' : 'No'},
                    styles : { 'default' : 'Default',
                        'danger' : 'Danger',
                        'warning' : 'Warning',
                        'info' : 'Info',
                        'success' : 'Success'
                    }
                }
            },
            methods : {
                insert_shortcode : function () {
                    sm_object.insert_shortcode( this.s, 'smps_sl_spoiler' );
                },
            },
            ready : function () {
                if ( smps_app.edit_target_item == this.component_name ) {
                    this.s = smps_app.edit_target_item_data;
                }
            }
        });

        /* list */
        Vue.component( 'smps_simple_light_list_settings',{
            template : '#smps_simple_light_list_settings',
            data : function () {
                return {
                    component_name : 'list',
                    s : {
                        items : [],
                        list_type : 'ul',
                        class : '',
                        id : ''
                    }
                }
            },
            methods : {
                add_item : function () {
                    this.s.items.push({ label : 'Item list'});
                },
                item_up : function (k) {
                    if ( k <= 0 ) return;
                    var temp_val = this.s.items[k];
                    this.s.items.splice(k,1);
                    this.s.items.splice( (k-1),0, temp_val );
                },
                item_down : function (k) {
                    if ( k >= this.s.items.length ) return;
                    var temp_val = this.s.items[k];
                    this.s.items.splice(k,1);
                    this.s.items.splice( (k + 1),0, temp_val );
                },
                item_remove : function (k) {
                    this.s.items.splice(k,1);
                },
                insert_shortcode : function () {
                    sm_object.insert_shortcode( this.s, 'smps_sl_list' );
                }
            },
            ready : function () {
                if ( smps_app.edit_target_item == this.component_name ) {
                    this.s = smps_app.edit_target_item_data;
                }
            }
        });

        /*highlight*/
        Vue.component( 'smps_simple_light_highlight_settings',{
            template : '#smps_simple_light_highlight_settings',
            data : function () {
                return {
                    component_name : 'highlight',
                    s : {
                        background : '',
                        text_color : '',
                        class : '',
                        id : '',
                        content : ''
                    }
                }
            },
            methods : {
                insert_shortcode : function () {
                    sm_object.insert_shortcode( this.s, 'smps_sl_highlight' );
                }
            },
            ready : function () {
                $('.colorpicker').wpColorPicker();

                if ( smps_app.edit_target_item == this.component_name ) {
                    this.s = smps_app.edit_target_item_data;
                }
            }
        });

        //member_content
        Vue.component( 'smps_simple_light_restricted_content_settings', {
            template : '#smps_simple_light_restricted_content_settings',
            data : function () {
                return {
                    component_name : 'restricted_content',
                    s : {
                        //message : 'This content is for registered users only. Please %login%.',

                        bg_color : '',
                        login_text : 'This content is for registered users only. Please %login%.',
                        login_link_url : 'default',
                        restricted_content : 'This content is visible for loggedin users only'
                    }
                }
            },
            methods : {
                insert_shortcode : function () {
                    sm_object.insert_shortcode( this.s, 'smps_sl_restricted_content' );
                }
            },
            ready : function () {
                $('.colorpicker').wpColorPicker();

                if ( smps_app.edit_target_item == this.component_name ) {
                    this.s = smps_app.edit_target_item_data;
                }
            }
        });
        //note
        Vue.component( 'smps_simple_light_note_settings', {
            template : '#smps_simple_light_note_settings',
            data : function () {
                return {
                    component_name : 'note',
                    s : {
                        bg_color : '',
                        text_color : '',
                        radius : '0',
                        class : '',
                        Id : '',
                        content : 'Test note'
                    }
                }
            },
            methods : {
                insert_shortcode : function () {
                    sm_object.insert_shortcode( this.s, 'smps_sl_note' );
                }
            },
            ready : function () {
                $('.colorpicker').wpColorPicker();

                if ( smps_app.edit_target_item == this.component_name ) {
                    this.s = smps_app.edit_target_item_data;
                }
            }
        });

        //youtube
        Vue.component( 'smps_simple_light_youtube_settings', {
            template : '#smps_simple_light_youtube_settings',
            data : function () {
                return {
                    component_name : 'youtube',
                    s : {
                        url : '',
                        width : '600',
                        height : '400',
                        responsive : 'yes',
                        controls : 0,
                        autohide : 2,
                        show_title_bar : 'yes',
                        autoplay : 'no',
                        loop : 'no',
                        related_videos : 'no',
                        full_screen_button : 'yes',
                        modestbranding : 'no',
                        class : '',
                    },

                    controls_opt : {
                        0 : 'Hide Controls',
                        1 : 'Show Controls',
                        2 : 'Show Control When Playback is Started'
                    },
                    autohide_opt : {
                        0 : 'Do not hide controls',
                        1 : 'Hide all controls on mouseout',
                        2 : 'Hide progressbar on mouseout'
                    }
                }
            },
            methods : {
                insert_shortcode : function () {
                    sm_object.insert_shortcode( this.s, 'smps_sl_youtube' );
                }
            },
            ready : function () {
                if ( smps_app.edit_target_item == this.component_name ) {
                    this.s = smps_app.edit_target_item_data;
                }
            }
        });

        //vimeo
        Vue.component( 'smps_simple_light_vimeo_settings', {
            template : '#smps_simple_light_vimeo_settings',
            data : function () {
                return {
                    component_name : 'vimeo',
                    s : {
                        url : 'https://vimeo.com/api/oembed.json?url=https%3A//vimeo.com/76979871',
                        width : '600',
                        height : '400',
                        loop : 'yes',
                        autoplay : 'no',
                        class : '',
                        Id : ''
                    }
                }
            },
            methods : {
                insert_shortcode : function () {
                    sm_object.insert_shortcode( this.s, 'smps_sl_vimeo' );
                }
            },
            ready : function () {
                if ( smps_app.edit_target_item == this.component_name ) {
                    this.s = smps_app.edit_target_item_data;
                }
            }
        });

        //image
        Vue.component( 'smps_simple_light_image_settings', {
            template : '#smps_simple_light_image_settings',
            data : function () {
                return {
                    component_name : 'image',
                    s : {
                        src : '',
                        width : '600',
                        height : '400',
                        responsive : 'yes',
                        class : '',
                        Id : ''
                    }
                }
            },
            methods : {
                insert_shortcode : function () {
                    sm_object.insert_shortcode( this.s, 'smps_sl_image' );
                }
            },
            ready : function () {
                var this_comp = this;
                $('.upload_image_button').click(function() {

                    formfield = $('.upload_image').attr('name');
                    tb_show( '', 'media-upload.php?type=image&amp;TB_iframe=true' );
                    return false;
                });

                window.send_to_editor = function(html) {
                    imgurl = $(html).attr('src');
                    this_comp.s.src = imgurl;
                    tb_remove();
                }

                if ( smps_app.edit_target_item == this.component_name ) {
                    this.s = smps_app.edit_target_item_data;
                }
            }
        });

        //scheduler
        Vue.component( 'smps_simple_light_scheduler_settings', {
            template : '#smps_simple_light_scheduler_settings',
            data : function () {
                return {
                    component_name : 'scheduler',
                    s : {
                        timespans : [
                            {from : '', to : ''}
                        ],
                        alternative_text : '',
                        content : '',
                        class : '',
                        Id : ''
                    }
                }
            },
            methods : {
                add_timeslot : function () {
                    Vue.set(this.s.timespans,this.s.timespans.length,{from:'',to:''});
                    this.reset_datepicker();

                },
                remove_timeslot : function (k) {
                    this.s.timespans.splice(k,1);
                },
                reset_datepicker : function () {
                    this.$nextTick(function () {
                        $('.datepicker').datetimepicker();
                    });
                },
                insert_shortcode : function () {
                    sm_object.insert_shortcode( this.s, 'smps_sl_scheduler' );
                }
            },
            ready : function () {
                this.reset_datepicker();

                if ( smps_app.edit_target_item == this.component_name ) {
                    this.s = smps_app.edit_target_item_data;
                }
            }
        });
    });

    //post_loop
    Vue.component( 'smps_simple_light_post_loop_settings',{
        template : '#smps_simple_light_post_loop_settings',
        data : function () {
            return {
                component_name : 'post_loop',
                s : {
                    category__in : '',
                    author : '',
                    posts_per_page : 5,
                    nopaging : 1,
                    orderby : 'date',
                    post_status : 'publish',
                    tag : '',
                    order : 'DESC',
                    class : '',
                    Id : ''
                },
                order_opts : { 'DESC' : 'DESC', 'ASC' : 'ASC' },
                orderby_opts :  sm_settings_data.post_loop.orderby,
                post_status_opts : sm_settings_data.post_loop.post_statuses
            }
        },
        methods : {
            insert_shortcode : function () {
                sm_object.insert_shortcode( this.s, 'smps_sl_' + this.component_name );
            }
        },
        ready : function () {
            if ( smps_app.edit_target_item == this.component_name ) {
                this.s = smps_app.edit_target_item_data;
            }
        }
    } );

    //page_loop
    Vue.component( 'smps_simple_light_page_loop_settings',{
        template : '#smps_simple_light_page_loop_settings',
        data : function () {
            return {
                component_name : 'page_loop',
                s : {
                    posts_per_page : 5,
                    nopaging : 1,
                    orderby : 'date',
                    post_status : 'publish',
                    order : 'DESC',
                    class : '',
                    Id : ''
                },
                order_opts : { 'DESC' : 'DESC', 'ASC' : 'ASC' },
                orderby_opts :  sm_settings_data.page_loop.orderby,
                post_status_opts : sm_settings_data.page_loop.post_statuses
            }
        },
        methods : {
            insert_shortcode : function () {
                sm_object.insert_shortcode( this.s, 'smps_sl_' + this.component_name );
            }
        },
        ready : function () {
            if ( smps_app.edit_target_item == this.component_name ) {
                this.s = smps_app.edit_target_item_data;
            }
        }
    } );

    //post meta
    Vue.component( 'smps_simple_light_post_meta_settings',{
        template : '#smps_simple_light_post_meta_settings',
        data : function () {
            return {
                component_name : 'post_meta',
                s : {
                    id : '',
                    key : '',
                    default_value : '',
                    class : '',
                    Id : ''
                }
            }
        },
        methods : {
            insert_shortcode : function () {
                sm_object.insert_shortcode( this.s, 'smps_sl_' + this.component_name );
            }
        },
        ready : function () {
            if ( smps_app.edit_target_item == this.component_name ) {
                this.s = smps_app.edit_target_item_data;
            }
        }
    } );

    //option
    Vue.component( 'smps_simple_light_option_settings',{
        template : '#smps_simple_light_option_settings',
        data : function () {
            return {
                component_name : 'option',
                s : {
                    name : '',
                    value : '',
                    class : '',
                    Id : ''
                }
            }
        },
        methods : {
            insert_shortcode : function () {
                sm_object.insert_shortcode( this.s, 'smps_sl_' + this.component_name );
            }
        },
        ready : function () {
            if ( smps_app.edit_target_item == this.component_name ) {
                this.s = smps_app.edit_target_item_data;
            }
        }
    } );

    //category_list
    Vue.component( 'smps_simple_light_category_list_settings',{
        template : '#smps_simple_light_category_list_settings',
        data : function () {
            return {
                component_name : 'category_list',
                s : {
                    title_li : 'Categories',
                    parent_id : 0,
                    exclude : [],
                    hide_empty : 1,
                    hierarchical : 1,
                    order : 'ASC',
                    separator : '<br/>',
                    show_count : 1,
                    show_option_all : '',
                    show_option_none : 'No categories',
                    class : '',
                    Id : ''
                },
                order_opts : { 'DESC' : 'DESC', 'ASC' : 'ASC' },
            }
        },
        methods : {
            insert_shortcode : function () {
                sm_object.insert_shortcode( this.s, 'smps_sl_' + this.component_name );
            }
        },
        ready : function () {
            if ( smps_app.edit_target_item == this.component_name ) {
                this.s = smps_app.edit_target_item_data;
            }
        }
    } );

    //menu
    Vue.component( 'smps_simple_light_menu_settings',{
        template : '#smps_simple_light_menu_settings',
        data : function () {
            return {
                component_name : 'menu',
                s : {
                    name : '',
                    class : '',
                    Id : ''
                }
            }
        },
        methods : {
            insert_shortcode : function () {
                sm_object.insert_shortcode( this.s, 'smps_sl_' + this.component_name );
            }
        },
        ready : function () {
            if ( smps_app.edit_target_item == this.component_name ) {
                this.s = smps_app.edit_target_item_data;
            }
        }
    } );

    /*custom shortcode*/
    Vue.component('sm_custom_shortcode',{
        template : '#sm_custom_shortcode',
        props : ['tag','id'],
        data : function () {
            return {
                shortcode_atts : {}
            };
        },
        methods : {
            insert_shortcode : function () {
                var atts_string = '';

                for( v in _this.shortcode_atts ) {
                    atts_string = atts_string + ' ' + v + '="' + _this.shortcode_atts[v] + '"';
                }
                tinyMCE.activeEditor.selection.setContent('[' + _this.tag + ' ' + atts_string + '][/' + _this.tag + ']' );
                smps_app.dismiss_settings_panel();
            }
        },
        created : function () {
            _this = this;
            $.post(
                ajaxurl,
                {
                    action : 'sm_get_shortcode_atts',
                    shortcode_id : _this.id,
                    tag : _this.tag
                },
                function( data ) {
                    if( data.success == true ) {
                        _this.shortcode_atts = data.data.shortcode_atts
                    } else {
                        tinyMCE.activeEditor.selection.setContent('[' + _this.tag + '][/' + _this.tag + ']' );
                        smps_app.dismiss_settings_panel();
                    }
                }
            );
        }
    });


    sm_object.insert_shortcode = function ( settings_data, shortcode_name ) {
        var data = encodeURIComponent(JSON.stringify(settings_data));
        var shortcode = '[' + shortcode_name + ' data="' + data + '" ]';
        tinyMCE.activeEditor.selection.setContent( shortcode );
        smps_app.dismiss_settings_panel();
    }

}(jQuery));