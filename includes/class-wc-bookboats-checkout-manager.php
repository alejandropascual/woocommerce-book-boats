<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WC_Bookboat_Checkout_Manager {

    public function __construct() {

        // Checkout page, display meta data
        add_filter( 'woocommerce_get_item_data', array( $this, 'show_meta' ), 100, 2 );

        // Mail order, display pdf link
        add_action( 'woocommerce_order_item_meta_end', array($this, 'pdf_link_terms') , 20, 3);
    }

    public function show_meta( $item_data, $cart_item) {

        if (isset($cart_item['bookboats'])) {

            $keys = array(
                'Ride Date',
                'Ride Hour',
                'Ride Boats',
                'Ride TOTAL Travel Time',
                'Ride Start point - title',
                'Ride End point - title'
            );

            foreach( $keys as $key ) {
                $item_data[] = array(
                    'name'  => $key,
                    'key'   => $key,
                    'value' => $cart_item['bookboats'][$key]
                );
            }

        }

        return $item_data;
    }

    public function pdf_link_terms( $item_id, $item, $order ) {
        $product_id = intval( $item['product_id'] );
        $terms = wp_get_post_terms( $product_id, 'product_type', array() );
        if (isset($terms[0]) && $terms[0]->slug == 'bookboats') {
            $pdf_link = get_post_meta( $product_id, '_wc_bookboats_pdf_terms', true );
            if ($pdf_link) {
                echo '<div><a href="'.$pdf_link.'">'.__('Download PDF with Terms & Conditions','woocommerce-bookboats').'</a></div>';
            }

        }
    }

}

new WC_Bookboat_Checkout_Manager();