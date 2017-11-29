(function ($) {
    $(document).ready(function () {
        /**
         * tabs
         */
        Vue.component('smps_simple_light_tabs_settings',{
            template : '#smps_simple_light_tabs_settings',
            data : function () {
                return {
                    type : 'tabs',
                    tab_data : {},
                    tab_template : {
                        'title' : 'Tab Label',
                        'content' : 'Tab content'
                    },
                    types : {
                        'tabs' : 'Tabs',
                        'pills' : 'Pills'
                    },
                    tab_target : '',
                    content_target : ''
                }
            },
            methods : {
                add_tab : function () {
                    Vue.set(this.tab_data,'tab_' + new Date().getTime(),JSON.parse(JSON.stringify(this.tab_template)));
                },
                insert_shortcode : function () {
                    var tab_data = JSON.stringify(this.tab_data).replace(/"/g, '\\"');
                    var shortcode = "[smps_sl_tabs tab_data='" + tab_data + "' type='" + this.type + "' ]";
                    tinyMCE.activeEditor.selection.setContent( shortcode );
                },
                remove_tab : function ( tab_key ) {
                    Vue.delete( this.tab_data, tab_key );
                }
            },
        });

        /**
         * accordion
         */
        Vue.component('smps_simple_light_accordion_settings',{
            template : '#smps_simple_light_accordion_settings',
            data : function () {
                return {
                    acc_data : {},
                    acc_template : {
                        'title' : 'Item Label',
                        'content' : 'Item content'
                    },
                    target_acc : '',
                    target_content : ''
                }
            },
            methods : {
                add_item : function () {
                    Vue.set(this.acc_data,'acc_' + new Date().getTime(),JSON.parse(JSON.stringify(this.acc_template)));
                },
                insert_shortcode : function () {
                    var acc_data = JSON.stringify(this.acc_data).replace(/"/g, '\\"');
                    var shortcode = "[smps_sl_accordion acc_data='" + acc_data + "']";
                    tinyMCE.activeEditor.selection.setContent( shortcode );
                },
                remove_accordion : function (key) {
                    Vue.delete(this.acc_data,key);
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
                    table_data : {},
                    col_template : {},
                    col_tracker : []
                }
            },
            methods : {
                add_col : function () {
                    var col_val = '';/*'td_' + new Date().getTime()*/
                    var col_key = new Date().getTime();
                    Vue.set(this.col_template, col_key, col_val );

                    for( var k in this.table_data ) {
                        Vue.set( this.table_data[k],col_key,col_val);
                    }

                    //col nums
                    this.col_tracker.push(this.col_tracker.length);
                },
                add_row : function () {
                    Vue.set( this.table_data, 'tr_' + new Date().getTime(), JSON.parse( JSON.stringify( this.col_template ) ) );
                },
                remove_col : function (col_number) {
                    console.log(col_number);
                    //remove col of table data
                    for( var k in this.table_data ) {
                        Vue.delete( this.table_data[k], Object.keys(this.table_data[k])[col_number] );
                    }
                    //remove col of col template
                    Vue.delete( this.col_template, Object.keys(this.col_template)[col_number]);
                    //remove from col tracker
                    this.col_tracker.splice( -1, 1 );
                },
                remove_td : function ( t_key, c_key ) {
                    Vue.delete( this.table_data[t_key],c_key);
                },
                remove_row : function ( t_key ) {
                    Vue.delete( this.table_data, t_key );
                    if( !Object.keys(this.table_data).length ) {
                        this.col_template = {};
                        this.col_tracker = [];
                    }
                },
                insert_shortcode : function () {
                    var table_data = JSON.stringify(this.table_data).replace(/"/g, '\\"');
                    var shortcode = "[smps_sl_table table_data='" + table_data + "']";
                    tinyMCE.activeEditor.selection.setContent( shortcode );
                }
            },
            ready : function () {
                this.add_col();
                this.add_row();
            }
        });

        Vue.component( 'smps_simple_light_panel_settings', {
            template : '#smps_simple_light_panel_settings',
            data : function () {
                return {
                    'type' : 'primary',
                    'types' : {
                        'primary' : 'Primary',
                        'success' : 'Success',
                        'info' : 'Info',
                        'warning' : 'Warning',
                        'danger' : 'Danger',
                        'default' : 'Default'
                    },
                    'header' : 'Panel Title',
                    'header_alignment' : 'left',
                    'header_alignments' : {
                        'right' : 'Right',
                        'center' : 'Center',
                        'left' : 'Left'
                    },
                    'body' : 'Panel content !',
                    'footer' : 'Footer text',
                    'footer_alignment' : 'left',
                    'footer_alignments' : {
                        'right' : 'Right',
                        'center' : 'Center',
                        'left' : 'Left'
                    }
                }
            },
            methods : {
                insert_shortcode : function () {
                    var shortcode = '[smps_sl_panel type="' + this.type + '" header="' + this.header + '" header_alignment="' + this.header_alignment + '"' +
                        ' body="' + this.body + '" footer="' + this.footer + '" footer_alignment="'+ this.footer_alignment +'"]';
                    tinyMCE.activeEditor.selection.setContent( shortcode );
                }
            }
        } );
        Vue.component('smps_simple_light_alert_settings',{
            template : '#smps_simple_light_alert_settings',
            data : function () {
                return {
                    type : 'success',
                    types : {
                        'primary' : 'Primary',
                        'success' : 'Success',
                        'info' : 'Info',
                        'warning' : 'Warning',
                        'danger' : 'Danger',
                        'default' : 'Default'
                    },
                    content : '',
                    dismissable : true
                }
            },
            methods : {
                insert_shortcode : function () {
                    var shortcode = '[smps_sl_alert type="' + this.type + '"  content="' + this.content + '" dismissable="' + this.dismissable + '"]';
                    tinyMCE.activeEditor.selection.setContent( shortcode );
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
                    text_align : 'left',
                    text_aligns : {
                        'right' : 'Right',
                        'center' : 'Center',
                        'left' : 'Left'
                    },
                    text : 'Lorem ipsum dolor sit amet, consectetur adipisicing elit',
                    type : 'h2',
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
                    var shortcode = '[smps_sl_heading text_align="'+ this.text_align +'" text="'+ this.text +'" type="' + this.type + '"]';
                    tinyMCE.activeEditor.selection.setContent( shortcode );
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
                    alignment : 'left',
                    alignments : {
                        'right' : 'Right',
                        'left' : 'Left'
                    },
                    quote : 'Lorem ipsum dolor sit amet, consectetur adipisicing elit',
                    author : 'John Doe'
                }
            },
            methods : {
                insert_shortcode : function () {
                    var shortcode = '[smps_sl_quote alignment="'+ this.alignment +'" quote="'+ this.quote +'" author="' + this.author + '"]';
                    tinyMCE.activeEditor.selection.setContent( shortcode );
                }
            }
        });

        Vue.component( 'smps_simple_light_button_settings',{
            template : '#smps_simple_light_button_settings',
            data : function () {
                return {
                    type : 'default',
                    types : {
                        'primary' : 'Primary',
                        'success' : 'Success',
                        'info' : 'Info',
                        'warning' : 'Warning',
                        'danger' : 'Danger',
                        'default' : 'Default'
                    },
                    'enable_text' : true,
                    'text' : 'Button',
                    'enable_icon' : false,
                    'icon' : '',
                    'shape' : 'rounded',
                    'shapes' : {
                        'rounded' : 'Rounded',
                        'normal' : 'Normal'
                    },
                    size : '',
                    sizes : {
                        'lg' : 'Large',
                        '' : 'Default',
                        'sm' : 'Small',
                        'xs' : 'Mini',
                        'block' : 'Block'
                    },
                    redirection_type : 'same_page',
                    redirection_types : {
                        'to_page' : 'To a Page',
                        'same_page' : 'Same Page',
                        'url' : 'Set Manually'
                    },
                    open_newtab : false,
                    url : 'http://',
                    page : '',
                    pages : {}
                }
            },
            methods : {
                insert_shortcode : function () {
                    var shortcode = '[smps_sl_button ' +
                        'type="'+ this.type +'" ' +
                        'size="'+ this.size +'" ' +
                        'enable_text="'+ this.enable_text +'" ' +
                        'text="'+ this.text+'" ' +
                        'enable_icon="'+ this.enable_icon +'" ' +
                        'icon="'+ this.icon +'" ' +
                        'shape="'+ this.shape +'" ' +
                        'redirection_type="' + this.redirection_type + '" ' +
                        'url="'+ this.url +'" ' +
                        'page="'+ this.page +'" ' +
                        'open_newtab="'+ this.open_newtab +'"' +
                        ']';
                    tinyMCE.activeEditor.selection.setContent( shortcode );
                }
            }
        } )
    });
}(jQuery));