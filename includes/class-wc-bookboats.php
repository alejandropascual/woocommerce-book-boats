<?php

/**
 * Main model class for all bookings boats, this handles all the data
 */

class WC_Bookboat {

    /** @public int */
    public $id;

    /** @public bool */
    public $populated;

    /** @public int */
    public $customer_id;

    /** @public object */
    public $post;

    /** @public object */
    public $order;

    /** @public int */
    public $order_id;
    public $order_item_id;

    /** @public int */
    public $product_id;

    /** @public array */
    public $ride;

    /** @public string */
    public $status; // alert/awaiting/done
    public $mode; // reserved/free

    public function __construct( $booking_data = false ) {
        $populated = false;

        if ( is_array( $booking_data ) ) {

        } else if ( is_int( intval( $booking_data ) ) && 0 < $booking_data ) {

            $populated = $this->populate_data( $booking_data );

        } else if ( is_object( $booking_data ) && isset( $booking_data->ID ) ) {

        }

        $this->populated = $populated;
    }

    /**
     * Populate the data with the id of the booking provided
     * Will query for the post belonging to this booking and store it
     * @param int $booking_id
     */
    public function populate_data( $booking_id ) {
        global $wpdb;

        if ( ! isset( $this->post ) ) {
            $post = get_post( $booking_id );
        }

        if ( is_object( $post ) ) {

            $this->id = $post->ID;

            $this->order_id     = $post->post_parent;
            $this->order_item_id  = get_post_meta( $this->id, '_bookboat_order_item_id', true );
            $this->product_id   = get_post_meta( $this->id, '_bookboat_product_id', true );
            $this->customer_id  = $post->post_author;

            $this->status = get_post_meta( $this->id, '_bookboat_status', true );
            $this->mode = get_post_meta( $this->id, '_bookboat_mode', true );

            /* This gets the data Ride... from the order
            $q = "SELECT meta_key, meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta
                      WHERE order_item_id = {$this->order_item_id}
                      AND meta_key LIKE 'Ride%'";

            $results = $wpdb->get_results($q, ARRAY_A);
            $this->ride = array();
            foreach( $results as $item ) {
                $this->ride[$item['meta_key']] = $item['meta_value'];
            }
            */

            // This gets the data from the custom fields
            $custom_fields = get_post_meta( $this->id );
            $this->ride = array();
            foreach( $custom_fields as $key => $data ) {
                if ( strpos($key, 'Ride') === 0 ) {
                    $this->ride[$key] = $data[0];
                }
            }
            /*
                [Ride Date] => 2016-02-28
                [Ride Hour] => 08:45
                [Ride Boats] => 3
                [Ride Which Boats] => 1 2 3
                [Ride TOTAL COST] => 11100
                [Ride TOTAL Travel Time] => 90
                [Ride TOTAL Ghost Time before] => 15
                [Ride TOTAL Ghost Time after] => 50
                [Ride Start point - title] => Start Point 1
                [Ride Start point - cost] => 200
                [Ride Start point - time] => 15
                [Ride Start point - ghost time] => 15
                [Ride End point - title] => End point 2
                [Ride End point - cost] => 500
                [Ride End point - time] => 30
                [Ride End point - ghost time] => 30
                [Ride Extra time - title] => +15min
                [Ride Extra time - cost] => 300
                [Ride Extra time - time] => 15
                [Ride Extra time - ghost time] => 5
             */
            return true;
        }
        return false;
    }

    public function get_order() {

        if ( empty( $this->order ) ) {
            if ( $this->populated && ! empty( $this->order_id ) && 'shop_order' === get_post_type( $this->order_id ) ) {
                $this->order = wc_get_order( $this->order_id );
            } else {
                return false;
            }
        }
        return $this->order;
    }

    /**
     * Get the product ID for the booking
     * @return int or false if booking is not populated
     */
    public function get_product_id() {
        if ( $this->populated ) {
            return $this->product_id;
        }

        return false;
    }

    /**
     * Returns the object of the order corresponding to this booking
     * @return Product object or false if booking is not populated
     */
    public function get_product() {

        if ( empty( $this->product ) ) {
            if ( $this->populated && $this->product_id ) {
                $this->product = wc_get_product( $this->product_id );
            } else {
                return false;
            }
        }

        return $this->product;
    }

    /**
     * Returns information about the customer of this order
     * @return array containing customer information
     */
    public function get_customer() {
        if ( $this->populated ) {
            $order = $this->get_order();

            if ( $order )
                return (object) array(
                    'name'    => trim( $order->billing_first_name . ' ' . $order->billing_last_name ),
                    'email'   => $order->billing_email,
                    'user_id' => $order->customer_user,
                );
            elseif ( $this->customer_id ) {
                $user = get_user_by( 'id', $this->customer_id );

                return (object) array(
                    'name'    => $user->display_name,
                    'email'   => $user->user_email,
                    'user_id' => $this->customer_id
                );
            }
        }

        return false;
    }

    public function get_date_hour() {
        return $this->ride['Ride Date'].' '.$this->ride['Ride Hour'].':00';
    }
    public function get_number_boats() {
        return $this->ride['Ride Boats'];
    }
    public function get_which_boats() {
        return $this->ride['Ride Which Boats'];
    }
    public function get_travel_time() {
        return $this->ride['Ride TOTAL Travel Time'];
    }
    public function get_ghost_time_before(){
        return $this->ride['Ride TOTAL Ghost Time before'];
    }
    public function get_ghost_time_after(){
        return $this->ride['Ride TOTAL Ghost Time after'];
    }
    public function get_start_point() {
        return $this->ride['Ride Start point - title'];
    }
    public function get_end_point() {
        return $this->ride['Ride End point - title'];
    }


    public function get_status() {
        if (empty($this->status)){
            $this->update_status('awaiting');
        }
        return $this->status;
    }

    public function update_status( $status ) {
        update_post_meta( $this->id, '_bookboat_status', $status);
        $this->status = $status;
    }

    public function get_mode() {
        if (empty($this->mode)){
            $this->update_mode('reserved');
        }
        return $this->mode;
    }

    public function update_mode( $mode ) {
        update_post_meta( $this->id, '_bookboat_mode', $mode);
        $this->mode = $mode;
    }
}

