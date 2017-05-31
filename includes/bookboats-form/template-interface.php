<style>
    #wc-bookboats-booking-form >div {  margin-top: 30px;  }
    #wc-bookboats-booking-form label { font-weight: bold; }
    #wc-bookboats-booking-form select {  min-width: 300px; height: 40px; font-size: 18px; }
    .selectize-input { font-size: 18px !important; }
    #wc-bookboats-booking-form .desc { opacity: 0.7; }
    #wc-bookboats-booking-form .spin{ position: absolute;
        top: 0px;
        left: -30px;
    }
</style>

    <div class="form-field form-field-wide">
        <label for=""><?php _e('SELECT DATE', 'woocommerce-bookboats'); ?></label><br>
        <input readonly type="text" id="ride_date" name="ride_date" data-bind="datepicker: selectedDate" value="">
    </div>

    <div>
        <label for=""><?php _e('SELECT NUMBER OF BOATS', 'woocommerce-bookboats'); ?></label>
        <select id="list_boats" name="list_boats" data-bind="options: list_boats, optionsText: 'label', value: selectedBoats"></select>
    </div>

	<div  data-bind="visible: list_extra_time().length>1">
        <label for=""><?php _e('SELECT EXTRA TIME', 'woocommerce-bookboats'); ?></label><br>
        <select id="list_extra_time" name="list_extra_time" data-bind="myselectize: list_extra_time, myvalue: selectedExtraTime"></select>
        <div class="desc">
            <span data-bind="text: selectedExtraTime().description"></span><br>
        </div>
    </div>
    
    <div>
        <label for=""><?php _e('SELECT START POINT', 'woocommerce-bookboats'); ?></label><br>
        <!-- select id="list_start_point" name="list_start_point" data-bind="options: list_start_point, optionsText: 'title', value: selectedStartPoint"></select -->
        <select id="list_start_point" name="list_start_point" class="selectized" data-bind="myselectize: list_start_point, myvalue: selectedStartPoint">
        </select>

        <div class="desc">
            <span data-bind="text: selectedStartPoint().description"></span><br>
            (+ <span data-bind="text: selectedStartPoint().time"></span> <?php _e('minutes', 'woocommerce-bookboats'); ?>,
            + <span data-bind="text: selectedStartPoint().cost"></span><span data-bind="text: currency"></span>)
        </div>
    </div>

    <div>
        <label for=""><?php _e('SELECT END POINT', 'woocommerce-bookboats'); ?></label><br>
        <!-- select id="list_end_point" name="list_end_point" data-bind="options: list_end_point, optionsText: 'title', value: selectedEndPoint"></select -->
        <select id="list_end_point" name="list_end_point" data-bind="myselectize: list_end_point, myvalue: selectedEndPoint"></select>
        <div class="desc">
            <span data-bind="text: selectedEndPoint().description"></span><br>
            (+ <span data-bind="text: selectedEndPoint().time"></span> <?php _e('minutes', 'woocommerce-bookboats'); ?>,
            + <span data-bind="text: selectedEndPoint().cost"></span><span data-bind="text: currency"></span>)
        </div>
    </div>

    <div style="position: relative;">
        <label for=""><?php _e('SELECT HOUR', 'woocommerce-bookboats'); ?></label><span class="spin" data-bind="visible: spin"><img src="<?php echo WC_BOOKBOATS_PLUGIN_URL.'/assets/img/rolling.gif'; ?>"></span><br>
        <select id="list_hours" name="list_hours" data-bind="visible: list_hours_loaded, options: list_hours, optionsText: 'label', value: selectedHour"></select>
        <div data-bind="html: selectedHourDesc"></div>
        <div data-bind="html: list_hours_message"></div>
    </div>

    <div>
        <!-- div>Base travel time: <span data-bind="text: base_travel_time"></span> minutes</div>
        <div>Base cost: <span data-bind="text: base_cost"></span> <span data-bind="text: currency"></span></div -->
        <h4><?php _e('TOTAL TIME: ', 'woocommerce-bookboats'); ?> <span data-bind="text: totalTime"></span> <?php _e('minutes', 'woocommerce-bookboats'); ?></h4>
        <h4><?php _e('TOTAL COST: ', 'woocommerce-bookboats'); ?> <span data-bind="text: totalCost"></span> <span data-bind="text: currency"></span></h4>
        <!-- div><?php //_e('Ghost time before: ', 'woocommerce-bookboats'); ?><span data-bind="text: totalGhostBefore"></span></div -->
        <!-- div><?php //_e('Ghost time after: ', 'woocommerce-bookboats'); ?><span data-bind="text: totalGhostAfter"></span></div -->
    </div>


<!-- div style="border:1px solid red;">PENDIENTE:
<ul>
    <li>Cancelar ajax cuando dos llamadas a la vez</li>
</ul>
</div -->

<input type="hidden" name="bookboats-date" data-bind="value: selectedDateValue"></input>
<input type="hidden" name="bookboats-hour" data-bind="value: selectedHourValue"></input>
<input type="hidden" name="bookboats-boats" data-bind="value: selectedBoatsValue"></input>
<input type="hidden" name="bookboats-indexboats" data-bind="value: selectedBoatsIndex"></input>

<input type="hidden" name="bookboats-start-point[title]" data-bind="value: selectedStartPoint().title"></input>
<input type="hidden" name="bookboats-start-point[cost]" data-bind="value: selectedStartPoint().cost"></input>
<input type="hidden" name="bookboats-start-point[time]" data-bind="value: selectedStartPoint().time"></input>
<input type="hidden" name="bookboats-start-point[ghost_time]" data-bind="value: selectedStartPoint().ghost_time"></input>

<input type="hidden" name="bookboats-end-point[title]" data-bind="value: selectedEndPoint().title"></input>
<input type="hidden" name="bookboats-end-point[cost]" data-bind="value: selectedEndPoint().cost"></input>
<input type="hidden" name="bookboats-end-point[time]" data-bind="value: selectedEndPoint().time"></input>
<input type="hidden" name="bookboats-end-point[ghost_time]" data-bind="value: selectedEndPoint().ghost_time"></input>

<input type="hidden" name="bookboats-extra-time[title]" data-bind="value: selectedExtraTime().title"></input>
<input type="hidden" name="bookboats-extra-time[cost]" data-bind="value: selectedExtraTime().cost"></input>
<input type="hidden" name="bookboats-extra-time[time]" data-bind="value: selectedExtraTime().time"></input>
<input type="hidden" name="bookboats-extra-time[ghost_time]" data-bind="value: selectedExtraTime().ghost_time"></input>

<input type="hidden" name="bookboats-total-travel-time" data-bind="value: totalTime">
<input type="hidden" name="bookboats-total-ghost-time-before" data-bind="value: totalGhostBefore">
<input type="hidden" name="bookboats-total-ghost-time-after" data-bind="value: totalGhostAfter">

<input type="hidden" name="bookboats-total-cost" data-bind="value: totalCost">
