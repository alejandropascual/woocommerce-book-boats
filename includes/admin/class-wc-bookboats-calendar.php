<?php

class WC_Bookboats_Calendar {

    private $bookings;

    public function output() {

        if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<' ) ) {
            //wp_enqueue_script( 'chosen' );
            //wc_enqueue_js( '$( "select#calendar-bookings-filter" ).chosen();' );
        } else {
            wp_enqueue_script( 'wc-enhanced-select' );
        }

        $product_filter = isset( $_REQUEST['filter_bookings'] ) ? absint( $_REQUEST['filter_bookings'] ) : '';
        $view           = isset( $_REQUEST['view'] ) && $_REQUEST['view'] == 'day' ? 'day' : 'month';

        // DAY VIEW
        if ( $view == 'day' ) {
            $day            = isset( $_REQUEST['calendar_day'] ) ? wc_clean( $_REQUEST['calendar_day'] ) : date( 'Y-m-d' );

            $this->bookings = WC_Bookboats_Controller::get_bookings_in_date_range(
                strtotime( 'midnight', strtotime( $day ) ),
                strtotime( 'midnight +1 day -1 min', strtotime( $day ) ),
                $product_filter,
                false
            );
        }

        // MONTH VIEW
        else {
            $month          = isset( $_REQUEST['calendar_month'] ) ? absint( $_REQUEST['calendar_month'] ) : date( 'n' );
            $year           = isset( $_REQUEST['calendar_year'] ) ? absint( $_REQUEST['calendar_year'] ) : date( 'Y' );

            if ( $year < ( date( 'Y' ) - 10 ) || $year > 2100 )
                $year = date( 'Y' );

            if ( $month > 12 ) {
                $month = 1;
                $year ++;
            }

            if ( $month < 1 ) {
                $month = 1;
                $year --;
            }

            $start_of_week = absint( get_option( 'start_of_week', 1 ) );
            $last_day      = date( 't', strtotime( "$year-$month-01" ) );
            $start_date_w  = absint( date( 'w', strtotime( "$year-$month-01" ) ) );
            $end_date_w    = absint( date( 'w', strtotime( "$year-$month-$last_day" ) ) );

            // Calc day offset
            $day_offset = $start_date_w - $start_of_week;
            $day_offset = $day_offset >= 0 ? $day_offset : 7 - abs( $day_offset );

            // Cald end day offset
            $end_day_offset = 7 - ( $last_day % 7 ) - $day_offset;
            $end_day_offset = $end_day_offset >= 0 && $end_day_offset < 7 ? $end_day_offset : 7 - abs( $end_day_offset );

            $start_timestamp   = strtotime( "-{$day_offset} day", strtotime( "$year-$month-01" ) );
            $end_timestamp     = strtotime( "+{$end_day_offset} day", strtotime( "$year-$month-$last_day" ) );

            $this->bookings     = WC_Bookboats_Controller::get_bookings_in_date_range(
                $start_timestamp,
                $end_timestamp,
                $product_filter,
                false
            );

            //echo '<pre>'; print_r( $this->bookings ); echo '</pre>';
        }

        // Lo introduzco aqui para los tips
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
        wp_enqueue_script('jquery-tiptip');
        wp_enqueue_script('wc_bookboats_admin_js');
        wp_enqueue_style( 'admin-woocommerce-bookings', WC_BOOKBOATS_PLUGIN_URL.'/assets/css/admin-woocommerce-bookings.css' );

        include( 'views/html-calendar-' . $view . '.php' );
    }

    public function list_bookings( $day, $month, $year ) {

        $date = $year.'-'.$month.'-'.$day;

        foreach( $this->bookings as $booking ) {
            if ( $booking->ride['Ride Date'] == $date ) {
                echo '<li><a href="' . admin_url( 'post.php?post=' . $booking->id . '&action=edit' ) . '">';
                    echo '<strong>#' . $booking->id . ' - ';
                    if ( $product = $booking->get_product() ) {
                        echo $product->get_title();
                    }
                    echo ' - '.$booking->status;
                    echo '</strong>';
                    echo '<ul>';
                    if ( ( $customer = $booking->get_customer() ) && ! empty( $customer->name ) ) {
                        echo '<li>' . __( 'Booked by', 'woocommerce-bookboats' ) . ' ' . $customer->name . '</li>';
                    }
                    echo '<li>' . __( 'Hour ', 'woocommerce-bookboats' ) . ' '.$booking->ride['Ride Hour'].' - ';
                    echo '' . __( 'Boats ', 'woocommerce-bookboats' ) . ' '.$booking->ride['Ride Which Boats'].'</li>';
                    echo '</ul></a>';
                echo '</li>';
            }
        }

    }

    /**
     * @param $day
     */
    public function list_bookings_for_day( $day ) {

        $bookings = WC_Bookboats_Controller::get_bookings_for_day( $day );
        //echo '<pre>'; print_r( $bookings ); echo '</pre>';

        foreach( $bookings as $booking ) {

            $percentages = $this->get_percentage_position( $booking );
            //echo '<pre>'; print_r( $percentages ); echo '</pre>';

            $boats = explode( ' ' , $booking->ride['Ride Which Boats'] );
            //echo '<pre>'; print_r( $boats ); echo '</pre>';

            switch ($booking->status) {
                case 'alert':
                    $bcolor = '#EF596D';
                    $color = 'white';
                    break;
                case 'awaiting':
                    $bcolor = '#F1F360';
                    $color = 'black';
                    break;
                case 'done':
                default:
                    $bcolor = '#81CC78';
                    $color = 'white';
                    break;
            }


            $tip = $this->get_tip( $booking );
            foreach ( $boats as $index ) {
                $left = $index * 120 - 100;

                echo '<li style="background: #A0A5AA; left:'.$left.'px; top: '.$percentages['ghost_start'].'%; bottom: '.( 100.0 - $percentages['travel_start'] ).'%;">';
                echo '</li>';

                echo '<li class="tips" data-tip="'.$tip.'" style="background: '.$bcolor.'; left:'.$left.'px; top: '.$percentages['travel_start'].'%; bottom: '.( 100.0 - $percentages['travel_end'] ).'%;">';
                echo '<a style="color:'.$color.';" href="' . admin_url( 'post.php?post=' . $booking->id . '&action=edit' ) . '">#' . $booking->id . ' - boat '.$index.'</a>';
                echo '</li>';

                echo '<li style="background: #B4B9BE; left:'.$left.'px; top: '.$percentages['travel_end'].'%; bottom: '.( 100.0 - $percentages['ghost_end'] ).'%;">';
                echo '</li>';
            }

        }

    }

    public function get_tip( $booking ) {
        $return = "";

        $return .= '#' . $booking->id . ' - ';
        if ( $product = $booking->get_product() ) {
            $return .= $product->get_title();
        }
        if ( ( $customer = $booking->get_customer() ) && ! empty( $customer->name ) ) {
            $return .= '<br/>' . __( 'Booked by', 'woocommerce-bookboats' ) . ' ' . $customer->name;
            $return .= '<br/>' . __( 'Hour', 'woocommerce-bookboats' ) . ' ' . $booking->ride['Ride Hour'];
            $return .= '<br/>' . __( 'Boats', 'woocommerce-bookboats' ) . ' ' . $booking->ride['Ride Which Boats'];
        }
        return esc_attr( $return );
    }


    // Teniendo en cuenta la hora de salida, llegada y ghosts times
    // devuelvo 4 valores en % para calcular la position de la barra de tiempo
    private function get_percentage_position( $booking ) {

        $start_minute = $this->hour_to_number( $booking->ride['Ride Hour'] );
        $ghost_start_minute = $start_minute - intval( $booking->ride['Ride TOTAL Ghost Time before'] );

        $end_minute = $start_minute + intval( $booking->ride['Ride TOTAL Travel Time'] );
        $ghost_end_minute = $end_minute + intval( $booking->ride['Ride TOTAL Ghost Time after'] );

        $minutes_day = 24*60;

        return array(
            'ghost_start'   => number_format( 100.00 * $ghost_start_minute / $minutes_day , 2 ),
            'travel_start'  => number_format( 100.00 * $start_minute / $minutes_day , 2 ),
            'travel_end'    => number_format( 100.00 * $end_minute / $minutes_day , 2 ),
            'ghost_end'     => number_format( 100.00 * $ghost_end_minute / $minutes_day , 2 )
        );
    }

    private function hour_to_number( $the_hour = '00:00', $step = 1 ) {

        $range = explode(':', $the_hour );
        if (count($range) != 2) return 0;

        $steps_hour = 60/$step;
        $hour_number = intval( $steps_hour * intval($range[0]) + intval($range[1]) / $step );
        return $hour_number;
    }

}