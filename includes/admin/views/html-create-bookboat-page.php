<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div class="wrap woocommerce">
    <div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
    <h2><?php _e( 'Create Booking', 'woocommerce-bookboats' ); ?></h2>

    <p><?php _e( 'You can create a new booking for a boat here. Will not create an order, it\'s only for putting boats out of service.', 'woocommerce-bookboats' ); ?></p>

    <?php $this->show_errors(); ?>

    <form method="POST">
        <table class="form-table">
            <tbody>

                <tr valign="top">
                    <th scope="row">
                        <label for="customer_id"><?php _e( 'Customer', 'woocommerce-bookboats' ); ?></label>
                    </th>
                    <td>
                        <?php if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<' ) ) : ?>
                            <select id="customer_id" name="customer_id" style="width:300px">
                                <option value=""><?php _e( 'Guest', 'woocommerce-bookboats' ) ?></option>
                            </select>
                        <?php else : ?>
                            <input type="hidden" class="wc-customer-search" id="customer_id" name="customer_id" data-placeholder="<?php _e( 'Guest', 'woocommerce-bookboats' ); ?>" data-allow_clear="true" />
                        <?php endif; ?>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">
                        <label for="bookable_product_id"><?php _e( 'Bookable Product', 'woocommerce-bookboats' ); ?></label>
                    </th>
                    <td>
                        <select id="bookable_product_id" name="bookable_product_id" class="chosen_select" style="width:300px">
                            <option value=""><?php _e( 'Select a bookboat product...', 'woocommerce-bookboats' ); ?></option>
                            <?php foreach ( WC_Book_Boats_Admin::get_bookboats_products() as $product ) : ?>
                                <option value="<?php echo $product->ID; ?>"><?php echo $product->post_title; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">
                        <label for="date"><?php _e( 'Date (Year-month-day)', 'woocommerce-bookboats' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="date" id="date" placeholder="<?php echo date('Y-m-d',time()); ?>">
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">
                        <label for="hour_start"><?php _e( 'Hour start (Hour:minutes)', 'woocommerce-bookboats' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="hour_start" id="hour_start" placeholder="06:15">
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">
                        <label for="hour_end"><?php _e( 'Hour end (Hour:minutes)', 'woocommerce-bookboats' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="hour_end" id="hour_end" placeholder="22:30">
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">
                        <label for="boats"><?php _e( 'Which boats', 'woocommerce-bookboats' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="boats" id="boats" placeholder="1 3 4">
                    </td>
                </tr>

                <?php do_action( 'woocommerce_bookboats_after_create_booking_page' ); ?>

                <tr valign="top">
                    <th scope="row">&nbsp;</th>
                    <td>
                        <input type="submit" name="create_booking" class="button-primary" value="<?php _e( 'Create', 'woocommerce-bookboats' ); ?>" />
                        <?php wp_nonce_field( 'create_bookboat_notification' ); ?>
                    </td>
                </tr>

            </tbody>
        </table>
    </form>

</div>
<?php

wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );

if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<' ) ) {
    // Ajax Chosen Customer Selectors JS
    wc_enqueue_js( "
		jQuery('select#customer_id').ajaxChosen({
		    method: 		'GET',
		    url: 			'" . admin_url('admin-ajax.php') . "',
		    dataType: 		'json',
		    afterTypeDelay: 100,
		    minTermLength: 	1,
		    data:		{
		    	action: 	'woocommerce_json_search_customers',
				security: 	'" . wp_create_nonce("search-customers") . "'
		    }
		}, function (data) {

			var terms = {};

		    $.each(data, function (i, val) {
		        terms[i] = val;
		    });

		    return terms;
		});

		jQuery('select.chosen_select').chosen();
	" );

} else {

    wp_enqueue_script('wc-enhanced-select');

    wc_enqueue_js( "
        jQuery(document).ready(function(){

            jQuery(document.body).trigger('wc-enhanced-select-init');

        });
    " );
}
