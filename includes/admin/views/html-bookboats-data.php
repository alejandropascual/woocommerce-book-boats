<div class="options_group show_if_bookboats">

    <?php
        $num_of_boats = get_post_meta( $post_id, '_wc_bookboats_num_boats', true );
        $persons_per_boat = get_post_meta( $post_id, '_wc_bookboats_persons_per_boat', true );
        $cost = get_post_meta( $post_id, '_wc_bookboats_base_cost', true );
        $travel_time = get_post_meta( $post_id, '_wc_bookboats_travel_time', true );
        $ghost_time = get_post_meta( $post_id, '_wc_bookboats_ghost_time', true );
        $every_minutes = get_post_meta( $post_id, '_wc_bookboats_book_every_minutes', true );
        $pdf_terms = get_post_meta( $post_id, '_wc_bookboats_pdf_terms', true );
    ?>
    <img src="<?php echo WC_BOOKBOATS_PLUGIN_URL.'/assets/img/ribalex_vessel.jpg'; ?>" style="max-width: 592px;">
    <p class="form-field">
        <label for="_wc_bookboats_num_boats"><?php _e( 'Max Number of Boats', 'woocommerce-bookboats' ); ?></label>
        <input type="number" class="short-field" name="_wc_bookboats_num_boats" id="_wc_bookboats_num_boats" value="<?php echo $num_of_boats; ?>" step="1" min="1">
    </p>
    <p class="form-field">
        <label for="_wc_bookboats_persons_per_boat"><?php _e( 'Max Persons/Boat', 'woocommerce-bookboats' ); ?></label>
        <input type="number" class="short-field" name="_wc_bookboats_persons_per_boat" id="_wc_bookboats_persons_per_boat" value="<?php echo $persons_per_boat; ?>" step="1" min="1">
    </p>
    <p class="form-field">
        <label for="_wc_bookboats_travel_time"><?php _e( 'Travel Time (minutes)', 'woocommerce-bookboats' ); ?></label>
        <input type="text" class="short-field" style="" name="_wc_bookboats_travel_time" id="_wc_bookboats_travel_time" value="<?php echo $travel_time; ?>" placeholder="">
    </p>
    <p class="form-field">
        <label for="_wc_bookboats_ghost_time"><?php _e( 'Ghost Time after (minutes)', 'woocommerce-bookboats' ); ?></label>
        <input type="text" class="short-field" style="" name="_wc_bookboats_ghost_time" id="_wc_bookboats_ghost_time" value="<?php echo $ghost_time; ?>" placeholder="">
    </p>
    <p class="form-field">
        <label for="_wc_bookboats_base_cost"><?php _e( 'Base Cost/Boat', 'woocommerce-bookboats' ); ?> (<?php echo get_woocommerce_currency_symbol(); ?>)</label>
        <input type="text" class="short-field" style="" name="_wc_bookboats_base_cost" id="_wc_bookboats_cost" value="<?php echo $cost; ?>" placeholder="">
    </p>
    <p class="form-field">
        <label for="_wc_bookboats_book_every_minutes"><?php _e( 'Allow booking every ...', 'woocommerce-bookboats' ); ?></label>
        <select name="_wc_bookboats_book_every_minutes">
            <option value="10" <?php selected( $every_minutes, 10 ); ?>><?php _e( '10 minutes', 'woocommerce-bookboats' ); ?></option>
            <option value="15" <?php selected( $every_minutes, 15 ); ?>><?php _e( '15 minutes', 'woocommerce-bookboats' ); ?></option>
            <option value="20" <?php selected( $every_minutes, 20 ); ?>><?php _e( '20 minutes', 'woocommerce-bookboats' ); ?></option>
            <option value="30" <?php selected( $every_minutes, 30 ); ?>><?php _e( '30 minutes', 'woocommerce-bookboats' ); ?></option>
            <option value="40" <?php selected( $every_minutes, 40 ); ?>><?php _e( '40 minutes', 'woocommerce-bookboats' ); ?></option>
            <option value="45" <?php selected( $every_minutes, 45 ); ?>><?php _e( '45 minutes', 'woocommerce-bookboats' ); ?></option>
            <option value="60" <?php selected( $every_minutes, 60 ); ?>><?php _e( '60 minutes', 'woocommerce-bookboats' ); ?></option>
            <option value="90" <?php selected( $every_minutes, 90 ); ?>><?php _e( '90 minutes', 'woocommerce-bookboats' ); ?></option>
        </select>
    </p>
    <p class="form-field">
        <label for="_wc_bookboats_pdf_terms"><?php _e( 'PDF file terms (link)', 'woocommerce-bookboats' ); ?></label>
        <input type="text" class="short-field" style="" name="_wc_bookboats_pdf_terms" id="_wc_bookboats_pdf_terms" value="<?php echo $pdf_terms; ?>" placeholder="">
    </p>
</div>