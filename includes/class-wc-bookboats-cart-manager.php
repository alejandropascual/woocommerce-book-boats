<?php
if ( ! defined( 'ABSPATH' ) )
    exit;

class WC_Bookboats_Cart_Manager {

    public function __construct()
    {
        add_action('woocommerce_bookboats_add_to_cart', array($this, 'add_to_cart'), 30);
        add_filter('woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 10, 1 );
        add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 10, 3 );

        add_filter('woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );

        add_action( 'woocommerce_add_order_item_meta', array( $this, 'order_item_meta' ), 50, 2 );

        add_action( 'woocommerce_cart_item_removed', array( $this, 'cart_item_removed' ), 20 );
    }

    public function add_to_cart() {
        global $product;

        // Prepare form
        $bookboats_form = new WC_Bookboats_form( $product );

        wc_get_template( 'single-product/add-to-cart/bookboats.php', array('bookboats_form' => $bookboats_form ), 'woocommerce-bookboats', WC_BOOKBOATS_TEMPLATE_PATH );
    }

    public function add_cart_item( $cart_item ) {

        if ( ! empty( $cart_item['bookboats'] ) && ! empty( $cart_item['bookboats']['_cost'] ) ) {
            $cart_item['data']->set_price( $cart_item['bookboats']['_cost'] );
        }

        //echo '<pre>'; print_r( $cart_item ); echo '</pre>';
        return $cart_item;
    }

    public function get_cart_item_from_session( $cart_item, $values, $cart_item_key ) {
        if ( ! empty( $values['bookboats'] ) ) {
            $cart_item['bookboats'] = $values['bookboats'];
            $cart_item            = $this->add_cart_item( $cart_item );
        }
        return $cart_item;
    }

    public function add_cart_item_data( $cart_item_meta, $product_id ) {

        $product = wc_get_product( $product_id );

        if ( 'bookboats' !== $product->product_type ) {
            return $cart_item_meta;
        }

        $bookboats_form                         = new WC_Bookboats_Form( $product );
        $cart_item_meta['bookboats']            = $bookboats_form->get_posted_data( $_POST );
        $cart_item_meta['bookboats']['_cost']   = $bookboats_form->calculate_booking_cost( $_POST );

        // Create new booking

        return $cart_item_meta;
    }

    /**
     * Before delete
     */
    public function cart_item_removed( $cart_item_key ) {
        $cart_item = WC()->cart->removed_cart_contents[ $cart_item_key ];

        if ( isset( $cart_item['bookboats'] ) ) {
            // TODO
        }
    }

    /**
     * Restore item
     */
    public function cart_item_restored( $cart_item_key ) {
        $cart      = WC()->cart->get_cart();
        $cart_item = $cart[ $cart_item_key ];

        if ( isset( $cart_item['bookboats'] ) ) {
            // TODO
        }
    }

    // Añade los datos al item meta
    // Aprovecho para crear el wc_bookboat aqui
    public function order_item_meta( $item_id, $values ) {
        global $wpdb;

        //echo '<pre>VALUES -> '; print_r( $values ); echo '</pre>'; exit();
        /*
         VALUES -> Array (
                [bookboats] => Array
                    (
                        [_qty] => 1
                        [Ride Date] => 2016-02-29
                        [Ride Hour] => 06:00
                        [Ride Boats] => 1
                        [Ride Which Boats] => 1
                        [Ride TOTAL COST] => 2700
                        [Ride TOTAL Travel Time] => 30
                        [Ride TOTAL Ghost Time before] => 0
                        [Ride TOTAL Ghost Time after] => 15
                        [Ride Start point - title] => Normal
                        [Ride Start point - cost] => 0
                        [Ride Start point - time] => 0
                        [Ride Start point - ghost time] => 0
                        [Ride End point - title] => Normal
                        [Ride End point - cost] => 0
                        [Ride End point - time] => 0
                        [Ride End point - ghost time] => 0
                        [Ride Extra time - title] => No extra time
                        [Ride Extra time - cost] => 0
                        [Ride Extra time - time] => 0
                        [Ride Extra time - ghost time] => 0
                        [_cost] => 2700
                    )

                [product_id] => 160
                [variation_id] => 0
                [variation] => Array
                    (
                    )

                [quantity] => 1
                [line_total] => 2700
                [line_tax] => 0
                [line_subtotal] => 2700
                [line_subtotal_tax] => 0
                [line_tax_data] => Array
                    (
                        [total] => Array
                            (
                            )

                        [subtotal] => Array
                            (
                            )

                    )

                [data] => WC_Product_Bookboats Object
                    (
                        [id] => 160
                        [post] => WP_Post Object
                            (
                                [ID] => 160
                                [post_author] => 1
                                [post_date] => 2015-11-03 20:39:36
                                [post_date_gmt] => 2015-11-03 20:39:36
                                [post_content] =>
                                [post_title] => Book Vessels
                                [post_excerpt] =>
                                [post_status] => publish
                                [comment_status] => open
                                [ping_status] => closed
                                [post_password] =>
                                [post_name] => reservation
                                [to_ping] =>
                                [pinged] =>
                                [post_modified] => 2015-12-02 13:38:17
                                [post_modified_gmt] => 2015-12-02 13:38:17
                                [post_content_filtered] =>
                                [post_parent] => 0
                                [guid] => http://localhost/_CODEABLE/wordpress/?post_type=product&#038;p=160
                                [menu_order] => 0
                                [post_type] => product
                                [post_mime_type] =>
                                [comment_count] => 0
                                [filter] => raw
                            )

                        [product_type] => bookboats
                        [shipping_class:protected] =>
                        [shipping_class_id:protected] => 0
                        [price] => 2700
                        [tax_status] => taxable
                        [manage_stock] => no
                        [stock_status] => instock
                    )

            )
         */

        if ( ! empty( $values['bookboats'] ) ) {

            $product        = $values['data'];
            $ride_data      = $values['bookboats'];

            // Create CPT wc_bookboat
            $order_id       = $wpdb->get_var( $wpdb->prepare( "SELECT order_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = %d", $item_id ) );
            $order_item_id  = $item_id;

            // Get wc_bookboat
            $bookboat_id = get_wc_bookboat_from_order( $order_id, $order_item_id );
            if ( !$bookboat_id ) {

                // TODO -> Deberia hacerse dentro de class-wc-bookboats.php
                // Create wc_bookboat
                $bookboat_id = wp_insert_post(array(
                    'post_type' => 'wc_bookboat',
                    'post_title' => 'Booking',
                    'post_status' => 'publish',
                    'post_parent' => $order_id,
                    'post_author' => get_current_user_id(),
                    //'post_date' => $ride_data['Ride Date'].' '.$ride_data['Ride Hour'].':00' // Como las fechas son a futuro se publican como future en vez de publish
                ));
                wp_update_post(array(
                    'ID' => $bookboat_id,
                    'post_title' => 'Booking #'.$bookboat_id
                ));

                update_post_meta( $bookboat_id, '_bookboat_customer_id', get_current_user_id());
                update_post_meta( $bookboat_id, '_bookboat_order_id', $order_id);
                update_post_meta( $bookboat_id, '_bookboat_order_item_id', $order_item_id);
                update_post_meta( $bookboat_id, '_bookboat_product_id', $values['product_id']);

                update_post_meta( $bookboat_id, '_bookboat_status', 'awaiting'); // alert/awaiting/done
                update_post_meta( $bookboat_id, '_bookboat_mode', 'reserved'); // reserved/free

                // Save RIDE data also for searching free hours
                foreach( $ride_data as $key => $value ) {
                    if ( strpos($key,'Ride') === 0 ) {
                        update_post_meta( $bookboat_id, $key, $value );
                    }
                }

                // I need this format for the date for searching ranges
                $start_date = $ride_data['Ride Date'].$ride_data['Ride Hour'].'00';
                $start_date = str_replace(array('-',' ',':'),'',$start_date);
                update_post_meta( $bookboat_id, '_bookboat_start_date', $start_date);
            }


            // Save order item meta data only Ride... data
            /*foreach( $ride_data as $key => $value ) {
                if ( strpos($key,'Ride') === 0 ) {
                    wc_add_order_item_meta( $item_id, $key, $bookboats[$key] );
                }
            }*/

            // Better save only specific data in the order
            $keys = array(
                'Ride Date',
                'Ride Hour',
                'Ride Boats',
                'Ride TOTAL Travel Time',
                'Ride Start point - title',
                'Ride End point - title',
                'Ride Extra time - title'
            );
            foreach( $keys as $key ) {
                wc_add_order_item_meta( $item_id, $key, $ride_data[$key] );
            }


        }
    }
}

new WC_Bookboats_Cart_Manager();