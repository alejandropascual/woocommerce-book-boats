<?php
/**
 * Gets bookings
 */
class WC_Bookboats_Controller {

    public static function get_bookings_in_date_range( $start_date, $end_date, $filter_id, $check_in_cart = false ) {

        $booking_ids = self::get_bookings_in_date_range_query( $start_date, $end_date, $filter_id, $check_in_cart );

        // Get objects
        $bookings = array();
        foreach ( $booking_ids as $booking_id ) {
            $bookings[] = get_wc_bookboat( $booking_id );
        }
        return $bookings;

    }

    private static function get_bookings_in_date_range_query( $start_date, $end_date, $filter_id = '', $check_in_cart = true ) {

        global $wpdb;

        $booking_ids = $wpdb->get_col( $wpdb->prepare( "
            SELECT ID FROM {$wpdb->posts} as p
            LEFT JOIN {$wpdb->postmeta} as startmeta ON p.ID = startmeta.post_id
            WHERE post_type = 'wc_bookboat'
            AND post_status = 'publish'
            AND startmeta.meta_key = '_bookboat_start_date'
            AND startmeta.meta_value >= %s
            AND startmeta.meta_value <= %s
            ", date( 'YmdHis', $start_date ), date( 'YmdHis', $end_date ) )
        );

        return $booking_ids;

    }

    public static function get_bookings_for_day( $day ) {

        $booking_ids = self::get_bookings_for_day_query( $day );

        // Get objects
        $bookings = array();
        foreach ( $booking_ids as $booking_id ) {
            $bookings[] = get_wc_bookboat( $booking_id );
        }
        return $bookings;
    }


    public static function get_bookings_for_day_query( $day ) {

        global $wpdb;

        $booking_ids = $wpdb->get_col( $wpdb->prepare( "
            SELECT ID FROM {$wpdb->posts} as p
            LEFT JOIN {$wpdb->postmeta} as startmeta ON p.ID = startmeta.post_id
            WHERE post_type = 'wc_bookboat'
            AND post_status = 'publish'
            AND startmeta.meta_key = 'Ride Date'
            AND startmeta.meta_value = %s
            ", $day )
        );

        return $booking_ids;

    }

}