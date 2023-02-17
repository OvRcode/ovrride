jQuery(document).ready(function(){

    /*
     * Add datepicker to start and end date boxes.
     */
    jQuery('.woooe-datepicker').datepicker({ dateFormat : "dd-mm-yy" });

    /*
     * Reorder element up
     */
    jQuery('.woooe-up').on('click', function(e){
        
        e.preventDefault();
       
       var this_element =  jQuery(this).parents('.reorder-row');
       var prev_element =  jQuery(this_element).prev('.reorder-row');
       
       if(prev_element.length > 0){
           jQuery(prev_element).before(jQuery(this_element));
       }
    });

    /*
     * Reorder element down
     */
    jQuery('.woooe-down').on('click', function(e){
        
        e.preventDefault();
       
       var this_element =  jQuery(this).parents('.reorder-row');
       var next_element =  jQuery(this_element).next('.reorder-row');
       
       if(next_element.length > 0){
           jQuery(next_element).after(jQuery(this_element));
       }
    });


    /*
     * 
     * @param Object params
     * @returns Void
     */
    var getReport = function( params ){

        jQuery.post(ajaxurl, params, function(response){

            if(response.success === true){

                var total_records = parseInt(response.data.total_records);
                var chunk_size = parseInt(response.data.chunk_size);
                var offset = parseInt(response.data.offset);
                var remaining_records = (total_records - (response.data.chunk_size * ++offset));

                if( remaining_records > 0 ){
                    ++response.data.offset;
                    new getReport(response.data);
                }

                if( remaining_records <= 0 ){
                    jQuery('body').trigger('woooe_process_completed', response);
                }
            }else{
                jQuery('body').trigger('woooe_process_completed', response);
            }
        });
    };

    /*
     * Fire ajax request for order export
     */
    jQuery('#woooe_field_export_now').on('click', function(){

       var start_date = jQuery("#woooe_field_start_date").val();
       var end_date   = jQuery("#woooe_field_end_date").val();

       var data = {
         action: 'woooe_get_report',
         startDate : start_date,  
         endDate : end_date,
       };
       
        jQuery("#woooe-loader").show();
        jQuery("#woooe-error-msg").removeClass('error').html('').hide();

        jQuery.post( ajaxurl, data, function(response){

            if( response.success === true ){
                if(parseInt(response.data.total_records) > 0){
                    new getReport(response.data);
                }
            }else{
                //Trigger completion and display error.
                jQuery('body').trigger('woooe_process_completed', response);
            }
        });
       
    });

    /*
     * Event listener fires when fetching of records are done.
     */
    jQuery('body').on('woooe_process_completed', function(event, response){

        jQuery("#woooe-loader").hide();

        if(response.success === true){
            window.location = response.data.fileurl;
        }else if(response.success === false){
            jQuery("#woooe-error-msg").addClass('error').html(response.data).show();
        }
    });

    /**
     * Dismiss notice for 24 hours
     */
    jQuery('.woooe-addon.is-dismissible .notice-dismiss').on('click', function(e){
        
        jQuery.post(ajaxurl, {action: 'addon_notice_dismiss'}, function(response){
            if(response.success !== true){
                alert(response.data);
            }
        });
    });

  /**
   * Field filter UI.
   */
  jQuery("#wooe-fields-filter .button").on('click', function(e){

        e.preventDefault();

        var target = jQuery(this), current = jQuery("#wooe-fields-filter .current"), filter = target.data('filter');
        current.removeClass('current');
        target.addClass('current');
        filter = ( 'all-fields' === filter ) ? 'woooe-field' : filter;

        //Itrate through each field
        jQuery('.woooe-field').each(function(){

          jQuery(this).parents('tr').show();

          //Hide rows do not matching criteria.
          if( !jQuery(this).hasClass( filter ) ){
            jQuery(this).parents('tr').hide();
          }

        });
    });
});