<?php
/**
 * Admin functions for the bookingboats post type
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Bookboats_CPT' ) ) :

/**
 * WC_Admin_CPT_Product Class
 */
class WC_Bookboats_CPT {

    public function __construct() {
        $this->type = 'wc_bookboat';

        // Admin Columns
        add_filter( 'manage_edit-' . $this->type . '_columns', array( $this, 'edit_columns' ) );
        add_action( 'manage_' . $this->type . '_posts_custom_column', array( $this, 'custom_columns' ), 2 );

    }

    public function edit_columns( $existing_columns ) {

        // Lo introduzco aqui, necesito los tips
        wp_enqueue_script('jquery-tiptip');
        wp_enqueue_script('wc_bookboats_admin_js');


        if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
            $existing_columns = array();
        }

        unset( $existing_columns['comments'], $existing_columns['title'], $existing_columns['date'] );

        $columns                    = array();
        $columns["bookboat_status"] = '<span class="status_head tips" data-tip="' . esc_attr__( 'Status', 'woocommerce-bookings' ) . '">' . esc_attr__( 'Status', 'woocommerce-bookings' ) . '</span>';
        $columns["bookboat_mode"]   = __( 'Mode', 'woocommerce-bookboats' );
        $columns["bookboat_id"]     = __( 'ID', 'woocommerce-bookboats' );
        $columns["booked_product"]  = __( 'Booked Product', 'woocommerce-bookboats' );
        $columns["order"]           = __( 'Order', 'woocommerce-bookboats' );
        $columns["customer"]        = __( 'Customer', 'woocommerce-bookboats' );
        $columns["date_boats"]      = __( 'Date/Boats', 'woocommerce-bookboats' );
        $columns["time"]            = __( 'Time / Ghost time before/after', 'woocommerce-bookboats' );
        $columns["points"]          = __( 'Start/End points', 'woocommerce-bookboats' );
        $columns["bookboat_actions"]         = __( 'Actions', 'woocommerce-bookboats' );

        //$columns["booking_status"]  = '<span class="status_head tips" data-tip="' . esc_attr__( 'Status', 'woocommerce-bookings' ) . '">' . esc_attr__( 'Status', 'woocommerce-bookings' ) . '</span>';
        //$columns["booking_id"]      = __( 'ID', 'woocommerce-bookings' );
        //$columns["num_of_persons"]  = __( '# of Persons', 'woocommerce-bookings' );
        //$columns["customer"]        = __( 'Booked By', 'woocommerce-bookings' );
        //$columns["order"]           = __( 'Order', 'woocommerce-bookings' );
        //$columns["start_date"]      = __( 'Start Date', 'woocommerce-bookings' );
        //$columns["end_date"]        = __( 'End Date', 'woocommerce-bookings' );
        //$columns["booking_actions"] = __( 'Actions', 'woocommerce-bookings' );

        return array_merge( $existing_columns, $columns );
    }

    public function custom_columns( $column ) {
        global $post, $bookboat;

        if ( empty($bookboat) || $bookboat->id != $post->ID ) {
            $bookboat = get_wc_bookboat( $post->ID );
        }

        $product  = $bookboat->get_product();

        switch ( $column ) {

            case 'bookboat_status' :
                $status = $bookboat->get_status();
                if ($status == 'awaiting') {
                    echo '<span class="bookboat-awaiting">'.__('Awaiting', 'woocommerce-bookboats').'</span>';
                } else if ($status == 'alert') {
                    echo '<span class="bookboat-alert">'.__('Alert', 'woocommerce-bookboats').'</span>';
                } else if ($status == 'done') {
                    echo '<span class="bookboat-done">'.__('Done', 'woocommerce-bookboats').'</span>';
                }
                break;

            case 'bookboat_mode' :
                $mode = $bookboat->get_mode();
                if ($mode == 'reserved') {
                    echo '<span class="bookboat-reserved">'.__('Reserved', 'woocommerce-bookboats').'</span>';
                } else if ($mode == 'free') {
                    echo '<span class="bookboat-free">'.__('Free', 'woocommerce-bookboats').'</span>';
                }
                break;

            case 'bookboat_id' :
                printf( '<a href="%s">' . __( 'Booking #%d', 'woocommerce-bookboats' ) . '</a>', admin_url( 'post.php?post=' . $post->ID . '&action=edit' ), $post->ID );
                break;

            case 'booked_product';
                if ( $product ) {
                    echo '<a href="' . admin_url( 'post.php?post=' . $product->id . '&action=edit' ) . '">' . $product->post->post_title . '</a>';
                } else {
                    echo '-';
                }
                break;

            case 'order';
                $order = $bookboat->get_order();
                if ( $order ) {
                    echo '<a href="' . admin_url( 'post.php?post=' . $order->id . '&action=edit' ) . '">#' . $order->get_order_number() . '</a> ( ' . esc_html( $order->status ) . ' )';
                } else {
                    echo '-';
                }
                break;

            case 'customer':
                $customer = $bookboat->get_customer();

                if ( $customer ) {
                    echo '<a href="mailto:' .  $customer->email . '">' . $customer->name . '<br>' . $customer->email . '</a>';
                } else {
                    echo '-';
                }
                break;

            case 'date_boats';
                echo $bookboat->get_date_hour().'<br>'.$bookboat->get_number_boats().' boats ('.$bookboat->get_which_boats().')';
                break;

            case 'time';
                echo $bookboat->get_travel_time().'min<br>'.$bookboat->get_ghost_time_before().'min / '.$bookboat->get_ghost_time_after() . 'min';
                break;

            case 'points';
                echo $bookboat->get_start_point().'<br>'.$bookboat->get_end_point();
                break;

            case 'bookboat_actions';
                $actions = array();
                $actions['view'] = array(
                    'url' 		=> admin_url( 'post.php?post=' . $post->ID . '&action=edit' ),
                    'name' 		=> __( 'View', 'woocommerce-bookboats' ),
                    'action' 	=> "view"
                );
                $actions['alert'] = array(
                    'url' 		=> wp_nonce_url( admin_url( 'admin-ajax.php?action=wc-bookboat-alert&bookboat_id=' . $post->ID ), 'wc-bookboat-alert' ),
                    'name' 		=> __( 'Alert', 'woocommerce-bookboats' ),
                    'action' 	=> "alert"
                );
                $actions['awaiting'] = array(
                    'url' 		=> wp_nonce_url( admin_url( 'admin-ajax.php?action=wc-bookboat-awaiting&bookboat_id=' . $post->ID ), 'wc-bookboat-awaiting' ),
                    'name' 		=> __( 'Awaiting', 'woocommerce-bookboats' ),
                    'action' 	=> "awaiting"
                );
                $actions['done'] = array(
                    'url' 		=> wp_nonce_url( admin_url( 'admin-ajax.php?action=wc-bookboat-done&bookboat_id=' . $post->ID ), 'wc-bookboat-done' ),
                    'name' 		=> __( 'Done', 'woocommerce-bookboats' ),
                    'action' 	=> "done"
                );
                $actions['reserved'] = array(
                    'url' 		=> wp_nonce_url( admin_url( 'admin-ajax.php?action=wc-bookboat-reserved&bookboat_id=' . $post->ID ), 'wc-bookboat-reserved' ),
                    'name' 		=> __( 'Reserved', 'woocommerce-bookboats' ),
                    'action' 	=> "reserved"
                );
                $actions['free'] = array(
                    'url' 		=> wp_nonce_url( admin_url( 'admin-ajax.php?action=wc-bookboat-free&bookboat_id=' . $post->ID ), 'wc-bookboat-free' ),
                    'name' 		=> __( 'Free', 'woocommerce-bookboats' ),
                    'action' 	=> "free"
                );

                $status = $bookboat->get_status();
                if ($status == 'awaiting') {
                    unset($actions['awaiting']);
                } else if ($status == 'alert') {
                    unset($actions['alert']);
                } else if ($status == 'done') {
                    unset($actions['done']);
                }

                $mode = $bookboat->get_mode();
                if ($mode == 'reserved') {
                    unset($actions['reserved']);
                } else if ($mode == 'free') {
                    unset($actions['free']);
                }

                $actions = apply_filters( 'woocommerce_admin_bookboat_actions', $actions, $bookboat );

                echo '<p>';
                foreach ( $actions as $action ) {
                    printf( '<a class="button tips %s" href="%s" data-tip="%s">%s</a>', esc_attr( $action['action'] ), esc_url( $action['url'] ), esc_attr( $action['name'] ), esc_attr( $action['name'] ) );
                }
                echo '</p>';

                break;
        }
    }
}

endif;
return new WC_Bookboats_CPT();

