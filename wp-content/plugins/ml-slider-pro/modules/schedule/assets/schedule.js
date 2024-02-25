window.jQuery(function ($) {

    /**
     * Get slide id attribute. e.g. `slide-${id}`
     * 
     * @param {string} el The input[name*="[schedule] element that enables/disabled schedule
     * 
     * @return string
     */
    var slideIdAttr = function (el) {
        return el.parents('tr.slide').attr('id') || null;
    }

    /**
     * Display a clock icon with a tooltip to visually communicate 
     * if the current slide is visible or not
     * 
     * @param {string} el       The input[name*="[schedule] element that enables/disabled schedule 
     * @param {string} id       The el's id
     * @param {string} color    The hex color for the clock icon
     * @param {string} message  Text to be displayed as tooltip in the clock icon
     * 
     * @return void
     */
    var visualIndicator = function (el, id, color, message) {
        var details = $(`#${id} .slide-details`);
        var icon    = '<span class="inline-block mr-1"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock" width="18" height="18" style=""><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg></span>';
        var html    = $(`<span class="schedule_visual_indicator float-left tipsy-tooltip-top" style="margin-top:1px;" title="${message}">${icon}</span>`);
                
        if(el.prop('checked')) {
            // Remove old visual indicator if exists
            if(details.find('.schedule_visual_indicator').length) {
                $(`#${id} .schedule_visual_indicator`).remove();
            }

            // Append new visual indicator
            details.append(html);
            html.tipsy({delayIn: 500, html: true, gravity: 's'});

            $(`#${id} .schedule_visual_indicator > span`).css('color', color);
        } else {
            // Remove visual indicator
            $(`#${id} .schedule_visual_indicator`).remove();
        }
    }

    /**
     * Check if schedule is enabled and execute visualIndicator()
     * 
     * @param {string} el The input[name*="[schedule] element that enables/disabled schedule
     * 
     * @return object
     */ 
    var isScheduleEnabled = function (el) {
        var id  = slideIdAttr(el);
        
        if (!id) return;

        var slideIdNumber = $(`#${id}`).find('.toolbar-button.delete-slide').data('slide-id') || null;
        
        if (!slideIdNumber) return;

        $.ajax({
            url: metaslider.ajaxurl,
            data: {
                action: 'schedule_status',
                slide_id: slideIdNumber,
                slider_id: window.parent.metaslider_slider_id,
                nonce: metaslider_schedule.nonce
            },
            type: 'POST',
            error: function (error) {
                console.error(error.status,error.statusText);
            },
            success: function (response) {
                visualIndicator(el, id, response.data.color, response.data.message);
            }
        });
    }

    // Make visual indicator gray when schedule settings changed without saving
    $(document).on('change', '.schedule .schedule_wrapper_settings input', function() {
        var el  = $(this).parents('.schedule').find('.ms-switch-button input[name*="[schedule]"]');
        var id  = slideIdAttr(el);

        visualIndicator(el, id, '', metaslider_schedule.save_changes_text);
    });

    // Display schedule settings only when "Schedule this slide" is enabled
    $(document).on('change', '.schedule .ms-switch-button input[name*="[schedule]"]', function() {
        var el  = $(this);
        var id  = slideIdAttr(el);

        if (!id) return;

        var content = $(`#${id} .schedule_wrapper_settings`);

        if(el.prop('checked')) {
            content.show();
            $(`#${id} .schedule .schedule_settings_date_range input`).trigger('change');
        } else {
            content.hide();
            visualIndicator(el, id, '', '');
        }
    });

    // Display daily constraint settings only when start/stop and days are configured properly
    $(document).on('change', '.schedule .schedule_settings_date_range input, .schedule .schedule_settings_weekdays input', function() {
        var el  = $(this);
        var id  = slideIdAttr(el);
        
        if (!id) return;

        // Date range is enabled?
        var dateRange = $(`#${id} input[name*="[date_range]"]`).is(':checked');

        // Start showing and stop showing date time are not empty?
        var fromDate    = $(`#${id} input[name*="[from][date]"]`).val().trim();
        var toDate      = $(`#${id} input[name*="[to][date]"]`).val().trim();
        var fromHh      = $(`#${id} input[name*="[from][hh]"]`).val().trim();
        var fromMn      = $(`#${id} input[name*="[from][mn]"]`).val().trim();
        var toHh        = $(`#${id} input[name*="[to][hh]"]`).val().trim();
        var toMn        = $(`#${id} input[name*="[to][mn]"]`).val().trim();

        // Check date time format 'YYYY-MM-DD HH:mm:ss'
        var dateTimeFormat  =   /^(?:\d{4}-(?:0[1-9]|1[0-2])-(?:0[1-9]|[12][0-9]|3[01]) (?:[01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9])$/;

        var datetimeFrom    = `${fromDate} ${fromHh}:${fromMn}:00`;
        var datetimeTo      = `${toDate} ${toHh}:${toMn}:00`;

        var datesValid  = true;
        var datesOrder  = true;

        // Date and time is empty, non-valid or incomplete?
        if (!dateTimeFormat.test(datetimeFrom) || !dateTimeFormat.test(datetimeTo)) {
            datesValid = false;
        }

        // datetimeFrom is after datetimeTo?
        var date1_ = new Date(datetimeFrom);
        var date2_ = new Date(datetimeTo);

        if (date1_ >= date2_) {
            datesOrder = false;
        }

        // At least one day is checked?
        var anyDayChecked = false;
        for (let i = 0; i <= 6; i++) {
            if ($(`#${id} input[name*="[days][${i}]"]`).is(':checked')) {
                anyDayChecked = true;
                break;
            }
        }

        // All days of the week is checked?
        var allWeekdays = $(`#${id} input[name*="[all_weekdays]"]`).is(':checked');

        var content         = $(`#${id} .schedule_settings_daily_constraint`);
        var errorInvalid    = $(`#${id} .schedule_datetime_invalid_notice`);
        var errorOrder      = $(`#${id} .schedule_datetime_order_notice`);
        var errorDays       = $(`#${id} .schedule_datetime_days_notice`);

        // Display error messages
        dateRange && !datesValid ? errorInvalid.show() : errorInvalid.hide();
        dateRange && !datesOrder && datesValid ? errorOrder.show() : errorOrder.hide();
        !anyDayChecked && !allWeekdays ? errorDays.show() : errorDays.hide();

        // Are daily constraint settings configured?
        var allDayConstraint    = $(`#${id} input[name*="[all_day]"]`).is(':checked');
        var showConstraint      = $(`#${id} input[name*="[show_during_constraint]"]`).is(':checked');
        var timeConstraints     = [
            $(`#${id} input[name*="[constraint_from][hh]"]`).val().trim() || null,
            $(`#${id} input[name*="[constraint_from][mn]"]`).val().trim() || null,
            $(`#${id} input[name*="[constraint_to][hh]"]`).val().trim() || null,
            $(`#${id} input[name*="[constraint_to][mn]"]`).val().trim() || null
        ];

        // Are the "Between" hours and minutes different to '00:00'?
        var betweenOnConstraint = false;
        for(var i = 0; i < timeConstraints.length; i++) {
            if (timeConstraints[i] !== '00') {
                betweenOnConstraint = true;
                break;
            }
        };

        // Display constraint settings?
        (
            datesValid 
            && datesOrder 
            && anyDayChecked
        ) || (
            allDayConstraint 
            || showConstraint
            || betweenOnConstraint
        ) ? content.show() : content.hide();
    });

    // Display "From" & "To" settings based on "Date range" status
    $(document).on('change', '.schedule .ms-switch-button input[name*="[date_range]"]', function() {
        var el  = $(this);
        var id  = slideIdAttr(el);

        if (!id) return;

        var content = $(`#${id} .schedule_settings_from_to`);

        if (el.prop('checked')){
            content.show();
        } else {
            content.hide();
        }
    });

    // Display "All day" description and "Between" settings only based on "All day" status
    $(document).on('change', '.schedule .ms-switch-button input[name*="[all_day]"]', function() {
        var el  = $(this);
        var id  = slideIdAttr(el);

        if (!id) return;

        var content = $(`#${id} .schedule_settings_between_constraint`);
        var desc    = $(`#${id} .schedule_all_day_description`);

        if (el.prop('checked')){
            content.hide();
            desc.show();
        } else {
            content.show();
            desc.hide();
        }
    });

    // Display "Show" description when is enabled
    $(document).on('change', '.schedule .ms-switch-button input[name*="[show_during_constraint]"]', function() {
        var el  = $(this);
        var id  = slideIdAttr(el);

        if (!id) return;

        var desc = $(`#${id} .schedule_show_description`);

        if (el.prop('checked')){
            desc.show();
        } else {
            desc.hide();
        }
    });

    // Validate "Between" settings
    $(document).on('change', '.schedule .schedule_settings_between_constraint input', function() {
        var el  = $(this);
        var id  = slideIdAttr(el);

        if (!id) return;

        // From and To times are valid?
        var fromHh  = $(`#${id} input[name*="[constraint_from][hh]"]`).val().trim();
        var fromMn  = $(`#${id} input[name*="[constraint_from][mn]"]`).val().trim();
        var toHh    = $(`#${id} input[name*="[constraint_to][hh]"]`).val().trim();
        var toMn    = $(`#${id} input[name*="[constraint_to][mn]"]`).val().trim();

        // Check time format 'HH:mm:ss'
        var timeFormat  = /^(?:[01]\d|2[0-3]):[0-5]\d:[0-5]\d$/;

        var timeFrom    = `${fromHh}:${fromMn}:00`;
        var timeTo      = `${toHh}:${toMn}:00`;

        var timesValid  = true;
        var timesOrder  = true;

        // Time is empty, non-valid or incomplete?
        if (!timeFormat.test(timeFrom) || !timeFormat.test(timeTo)) {
            timesValid = false;
        }

        // datetimeFrom is after datetimeTo? - We add a static date for a proper time comparison
        var time1_ = new Date(`2023-01-01 ${timeFrom}`);
        var time2_ = new Date(`2023-01-01 ${timeTo}`);

        if (time1_ > time2_) {
            timesOrder = false;
        }

        var errorInvalid    = $(`#${id} .schedule_constraint_time_invalid_notice`);
        var errorOrder      = $(`#${id} .schedule_constraint_time_order_notice`);

        // Display error messages
        !timesValid ? errorInvalid.show() : errorInvalid.hide();
        !timesOrder && timesValid ? errorOrder.show() : errorOrder.hide();
    });

    // Reset "Between" From and To times to 00:00
    $(document).on('click', '.schedule_reset_time_constraint', function (e) {
        e.preventDefault();

        var el  = $(this);
        var id  = slideIdAttr(el);

        if (!id) return;

        // Reset each field to 00
        $(`#${id} input[name*="[constraint_from][hh]"]`).val('00');
        $(`#${id} input[name*="[constraint_from][mn]"]`).val('00');
        $(`#${id} input[name*="[constraint_to][hh]"]`).val('00');
        $(`#${id} input[name*="[constraint_to][mn]"]`).val('00');

        // Trigger change event when resetting time through the "Reset" button
        //$('.schedule .schedule_settings_between_constraint input').trigger('change');

        // Hide error notices in case are visible
        $(`#${id} .schedule_constraint_time_invalid_notice`).hide();
        $(`#${id} .schedule_constraint_time_order_notice`).hide();
    });

    // Display weekdays checkboxes when "All days of the week" is disabled
    $(document).on('change', '.schedule .ms-switch-button input[name*="[all_weekdays]"]', function() {
        var el  = $(this);
        var id  = slideIdAttr(el);

        if (!id) return;

        var content = $(`#${id} .schedule_settings_specific_weekdays`);
        var desc    = $(`#${id} .schedule_all_weekdays_description`);

        if (el.prop('checked')){
            content.hide();
            desc.show();
        } else {
            content.show();
            desc.hide();
        }
    });

    // Display visual indicator on load
    $('.schedule .ms-switch-button input[name*="[schedule]"]').each( function() {
        var el  = $(this);
        isScheduleEnabled(el);
    });
});
