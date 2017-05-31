<tr>
    <td class="sort">=</td>

    <td>
        <div class="from_date">
            <input type="time" class="time-picker" name="wc_bookboats_non_available_from_hour[]" value="<?php if ( ! empty( $availability['from'] ) ) echo $availability['from'] ?>" />
        </div>
    </td>
    <td>
        <div class="to_date">
            <input type="time" class="time-picker" name="wc_bookboats_non_available_to_hour[]" value="<?php if ( ! empty( $availability['to'] ) ) echo $availability['to']; ?>" />
        </div>
    </td>

    <td class="boat_remove_row">X</td>
</tr>

