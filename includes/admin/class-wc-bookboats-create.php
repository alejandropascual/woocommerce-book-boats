<?php
/**
 * Create new bookings page
 */
class WC_Bookboats_Create {

    private $errors = array();

    public function output() {

        $this->errors = array();

        try {

            if ( ! empty( $_POST ) && ! check_admin_referer( 'create_bookboat_notification' ) ) {
                throw new Exception( __( 'Error - please try again', 'woocommerce-bookboats' ) );
            }

            if ( ! empty( $_POST['create_booking'] ) ) {

                $customer_id         = absint( $_POST['customer_id'] );
                $bookable_product_id = absint( $_POST['bookable_product_id'] );

                if ( ! $bookable_product_id ) {
                    throw new Exception( __( 'Please choose a bookable product', 'woocommerce-bookboats' ) );
                }

                $product = wc_get_product( $bookable_product_id );

                $date = $_POST['date'];
                $hour_start = $_POST['hour_start'];
                $hour_end = $_POST['hour_end'];
                $boats = $_POST['boats'];

                // Create bookboat booking
                $bookboat_id = wp_insert_post(array(
                    'post_type' => 'wc_bookboat',
                    'post_title' => 'Booking',
                    'post_status' => 'publish',
                    'post_parent' => 0,
                    'post_author' => $customer_id
                ));

                //echo '<pre>$bookboat_id => '; print_r( $bookboat_id ); echo '</pre>';

                wp_update_post(array(
                    'ID' => $bookboat_id,
                    'post_title' => 'Manual Booking #'.$bookboat_id
                ));

                update_post_meta( $bookboat_id, '_bookboat_customer_id', $customer_id);
                update_post_meta( $bookboat_id, '_bookboat_product_id', $bookable_product_id);

                update_post_meta( $bookboat_id, '_bookboat_status', 'awaiting'); // alert/awaiting/done
                update_post_meta( $bookboat_id, '_bookboat_mode', 'reserved'); // reserved/free

                $start_date = $date.$hour.'00';
                $start_date = str_replace(array('-',' ',':'),'',$start_date);
                update_post_meta( $bookboat_id, '_bookboat_start_date', $start_date);

                $meta = array(
                    'Ride Date' => $date,
                    'Ride Hour'	=> $hour_start,
                    'Ride Boats' =>	strlen( str_replace(' ','',$boats) ),
                    'Ride Which Boats' => $boats,
                    'Ride TOTAL COST' => 0,
                    'Ride TOTAL Travel Time' => WC_Product_Bookboats::hour_to_number( $hour_end ) - WC_Product_Bookboats::hour_to_number( $hour_start ),
                    'Ride TOTAL Ghost Time before' => 0,
                    'Ride TOTAL Ghost Time after' => 0,
                    'Ride Start point - title' => '',
                    'Ride Start point - cost' => 0,
                    'Ride Start point - time' => 0,
                    'Ride Start point - ghost time'	=> 0,
                    'Ride End point - title	End point' => '',
                    'Ride End point - cost'	=> 0,
                    'Ride End point - time'	=> 0,
                    'Ride End point - ghost time' => 0,
                    'Ride Extra time - title' => '',
                    'Ride Extra time - cost' => 0,
                    'Ride Extra time - time' => 0,
                    'Ride Extra time - ghost time' => 0
                );

                foreach( $meta as $key => $value ) {
                    update_post_meta( $bookboat_id, $key, $value );
                }


                // Redirect gives an error, so just a message
                //wp_redirect( admin_url( 'post.php?post=' . $bookboat_id . '&action=edit' ) );
                //exit;

                echo '<h3>Booking has been created <a href="'.admin_url( 'post.php?post=' . $bookboat_id . '&action=edit' ).'">EDIT Booking '.$bookboat_id.'</a></h3>';

            }

        } catch ( Exception $e ) {
            $this->errors[] = $e->getMessage();
        }


        // Select product
        // From product -> get hours step, num of boats
        // Select user
        // Select date
        // Select start hour -> ghost before = 0
        // Select end hour -> ghost after = 0
        // Select boats
        // Save

        include( 'views/html-create-bookboat-page.php' );

    }

    public function show_errors() {
        foreach ( $this->errors as $error )
            echo '<div class="error"><p>' . esc_html( $error ) . '</p></div>';
    }

}