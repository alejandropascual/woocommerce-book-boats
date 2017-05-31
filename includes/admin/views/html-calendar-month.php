<div class="wrap woocommerce">
    <div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
    <h2><?php _e( 'Bookings by month', 'woocommerce-bookboats' ); ?></h2>

    <form method="get" id="mainform" enctype="multipart/form-data" class="wc_bookings_calendar_form">
        <input type="hidden" name="post_type" value="wc_bookboat" />
        <input type="hidden" name="page" value="wc_bookboats_calendar" />
        <input type="hidden" name="calendar_month" value="<?php echo absint( $month ); ?>" />
        <input type="hidden" name="view" value="<?php echo esc_attr( $view ); ?>" />
        <input type="hidden" name="tab" value="calendar" />

        <div class="tablenav">
            <div class="date_selector">
                <a class="prev" href="<?php echo esc_url( add_query_arg( array( 'calendar_year' => $year, 'calendar_month' => $month - 1 ) ) ); ?>">&larr;</a>
                <div>
                    <select name="calendar_month">
                        <?php for ( $i = 1; $i <= 12; $i ++ ) : ?>
                            <option value="<?php echo $i; ?>" <?php selected( $month, $i ); ?>><?php echo ucfirst( date_i18n( 'M', strtotime( '2013-' . $i . '-01' ) ) ); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div>
                    <select name="calendar_year">
                        <?php for ( $i = ( date( 'Y' ) - 1 ); $i <= ( date( 'Y' ) + 5 ); $i ++ ) : ?>
                            <option value="<?php echo $i; ?>" <?php selected( $year, $i ); ?>><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <a class="next" href="<?php echo esc_url( add_query_arg( array( 'calendar_year' => $year, 'calendar_month' => $month + 1 ) ) ); ?>">&rarr;</a>
            </div>
            <div class="views">
                <a class="day" href="<?php echo esc_url( add_query_arg( 'view', 'day' ) ); ?>"><?php _e( 'Day View', 'woocommerce-bookboats' ); ?></a>
            </div>
            <script type="text/javascript">
                jQuery(".tablenav select").change(function() {
                    jQuery("#mainform").submit();
                });
            </script>
        </div>

        <table class="wc_bookings_calendar widefat">
            <thead>
            <tr>
                <?php for ( $ii = get_option( 'start_of_week', 1 ); $ii < get_option( 'start_of_week', 1 ) + 7; $ii ++ ) : ?>
                    <th><?php echo date_i18n( _x( 'l', 'date format', 'woocommerce-bookings' ), strtotime( "next sunday +{$ii} day" ) ); ?></th>
                <?php endfor; ?>
            </tr>
            </thead>
            <tbody>
            <tr>
                <?php
                $timestamp = $start_timestamp;
                $index     = 0;
                while ( $timestamp <= $end_timestamp ) :
                    ?>
                    <td width="14.285%" class="<?php
                    if ( date( 'n', $timestamp ) != absint( $month ) ) {
                        echo 'calendar-diff-month';
                    }
                    ?>">
                        <a href="<?php echo admin_url( 'edit.php?post_type=wc_bookboat&page=wc_bookboats_calendar&view=day&tab=calendar&calendar_day=' . date( 'Y-m-d', $timestamp ) ); ?>">
                            <?php echo date( 'd', $timestamp ); ?>
                        </a>
                        <div class="bookings">
                            <ul>
                                <?php $this->list_bookings(
                                    date( 'd', $timestamp ),
                                    date( 'm', $timestamp ),
                                    date( 'Y', $timestamp )
                                ); ?>
                            </ul>
                        </div>
                    </td>
                    <?php
                    $timestamp = strtotime( '+1 day', $timestamp );
                    $index ++;

                    if ( $index % 7 === 0 ) {
                        echo '</tr><tr>';
                    }
                endwhile;
                ?>
            </tr>
            </tbody>
        </table>
    </form>
</div>