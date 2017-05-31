<?php


function get_wc_bookboat( $id ) {
    return new WC_Bookboat( $id );
}

function get_wc_bookboat_from_order( $order_id, $order_item_id ) {
    global $wpdb;
    // Get wc_bookboat ID
    // TODO
    return false;
}

