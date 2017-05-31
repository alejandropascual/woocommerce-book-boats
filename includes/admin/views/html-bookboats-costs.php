
<div id="bookboats_costs" class="woocommerce_options_panel panel">

    <!-- EXTRA COST FOR TRAVEL TIME -->
    <div class="options_group">
        <p><?php _e('INCREMENT COST BASED ON TRAVEL TIME (apart from start point & end point)', 'woocommerce-bookboats'); ?></p>
        <div class="table_grid">
            <table class="widefat">
                <thead>
                    <th class="sort" width="1%"> </th>
                    <th><?php _e( 'Title', 'woocommerce-bookboats' ); ?></th>
                    <th><?php _e( 'Extra Time (minutes)', 'woocommerce-bookboats' ); ?></th>
                    <th><?php _e( 'Ghost Time after (minutes)', 'woocommerce-bookboats' ); ?></th>
                    <th><?php _e( 'Extra cost', 'woocommerce-bookboats' ); ?></th>
                    <th><?php _e( 'Description', 'woocommerce-bookboats' ); ?></th>
                    <th class="remove" width="1%">&nbsp;</th>
                </thead>
                <tfoot>
                    <th colspan="8">
                        <a href="#" class="button button-primary boat_add_row" data-row="<?php
                        ob_start();
                        $pricing = array();
                        include( 'html-bookboats-costs-time-fields.php' );
                        $html = ob_get_clean();
                        echo esc_attr( $html );
                        ?>"><?php _e( 'Add Travel Time', 'woocommerce-bookboats' ); ?></a>
                        <span class="description"><?php _e( 'Create new Travel Time option', 'woocommerce-bookboats' ); ?></span>
                    </th>
                </tfoot>
                <tbody id="boat_pricing_rows_time">
                    <?php
                    $values = get_post_meta( $post_id, '_wc_bookboats_pricing_time', true );
                    if ( ! empty( $values ) && is_array( $values ) ) {
                        foreach ( $values as $pricing ) {
                            include( 'html-bookboats-costs-time-fields.php' );
                            do_action( 'woocommerce_bookboats_pricing_time_fields', $pricing );
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>


    <!-- EXTRA COST FOR SPECIFIC DAYS -->
    <div class="options_group">
        <p><?php _e('INCREMENT COST BASED ON SPECIFIC DATES OR WEEK DAYS', 'woocommerce-bookboats'); ?></p>

        <div class="table_grid">
            <table class="widefat">

                <thead>
                <tr>
                    <th class="sort" width="1%"> </th>
                    <th><?php _e( 'Range type', 'woocommerce-bookboats' ); ?></th>
                    <th><?php _e( 'From date', 'woocommerce-bookboats' ); ?></th>
                    <th><?php _e( 'To date', 'woocommerce-bookboats' ); ?></th>
                    <th><?php _e( 'From time', 'woocommerce-bookboats' ); ?></th>
                    <th><?php _e( 'To time', 'woocommerce-bookboats' ); ?></th>
                    <th colspan="2"><?php _e( 'Base cost/boat', 'woocommerce-bookboats' ); ?>&nbsp;<a class="tips" data-tip="<?php _e( 'Enter a cost for this rule.', 'woocommerce-bookboats' ); ?>">[?]</a></th>
                    <th><?php _e( 'Description', 'woocommerce-bookboats' ); ?></th>
                    <th class="remove" width="1%">&nbsp;</th>
                </tr>
                </thead>

                <tfoot>
                <tr>
                    <th colspan="10">
                        <a href="#" class="button button-primary boat_add_row" data-row="<?php
                        ob_start();
                        include( 'html-bookboats-costs-dates-fields.php' );
                        $pricing = array();
                        $html = ob_get_clean();
                        echo esc_attr( $html );
                        ?>"><?php _e( 'Add Range', 'woocommerce-bookboats' ); ?></a>
                        <span class="description"><?php _e( 'All matching rules will be applied to the booking.', 'woocommerce-bookboats' ); ?></span>
                    </th>
                </tr>
                </tfoot>

                <tbody id="boat_pricing_rows">
                    <?php
                    $values = get_post_meta( $post_id, '_wc_bookboats_pricing', true );
                    if ( ! empty( $values ) && is_array( $values ) ) {
                        foreach ( $values as $pricing ) {
                            include( 'html-bookboats-costs-dates-fields.php' );
                            do_action( 'woocommerce_bookboats_pricing_fields', $pricing );
                        }
                    }
                    ?>
                </tbody>

            </table>
        </div>
    </div>

</div>