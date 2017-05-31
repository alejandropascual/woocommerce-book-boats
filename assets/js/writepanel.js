jQuery(document).ready(function($) {

    // Add taxes options to the product
    $('.form-field._tax_status_field ').parent().addClass('show_if_bookboats');


    // Show / Hide tabs content
    $('body').on( 'woocommerce-product-type-change', function( type ) {

        if ( $('#product-type').val() !== 'bookboats' ) {
            //$('#_wc_bookboats_has_persons').removeAttr( 'checked' );
            //$('#_wc_bookboats_has_resources').removeAttr( 'checked' );
            $('.show_if_bookboats').hide();
            //$('.shipping_tab, .attribute_tab, .advanced_tab').hide();
        } else {
            $('.show_if_bookboats').show();
            //$('.shipping_tab, .attribute_tab, .advanced_tab').show();
        }
        wc_bookboats_trigger_change_events();
    });

    $('body').trigger('woocommerce-product-type-change');


    function wc_bookboats_trigger_change_events() {
        $('.wc_bookboats_pricing_type select, .wc_bookboats_availability_type select').change();
    }


    // Change select
    $('#bookboats_costs, #bookboats_availability').on('change', '.wc_bookboats_pricing_type select, .wc_bookboats_availability_type select', function(){
        var value = $(this).val();
        var row   = $(this).closest('tr');

        $(row).find('.from_date, .from_month, .from_day_of_week').hide();
        $(row).find('.to_date, .to_month, .to_day_of_week').hide();

        if ( value == 'custom' ) {
            $(row).find('.from_date, .to_date').show();
        }
        if ( value == 'months' ) {
            $(row).find('.from_month, .to_month').show();
        }
        if ( value == 'days' ) {
            $(row).find('.from_day_of_week, .to_day_of_week').show();
        }

    });

    wc_bookboats_trigger_change_events();

    // Sortable
    $('#boat_pricing_rows, #boat_pricing_rows_time, #boat_availability_rows').sortable({
        items:'tr',
        cursor:'move',
        axis:'y',
        handle: '.sort',
        scrollSensitivity:40,
        forcePlaceholderSize: true,
        helper: 'clone',
        opacity: 0.65,
        placeholder: 'wc-metabox-sortable-placeholder',
        start:function(event,ui){
            ui.item.css('background-color','#f6f6f6');
        },
        stop:function(event,ui){
            ui.item.removeAttr('style');
        }
    });

    $( '.date-picker' ).datepicker({
        dateFormat: 'yy-mm-dd',
        numberOfMonths: 1,
        //showButtonPanel: true,
        //showOn: 'button',
        //buttonImage: wc_bookings_writepanel_js_params.calendar_image,
        //buttonImageOnly: true
    });

    // Add row in a table
    $('.boat_add_row').click(function(){
        $(this).closest('table').find('tbody').append( $(this).data('row') );
        $('body').trigger('boat_row_added');
        return false;
    });

    $('body').on('click', 'td.boat_remove_row', function(){
        $(this).closest('tr').remove();
        return false;
    });

    // Row added to a table
    $('body').on('boat_row_added', function(){

        $('.wc_bookboats_pricing_type select, .wc_bookboats_availability_type select').change();

        $( '.date-picker' ).datepicker({
            dateFormat: 'yy-mm-dd',
            numberOfMonths: 1,
            //showButtonPanel: true,
            //showOn: 'button',
            //buttonImage: wc_bookings_writepanel_js_params.calendar_image,
            //buttonImageOnly: true
        });
    });



});