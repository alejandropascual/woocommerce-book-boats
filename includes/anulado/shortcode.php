<?php

// ANULADO

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


function ABB_interface( $atts ) {

    //$pieces = sushi_get_products_pieces();
    //$boxes = sushi_get_products_boxes();

    //$html  = '<script>window.SUSHI_pieces = '.json_encode($pieces).';</script>';
    //$html .= '<script>window.SUSHI_boxes = '.json_encode($boxes).';</script>';

    ob_start();
    include 'template-interface.php';
    $html .= ob_get_clean();

    return $html;
}

add_shortcode('abb_booking', 'ABB_interface');