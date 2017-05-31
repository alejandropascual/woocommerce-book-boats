<?php

if ( ! defined( 'ABSPATH' ) )
    exit;


class WC_Bookboats_Ajax {

    public function __construct() {

        add_action( 'wp_ajax_wc-bookboat-alert', array( $this, 'mark_bookboat_alert' ) );
        add_action( 'wp_ajax_wc-bookboat-awaiting', array( $this, 'mark_bookboat_awaiting' ) );
        add_action( 'wp_ajax_wc-bookboat-done', array( $this, 'mark_bookboat_done' ) );
        add_action( 'wp_ajax_wc-bookboat-reserved', array( $this, 'mark_bookboat_reserved' ) );
        add_action( 'wp_ajax_wc-bookboat-free', array( $this, 'mark_bookboat_free' ) );

        add_action( 'wp_ajax_wc_bookboats_get_hours', array( $this, 'get_list_hours_available' ) );
        add_action( 'wp_ajax_nopriv_wc_bookboats_get_hours', array( $this, 'get_list_hours_available' ) );

    }


    // Actions change status wc_bookboat
    public function mark_bookboat_alert() {
        $this->process_ajax_bookboat( 'status', 'wc-bookboat-alert', 'alert' );
    }
    public function mark_bookboat_awaiting() {
        $this->process_ajax_bookboat( 'status', 'wc-bookboat-awaiting', 'awaiting' );
    }
    public function mark_bookboat_done() {
        $this->process_ajax_bookboat( 'status', 'wc-bookboat-done', 'done' );
    }

    // Actions change mode wc_bookboat
    public function mark_bookboat_reserved() {
        $this->process_ajax_bookboat( 'mode', 'wc-bookboat-reserved', 'reserved' );
    }
    public function mark_bookboat_free() {
        $this->process_ajax_bookboat( 'mode', 'wc-bookboat-free', 'free' );
    }

    // Change status / mode
    public function process_ajax_bookboat( $type, $nonce, $status ) {
        if ( ! check_admin_referer( $nonce ) ) {
            wp_die( __( 'You have taken too long. Please go back and retry.', 'woocommerce-bookboats' ) );
        }
        $bookboat_id = isset( $_GET['bookboat_id'] ) && (int) $_GET['bookboat_id'] ? (int) $_GET['bookboat_id'] : '';
        if ( ! $bookboat_id ) { die; }

        $bookboat = get_wc_bookboat( $bookboat_id );

        if ( $type== 'status') {
            if ( $bookboat->get_status() !== $status ) {
                $bookboat->update_status( $status );
            }
        } else if ( $type == 'mode' ) {
            if ( $bookboat->get_mode() !== $status ) {
                $bookboat->update_mode( $status );
            }
        }

        //echo '<pre>STATUS = '; print_r( $bookboat->get_status() ); echo '</pre>';
        //echo '<pre>MODE = '; print_r( $bookboat->get_mode() ); echo '</pre>';

        wp_safe_redirect( wp_get_referer() );
    }




    // Get list of available hours given a date, etc..
    public function get_list_hours_available() {

        $product_id = intval($_POST['product_id']);
        $date = $_POST['date'];
        $num_boats = intval( $_POST['num_boats'] );
        $travel_time = intval( $_POST['travel_time'] );
        $ghost_time_before = intval( $_POST['ghost_time_before'] );
        $ghost_time_after = intval( $_POST['ghost_time_after'] );

        //$list = $this->get_list_free_hours_for_date( $product_id, $date, $num_boats, $travel_time, $ghost_time_before, $ghost_time_after );

        $product = new WC_Product_Bookboats($product_id);
        $list = $product->get_list_free_hours_for_date( $date, $num_boats, $travel_time, $ghost_time_before, $ghost_time_after );

        if (empty($list)) {

            wp_send_json_success( array('message'=>'<h3 style="color:red; margin: 5px 0px;">NON AVAILABLE</h3>'));

        } else {

            wp_send_json_success( array(
                    'message'=>'ok',
                    'list' => $list
                )
            );

        }

        // Pruebas
        //echo '<pre>'; print_r( $_POST ); echo '</pre>';


        $list = array(
            array(
                'label' => '10:15',
                'value' => '10:15',
                'extra_price_boat' => 0,
                'desc' => '',
                'boats' => array(2,4,3,1)
            ),
            array(
                'label' => '10:30',
                'value' => '10:30',
                'extra_price_boat' => -200,
                'desc' => 'This has a reduced priced of -200DKK/boat',
                'boats' => array(1,2,3,4)
            ),
            array(
                'label' => '10:45',
                'value' => '10:45',
                'extra_price_boat' => 300,
                'desc' => 'This has an extra price of 300DKK/boat',
                'boats' => array(4,3,2,1)
            )
        );


        //wp_send_json_success( array('message'=>'<h3 style="color:red; margin: 5px 0px;">NON AVAILABLE</h3>'));


        wp_send_json_success( array(
                'message'=>'ok',
                'list' => $list
            )
        );
    }


    // UTILS PARA CALCULAR HORAS DISPONIBLES CONSULTANDO LAS ORDERS
    //-----------------------------------------


    // Get array of orders with all the metadata 'Ride...'
    function get_list_orders_bookboats_for_date( $product_id, $date ) {

        // Get list orders that are cmpleted
        global $wpdb;
        $q = "SELECT oi.order_id,oim.order_item_id  FROM {$wpdb->prefix}woocommerce_order_itemmeta oim
              INNER JOIN {$wpdb->prefix}woocommerce_order_items oi
              ON oi.order_item_id = oim.order_item_id
              INNER JOIN {$wpdb->posts} p
              ON p.ID = oi.order_id
              INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta oimm
              ON oimm.order_item_id = oim.order_item_id
              WHERE oim.meta_key = 'Ride Date' AND oim.meta_value = '{$date}'
              AND oimm.meta_key = '_product_id' AND oimm.meta_value = '{$product_id}'
              AND p.post_status = 'wc-completed'
              ";

        $result = array();
        $list = $wpdb->get_results( $q, ARRAY_A );

        // Get the meta data of the Ride
        if ($list) {
            foreach( $list as $order ) {

                $q = "SELECT meta_key, meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta
                      WHERE order_item_id = {$order['order_item_id']}
                      AND meta_key LIKE 'Ride%'";
                $metas = $wpdb->get_results($q, ARRAY_A);
                //echo '<pre>'; print_r( $metas ); echo '</pre>';
                if ($metas) {
                    foreach( $metas as $meta ) {
                        $order[$meta['meta_key']] = $meta['meta_value'];
                    }
                    $result[] = $order;
                }
            }
        }

        return $result;
    }


    function get_list_boats_booked_for_date( $product_id, $date ) {

        $result_boats = array();

        // Get list orders with metadata
        $list_orders = $this->get_list_orders_bookboats_for_date( $product_id, $date );
        //echo '<pre>'; print_r( $list_orders ); echo '</pre>';

        // Prepare default list of boats
        $num_boats = get_post_meta( $product_id, '_wc_bookboats_num_boats', true);
        for ($index=1; $index <= $num_boats; $index++) {
            $result_boats['boat-'.$index] = array();
        }

        // Get default not available hours
        $non_available_hours = get_post_meta( $product_id, '_wc_bookboats_non_available_hours', true);
        //echo '<pre>'; print_r( $non_available_hours ); echo '</pre>';

        foreach($non_available_hours as $range) {
            $from = $range['from'];
            $to = $range['to'];
            if ( $to == '00:00' ) $to = '24:00';
            for ($index=1; $index <= $num_boats; $index++) {
                $result_boats['boat-'.$index][] = $from . '-' . $to;
            }
        }

        // Add the orders non-available hours
        foreach( $list_orders as $the_order ) {

            $travel_hour = $the_order['Ride Hour'];
            $travel_minutes = intval( $the_order['Ride TOTAL Travel Time'] );
            $ghost_before = intval( $the_order['Ride TOTAL Ghost Time before'] );
            $ghost_after = intval( $the_order['Ride TOTAL Ghost Time after'] );

            $start = $this->hour_to_number($travel_hour) - $ghost_before;
            $end = $start + $ghost_before + $travel_minutes + $ghost_after;
            $range = $this->interval_from_numbers_to_hours( array($start, $end) );
            $range_str = $range[0].'-'.$range[1];

            //echo '<pre>HOUR: '; print_r( $travel_hour ); echo '</pre>';
            //echo '<pre>HOUR NUMBER: '; print_r( $this->hour_to_number($travel_hour) ); echo '</pre>';
            //echo '<pre>Total minutes: '; print_r( $travel_minutes ); echo '</pre>';
            //echo '<pre>Ghost before: '; print_r( $ghost_before ); echo '</pre>';
            //echo '<pre>Ghost after: '; print_r( $ghost_after ); echo '</pre>';
            //echo '<pre>RANGE: '; print_r( $range_str ); echo '</pre>';

            $which_boats = $the_order['Ride Which Boats'];
            //echo '<pre>$which_boats '; print_r( $which_boats ); echo '</pre>';
            $which_boats = explode(' ',$which_boats);
            //echo '<pre>$which_boats '; print_r( $which_boats ); echo '</pre>';

            foreach( $which_boats as $boat_index ) {
                if (empty($boat_index)) continue;
                $boat_index = intval($boat_index);
                if (isset($result_boats['boat-'.$boat_index])) {
                    $result_boats['boat-'.$boat_index][] = $range_str;
                }
            }
        }

        return $result_boats;
    }



    // Get the raw list of free hours to book
    function get_raw_free_hours_for_date( $product_id, $date = '2015-12-01', $boats_needed = 1, $duration = 90 ) {

        $boats_booked = $this->get_list_boats_booked_for_date( $product_id, $date );
        $step_minutes = intval( get_post_meta($product_id, '_wc_bookboats_book_every_minutes', true) );

        //echo '<pre>$boats_booked '; print_r( $boats_booked ); echo '</pre>';
        $free_hours = $this->get_available_times_for_boats( $boats_needed, $duration, $boats_booked, $step_minutes );
        return $free_hours;
    }

    // GET THE CUSTOMIZED LIST FOR AJAX OF AVAILABLE DATES
    function get_list_free_hours_for_date(
        $product_id,
        $date = '2015-12-01',
        $boats_needed = 1,
        $travel_time = 60,
        $ghost_time_before = 30,
        $ghost_time_after = 30
    ) {

        $duration = intval($travel_time) + intval($ghost_time_before) + intval($ghost_time_after);
        $raw_list = $this->get_raw_free_hours_for_date( $product_id, $date, $boats_needed, $duration );
        $final_list = array();

        foreach( $raw_list as $item ) {

            $start_hour_ghost = $item['range_time'][0]; // 17:45
            $start_hour = $this->number_to_hour($this->hour_to_number($start_hour_ghost)+intval($ghost_time_before));

            // Extra cost
            $extra_cost = $this->get_extra_cost_for_hour( $product_id, $date, $start_hour );

            $final_list[] = array(
                'label' => $start_hour,
                'value' => $start_hour,
                'extra_price_boat' => $extra_cost['extra_price_boat'],
                'desc' => $extra_cost['desc'],
                'boats' => array_map( function($val){ return str_replace('boat-','',$val); }, $item['boats_index'])
            );
        }

        // Filter the list with the -Minimum time to book-
        // only minutes & ahours, because days, weeks or months was taken
        // into acount when the list of dates available

        $minimun_date_unit = get_post_meta( $product_id, '_wc_bookboats_min_date_unit', true );

        if ( $minimun_date_unit == 'minute' ||  $minimun_date_unit == 'hour' ) {

            $final_list_filtered = array();

            $min_date = intval( get_post_meta( $product_id, '_wc_bookboats_min_date', true ) );
            $after_time = 0;
            if ( $minimun_date_unit == 'minute' ) {
                $after_time = $min_date * 60;
            } else {
                $after_time = $min_date * 3600;
            }
            $future_time = time() + $after_time;

            // Enumerate each element in the list
            foreach( $final_list as $item ) {
                $hora_str = $date . ' ' . $item['value'] . ':00';
                $hora_time = strtotime( $hora_str );
                if ( $hora_time > $future_time ) {
                    $final_list_filtered[] = $item;
                }
            }

        } else {
            $final_list_filtered = $final_list;
        }

        // Result
        return $final_list_filtered;
    }


    // Check if this hour has extra cost
    function get_extra_cost_for_hour( $product_id, $date, $hour ) {

        $result = array(
            'extra_price_boat' => 0,
            'desc' => ''
        );

        $extras = get_post_meta( $product_id, '_wc_bookboats_pricing', true );
        //echo '<pre>'; print_r( $extras ); echo '</pre>';

        foreach( $extras as $extra ) {

            $time_from = strtotime( $extra['from'] );
            $time_to = strtotime( $extra['to'] );
            $time_date = strtotime( $date );

            if ( $time_date >= $time_from && $time_date <= $time_to ) {

                $minutes_from = $this->hour_to_number( $extra['from_time'] );
                $minutes_to = $this->hour_to_number( $extra['to_time'] );
                $minutes_hour = $this->hour_to_number( $hour );

                if ( $minutes_hour >= $minutes_from && $minutes_hour <= $minutes_to ) {
                    $value = floatval( $extra['base_cost'] );
                    if ($extra['base_cost_modifier'] == 'minus') {
                        $value *= -1;
                    }
                    $result['extra_price_boat'] = $value;
                    $result['desc'] = $extra['description'];
                }
            }
        }


        return $result;
    }

    // UTILS PARA CALCULAR HORAS DISPONIBLES DADOS LOS INTERVALOS OCUPADOS
    //-----------------------------------------


    // Obtener los horarios libres de los barcos
    /*  EJEMPLO:
        $boats_booked = array(
            array('00:00-06:00','09:30-12:00','20:00-24:00'),
            array('00:00-05:00','14:00-24:00'),
            array('00:00-07:00','16:00-24:00'),
            array('00:00-05:00','220:00-24:00')
        );
        $num_boats = 1;
        $duration = 120;
        $free_hours = get_available_times_for_boats( $num_boats, $duration, $boats_booked, 15 );
     */
    function get_available_times_for_boats( $boats_needed = 1, $minutes_needed = 60, $time_booked, $every_minutes = 15 ) {

        // Pasar a numeros los intervalos ocupados teniendo en cuenta el step_minutes
        $booked_minutes_boats = array();

        foreach( $time_booked as $arr_intervals ) {
            $row = array();
            foreach($arr_intervals as $interval) {
                $row[] = $this->interval_from_hours_to_numbers( $interval, 1 );
            }
            $booked_minutes_boats[] = $row;
        }
        //echo '<pre>'; print_r($booked_minutes_boats); echo '</pre>';

        // Procesar desde 00:00 hasta 24:00 cada step y ver si esta libre
        $result_intervals = array();

        // 1 dia = 1440 minutes
        for( $i=0; $i<1440; $i+=$every_minutes) {

            $range_needed = array( $i, $i+$minutes_needed );
            //echo '<p>BUSCANDO EL INTERVALO '.$i.' - '.($i+$minutes_needed).'</p>';
            //echo '<pre>'; print_r( aps_interval_from_numbers_to_hours($range_needed,1) ); echo '</pre>';

            $available_num_of_boats = 0;
            $available_index_of_boats = array();

            $index = 1;
            foreach( $booked_minutes_boats as $day_boat ) {

                $available_for_this_boat = true;
                foreach( $day_boat as $time_boat_occupied ) {
                    if ( $this->is_overlap_interval( $range_needed, $time_boat_occupied, 1440 ) ) {
                        $available_for_this_boat = false;
                    }
                }

                if ($available_for_this_boat) {
                    $available_num_of_boats++;
                    $available_index_of_boats[] = 'boat-'.$index;
                }

                $index++;

            }

            if ( $available_num_of_boats >= $boats_needed ) {
                $result_intervals[] = array(
                    'range_minutes' => $range_needed,
                    'range_time' => $this->interval_from_numbers_to_hours( $range_needed, 1),
                    'boats_num' => $available_num_of_boats,
                    'boats_index' => $available_index_of_boats
                );
            }

        }

        return $result_intervals;

    }

    // Comprobar si solapa con el intervalo
    // $needle = array( 50,81)
    // $interval = array( 80,90)
    function is_overlap_interval( $needle = array(), $interval = array(), $top = 1440) {

        //Error, necesito intervalos
        if ( count($needle) != 2 || count($interval) != 2 ) return true;

        // Se sale de las 24 horas ? condicion de limite superior
        if ( $needle[1] > $top) { return true; }

        // Dentro del intervalo ?
        if ( ( $needle[0] > $interval[0] && $needle[0] < $interval[1] ) ||
            ( $needle[1] > $interval[0] && $needle[1] < $interval[1] ) ||
            ( $needle[0] <= $interval[0] && $needle[1] >= $interval[1] )
        ) {
            return true;
        }


        // Outside of interval
        return false;
    }


    // Si por ejemplo step = 15 entonces el intervalo es de 0 a 96
    // Para minutos totals poner step = 1, el intervalo seria 0-1440 (dia)
    function interval_from_hours_to_numbers( $hours = '00:00-24:00', $step = 1 ) {

        $data = explode('-', $hours);
        if (count($data) != 2) return false;

        $start = explode(':', $data[0] );
        if (count($start) != 2) return false;

        $end = explode(':', $data[1] );
        if (count($end) != 2) return false;

        $steps_hour = 60/$step;

        $start_number = intval( $steps_hour * intval($start[0]) + intval($start[1]) / $step );
        $end_number = intval( $steps_hour * intval($end[0]) + intval($end[1]) / $step );

        return array( $start_number, $end_number );
    }


    function interval_from_numbers_to_hours( $interval = array(0,1440), $step = 1 ) {

        $hour_steps = 60 / $step;

        $hour = intval( $interval[0] / $hour_steps );
        $minutes = $step * ( $interval[0] - $hour * $hour_steps );
        $start = ( $hour < 10 ? '0'.$hour : $hour ) . ':' . ( $minutes < 10 ? '0'.$minutes : $minutes );

        $hour = intval( $interval[1] / $hour_steps );
        $minutes = $step * ( $interval[1] - $hour * $hour_steps );
        $end = ( $hour < 10 ? '0'.$hour : $hour ) . ':' . ( $minutes < 10 ? '0'.$minutes : $minutes );

        return array($start,$end);
    }

    function hour_to_number( $the_hour = '00:00', $step = 1 ) {

        $range = explode(':', $the_hour );
        if (count($range) != 2) return 0;

        $steps_hour = 60/$step;
        $hour_number = intval( $steps_hour * intval($range[0]) + intval($range[1]) / $step );
        return $hour_number;
    }

    function number_to_hour( $number = 0, $step = 1 ) {

        $hour_steps = 60 / $step;
        $hour = intval( $number / $hour_steps );
        $minutes = $step * ( $number - $hour * $hour_steps );
        $start = ( $hour < 10 ? '0'.$hour : $hour ) . ':' . ( $minutes < 10 ? '0'.$minutes : $minutes );
        return $start;
    }
}

new WC_Bookboats_Ajax();