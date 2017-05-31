<div class="wrap woocommerce">
    <div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
    <h2><?php _e( 'Bookings by day', 'woocommerce-bookings' ); ?></h2>

    <form method="get" id="mainform" enctype="multipart/form-data" class="wc_bookings_calendar_form">
        <input type="hidden" name="post_type" value="wc_bookboat" />
        <input type="hidden" name="page" value="wc_bookboats_calendar" />
        <input type="hidden" name="view" value="<?php echo esc_attr( $view ); ?>" />
        <input type="hidden" name="tab" value="calendar" />

        <div class="tablenav">
            <div class="date_selector">
                <a class="prev" href="<?php echo esc_url( add_query_arg( 'calendar_day', date_i18n( 'Y-m-d', strtotime( '-1 day', strtotime( $day ) ) ) ) ); ?>">&larr;</a>
                <div>
                    <input type="text" name="calendar_day" class="calendar_day" placeholder="yyyy-mm-dd" value="<?php echo esc_attr( $day ); ?>" />
                </div>
                <a class="next" href="<?php echo esc_url( add_query_arg( 'calendar_day', date_i18n( 'Y-m-d', strtotime( '+1 day', strtotime( $day ) ) ) ) ); ?>">&rarr;</a>
            </div>
            <div class="views">
                <a class="month" href="<?php echo esc_url( add_query_arg( 'view', 'month' ) ); ?>"><?php _e( 'Month View', 'woocommerce-bookings' ); ?></a>
            </div>
            <script type="text/javascript">
                jQuery(function() {
                    jQuery(".tablenav select, .tablenav input").change(function() {
                        jQuery("#mainform").submit();
                    });
                    jQuery( '.calendar_day' ).datepicker({
                        dateFormat: 'yy-mm-dd',
                        numberOfMonths: 1,
                    });
                    // Tooltips
                    jQuery(".bookings li").tipTip({
                        'attribute' : 'data-tip',
                        'fadeIn' : 50,
                        'fadeOut' : 50,
                        'delay' : 200
                    });
                });
            </script>
        </div>

        <div class="calendar_days">
            <ul class="hours">
                <?php for ( $i = 0; $i < 24; $i ++ ) : ?>
                    <li><label><?php if ( $i != 0 && $i != 24 ) echo date_i18n( 'g:ia', strtotime( "midnight +{$i} hour" ) ); ?></label></li>
                <?php endfor; ?>
            </ul>
            <ul class="bookings">
                <?php $this->list_bookings_for_day( $_GET['calendar_day'] ); ?>
            </ul>
        </div>
    </form>
</div>