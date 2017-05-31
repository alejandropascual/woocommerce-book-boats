<?php
    $intervals = array();
    $intervals['months'] = array(
        '1' => __( 'January', 'woocommerce-bookboats' ),
        '2' => __( 'Febuary', 'woocommerce-bookboats' ),
        '3' => __( 'March', 'woocommerce-bookboats' ),
        '4' => __( 'April', 'woocommerce-bookboats' ),
        '5' => __( 'May', 'woocommerce-bookboats' ),
        '6' => __( 'June', 'woocommerce-bookboats' ),
        '7' => __( 'July', 'woocommerce-bookboats' ),
        '8' => __( 'August', 'woocommerce-bookboats' ),
        '9' => __( 'September', 'woocommerce-bookboats' ),
        '10' => __( 'October', 'woocommerce-bookboats' ),
        '11' => __( 'November', 'woocommerce-bookboats' ),
        '12' => __( 'December', 'woocommerce-bookboats' )
    );
    $intervals['days'] = array(
        '1' => __( 'Monday', 'woocommerce-bookboats' ),
        '2' => __( 'Tuesday', 'woocommerce-bookboats' ),
        '3' => __( 'Wednesday', 'woocommerce-bookboats' ),
        '4' => __( 'Thursday', 'woocommerce-bookboats' ),
        '5' => __( 'Friday', 'woocommerce-bookboats' ),
        '6' => __( 'Saturday', 'woocommerce-bookboats' ),
        '7' => __( 'Sunday', 'woocommerce-bookboats' )
    );

    if ( ! isset( $pricing['type'] ) ) {
        $pricing['type'] = 'custom';
    }
    if ( ! isset( $pricing['base_cost'] ) ) {
        $pricing['base_cost'] = '';
    }
    if ( ! isset( $pricing['base_cost_modifier'] ) ) {
        $pricing['base_cost_modifier'] = '';
    }
    if ( ! isset( $pricing['description'] ) ) {
        $pricing['description'] = '';
    }

?>
<tr>
    <td class="sort">=</td>

    <td>
        <div class="select wc_bookboats_pricing_type">
            <select name="wc_bookboats_pricing_type[]">
                <option value="custom" <?php selected( $pricing['type'], 'custom' ); ?>><?php _e( 'Custom date range', 'woocommerce-bookboats' ); ?></option>
                <!-- option value="months" <?php //selected( $pricing['type'], 'months' ); ?>><?php //_e( 'Range of months', 'woocommerce-bookboats' ); ?></option>
                <option value="days" <?php //selected( $pricing['type'], 'days' ); ?>><?php //_e( 'Range of days', 'woocommerce-bookboats' ); ?></option -->
            </select>
        </div>
    </td>

    <td>
        <div class="from_date">
            <input type="text" class="date-picker" name="wc_bookboats_pricing_from_date[]" value="<?php if ( $pricing['type'] == 'custom' && ! empty( $pricing['from'] ) ) echo $pricing['from'] ?>" />
        </div>
        <div class="select from_month">
            <select name="wc_bookboats_pricing_from_month[]">
                <?php foreach ( $intervals['months'] as $key => $label ) : ?>
                    <option value="<?php echo $key; ?>" <?php selected( isset( $pricing['from'] ) && $pricing['from'] == $key, true ) ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="select from_day_of_week">
            <select name="wc_bookboats_pricing_from_day_of_week[]">
                <?php foreach ( $intervals['days'] as $key => $label ) : ?>
                    <option value="<?php echo $key; ?>" <?php selected( isset( $pricing['from'] ) && $pricing['from'] == $key, true ) ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </td>

    <td>
        <div class="to_date">
            <input type="text" class="date-picker" name="wc_bookboats_pricing_to_date[]" value="<?php if ( $pricing['type'] == 'custom' && ! empty( $pricing['to'] ) ) echo $pricing['to'] ?>" />
        </div>
        <div class="select to_month">
            <select name="wc_bookboats_pricing_to_month[]">
                <?php foreach ( $intervals['months'] as $key => $label ) : ?>
                    <option value="<?php echo $key; ?>" <?php selected( isset( $pricing['to'] ) && $pricing['to'] == $key, true ) ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="select to_day_of_week">
            <select name="wc_bookboats_pricing_to_day_of_week[]">
                <?php foreach ( $intervals['days'] as $key => $label ) : ?>
                    <option value="<?php echo $key; ?>" <?php selected( isset( $pricing['to'] ) && $pricing['to'] == $key, true ) ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </td>

    <td>
        <div>
            <input type="time" class="time-picker" name="wc_bookboats_pricing_from_time[]" value="<?php echo ( !empty($pricing['from_time']) ? $pricing['from_time'] : '00:00' ); ?>"/>
        </div>
    </td>

    <td>
        <div>
            <input type="time" class="time-picker" name="wc_bookboats_pricing_to_time[]" value="<?php echo ( !empty($pricing['to_time']) ? $pricing['to_time'] : '23:59' ); ?>"/>
        </div>
    </td>

    <td>
        <div class="select">
            <select name="wc_bookboats_pricing_base_cost_modifier[]">
                <option <?php selected( $pricing['base_cost_modifier'], '' ); ?> value="">+</option>
                <option <?php selected( $pricing['base_cost_modifier'], 'minus' ); ?> value="minus">-</option>
                <!-- option <?php selected( $pricing['base_cost_modifier'], 'times' ); ?> value="times">&times;</option -->
                <!-- option <?php selected( $pricing['base_cost_modifier'], 'divide' ); ?> value="divide">&divide;</option -->
            </select>
        </div>
    </td>

    <td>
        <input type="number" step="0.01" name="wc_bookboats_pricing_base_cost[]" value="<?php if ( ! empty( $pricing['base_cost'] ) ) echo $pricing['base_cost']; ?>" placeholder="0" />
        <?php do_action( 'woocommerce_bookboats_after_booking_pricing_base_cost', $pricing, $post_id ); ?>
    </td>

    <td style="width: 300px;">
        <input type="text" class="description" name="wc_bookboats_pricing_description[]" value="<?php if ( !empty( $pricing['description'] ) ) echo $pricing['description']; ?>" placeholder="" />
    </td>

    <td class="boat_remove_row">X</td>

</tr>
