<?php
/*
Plugin Name: Kodamaps
Author: Tomoka Baba (Robox.org)
Plugin URI: https://github.com/RoboxOrg/kodamaps
Description: Post the location information
Version: 0.2.0
Author URI: http://robox.org/
*/

class Kodamaps_Plugin
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_kodamaps_admin_js'));
        add_action('admin_menu', array($this,'add_custom_box'));
        add_action('save_post', array($this,'save_postdata'));
        add_action('wp_enqueue_scripts', array($this, 'register_kodamaps_style'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_kodamaps_js'));
        add_shortcode('kodamaps', array($this,'kodamaps_map_shortcode'));
    }

    public function enqueue_kodamaps_admin_js()
    {
        wp_register_script (
            'google-maps-api',
            '//maps.google.com/maps/api/js?sensor=false',
            false,
            null,
            true
        );
        wp_register_script (
            'kodamaps-admin-js-script',
            plugins_url('js/admin-map.js', __FILE__),
            array('jquery', 'google-maps-api'),
            filemtime(dirname(__FILE__).'/js/admin-map.js'),
            true
        );
        wp_enqueue_script('kodamaps-admin-js-script');
    }

    public function add_custom_box()
    {
        add_meta_box(
            'kodamaps-section-id',
            'Kodamaps - input area -',
            array($this,'inner_custom_box'),
            'post',
            'advanced',
            'low'
        );
    }

    public function inner_custom_box()
    {
        wp_nonce_field(plugin_basename(__FILE__), 'kodamaps_noncename');
        $field_name_address = 'kodamaps_address_field';
        $field_name_lat = 'kodamaps_lat_field';
        $field_name_lng = 'kodamaps_lng_field';
        $field_val_address = get_post_meta(get_the_ID(), $field_name_address, true);
        $field_val_lat = get_post_meta(get_the_ID(), $field_name_lat, true);
        $field_val_lng = get_post_meta(get_the_ID(), $field_name_lng, true);
        echo('<div>');
            printf(
                '<label for="%s">%s</label> ',
                esc_attr($field_name_address),
                "Address"
            );
            printf(
                '<input type="text" id="kodamaps-txt-input-address" value="%s" />',
                esc_attr($field_val_address)
            );
        echo('</div>');
        echo('<div>');
            printf(
                '<label for="%s">%s</label> ',
                esc_attr($field_name_address),
                "Latitude"
            );
            printf(
                '<input type="text" id="kodamaps-txt-input-lat" value="%s" />',
                esc_attr($field_val_lat)
            );
        echo('</div>');
        echo('<div>');
            printf(
                '<label for="%s">%s</label> ',
                esc_attr($field_name_address),
                "Longitude"
            );
            printf(
                '<input type="text" id="kodamaps-txt-input-lng" value="%s" />',
                esc_attr($field_val_lng)
            );
        echo('</div>');

        printf(
            '<input type="hidden" class="kodamaps-postdata-address" name="%s" value="%s" />',
            esc_attr($field_name_address),
            esc_attr($field_val_address)
        );
        printf(
            '<input type="hidden" class="kodamaps-postdata-lat" name="%s" value="%s" />',
            esc_attr($field_name_lat),
            esc_attr($field_val_lat)
        );
        printf(
            '<input type="hidden" class="kodamaps-postdata-lng" name="%s" value="%s" />',
            esc_attr($field_name_lng),
            esc_attr($field_val_lng)
        );

        echo (
            '<div id="map_canvas" style="width: 100%; height: 450px"></div>'
        );
    }

    public function save_postdata($post_id)
    {
        if(!isset($_POST['kodamaps_noncename'])) {
            return $post_id;
        }

        if(!wp_verify_nonce($_POST['kodamaps_noncename'], plugin_basename(__FILE__))) {
            return $post_id;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        $post_type = isset($_POST['post_type']) ? $_POST['post_type'] : '';
        $post_types = wp_list_filter(
            get_post_types(array('public' => true)),
            array('attachment'),
            'NOT'
        );
        if(in_array($post_type, $post_types)) {
            if(!current_user_can('edit_'.$post_type, $post_id)) {
                return $post_id;
            }
        } else {
            return $post_id;
        }

        $mypostdata = array(
            'address_data' => isset($_POST['kodamaps_address_field']) ? $_POST['kodamaps_address_field'] : '',
            'lat_data' => isset($_POST['kodamaps_lat_field']) ? $_POST['kodamaps_lat_field'] : '',
            'lng_data' => isset($_POST['kodamaps_lng_field']) ? $_POST['kodamaps_lng_field'] : ''
        );
        if(!empty($mypostdata['address_data']) && !empty($mypostdata['lat_data']) && !empty($mypostdata['lng_data'])) {
            update_post_meta($post_id, 'kodamaps_address_field', $mypostdata['address_data']);
            update_post_meta($post_id, 'kodamaps_lat_field', $mypostdata['lat_data']);
            update_post_meta($post_id, 'kodamaps_lng_field', $mypostdata['lng_data']);
        } else {
            delete_post_meta($post_id, 'kodamaps_address_field');
            delete_post_meta($post_id, 'kodamaps_lat_field');
            delete_post_meta($post_id, 'kodamaps_lng_field');
        }

        return $mypostdata;
    }

    public function register_kodamaps_style()
    {
      wp_register_style (
        'kodamaps.css',
        plugins_url('css/kodamaps.css',__FILE__),
        array(),
        '1.0.0',
        'all'
      );
      wp_enqueue_style('kodamaps.css');
    }

    public function enqueue_kodamaps_js()
    {
        wp_register_script (
            'google-maps-api',
            '//maps.google.com/maps/api/js?sensor=false',
            false,
            null,
            true
        );
    }

    public function kodamaps_map_shortcode($atts)
    {
        $default_atts = array(
            'type' => 'single',
            'width' => '',
            'height' => '',
            'addr' => '',
            'lat' => '',
            'lng' => '',
            'zoom' => '',
            'position' => 'left',
            'no' => 0
        );
        $merged_atts = shortcode_atts($default_atts, $atts);
        extract($merged_atts);

        if ($type === 'all') {
            wp_register_script (
                'kodamaps-js-script',
                plugins_url('js/allpost-map.js', __FILE__),
                array('jquery', 'google-maps-api'),
                filemtime(dirname(__FILE__).'/js/allpost-map.js'),
                true
            );
            wp_enqueue_script('kodamaps-js-script');
            return $this->displayAllPostOnMap($width, $height, $addr, $lat, $lng, $zoom, $position);
        } else if ($type === 'notuse') {
            wp_register_script (
                'kodamaps-js-script',
                plugins_url('js/notusepost-map.js', __FILE__),
                array('jquery', 'google-maps-api'),
                filemtime(dirname(__FILE__).'/js/notusepost-map.js'),
                true
            );
            wp_enqueue_script('kodamaps-js-script');
            return $this->displayNotusePostOnMap($width, $height, $addr, $lat, $lng, $zoom, $position, $no);
        } else {
            wp_register_script (
                'kodamaps-js-script',
                plugins_url('js/post-map.js', __FILE__),
                array('jquery', 'google-maps-api'),
                filemtime(dirname(__FILE__).'/js/post-map.js'),
                true
            );
            wp_enqueue_script('kodamaps-js-script');
            return $this->displaySinglePostOnMap($width, $height, $addr, $lat, $lng, $zoom, $position);
        }
    }

    private function displaySinglePostOnMap($width, $height, $centerAddr, $centerLat, $centerLng, $zoom, $position) {
        $id = get_the_ID();
        $lat = get_post_meta($id, 'kodamaps_lat_field', true);
        $lng = get_post_meta($id, 'kodamaps_lng_field', true);
        $data = array(
            'id' => $id,
            'lat' => $lat,
            'lng' => $lng,
            'centerAddr' => $centerAddr,
            'centerLat' => $centerLat,
            'centerLng' => $centerLng,
            'zoom' => $zoom
        );
        wp_localize_script('kodamaps-js-script', 'kodamaps_post', $data);
        $width = $width === '' ? '100%' : $width.'px';
        $height = $height === '' ? '450px' : $height.'px';
        $positionStyle = 'margin-right: auto';
        if ($position === 'center') {
          $positionStyle = 'margin: 0 auto';
        } else if ($position === 'right') {
          $positionStyle = 'margin-left: auto';
        }
        if (is_single() || is_page()) {
            return '<div id="map_canvas" style="width: '.$width.'; height: '.$height.'; '.$positionStyle.'; max-width: 100%;"></div>';
        }
    }

    private function displayAllPostOnMap($width, $height, $centerAddr, $centerLat, $centerLng, $zoom, $position) {
        $allposts = get_posts('numberposts=-1');
        $data = array(
            'postInfo' => array(),
            'centerAddr' => $centerAddr,
            'centerLat' => $centerLat,
            'centerLng' => $centerLng,
            'zoom' => $zoom
        );
        foreach($allposts as $unipost) {
            setup_postdata($unipost);
            $id = $unipost->ID;
            $lat = get_post_meta($id, 'kodamaps_lat_field', true);
            $lng = get_post_meta($id, 'kodamaps_lng_field', true);
            $data['postInfo'][] = array(
                'id' => $id,
                'lat' => $lat,
                'lng' => $lng,
                'zoom' => $zoom
            );
        }
        wp_localize_script('kodamaps-js-script', 'kodamaps_posts', $data);
        $width = $width === '' ? '100%' : $width.'px';
        $height = $height === '' ? '450px' : $height.'px';
        $positionStyle = 'margin-right: auto';
        if ($position === 'center') {
          $positionStyle = 'margin: 0 auto';
        } else if ($position === 'right') {
          $positionStyle = 'margin-left: auto';
        }
        if (is_single() || is_page()) {
            return '<div id="map_canvas" style="width: '.$width.'; height: '.$height.'; '.$positionStyle.'; max-width: 100%;"></div>';
        }
    }

    private function displayNotusePostOnMap($width, $height, $centerAddr, $centerLat, $centerLng, $zoom, $position, $no) {
        $data = array(
            'centerAddr' => $centerAddr,
            'centerLat' => $centerLat,
            'centerLng' => $centerLng,
            'zoom' => $zoom
        );
        wp_localize_script('kodamaps-js-script', 'kodamaps_posts_'.$no, $data);
        $width = $width === '' ? '100%' : $width.'px';
        $height = $height === '' ? '450px' : $height.'px';
        $positionStyle = 'margin-right: auto';
        if ($position === 'center') {
          $positionStyle = 'margin: 0 auto';
        } else if ($position === 'right') {
          $positionStyle = 'margin-left: auto';
        }
        return '<div id="map_canvas_'.$no.'" style="width: '.$width.'; height: '.$height.'; '.$positionStyle.'; max-width: 100%;"></div>';
    }
}
new Kodamaps_Plugin();
