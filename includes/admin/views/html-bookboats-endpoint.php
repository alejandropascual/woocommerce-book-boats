<div id="bookboats_endpoint" class="woocommerce_options_panel panel wc-metaboxes-wrapper">

    <div class="options_group">
        <p><?php _e('INCREMENT COST BASED ON END POINT', 'woocommerce-bookboats'); ?></p>
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
                    include( 'html-bookboats-endpoint-fields.php' );
                    $html = ob_get_clean();
                    echo esc_attr( $html );
                    ?>"><?php _e( 'Add Travel Time', 'woocommerce-bookboats' ); ?></a>
                    <span class="description"><?php _e( 'Create new End Point', 'woocommerce-bookboats' ); ?></span>
                </th>
                </tfoot>
                <tbody id="boat_pricing_rows_time">
                <?php
                $values = get_post_meta( $post_id, '_wc_bookboats_pricing_endpoint', true );
                if ( ! empty( $values ) && is_array( $values ) ) {
                    foreach ( $values as $pricing ) {
                        include( 'html-bookboats-endpoint-fields.php' );
                        do_action( 'woocommerce_bookboats_pricing_endpoint_fields', $pricing );
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

</div>