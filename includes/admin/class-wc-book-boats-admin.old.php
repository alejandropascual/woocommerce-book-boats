<?php

if ( ! defined( 'ABSPATH' ) )
    exit;

/**
 * Booking admin
 */

class WC_Book_Boats_Admin {

    public function __construct() {
        add_filter( 'product_type_options', array( $this, 'product_type_options' ) );
        add_filter( 'product_type_selector' , array( $this, 'product_type_selector' ) );

        add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'add_tab' ), 5 );
        add_action( 'woocommerce_product_write_panels', array( $this, 'booking_panels' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'styles_and_scripts' ) );

        add_action( 'woocommerce_process_product_meta', array( $this,'save_product_data' ), 20 );
        add_action( 'woocommerce_product_options_general_product_data', array( $this, 'booking_data' ) );

        add_filter( 'product_type_options', array( $this, 'booking_product_type_options' ) );
    }

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
        return array_merge( $options, array(
            'wc_bookboats_has_persons' => array(
                'id'            => '_wc_bookboats_has_persons',
                'wrapper_class' => 'show_if_bookboats',
                'label'         => __( 'Has Boat persons', 'woocommerce-bookings' ),
                'description'   => __( 'Enable this if this bookable product can be booked by a customer defined number of persons.', 'woocommerce-bookboats' ),
                'default'       => 'no'
            ),
            'wc_bookboats_has_resources' => array(
                'id'            => '_wc_bookboats_has_resources',
                'wrapper_class' => 'show_if_bookboats',
                'label'         => __( 'Has Boat resources', 'woocommerce-bookings' ),
                'description'   => __( 'Enable this if this bookable product has multiple bookable resources, for example room types or instructors.', 'woocommerce-bookboats' ),
                'default'       => 'no'
            ),
        ) );
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

        include( 'views/html-bookboats-calendar.php' );
        include( 'views/html-bookboats-startpoint.php' );
        include( 'views/html-bookboats-endpoint.php' );
    }

    public function styles_and_scripts() {

        $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
        $suffix = '';
        wp_enqueue_style( 'wc_bookboats_admin_styles', WC_BOOKBOATS_PLUGIN_URL . '/assets/css/admin.css', null, WC_BOOKBOATS_VERSION );
        wp_register_script( 'wc_bookboats_writepanel_js', WC_BOOKBOATS_PLUGIN_URL . '/assets/js/writepanel' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker' ), WC_BOOKBOATS_VERSION, true );
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
            '_wc_bookboats_num_boats'               => 'int',
            '_wc_bookboats_persons_per_boat'        => 'int'
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

        update_post_meta( $post_id, '_regular_price', $_POST['_regular_price'] );
    }

}

new WC_Book_Boats_Admin();