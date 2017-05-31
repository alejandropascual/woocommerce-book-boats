<?php
/**
 * Bookboats product add to cart
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $product;

if ( ! $product->is_purchasable() ) {
    return;
}

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<?php
//echo '<style>.summary { width:1000px !important; }</style>';
//$prueba = new WC_Bookboats_Ajax();
//$list = $prueba->get_list_free_hours_for_date( 160, '2015-12-01', 4, 90, 30, 30 );
//echo '<pre>'; print_r( $list ); echo '</pre>';
?>

<noscript><?php _e( 'Your browser must support JavaScript in order to make a booking.', 'woocommerce-bookings' ); ?></noscript>

<form class="cart" method="post" enctype='multipart/form-data'>

    <div id="wc-bookboats-booking-form" class="wc-bookboats-booking-form">

        <?php do_action( 'woocommerce_before_bookboats_form' ); ?>

        <?php $bookboats_form->output(); ?>

        <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

        <div class="wc-bookboats-booking-cost" style="display:none"></div>

    </div>


    <input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->id ); ?>" />

    <button type="submit" class="wc-bookboats-booking-form-button single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

    <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
