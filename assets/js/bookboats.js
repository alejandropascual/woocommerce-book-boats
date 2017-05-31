jQuery(document).ready(function($){

    // Util functions
    var DAT = wc_bookboats_booking_form;

    function transformDateToString(date) {
        var m = date.getMonth()+1;
        var d = date.getDate();
        var y = date.getFullYear();
        m = (m<10) ? '0'+m : m;
        d = (d<10) ? '0'+d : d;
        return y+'-'+m+'-'+d;
    }
    function daysAvailable(date) {

        var dateStr = transformDateToString(date);
        //console.log(date); console.log(dateStr);

        if ( $.inArray(dateStr, DAT.dates_not_available) != -1 ) {
            return [false];
        }
        return [true];
    }

    /*$('input[name=ride_date]').datepicker({
        gotoCurrent: true,
        defaultDate: null,
        minDate: new Date( DAT.min_date_available ),
        maxDate: new Date( DAT.max_date_available ),
        dateFormat: "yy-mm-dd",
        contrainInput: true,
        beforeShowDay: daysAvailable
    });*/

    // Knockout bindings
    // valueAccessor con Date
    ko.bindingHandlers.datepicker = {

        init: function(element, valueAccessor, allBindingsAccessor){

            //Create datepicker
            jQuery(element).datepicker({
                gotoCurrent: true,
                defaultDate: null,
                minDate: new Date( DAT.min_date_available ),
                maxDate: new Date( DAT.max_date_available ),
                //dateFormat: "yy-mm-dd",
                dateFormat: 'DD, d MM, yy',
                contrainInput: true,
                beforeShowDay: daysAvailable
            });

            //Datepicker change
            ko.utils.registerEventHandler(element, 'change', function(){

                var theobservable = valueAccessor();
                var getdate = jQuery(element).datepicker('getDate');
                if (!getdate) { theobservable(false); return; }

                var date = theobservable() || new Date();
                date.setFullYear( getdate.getFullYear() );
                date.setMonth( getdate.getMonth() );
                date.setDate( getdate.getDate() );
                theobservable( date );
            });

            // Open when clicked again
            ko.utils.registerEventHandler(element, 'click', function(){
                jQuery(element).datepicker('show');
            });

            //Dispose datepicker when remove element
            ko.utils.domNodeDisposal.addDisposeCallback(element, function() {
                jQuery(element).datepicker("destroy");
            });
        },

        update: function(element, valueAccessor){

            var value = ko.utils.unwrapObservable(valueAccessor());
            var current = jQuery(element).datepicker('getDate');
            if ( value - current !== 0 ) {
                jQuery(element).datepicker('setDate', value);
            }

        }
    };


    ko.bindingHandlers.myselectize = {

        init: function(element, valueAccessor, allBindingsAccessor){

            var options = valueAccessor()();

            $(element).selectize({
                options: valueAccessor()(),
                valueField: 'title',
                labelField: 'title',
                searchField: 'title',
                onChange: function(thevalue){
                    $.each(options, function(index, option){
                        console.log('OPTION');
                        console.log(option);
                        if ( option.title == thevalue ) {
                            allBindingsAccessor().myvalue( option );
                            return false;
                        }
                    });
                }
            });
            var selectize = $(element)[0].selectize;
            selectize.setValue(options[0].title);

        },
        update: function (element, valueAccessor, allBindingsAccessor) {

        }
    }






    // Model for knockout
    function Model() {

        var self = this;
        self.spin = ko.observable(false);

        self.product_id = DAT.product_id;

        self.selectedDate = ko.observable();
        self.selectedDateValue = ko.computed(function(){
            if ( undefined !== self.selectedDate() ) {
                var d = self.selectedDate();
                var day = d.getDate();
                day = day < 10 ? '0'+day : day;
                var month = d.getMonth()+1;
                month = month < 10 ? '0'+month : month;
                var year = d.getFullYear();
                return year + '-' + month + '-' + day;
            }
            return 0;
        });

        self.list_boats = ko.observableArray( DAT.number_of_boats );
        self.selectedBoats = ko.observable( DAT.number_of_boats[0] );
        self.selectedBoatsValue = ko.computed(function(){
             return self.selectedBoats().value;
        });

        self.list_start_point = ko.observableArray( DAT.list_start_point );
        self.list_end_point = ko.observableArray( DAT.list_end_point );
        self.list_extra_time = ko.observableArray( DAT.list_extra_time );

        self.selectedStartPoint = ko.observable( self.list_start_point()[0] );
        self.selectedEndPoint = ko.observable( self.list_end_point()[0] );
        self.selectedExtraTime = ko.observable();

        self.base_travel_time = parseInt( DAT.base_travel_time );
        self.base_cost = parseFloat( DAT.base_cost );
        self.currency = DAT.currency;

        self.list_hours = ko.observableArray([]);
        self.list_hours_loaded = ko.observable(false);
        self.list_hours_message = ko.observable( DAT.list_hours_message );
        self.selectedHour = ko.observable();
        self.selectedHourValue = ko.computed(function(){
            if ( undefined !== self.selectedHour() ) {
                return self.selectedHour().value;
            } else {
                return 0;
            }
        });
        self.selectedHourDesc = ko.computed(function(){
           if ( undefined !== self.selectedHour() && self.selectedHour().desc ) {
               return self.selectedHour().desc;
           } else {
               return '';
           }
        });

        // Boats selected
        self.selectedBoatsIndex = ko.computed(function(){
            if ( undefined === self.selectedHour() ) return '';
            var num = self.selectedBoats().value;
            var result = '';
            for(var i=0; i<num; i++) {
                result += self.selectedHour().boats[i] + ' ';
            }
            return result.trim();
        });


        // TOTALS
        //--------------------------
        self.totalTime = ko.computed(function(){
            return parseInt(self.base_travel_time) +
                parseInt( undefined !== self.selectedStartPoint() ? self.selectedStartPoint().time : 0 ) +
                parseInt( undefined !== self.selectedEndPoint() ? self.selectedEndPoint().time : 0 ) +
                parseInt( undefined !== self.selectedExtraTime() ? self.selectedExtraTime().time : 0 );
        });

        self.totalCost = ko.computed(function(){
            return parseFloat(self.selectedBoats().value) *
                (
                parseFloat(self.base_cost) +
                parseFloat( undefined !== self.selectedStartPoint() ? self.selectedStartPoint().cost : 0 ) +
                parseFloat( undefined !== self.selectedEndPoint() ? self.selectedEndPoint().cost : 0 ) +
                parseFloat( undefined !== self.selectedExtraTime() ? self.selectedExtraTime().cost : 0 ) +
                parseFloat( undefined !== self.selectedHour() ? self.selectedHour().extra_price_boat : 0 )
                );
        });

        self.totalGhostBefore = ko.computed(function(){
            return parseInt( undefined !== self.selectedStartPoint() ? self.selectedStartPoint().ghost_time : 0 ) ;
        });

        self.totalGhostAfter = ko.computed(function(){
            return  parseInt( DAT.ghost_time ) +
                    parseInt( undefined !== self.selectedEndPoint() ? self.selectedEndPoint().ghost_time : 0 ) +
                    parseInt( undefined !== self.selectedExtraTime() ? self.selectedExtraTime().ghost_time : 0 );
        });


        // AJAX GET LIST OF HOURS
        //--------------------------
        self.ajax_hours = function(){

            if ( undefined === self.selectedDate() ) return false;

            var data = {
                action: 'wc_bookboats_get_hours',
                product_id: self.product_id,
                date: self.selectedDateValue(),
                num_boats: self.selectedBoats().value,
                travel_time: self.totalTime(),
                ghost_time_before: self.totalGhostBefore(),
                ghost_time_after: self.totalGhostAfter()
            };

            //console.log('AJAX HOURS');
            //console.log( data );

            self.spin(true);
            $.ajax({
                url: DAT.ajaxurl,
                type: 'POST',
                data: data,
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                success: function( response ) {

                    console.log(response);
                    self.spin(false);

                    if ( response.data.message == 'ok' ) {

                        self.list_hours( response.data.list );
                        self.list_hours_loaded(true);
                        self.list_hours_message('');

                    } else {

                        self.list_hours_loaded(false);
                        self.list_hours_message(response.data.message);
                    }

                },
                error: function( error ) {

                    if ( error.statusText === 'abort' ) { return; }

                }
            });

        };

        setTimeout( function(){
            self.selectedDate.subscribe( self.ajax_hours );
            self.selectedBoats.subscribe( self.ajax_hours );
            self.selectedStartPoint.subscribe( self.ajax_hours );
            self.selectedEndPoint.subscribe( self.ajax_hours );
            self.selectedExtraTime.subscribe( self.ajax_hours );
            self.selectedExtraTime.subscribe(function(){
               console.log( self.selectedExtraTime() );
            });
        }, 200);


        // DISABLE ADD-TO-CART BUTTON
        //--------------------------
        self.enableDisableAddToCart = function(){
            if (self.list_hours_loaded()) {
                $('.wc-bookboats-booking-form-button').prop("disabled",false);
            } else {
                $('.wc-bookboats-booking-form-button').prop("disabled",true);
            }
        };
        self.list_hours_loaded.subscribe( self.enableDisableAddToCart );
        self.enableDisableAddToCart();

    }

    ko.applyBindings( new Model(), document.getElementById('wc-bookboats-booking-form') );

});