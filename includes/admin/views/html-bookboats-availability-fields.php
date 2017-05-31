<?php
    $intervals = array();
    
    $intervals['months'] = array(
        '1'  => __( 'January', 'woocommerce-bookboats' ),
        '2'  => __( 'Febuary', 'woocommerce-bookboats' ),
        '3'  => __( 'March', 'woocommerce-bookboats' ),
        '4'  => __( 'April', 'woocommerce-bookboats' ),
        '5'  => __( 'May', 'woocommerce-bookboats' ),
        '6'  => __( 'June', 'woocommerce-bookboats' ),
        '7'  => __( 'July', 'woocommerce-bookboats' ),
        '8'  => __( 'August', 'woocommerce-bookboats' ),
        '9'  => __( 'September', 'woocommerce-bookboats' ),
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

    if ( ! isset( $availability['type'] ) ) {
        $availability['type'] = 'custom';
    }
?>

<tr>
    <td class="sort">=</td>
    <td>
        <div class="select wc_bookboats_availability_type">
            <select name="wc_bookboats_availability_type[]">
                <option value="custom" <?php selected( $availability['type'], 'custom' ); ?>><?php _e( 'Custom date range', 'woocommerce-bookboats' ); ?></option>
                <option value="months" <?php selected( $availability['type'], 'months' ); ?>><?php _e( 'Range of months', 'woocommerce-bookboats' ); ?></option>
                <option value="days" <?php selected( $availability['type'], 'days' ); ?>><?php _e( 'Range of days', 'woocommerce-bookboats' ); ?></option>
            </select>
        </div>
    </td>
    <td>
        <div class="select from_day_of_week">
            <select name="wc_bookboats_availability_from_day_of_week[]">
                <?php foreach ( $intervals['days'] as $key => $label ) : ?>
                    <option value="<?php echo $key; ?>" <?php selected( isset( $availability['from'] ) && $availability['from'] == $key, true ) ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="select from_month">
            <select name="wc_bookboats_availability_from_month[]">
                <?php foreach ( $intervals['months'] as $key => $label ) : ?>
                    <option value="<?php echo $key; ?>" <?php selected( isset( $availability['from'] ) && $availability['from'] == $key, true ) ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="from_date">
            <input type="text" class="date-picker" name="wc_bookboats_availability_from_date[]" value="<?php if ( $availability['type'] == 'custom' && ! empty( $availability['from'] ) ) echo $availability['from'] ?>" />
        </div>
    </td>
    <td>
        <div class="select to_day_of_week">
            <select name="wc_bookboats_availability_to_day_of_week[]">
                <?php foreach ( $intervals['days'] as $key => $label ) : ?>
                    <option value="<?php echo $key; ?>" <?php selected( isset( $availability['to'] ) && $availability['to'] == $key, true ) ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="select to_month">
            <select name="wc_bookboats_availability_to_month[]">
                <?php foreach ( $intervals['months'] as $key => $label ) : ?>
                    <option value="<?php echo $key; ?>" <?php selected( isset( $availability['to'] ) && $availability['to'] == $key, true ) ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="to_date">
            <input type="text" class="date-picker" name="wc_bookboats_availability_to_date[]" value="<?php if ( $availability['type'] == 'custom' && ! empty( $availability['to'] ) ) echo $availability['to']; ?>" />
        </div>
    </td>
    <td>
        <div class="select">
            <select name="wc_bookboats_availability_bookable[]">
                <option value="no" <?php selected( isset( $availability['bookable'] ) && $availability['bookable'] == 'no', true ) ?>><?php _e( 'No', 'woocommerce-bookboats' ) ;?></option>
                <option value="yes" <?php selected( isset( $availability['bookable'] ) && $availability['bookable'] == 'yes', true ) ?>><?php _e( 'Yes', 'woocommerce-bookboats' ) ;?></option>
            </select>
        </div>
    </td>
    <td class="boat_remove_row">X</td>
</tr>




