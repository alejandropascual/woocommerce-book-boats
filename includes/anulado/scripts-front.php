<?php

// ANULADO

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function abb_load_scripts() {

    global $post;

    if (isset($post->post_content)) {

        $js_dir  = ABB_PLUGIN_URL . 'assets/js/';
        $css_dir = ABB_PLUGIN_URL . 'assets/css/';

        if (has_shortcode($post->post_content,'abb_booking')) {

            wp_enqueue_style( 'fontawesome', $css_dir.'font-awesome.min.css' );
            wp_enqueue_style( 'abb_booking', $css_dir.'abb_booking.css' );

            wp_enqueue_script( 'knockout', $js_dir . 'knockout.min.js', array( 'jquery' ), '3.3.0', true );
            wp_enqueue_script( 'knockout-sortable', $js_dir . 'knockout-sortable.min.js', array( 'jquery','jquery-ui-draggable','jquery-ui-droppable','knockout' ), '0.11.0', true );

            wp_register_script( 'abb_booking', $js_dir . 'abb_booking.js', array( 'knockout-sortable' ), '1.0.0', true );
            $data = array();
            wp_localize_script( 'abb_booking', 'ABB_BOOKING', $data );
            wp_localize_script( 'abb_booking', 'Ajax_url', array('url'=>admin_url('admin-ajax.php')) );
            wp_enqueue_script( 'abb_booking' );
        }

    }

}

add_action( 'wp_enqueue_scripts', 'abb_load_scripts', 100 );