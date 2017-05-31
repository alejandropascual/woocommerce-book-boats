<div id="bookboats_availability" class="woocommerce_options_panel panel wc-metaboxes-wrapper">

    <div class="options_group">

        <?php
            $min_date      = absint( get_post_meta( $post_id, '_wc_bookboats_min_date', true ) );
            $min_date_unit = get_post_meta( $post_id, '_wc_bookboats_min_date_unit', true );
        ?>
        <p class="form-field">
            <label for="_wc_bookboats_min_date"><?php _e( 'Minimum time to book', 'woocommerce-bookboats' ); ?></label>
            <input type="number" name="_wc_bookboats_min_date" id="_wc_bookboats_min_date" value="<?php echo $min_date; ?>" step="1" min="0" style="margin-right: 7px; width: 4em;">
            <select name="_wc_bookboats_min_date_unit" id="_wc_bookboats_min_date_unit" class="short" style="margin-right: 7px;">
                <option value="minute" <?php selected( $min_date_unit, 'minute' ); ?>><?php _e( 'Minute(s)', 'woocommerce-bookboats' ); ?></option>
                <option value="hour" <?php selected( $min_date_unit, 'hour' ); ?>><?php _e( 'Hour(s)', 'woocommerce-bookboats' ); ?></option>
                <option value="day" <?php selected( $min_date_unit, 'day' ); ?>><?php _e( 'Day(s)', 'woocommerce-bookboats' ); ?></option>
                <option value="week" <?php selected( $min_date_unit, 'week' ); ?>><?php _e( 'Week(s)', 'woocommerce-bookboats' ); ?></option>
                <option value="month" <?php selected( $min_date_unit, 'month' ); ?>><?php _e( 'Month(s)', 'woocommerce-bookboats' ); ?></option>
            </select> <?php _e( 'into the future', 'woocommerce-bookboats' ); ?>
        </p>

        <?php
            $max_date = get_post_meta( $post_id, '_wc_bookboats_max_date', true );
            if ( $max_date == '' )
                $max_date = 12;
            $max_date      = max( absint( $max_date ), 1 );
            $max_date_unit = get_post_meta( $post_id, '_wc_bookboats_max_date_unit', true );
        ?>
        <p class="form-field">
            <label for="_wc_bookboats_max_date"><?php _e( 'Maximum time to book', 'woocommerce-bookboats' ); ?></label>
            <input type="number" name="_wc_bookboats_max_date" id="_wc_bookboats_max_date" value="<?php echo $max_date; ?>" step="1" min="1" style="margin-right: 7px; width: 4em;">
            <select name="_wc_bookboats_max_date_unit" id="_wc_bookboats_max_date_unit" class="short" style="margin-right: 7px;">
                <option value="minute" <?php selected( $max_date_unit, 'minute' ); ?>><?php _e( 'Minute(s)', 'woocommerce-bookboats' ); ?></option>
                <option value="hour" <?php selected( $max_date_unit, 'hour' ); ?>><?php _e( 'Hour(s)', 'woocommerce-bookboats' ); ?></option>
                <option value="day" <?php selected( $max_date_unit, 'day' ); ?>><?php _e( 'Day(s)', 'woocommerce-bookboats' ); ?></option>
                <option value="week" <?php selected( $max_date_unit, 'week' ); ?>><?php _e( 'Week(s)', 'woocommerce-bookboats' ); ?></option>
                <option value="month" <?php selected( $max_date_unit, 'month' ); ?>><?php _e( 'Month(s)', 'woocommerce-bookboats' ); ?></option>
            </select> <?php _e( 'into the future', 'woocommerce-bookboats' ); ?>
        </p>

        <?php
            woocommerce_wp_select(
                array(
                    'id'          => '_wc_bookboats_default_date_availability',
                    'label'       => __( 'All dates are...', 'woocommerce-bookboats' ),
                    'description' => '',
                    'value'       => get_post_meta( $post_id, '_wc_bookboats_default_date_availability', true ),
                    'options' => array(
                        'available'     => __( 'available by default', 'woocommerce-bookboats' ),
                        'non-available' => __( 'not-available by default', 'woocommerce-bookboats' )
                    ),
                    'description' => __( 'This option affects how you use the rules below.', 'woocommerce-bookboats' )
                )
            );
        ?>

    </div>

    <!-- DATES RANGES AVAILABILITY -->
    <div class="options_group">
        <p><?php _e('INSERT RANGE OF DATES FOR EXCEPTIONS IN AVAILABILITY / NON-AVAILABILITY', 'woocommerce-bookboats'); ?></p>
        <div class="table_grid">
            <table class="widefat">

                <thead>
                    <tr>
                        <th class="sort" width="1%">&nbsp;</th>
                        <th><?php _e( 'Range type', 'woocommerce-bookboats' ); ?></th>
                        <th><?php _e( 'From', 'woocommerce-bookboats' ); ?></th>
                        <th><?php _e( 'To', 'woocommerce-bookboats' ); ?></th>
                        <th><?php _e( 'Bookable', 'woocommerce-bookboats' ); ?>&nbsp;<a class="tips" data-tip="<?php _e( 'If not bookable, users won\'t be able to choose this block for their booking.', 'woocommerce-bookboats' ); ?>">[?]</a></th>
                        <th class="remove" width="1%">&nbsp;</th>
                    </tr>
                </thead>

                <tfoot>
                    <tr>
                        <th colspan="6">
                            <a href="#" class="button button-primary boat_add_row" data-row="<?php
                            ob_start();
                            $availability = array();
                            include( 'html-bookboats-availability-fields.php' );
                            $html = ob_get_clean();
                            echo esc_attr( $html );
                            ?>"><?php _e( 'Add Range', 'woocommerce-bookboats' ); ?></a>
                            <span class="description"><?php _e( 'Rules further down the table will override those at the top.', 'woocommerce-bookboats' ); ?></span>
                        </th>
                    </tr>
                </tfoot>

                <tbody id="boat_availability_rows">
                    <?php
                    $values = get_post_meta( $post_id, '_wc_bookboats_availability', true );
                    if ( ! empty( $values ) && is_array( $values ) ) {
                        foreach ( $values as $availability ) {
                            include( 'html-bookboats-availability-fields.php' );
                        }
                    }
                    ?>
                </tbody>

            </table>
        </div>
    </div>


    <!-- TIME TANGE AVAILABILITY -->
    <div class="options_group">
        <p><?php _e('BY DEFAULT ALL HOURS ARE AVAILABLE, INSERT HERE NON-AVAILABLE HOURS (example: 22:00 - 23:59)', 'woocommerce-bookboats'); ?></p>
        <div class="table_grid">
            <table class="widefat">

                <thead>
                <tr>
                    <th class="sort" width="1%">&nbsp;</th>
                    <th><?php _e( 'From Hour', 'woocommerce-bookboats' ); ?></th>
                    <th><?php _e( 'To Hour', 'woocommerce-bookboats' ); ?></th>
                    <th class="remove" width="1%">&nbsp;</th>
                </tr>
                </thead>

                <tfoot>
                <tr>
                    <th colspan="6">
                        <a href="#" class="button button-primary boat_add_row" data-row="<?php
                        ob_start();
                        $availability = array();
                        include( 'html-bookboats-availability-hours.php' );
                        $html = ob_get_clean();
                        echo esc_attr( $html );
                        ?>"><?php _e( 'Add Range', 'woocommerce-bookboats' ); ?></a>
                        <span class="description"><?php _e( 'If you don\'t insert any rule then will be available from 00:00 to 24:00.', 'woocommerce-bookboats' ); ?></span>
                    </th>
                </tr>
                </tfoot>

                <tbody id="boat_availability_rows">
                <?php
                $values = get_post_meta( $post_id, '_wc_bookboats_non_available_hours', true );
                if ( ! empty( $values ) && is_array( $values ) ) {
                    foreach ( $values as $availability ) {
                        include( 'html-bookboats-availability-hours.php' );
                    }
                }
                ?>
                </tbody>

            </table>
        </div>
    </div>


</div>