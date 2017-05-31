<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


class WC_Bookboats_Details_Meta_Box
{
    public $id;
    public $title;
    public $context;
    public $priority;
    public $post_types;


    public function __construct()
    {
        $this->id = 'woocommerce-bookboat-data';
        $this->title = __('Booking Details', 'woocommerce-bookboats');
        $this->context = 'normal';
        $this->priority = 'high';
        $this->post_types = array('wc_bookboat');

        add_action('save_post', array($this, 'meta_box_save'), 10, 1);
    }


    public function meta_box_inner( $post ) {
        wp_nonce_field( 'wc_bookboats_details_meta_box', 'wc_bookboats_details_meta_box_nonce' );

        //...
        $booking_order_id = get_post_meta( $post->ID, '_bookboat_order_id', true );
        $booking_order_item_id = get_post_meta( $post->ID, '_bookboat_order_item_id', true );

        ?>
        <style type="text/css">
            #post-body-content, #titlediv, #major-publishing-actions, #minor-publishing-actions, #visibility, #submitdiv { display:none }
        </style>
        <div class="panel-wrap woocommerce">
            <div id="booking_data" class="panel">
                <h2><?php _e( 'Booking Details', 'woocommerce-bookboats' ); ?></h2>
                    <p class="booking_number"><?php

                    printf( __( 'Booking number: #%s.', 'woocommerce-bookboats' ), esc_html( $post->ID ) );

                    if ( $post->post_parent ) {
                        $order = new WC_Order( $post->post_parent );
                        printf( ' ' . __( 'Order number: %s.', 'woocommerce-bookboats' ), '<a href="' . admin_url( 'post.php?post=' . absint( $post->post_parent ) . '&action=edit' ) . '">#' . esc_html( $order->get_order_number() ) . '</a>' );
                    }

                    ?></p>

                    <hr>

                    <?php

                    /* Before I was saving data of the Ride inside the order, now it's saved inside the bookboat CPT
                    if ( $booking_order_id && $booking_order_item_id && function_exists( 'wc_get_order' ) && ( $order = wc_get_order( $booking_order_id ) ) ) {
                        $items = $order->get_items();

                        if (isset($items[$booking_order_item_id])) {

                            $item_meta = $items[$booking_order_item_id]['item_meta'];
                            $data = array(
                                'Ride Date'                     => __('Ride Date','woocommerce-bookboats'),
                                'Ride Hour'                     => __('Ride Hour','woocommerce-bookboats'),
                                'Ride Boats'                    => __('Ride Boats','woocommerce-bookboats'),
                                'Ride Which Boats'              => __('Ride Which Boats','woocommerce-bookboats'),
                                'Ride TOTAL COST'               => __('Ride TOTAL COST','woocommerce-bookboats'),
                                'Ride TOTAL Travel Time'        => __('Ride TOTAL Travel Time','woocommerce-bookboats'),
                                'Ride TOTAL Ghost Time before'  => __('Ride TOTAL Ghost Time before','woocommerce-bookboats'),
                                'Ride TOTAL Ghost Time after'   => __('Ride TOTAL Ghost Time after','woocommerce-bookboats'),
                                'Ride Start point - title'      => __('Ride Start point - title','woocommerce-bookboats'),
                                'Ride Start point - cost'       => __('Ride Start point - cost','woocommerce-bookboats'),
                                'Ride Start point - time'       => __('Ride Start point - time','woocommerce-bookboats'),
                                'Ride Start point - ghost time' => __('Ride Start point - ghost time','woocommerce-bookboats'),
                                'Ride End point - title'        => __('Ride End point - title','woocommerce-bookboats'),
                                'Ride End point - cost'         => __('Ride End point - cost','woocommerce-bookboats'),
                                'Ride End point - time'         => __('Ride End point - time','woocommerce-bookboats'),
                                'Ride End point - ghost time'   => __('Ride End point - ghost time','woocommerce-bookboats'),
                                'Ride Extra time - title'       => __('Ride Extra time - title','woocommerce-bookboats'),
                                'Ride Extra time - cost'        => __('Ride Extra time - cost','woocommerce-bookboats'),
                                'Ride Extra time - time'        => __('Ride Extra time - time','woocommerce-bookboats'),
                                'Ride Extra time - ghost time'  => __('Ride Extra time - ghost time','woocommerce-bookboats')
                            );

                            echo '<table>';
                            foreach( $data as $key => $title ) {
                                echo '<tr>';
                                echo '<td>'.$title.'</td>';
                                echo '<td>'.$item_meta[$key][0].'</td>';
                                echo '</tr>';
                            }
                            echo '</table>';
                        }

                    }
                    */

                    // GET the CPT wc_bookboat
                    $bookboat = get_wc_bookboat( $post->ID );


                    // STATUS
                    $status = $bookboat->get_status();
                    echo '<p><h4 style="display: inline-block; width: 120px;">STATUS: </h4> ';
                    if ($status == 'awaiting') {
                        echo '<span class="bookboat-awaiting">'.__('Awaiting', 'woocommerce-bookboats').'</span>';
                    } else if ($status == 'alert') {
                        echo '<span class="bookboat-alert">'.__('Alert', 'woocommerce-bookboats').'</span>';
                    } else if ($status == 'done') {
                        echo '<span class="bookboat-done">'.__('Done', 'woocommerce-bookboats').'</span>';
                    }
                    echo '</p>';

                    // MODE
                    $mode = $bookboat->get_mode();
                    echo '<p><h4 style="display: inline-block; width: 120px;">MODE: </h4> ';
                    if ($mode == 'reserved') {
                        echo '<span class="bookboat-reserved">'.__('Reserved', 'woocommerce-bookboats').'</span>';
                    } else if ($mode == 'free') {
                        echo '<span class="bookboat-free">'.__('Free', 'woocommerce-bookboats').'</span>';
                    }
                    echo '</p>';

                    // ROde data
                    $data = array(
                        'Ride Date'                     => __('Ride Date','woocommerce-bookboats'),
                        'Ride Hour'                     => __('Ride Hour','woocommerce-bookboats'),
                        'Ride Boats'                    => __('Ride Boats','woocommerce-bookboats'),
                        'Ride Which Boats'              => __('Ride Which Boats','woocommerce-bookboats'),
                        'Ride TOTAL COST'               => __('Ride TOTAL COST','woocommerce-bookboats'),
                        'Ride TOTAL Travel Time'        => __('Ride TOTAL Travel Time','woocommerce-bookboats'),
                        'Ride TOTAL Ghost Time before'  => __('Ride TOTAL Ghost Time before','woocommerce-bookboats'),
                        'Ride TOTAL Ghost Time after'   => __('Ride TOTAL Ghost Time after','woocommerce-bookboats'),
                        'Ride Start point - title'      => __('Ride Start point - title','woocommerce-bookboats'),
                        'Ride Start point - cost'       => __('Ride Start point - cost','woocommerce-bookboats'),
                        'Ride Start point - time'       => __('Ride Start point - time','woocommerce-bookboats'),
                        'Ride Start point - ghost time' => __('Ride Start point - ghost time','woocommerce-bookboats'),
                        'Ride End point - title'        => __('Ride End point - title','woocommerce-bookboats'),
                        'Ride End point - cost'         => __('Ride End point - cost','woocommerce-bookboats'),
                        'Ride End point - time'         => __('Ride End point - time','woocommerce-bookboats'),
                        'Ride End point - ghost time'   => __('Ride End point - ghost time','woocommerce-bookboats'),
                        'Ride Extra time - title'       => __('Ride Extra time - title','woocommerce-bookboats'),
                        'Ride Extra time - cost'        => __('Ride Extra time - cost','woocommerce-bookboats'),
                        'Ride Extra time - time'        => __('Ride Extra time - time','woocommerce-bookboats'),
                        'Ride Extra time - ghost time'  => __('Ride Extra time - ghost time','woocommerce-bookboats')
                    );
                    echo '<table>';
                    foreach( $data as $key => $title ) {
                        echo '<tr>';
                        echo '<td>'.$title.'</td>';
                        echo '<td>'.$bookboat->ride[$key].'</td>';
                        echo '</tr>';
                    }
                    echo '</table>';


                    ?>

            </div>
            <div class="clear"></div>
        </div>
        <?php
    }

    public function meta_box_save( $post_id ) {
        if ( ! isset( $_POST['wc_bookboats_details_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['wc_bookboats_details_meta_box_nonce'], 'wc_bookboats_details_meta_box' ) ) {
            return $post_id;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }

        if ( ! in_array( $_POST['post_type'], $this->post_types ) ) {
            return $post_id;
        }

        global $wpdb, $post;
    }
}

return new WC_Bookboats_Details_Meta_Box();