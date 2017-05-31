<?php
/**
 * Bookboats form class
 */

class WC_Bookboats_form {

    public $product;

    private $fields;

    public function __construct( $product ) {
        $this->product = $product;
    }

    public function scripts() {

        //wp_enqueue_script('jquery-ui-datepicker');
        //wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

        //$this->product->is_date_available('2015-12-01');

        wp_enqueue_style('selectize', WC_BOOKBOATS_PLUGIN_URL . '/assets/css/selectize-default.css');

        $wc_bookboats_args = array(
            'ajaxurl'               => admin_url('admin-ajax.php'),
            'product_id'            => $this->product->id,
            'base_travel_time'      => $this->product->get_base_time(),
            'base_cost'             => $this->product->get_base_cost(),
            'currency'              => get_woocommerce_currency_symbol(),
            'ghost_time'            => $this->product->get_ghost_time(),
            'min_date_available'    => $this->product->get_min_date_available(),
            'max_date_available'    => $this->product->get_max_date_available(),
            'dates_not_available'   => $this->product->get_dates_not_available(),
            'number_of_boats'       => $this->product->get_number_of_boats(),
            'list_start_point'      => $this->product->get_list_start_point(),
            'list_end_point'        => $this->product->get_list_end_point(),
            'list_extra_time'       => $this->product->get_list_extra_time(),
            'messages'              => array(
                'list_hours_message' => __('Select date before selecting the hour !','woocommerce-bookboats')
            )
        );


        wp_enqueue_script( 'knockout', WC_BOOKBOATS_PLUGIN_URL . '/assets/js/knockout.min.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-datepicker' ), '3.3.0', true );
        wp_enqueue_script( 'selectize', WC_BOOKBOATS_PLUGIN_URL . '/assets/js/selectize.min.js', array('jquery'), '1.0.0', true);
        wp_enqueue_script( 'bookboats', WC_BOOKBOATS_PLUGIN_URL . '/assets/js/bookboats.js', array('knockout','selectize'), '1.0.0', true);
        wp_localize_script('bookboats', 'wc_bookboats_booking_form', $wc_bookboats_args);
    }

    public function prepare_fields() {
        // Destroy existing fields
        $this->reset_fields();
    }

    public function reset_fields() {
        $this->fields = array();
    }

    public function output() {
        $this->scripts();
        $this->prepare_fields();

        include('template-interface.php');
    }

    public function add_field( $field ) {
        $default = array(
            'name'  => '',
            'class' => array(),
            'label' => '',
            'type'  => 'text'
        );
    }

    // Called from class-wc-bookboats-cart-manager.php
    public function get_posted_data( $posted = array() ) {

        //echo '<pre>'; print_r( $posted ); echo '</pre>';
        //exit();

        $data = array();

        $data['_qty'] = 1;
        $data['Ride Date']          = $posted['bookboats-date'];
        $data['Ride Hour']          = $posted['bookboats-hour'];
        $data['Ride Boats']         = $posted['bookboats-boats'];
        $data['Ride Which Boats']   = $posted['bookboats-indexboats'];

        $data['Ride TOTAL COST']                = $posted['bookboats-total-cost'];
        $data['Ride TOTAL Travel Time']         = $posted['bookboats-total-travel-time'];
        $data['Ride TOTAL Ghost Time before']   = $posted['bookboats-total-ghost-time-before'];
        $data['Ride TOTAL Ghost Time after']    = $posted['bookboats-total-ghost-time-after'];

        $data['Ride Start point - title']       = $posted['bookboats-start-point']['title'];
        $data['Ride Start point - cost']        = $posted['bookboats-start-point']['cost'];
        $data['Ride Start point - time']        = $posted['bookboats-start-point']['time'];
        $data['Ride Start point - ghost time']  = $posted['bookboats-start-point']['ghost_time'];

        $data['Ride End point - title']       = $posted['bookboats-end-point']['title'];
        $data['Ride End point - cost']        = $posted['bookboats-end-point']['cost'];
        $data['Ride End point - time']        = $posted['bookboats-end-point']['time'];
        $data['Ride End point - ghost time']  = $posted['bookboats-end-point']['ghost_time'];

        $data['Ride Extra time - title']       = $posted['bookboats-extra-time']['title'];
        $data['Ride Extra time - cost']        = $posted['bookboats-extra-time']['cost'];
        $data['Ride Extra time - time']        = $posted['bookboats-extra-time']['time'];
        $data['Ride Extra time - ghost time']  = $posted['bookboats-extra-time']['ghost_time'];

        //echo '<pre>'; print_r( $posted ); echo '</pre>';
        //echo '<pre>'; print_r( $posted['bookboats-start-point'] ); echo '</pre>';
        //echo '<pre>'; print_r( $data ); echo '</pre>';
        //exit();

        return $data;



        // Ejemplo;
        $data = array();
        $data['_qty'] = 1;
        $data['Ride date'] = '2015-11-05';
        $data['Ride boats'] = 3;
        $data['Ride persons'] = 30;
        $data['Ride time'] = '11:30';
        $data['Ride Start point'] = 'Start point 1';
        $data['Ride End point'] = 'End point 3';
        $data['Ride minutes Base'] = 30;
        $data['Ride minutes Extra'] = 15;
        $data['Ride minutes Start point'] = 15;
        $data['Ride minutes End point'] = 15;
        $data['Ride ghost minutes Base'] = 0;
        $data['Ride ghost minutes Extra'] = 0;
        $data['Ride ghost minutes Start point'] = 0;
        $data['Ride ghost minutes End point'] = 0;

        return $data;
    }

    public function calculate_booking_cost( $posted ) {
        return floatval( $posted['bookboats-total-cost'] );
    }

}