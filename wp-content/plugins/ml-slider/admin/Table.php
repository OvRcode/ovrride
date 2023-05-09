<?php
if (!defined('ABSPATH')) {
    die('No direct access.');
}
if (!class_exists('WP_List_table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
class MetaSlider_Admin_Table extends WP_List_table
{
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        if (!isset( $_POST['search_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['search_wpnonce']), 'metaslider_search_slideshows')) {
            $table_data = $this->table_data();
        } else {
            if (isset($_POST['s'])) {
                $table_data = $this->table_data(sanitize_text_field($_POST['s']));
            }
        }

        //pagination
        $slideshows_per_page = 10;
        $table_page = $this->get_pagenum();
        $this->items = array_slice($table_data, (($table_page - 1) * $slideshows_per_page), $slideshows_per_page);
        $total_slideshows = count($table_data);
        $this->set_pagination_args(array(
            'total_items' => $total_slideshows,
            'per_page'    => $slideshows_per_page,
            'total_pages' => ceil($total_slideshows/$slideshows_per_page)
        ));
    }

    public function get_columns()
    {
        $columns = array(
            'slides' => esc_html__('Preview', 'ml-slider'),
            'post_title' => esc_html__('Title', 'ml-slider'),
            'post_date' => esc_html__('Created', 'ml-slider'),
            'ID' => esc_html__('Shortcode', 'ml-slider')
        );

        return $columns;
    }

    public function get_hidden_columns()
    {
        return array();
    }

    public function get_sortable_columns()
    {
        $sortable_columns = array(
          'post_title' => array('post_title',false),
          'post_date' => array('post_date',false),
          'ID' => array('ID',false)
        );
        return $sortable_columns;
    }

    public function column_default($item, $column_name)
    {
        switch($column_name) {
            case 'slides':
                return $this->slideshow_thumb($item[ 'ID' ]);
            case 'post_title':
                return $item[ $column_name ];
            case 'post_date':
                $date = date_create($item[ $column_name ]);
                $dateFormat = get_option('date_format');
                $timeFormat = get_option( 'time_format' );
                return date_format($date, $dateFormat.' \a\t '.$timeFormat);
            case 'ID':
                return $this->shortcodeColumn($item[$column_name]);
            default:
                return print_r($item, true);
        }
    }

    public function shortcodeColumn($slideshowID)
    {
        return ('<metaslider-shortcode inline-template>
        <pre id="shortcode" ref="shortcode" dir="ltr" class="text-gray text-sm"><div @click.prevent="copyShortcode($event)" class="text-orange cursor-pointer whitespace-normal inline">[metaslider id="{{'. esc_attr($slideshowID) .'}}"]</div></pre>
        </metaslider-shortcode>');
    }

    protected function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="slideshows[]" value="%d" />', (int)$item['ID']
        );
    }

    protected function process_bulk_action()
    {
        $action = 'bulk-' . $this->_args['plural'];

        if (empty($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['_wpnonce']), $action)) {
            wp_die(esc_html('Security check failed!', 'ml-slider'));
            return;
        }

        if ('delete' !== $this->current_action()) {
            return;
        }

        $slideshows = isset($_REQUEST['slideshows']) ? $_REQUEST['slideshows'] : array();
        if (empty($slideshows)) {
            return;
        }

        foreach ($slideshows as $slideshow_id) {
            wp_trash_post($slideshow_id);
        }
    }


    private function table_data($search='')
    {
        global $wpdb;
        $wpdb_table = $wpdb->prefix . 'posts';
        $columns = ['slides', 'post_title', 'post_date'];
        $orderBy = isset($_GET['orderby']) && in_array($_GET['orderby'], $columns, true ) ? $_GET['orderby'] : 'ID';
        $order = isset( $_GET['order'] ) && 'desc' === $_GET['order'] ? 'desc' : 'asc';
        $orderBySql = sanitize_sql_orderby( "{$orderBy} {$order}" );

        if (!empty($search)) {
            $slides_query = $wpdb->prepare("SELECT ID, post_title, post_date FROM $wpdb_table WHERE post_type = %s AND post_status = %s AND post_title LIKE %s ORDER BY $orderBySql", array('ml-slider', 'publish', '%'. $wpdb->esc_like($search). '%'));  // WPCS: unprepared SQL OK.
        } else {
            $slides_query = $wpdb->prepare("SELECT ID, post_title, post_date FROM $wpdb_table WHERE post_type = %s AND post_status = %s ORDER BY $orderBySql", array('ml-slider', 'publish'));  // WPCS: unprepared SQL OK.
        }

        $query_results = $wpdb->get_results($slides_query, ARRAY_A ); // WPCS: unprepared SQL OK.

        return $query_results;
    }

    public function slideshow_thumb($slideshowId)
    {
        $slides = get_posts(array(
            'post_type' => array('ml-slide'),
            'post_status' => array('publish'),
            'lang' => '',
            'suppress_filters' => 1,
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'ml-slider',
                    'field' => 'slug',
                    'terms' => (int) $slideshowId
                )
            )
        ));
        $numberOfSlides = count($slides);
        $logo = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents(dirname(__FILE__) . '/assets/metaslider.svg'));
        $thumbHtml = "<div class='w-16 h-16 bg-gray-light'>";
        if ($numberOfSlides === 0) {
            $thumbHtml .= "<div class='relative w-16 h-16'><img src='". esc_url($logo) ."' class='absolute block inset-0 default-thumb'></div>";
        } else {
            $thumbHtml .= "<div class='relative w-16 h-16 image-wrap'>";
            foreach ($slides as $key => $slide) {
                $image = get_post(get_post_meta($slide->ID, '_thumbnail_id', true));
                if ($image) {
                    $thumb = wp_get_attachment_image_src($image->ID, 'thumbnail');
                    $thumbHtml .= "<img src='". esc_url($thumb[0]) ."' class='absolute block inset-0 transition-all duration-1000 ease-linear'>";
                } else {
                    $thumbHtml .= "<img src='". esc_url($logo) ."' class='absolute block inset-0 default-thumb'>";
                }
            }
            $thumbHtml .= "</div>";
        }
        $thumbHtml .= "</div>";

        return $thumbHtml;
    }

    public function column_post_title($item)
    {
        $page = empty($_REQUEST['page']) ? 'metaslider' : sanitize_key($_REQUEST['page']);

        $url = '?page=' . $page . '&id=' . absint($item['ID']);
        $actions = [
            'edit' => '<a href="' . esc_url($url) . '">' . esc_html__('Edit', 'ml-slider') . '</a>'
        ];

        return sprintf(
            '%1$s %2$s',
            '<a class="row-title" href="' . esc_url($url) . '">' . esc_html($item['post_title']) . '</a>',
            $this->row_actions($actions)
        );
    }

    public function check_num_rows()
    {
        $table_data = $this->table_data();
        return $table_data;
    }
}
