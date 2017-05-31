<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_Product_Bookboats extends WC_Product {

    public function __construct( $product ) {
        $this->product_type = 'bookboats';
        parent::__construct( $product );
    }

    public function is_sold_individually() {
        return true;
    }

    public function is_purchasable() {
        return true;
    }



    //-------------------------------------------
    // UTILS FRONT-END FORM
    // for sending data in json format to the form
    //-------------------------------------------

    public function get_min_date_available() {

        $unit = get_post_meta( $this->id, '_wc_bookboats_min_date_unit', true);
        $value = intval(get_post_meta( $this->id, '_wc_bookboats_min_date', true));

        $duration = 0;
        switch ($unit) {
            case 'minute':
                $duration = 60 * $value;
                break;
            case 'hour':
                $duration = 3600 * $value;
                break;
            case 'day':
                $duration = 86400 * $value;
                break;
            case 'week':
                $duration = 86400 * 7 * $value;
                break;
            case 'month':
                $duration = 86400 * 30 * $value;
                break;
            default:
                $duration = 0;
        }

        return date('Y-m-d', time() + $duration);
    }

    public function get_max_date_available() {

        $unit = get_post_meta( $this->id, '_wc_bookboats_max_date_unit', true);
        $value = intval(get_post_meta( $this->id, '_wc_bookboats_max_date', true));

        $duration = 0;
        switch ($unit) {
            case 'minute':
                $duration = 60 * $value;
                break;
            case 'hour':
                $duration = 3600 * $value;
                break;
            case 'day':
                $duration = 86400 * $value;
                break;
            case 'week':
                $duration = 86400 * 7 * $value;
                break;
            case 'month':
                $duration = 86400 * 30 * $value;
                break;
            default:
                $duration = 0;
        }

        return date('Y-m-d', time() + $duration);
    }

    // Returns if a date (ex:2015-12-01) is available to book
    public function is_date_available( $datestr ) {

        // Check againts dates available only
        $default = get_post_meta( $this->id, '_wc_bookboats_default_date_availability', true);
        $range_arr = get_post_meta( $this->id, '_wc_bookboats_availability', true);

        $is_available = true;
        if ($default == 'non-available') $is_available = false;

        foreach( $range_arr as $range ) {

            //echo '<pre>Range '; print_r( $range ); echo '</pre>';

            $from = $range['from'];
            $to = $range['to'];

            if ($range['type'] == 'custom') {

                $from_date = strtotime($from);
                $to_date = strtotime($to);
                $needle = strtotime($datestr);
                $included = ( $needle >= $from_date && $needle <= $to_date ) ? true  : false;

                if ( $included && $range['bookable'] == 'yes') {
                    $is_available = true;
                } else if ( $included && $range['bookable'] == 'no' ) {
                    $is_available = false;
                }

            } else if ($range['type'] == 'days') {

                $week_day = date("N", strtotime($datestr));
                $included = ( $week_day >= intval($from) && $week_day <= intval($to) ) ? true : false;

                if ( $included && $range['bookable'] == 'yes') {
                    $is_available = true;
                } else if ( $included && $range['bookable'] == 'no' ) {
                    $is_available = false;
                }


            } else if ($range['type'] == 'months' ) {

                $month_num = date("n", strtotime($datestr));
                $included = ( $month_num >= intval($from) && $month_num <= intval($to) ) ? true : false;

                if ( $included && $range['bookable'] == 'yes') {
                    $is_available = true;
                } else if ( $included && $range['bookable'] == 'no' ) {
                    $is_available = false;
                }
            }

        }
        return $is_available;
        //echo $is_available ? '<h1>IS AVAILABLE</h1>' : '<h1>NO AVAILABLE</h1>';
    }

    function createDateRangeArray($strDateFrom,$strDateTo)
    {
        $aryRange=array();

        $iDateFrom = mktime(1,0,0,substr($strDateFrom,5,2), substr($strDateFrom,8,2), substr($strDateFrom,0,4));
        $iDateTo = mktime(1,0,0,substr($strDateTo,5,2), substr($strDateTo,8,2), substr($strDateTo,0,4));

        if ($iDateTo>=$iDateFrom)
        {
            array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
            while ($iDateFrom<$iDateTo)
            {
                $iDateFrom+=86400; // add 24 hours
                array_push($aryRange,date('Y-m-d',$iDateFrom));
            }
        }
        return $aryRange;
    }

    // Dates non-available between the min date and the max date, for the datepicker
    public function get_dates_not_available() {

        // Calculate every date between the start date & end date
        $start_date = $this->get_min_date_available();
        $end_date = $this->get_max_date_available();

        // Calculate range of dates
        $list_dates = $this->createDateRangeArray($start_date,$end_date);

        // Enumerate the list and add to the final list of date not available
        $list_not_available = array();
        foreach( $list_dates as $the_date ) {
            if ( !$this->is_date_available( $the_date ) ) {
                $list_not_available[] = $the_date;
            }
        }
        return $list_not_available;
    }

    // For selecting the number of boats at the front-end
    public function get_number_of_boats() {
        $num_boats = intval( get_post_meta($this->id, '_wc_bookboats_num_boats', true) );
        $pers_per_boat = intval( get_post_meta($this->id, '_wc_bookboats_persons_per_boat', true) );
        $result = array();
        for($i=1; $i<=$num_boats; $i++) {
            if ($i==1) {
                $result[] = array(
                    'label' => $i.' '.__('boat ( up to','woocommerce-bookboats').' '.$pers_per_boat.' '.__('people )','woocommerce-bookboats'),
                    'value' => $i
                );
            } else {
                $result[] = array(
                    'label' => $i.' '.__('boats ( from','woocommerce-bookboats').' '.(1+($i-1)*$pers_per_boat).' '.__('to','woocommerce-bookboats').' '.(($i)*$pers_per_boat).' '.__('people )','woocommerce-bookboats'),
                    'value' => $i
                );
            }
        }
        return $result;
    }

    public function get_base_time() {
        return intval( get_post_meta($this->id, '_wc_bookboats_travel_time', true) );
    }

    public function get_base_cost() {
        return intval( get_post_meta($this->id, '_wc_bookboats_base_cost', true) );
    }

    public function get_ghost_time() {
        return intval( get_post_meta($this->id, '_wc_bookboats_ghost_time', true) );
    }

    public function get_list_extra_time() {
        $list = get_post_meta( $this->id, '_wc_bookboats_pricing_time', true );
        $list2 = array_merge(array( array(
            'title' => 'No extra time',
            'description' => '',
            'cost' => 0,
            'time' => 0,
            'ghost_time' => 0
        )), $list);
        return $list2;
    }

    public function get_list_start_point() {
        $list = get_post_meta( $this->id, '_wc_bookboats_pricing_startpoint', true );
        return $list;
    }

    public function get_list_end_point() {
        $list = get_post_meta( $this->id, '_wc_bookboats_pricing_endpoint', true );
        return $list;
    }


    //-------------------------------------------
    // UTILS GET LIST OF HOURS AVAILABLE for the front-end ajax request
    //-------------------------------------------


    // UTILS PARA CALCULAR HORAS DISPONIBLES CONSULTANDO LAS ***** ORDERS ****** no los CPT wc_bookboat
    //-----------------------------------------


    // Get array of orders with all the metadata 'Ride...'
    // Now I store the data in the bookboat not in the order, so should not use this function
    // Not working good if there are several items in the order
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


    /**
     * @param $product_id
     * @param $date
     * @return array|null|object
     */
    function get_list_booking_boats_for_date( $product_id, $date ) {
        global $wpdb;
        $q = "SELECT pm.post_id,pm3.meta_key,pm3.meta_value FROM {$wpdb->prefix}postmeta pm
              INNER JOIN {$wpdb->posts} posts
              ON posts.ID = pm.post_id
              INNER JOIN  {$wpdb->prefix}postmeta pm2
              ON pm2.post_id = pm.post_id
              INNER JOIN  {$wpdb->prefix}postmeta pm3
              ON pm3.post_id = pm.post_id
              INNER JOIN  {$wpdb->prefix}postmeta pm4
              ON pm4.post_id = pm.post_id
              WHERE pm.meta_key = '_bookboat_product_id'
              AND posts.post_status = 'publish'
              AND pm.meta_value = '{$product_id}'
              AND pm2.meta_key = 'Ride Date'
              AND pm2.meta_value = '{$date}'
              AND pm3.meta_key LIKE 'Ride%'
              AND pm4.meta_key = '_bookboat_mode'
              AND pm4.meta_value = 'reserved'
              ";

        $metas = $wpdb->get_results($q, ARRAY_A);

        $result = array();
        foreach($metas as $meta) {
            if( !isset($result[$meta['post_id']])) {
                $result[$meta['post_id']] = array();
                $result[$meta['post_id']]['bookboat_id'] =  $meta['post_id'];
            }
            $result[$meta['post_id']][$meta['meta_key']] = $meta['meta_value'];
        }

        return $result;

        //echo '<pre>'; print_r( $metas ); echo '</pre>';
    }


    function get_list_boats_booked_for_date( $product_id, $date ) {

        $result_boats = array();

        // Get list orders with metadata - ANtes usaba este
        //$list_orders = $this->get_list_orders_bookboats_for_date( $product_id, $date );

        // Now best the list of bookings *******
        $list_orders = $this->get_list_booking_boats_for_date( $product_id, $date );
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
    //---------------------------------------------------
    // This is the function called from ajax request at the front end

    function get_list_free_hours_for_date(
        $date = '2015-12-01',
        $boats_needed = 1,
        $travel_time = 60,
        $ghost_time_before = 30,
        $ghost_time_after = 30
    ) {

        $product_id = $this->id;

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

    public static function hour_to_number( $the_hour = '00:00', $step = 1 ) {

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