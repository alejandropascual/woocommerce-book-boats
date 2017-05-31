<?php

if ( ! defined( 'ABSPATH' ) )
    exit;

/**
 * Booking admin
 */

class WC_Book_Boats_Admin {

    public function __construct() {

        // Manage custom post type
        add_action( 'admin_init', array( $this, 'include_post_type_handlers' ) );
        add_action( 'admin_init', array( $this, 'include_meta_box_handlers' ) );
        //add_action( 'admin_init', array( $this, 'redirect_new_add_booking_url' ) );

        // Manage the product
        add_filter( 'product_type_options', array( $this, 'product_type_options' ) );
        add_filter( 'product_type_selector' , array( $this, 'product_type_selector' ) );

        add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'add_tab' ), 5 );
        add_action( 'woocommerce_product_write_panels', array( $this, 'booking_panels' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'styles_and_scripts' ) );

        add_action( 'woocommerce_process_product_meta', array( $this,'save_product_data' ), 20 );
        add_action( 'woocommerce_product_options_general_product_data', array( $this, 'booking_data' ) );

        add_filter( 'product_type_options', array( $this, 'booking_product_type_options' ) );

        // Admin menu
        include( 'class-wc-bookboats-menus.php' );
    }

    // Don't know if I need something here yet
    public function woocommerce_duplicate_product( $new_post_id, $post ) {

    }

    /**
     * Change messages when a post type is updated.
     *
     * @param  array $messages
     * @return array
     */
    public function post_updated_messages( $messages ) {

    }

    /**
     * Include CPT handlers
     */
    public function include_post_type_handlers() {
        include( 'class-wc-bookboats-cpt.php' );
    }

    /**
     * Include meta box handlers
     */
    public function include_meta_box_handlers() {
        include( 'class-wc-bookboats-meta-boxes.php' );
    }

    /**
     * Redirect the default add booking url to the custom one ?
     */
    public function redirect_new_add_booking_url() {
        global $pagenow;

        //if ( 'post-new.php' == $pagenow && isset( $_GET['post_type'] ) && 'wc_booking' == $_GET['post_type'] ) {
        //    wp_redirect( admin_url( 'edit.php?post_type=wc_booking&page=create_booking' ), '301' );
        //}
    }



    // Manage product

    public function product_type_options( $options ) {
        $options['virtual']['wrapper_class'] .= ' show_if_bookboats';
        return $options;
    }

    public function product_type_selector( $types ) {
        $types[ 'bookboats' ] = __( 'Book Boats product', 'woocommerce-bookboats' );
        return $types;
    }

    public function booking_product_type_options( $options ) {
        return $options;
    }

    public function booking_data() {
        global $post;
        $post_id = $post->ID;
        include( 'views/html-bookboats-data.php' );
    }

    public function add_tab() {
        include( 'views/html-bookboats-tab.php' );
    }

    public function booking_panels() {
        global $post;

        $post_id = $post->ID;

        wp_enqueue_script( 'wc_bookboats_writepanel_js' );

        include( 'views/html-bookboats-costs.php' );
        include( 'views/html-bookboats-availability.php' );
        include( 'views/html-bookboats-startpoint.php' );
        include( 'views/html-bookboats-endpoint.php' );
    }

    public function styles_and_scripts() {

        $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
        $suffix = '';
        wp_enqueue_style( 'wc_bookboats_admin_styles', WC_BOOKBOATS_PLUGIN_URL . '/assets/css/admin.css', null, WC_BOOKBOATS_VERSION );
        wp_register_script( 'wc_bookboats_writepanel_js', WC_BOOKBOATS_PLUGIN_URL . '/assets/js/writepanel' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker' ), WC_BOOKBOATS_VERSION, true );

        wp_register_script( 'wc_bookboats_admin_js', WC_BOOKBOATS_PLUGIN_URL . '/assets/js/bookboats-admin.js', array( 'jquery', 'jquery-tiptip' ), WC_BOOKBOATS_VERSION, true );


    }

    public function save_product_data( $post_id ) {
        global $wpdb;

        $product_type         = empty( $_POST['product-type'] ) ? 'simple' : sanitize_title( stripslashes( $_POST['product-type'] ) );
        $has_additional_costs = false;

        if ( 'bookboats' !== $product_type ) {
            return;
        }

        // Save meta
        $meta_to_save = array(
            '_wc_bookboats_base_cost'               => 'float',
            '_wc_bookboats_num_boats'               => 'int',
            '_wc_bookboats_persons_per_boat'        => 'int',
            '_wc_bookboats_travel_time'             => 'int',
            '_wc_bookboats_ghost_time'              => 'int',
            '_wc_bookboats_book_every_minutes'      => 'int',
            '_wc_bookboats_min_date'                => 'int',
            '_wc_bookboats_max_date'                => 'int',
            '_wc_bookboats_min_date_unit'           => 'text',
            '_wc_bookboats_max_date_unit'           => 'text',
            '_wc_bookboats_default_date_availability' => 'text',
            '_wc_bookboats_pdf_terms' => 'text'
        );

        foreach ( $meta_to_save as $meta_key => $sanitize ) {
            $value = ! empty( $_POST[ $meta_key ] ) ? $_POST[ $meta_key ] : '';
            switch ( $sanitize ) {
                case 'int' :
                    $value = $value ? absint( $value ) : '';
                    break;
                case 'float' :
                    $value = $value ? floatval( $value ) : '';
                    break;
                case 'yesno' :
                    $value = $value == 'yes' ? 'yes' : 'no';
                    break;
                case 'issetyesno' :
                    $value = $value ? 'yes' : 'no';
                    break;
                case 'max_date' :
                    $value = absint( $value );
                    if ( $value == 0 ) {
                        $value = 1;
                    }
                    break;
                default :
                    $value = sanitize_text_field( $value );
            }
            update_post_meta( $post_id, $meta_key, $value );
        }



        // Availability
        $availability = array();
        $row_size     = isset( $_POST[ "wc_bookboats_availability_type" ] ) ? sizeof( $_POST[ "wc_bookboats_availability_type" ] ) : 0;
        for ( $i = 0; $i < $row_size; $i ++ ) {
            $availability[ $i ]['type']     = wc_clean( $_POST[ "wc_bookboats_availability_type" ][ $i ] );
            $availability[ $i ]['bookable'] = wc_clean( $_POST[ "wc_bookboats_availability_bookable" ][ $i ] );

            switch ( $availability[ $i ]['type'] ) {
                case 'custom' :
                    $availability[ $i ]['from'] = wc_clean( $_POST[ "wc_bookboats_availability_from_date" ][ $i ] );
                    $availability[ $i ]['to']   = wc_clean( $_POST[ "wc_bookboats_availability_to_date" ][ $i ] );
                    break;
                case 'months' :
                    $availability[ $i ]['from'] = wc_clean( $_POST[ "wc_bookboats_availability_from_month" ][ $i ] );
                    $availability[ $i ]['to']   = wc_clean( $_POST[ "wc_bookboats_availability_to_month" ][ $i ] );
                    break;
                case 'days' :
                    $availability[ $i ]['from'] = wc_clean( $_POST[ "wc_bookboats_availability_from_day_of_week" ][ $i ] );
                    $availability[ $i ]['to']   = wc_clean( $_POST[ "wc_bookboats_availability_to_day_of_week" ][ $i ] );
                    break;
            }
        }
        update_post_meta( $post_id, '_wc_bookboats_availability', $availability );


        // Availability hours
        $availability = array();
        $row_size     = isset( $_POST[ "wc_bookboats_non_available_from_hour" ] ) ? sizeof( $_POST[ "wc_bookboats_non_available_from_hour" ] ) : 0;
        for ( $i = 0; $i < $row_size; $i ++ ) {
            $availability[ $i ]['from'] = wc_clean( $_POST[ "wc_bookboats_non_available_from_hour" ][ $i ] );
            $availability[ $i ]['to']   = wc_clean( $_POST[ "wc_bookboats_non_available_to_hour" ][ $i ] );
        }
        update_post_meta( $post_id, '_wc_bookboats_non_available_hours', $availability );
        

        // Pricing costs for extra times
        $pricing = array();
        $row_size     = isset( $_POST[ "wc_bookboats_extratime_title" ] ) ? sizeof( $_POST[ "wc_bookboats_extratime_title" ] ) : 0;
        for ( $i = 0; $i < $row_size; $i ++ ) {
            $pricing[ $i ]['title']         = wc_clean( $_POST[ "wc_bookboats_extratime_title" ][ $i ] );
            $pricing[ $i ]['time']          = wc_clean( $_POST[ "wc_bookboats_extratime_time" ][ $i ] );
            $pricing[ $i ]['ghost_time']    = wc_clean( $_POST[ "wc_bookboats_extratime_ghost_time" ][ $i ] );
            $pricing[ $i ]['cost']          = wc_clean( $_POST[ "wc_bookboats_extratime_cost" ][ $i ] );
            $pricing[ $i ]['description']   = wc_clean( $_POST[ "wc_bookboats_extratime_description" ][ $i ] );
        }
        update_post_meta( $post_id, '_wc_bookboats_pricing_time', $pricing );


        // Pricing costs for dates
        $pricing = array();
        $row_size     = isset( $_POST[ "wc_bookboats_pricing_type" ] ) ? sizeof( $_POST[ "wc_bookboats_pricing_type" ] ) : 0;
        for ( $i = 0; $i < $row_size; $i ++ ) {
            $pricing[ $i ]['type']          = wc_clean( $_POST[ "wc_bookboats_pricing_type" ][ $i ] );
            $pricing[ $i ]['base_cost']     = wc_clean( $_POST[ "wc_bookboats_pricing_base_cost" ][ $i ] );
            $pricing[ $i ]['base_cost_modifier'] = wc_clean( $_POST[ "wc_bookboats_pricing_base_cost_modifier" ][ $i ] );
            $pricing[ $i ]['description'] = wc_clean( $_POST[ "wc_bookboats_pricing_description" ][ $i ] );
            $pricing[ $i ]['from_time'] = wc_clean( $_POST[ "wc_bookboats_pricing_from_time" ][ $i ] );
            $pricing[ $i ]['to_time'] = wc_clean( $_POST[ "wc_bookboats_pricing_to_time" ][ $i ] );

            switch ( $pricing[ $i ]['type'] ) {
                case 'custom' :
                    $pricing[ $i ]['from'] = wc_clean( $_POST[ "wc_bookboats_pricing_from_date" ][ $i ] );
                    $pricing[ $i ]['to']   = wc_clean( $_POST[ "wc_bookboats_pricing_to_date" ][ $i ] );
                    break;
                case 'months' :
                    $pricing[ $i ]['from'] = wc_clean( $_POST[ "wc_bookboats_pricing_from_month" ][ $i ] );
                    $pricing[ $i ]['to']   = wc_clean( $_POST[ "wc_bookboats_pricing_to_month" ][ $i ] );
                    break;
                case 'days' :
                    $pricing[ $i ]['from'] = wc_clean( $_POST[ "wc_bookboats_pricing_from_day_of_week" ][ $i ] );
                    $pricing[ $i ]['to']   = wc_clean( $_POST[ "wc_bookboats_pricing_to_day_of_week" ][ $i ] );
                    break;
                default :
                    break;
            }

        }
        update_post_meta( $post_id, '_wc_bookboats_pricing', $pricing );


        // Pricing costs for Start Point
        $pricing = array();
        $row_size     = isset( $_POST[ "wc_bookboats_startpoint_title" ] ) ? sizeof( $_POST[ "wc_bookboats_startpoint_title" ] ) : 0;
        for ( $i = 0; $i < $row_size; $i ++ ) {
            $pricing[ $i ]['title']         = wc_clean( $_POST[ "wc_bookboats_startpoint_title" ][ $i ] );
            $pricing[ $i ]['time']          = wc_clean( $_POST[ "wc_bookboats_startpoint_time" ][ $i ] );
            $pricing[ $i ]['ghost_time']    = wc_clean( $_POST[ "wc_bookboats_startpoint_ghost_time" ][ $i ] );
            $pricing[ $i ]['cost']          = wc_clean( $_POST[ "wc_bookboats_startpoint_cost" ][ $i ] );
            $pricing[ $i ]['description']   = wc_clean( $_POST[ "wc_bookboats_startpoint_description" ][ $i ] );
        }
        update_post_meta( $post_id, '_wc_bookboats_pricing_startpoint', $pricing );


        // Pricing costs for End Point
        $pricing = array();
        $row_size     = isset( $_POST[ "wc_bookboats_endpoint_title" ] ) ? sizeof( $_POST[ "wc_bookboats_endpoint_title" ] ) : 0;
        for ( $i = 0; $i < $row_size; $i ++ ) {
            $pricing[ $i ]['title']         = wc_clean( $_POST[ "wc_bookboats_endpoint_title" ][ $i ] );
            $pricing[ $i ]['time']          = wc_clean( $_POST[ "wc_bookboats_endpoint_time" ][ $i ] );
            $pricing[ $i ]['ghost_time']    = wc_clean( $_POST[ "wc_bookboats_endpoint_ghost_time" ][ $i ] );
            $pricing[ $i ]['cost']          = wc_clean( $_POST[ "wc_bookboats_endpoint_cost" ][ $i ] );
            $pricing[ $i ]['description']   = wc_clean( $_POST[ "wc_bookboats_endpoint_description" ][ $i ] );
        }
        update_post_meta( $post_id, '_wc_bookboats_pricing_endpoint', $pricing );


        // Usual fields
        update_post_meta( $post_id, '_has_additional_costs', 'no' );
        update_post_meta( $post_id, '_regular_price', '' );
        update_post_meta( $post_id, '_sale_price', '' );
        update_post_meta( $post_id, '_manage_stock', 'no' );


        // Set price so filters work
        //$bookable_product = get_product( $post_id );
        //update_post_meta( $post_id, '_price', $bookable_product->get_base_cost() );
        update_post_meta( $post_id, '_price', floatval( $_POST[ '_wc_bookboats_base_cost' ] ) );
    }

    /**
     * Get booking products
     * @return array
     */
    public static function get_bookboats_products() {
        return get_posts( apply_filters( 'get_bookboats_products_args', array(
            'post_status'    => 'publish',
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => 'bookboats'
                )
            ),
            'suppress_filters' => true
        ) ) );
    }

}

new WC_Book_Boats_Admin();