<?php

if ( ! defined( 'ABSPATH' ) )
    exit;

/**
 * WC_Bookboats_Menus
 */
class WC_Bookboats_Menus {

    public function __construct() {

        add_action( 'admin_menu', array( $this, 'remove_default_add_booking_url' ), 10 );
        add_action( 'admin_menu', array( $this, 'admin_menu' ), 49 );
    }

    public function admin_menu() {

        //$test_page = add_submenu_page( 'edit.php?post_type=wc_bookboat', __( 'Testing', 'woocommerce-bookboats' ), __( 'Testing', 'woocommerce-bookboats' ), 'manage_woocommerce', 'wc_bookboats_testing', array( $this, 'testing_page' ) );
        $create_booking_page = add_submenu_page( 'edit.php?post_type=wc_bookboat', __( 'Create Booking', 'woocommerce-bookboats' ), __( 'Create Booking', 'woocommerce-bookboats' ), 'manage_woocommerce', 'wc_create_bookboat', array( $this, 'create_bookboat_page' ) );
        $calendar_page = add_submenu_page( 'edit.php?post_type=wc_bookboat', __( 'Calendar', 'woocommerce-bookboats' ), __( 'Calendar', 'woocommerce-bookboats' ), 'manage_woocommerce', 'wc_bookboats_calendar', array( $this, 'calendar_page' ) );
    }

    public function testing_page() {

        echo '<h1>TESTING</h1>';

        /*
        $product_id = 160;
        $product = new WC_Product_Bookboats($product_id);
        $date = '2016-02-28';
        $list_orders = $product->get_list_orders_bookboats_for_date( $product_id, $date );
        echo '<pre>'; print_r( $list_orders ); echo '</pre>';
        */

        //$bookboat = get_wc_bookboat(261);
        //echo '<pre>'; print_r( $bookboat->order_id ); echo '</pre>';
        //echo '<pre>'; print_r( $bookboat->ride ); echo '</pre>';


        /*
        $product = new WC_Product_Bookboats(160);

        $data = $product->get_list_orders_bookboats_for_date(160, '2016-02-28');
        echo '<pre>'; print_r( $data ); echo '</pre>';

        $data = $product->get_list_booking_boats_for_date(160, '2016-02-28');
        echo '<pre>'; print_r( $data ); echo '</pre>';

        $result = $product->get_list_free_hours_for_date('2016-02-28', 3, 90, 30,30);
        echo '<pre>'; print_r( $result ); echo '</pre>';
        */

    }

    public function calendar_page() {
        require_once( 'class-wc-bookboats-calendar.php' );
        $page = new WC_Bookboats_Calendar();
        $page->output();
    }

    public function remove_default_add_booking_url() {
        global $submenu;

        if ( isset( $submenu['edit.php?post_type=wc_bookboat'] ) ) {
            foreach ( $submenu['edit.php?post_type=wc_bookboat'] as $key => $value ) {
                if ( 'post-new.php?post_type=wc_bookboat' == $value[2] ) {
                    unset( $submenu['edit.php?post_type=wc_bookboat'][ $key ] );
                    return;
                }
            }
        }
    }

    public function create_bookboat_page() {
        require_once( 'class-wc-bookboats-create.php' );
        $page = new WC_Bookboats_Create();
        $page->output();
    }
}

new WC_Bookboats_Menus();