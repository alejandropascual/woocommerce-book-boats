<?php
    if ( ! isset( $pricing['title'] ) ) {
        $pricing['title'] = '';
    }
    if ( ! isset( $pricing['time'] ) ) {
        $pricing['time'] = 0;
    }
    if ( ! isset( $pricing['ghost_time'] ) ) {
        $pricing['ghost_time'] = 0;
    }
    if ( ! isset( $pricing['cost'] ) ) {
        $pricing['cost'] = 0;
    }
    if ( ! isset( $pricing['description'] ) ) {
        $pricing['description'] = '';
    }
?>
<tr>
    <td class="sort">=</td>

    <td>
        <input type="text" name="wc_bookboats_extratime_title[]" value="<?php if ( !empty( $pricing['title'] ) ) echo $pricing['title']; ?>" placeholder="" />
    </td>

    <td>
        <input type="text" name="wc_bookboats_extratime_time[]" value="<?php echo ( !empty( $pricing['time'] ) ? $pricing['time'] : 0 ); ?>" placeholder="" />
    </td>

    <td>
        <input type="text" name="wc_bookboats_extratime_ghost_time[]" value="<?php echo ( !empty( $pricing['ghost_time'] ) ? $pricing['ghost_time'] : 0 ); ?>" placeholder="" />
    </td>

    <td>
        <input type="number" step="0.01" name="wc_bookboats_extratime_cost[]" value="<?php echo ( !empty( $pricing['cost'] ) ? $pricing['cost'] : 0 ); ?>" placeholder="0" />
        <?php do_action( 'woocommerce_bookboats_after_booking_pricing_time_cost', $pricing, $post_id ); ?>
    </td>

    <td style="width: 300px;">
        <input type="text" class="description" name="wc_bookboats_extratime_description[]" value="<?php if ( !empty( $pricing['description'] ) ) echo $pricing['description']; ?>" plceholder="" />
    </td>

    <td class="boat_remove_row">X</td>

</tr>

