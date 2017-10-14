jQuery(document).ready(function( $ ) {
  // Admin form click event
	$('form[name="ovr_calendar_add_event"] input[type="button"]').on('click', function(){
    var check = { fail: false, reason: ""};
    ajax.name = $("input#ovr_calendar_event_name").val();
    ajax.url = $("input#ovr_calendar_event_url").val();
    ajax.start = $("input#ovr_calendar_event_start").val();
    ajax.end = $("input#ovr_calendar_event_end").val();
    var dateRegex = "20[0-9][0-9]-[0-9][0-9]-[0-3][0-9]";
    var date = new RegExp(dateRegex);

    if( !ajax.name.trim() ) {
      // Name is blank or white space
      check.fail = true;
      check.reason = "This event needs a name.";
    }
    if (!isUrlValid(ajax.url)) {
      // invalid url
      check.fail = true;
      check.reason = check.reason.concat(" URL is invalid.");
    }
    if (!ajax.start.trim()) {
      check.fail=true;
      check.reason = check.reason.concat(" Enter a start date.");
    }
    if(!ajax.end.trim()) {
      check.fail=true;
      check.reason = check.reason.concat(" Enter an end date.");
    }
    if ( !date.test(ajax.start) ) {
      check.fail = true;
      check.reason = check.reason.concat(" Invalid date format for start date.");
    }
    if ( !date.test(ajax.end) ) {
      check.fail = true;
      check.reason = check.reason.concat(" Invalid date format for end date.");
    }
    if ( ajax.end < ajax.start ) {
      check.fail = true;
      check.reason = check.reason.concat(" End date should be AFTER start");
    }

    if ( check.fail ) {
      alert( check.reason );
    } else {
			jQuery.post(
	 			ajax.ajaxurl,
				{
	 				action : 'ovr_calendar_add_event',
	 				add_nonce : ajax.add_nonce,
					name: ajax.name,
					url: ajax.url,
					start: ajax.start,
					end: ajax.end
	 			},
	 			function( response ) {
	 				if ( response == "false" ) {
						alert("Couldn't save event");
					} else {
						alert("Event saved");
						location.reload();
					}
	 		}
		);
		}

  });
	// Delete an evenet from the existing events list
	$('table#ovr_calendar_custom_events_table i.dashicons-no').on('click', function(event){
		$.post( ajax.ajaxurl,
		{
			action: 'ovr_calendar_remove_event',
			remove_nonce: ajax.remove_nonce,
			id: event.target.id
		},
		function( response ) {
			if ( response == "false" ) {
				alert("Couldn't remove event!");
			} else {
				alert("Event removed");
				location.reload();
			}
		}
	);
});
// Change status on an event
$('table#ovr_calendar_custom_events_table select.activeInactive').on('change', function(event) {
		$.post( ajax.ajaxurl,
		{
			action: 'ovr_calendar_update_event',
			update_nonce: ajax.update_nonce,
			id: event.target.id,
			active: $(this).val()
		},
		function( response ) {
			if ( response == "false" ) {
				alert("Couldn't update event status. ");
			}
		}
	);
});
	function isUrlValid(userInput) {
    var regexQuery = "^(https?://)?(www\\.)?([-a-z0-9]{1,63}\\.)*?[a-z0-9][-a-z0-9]{0,61}[a-z0-9]\\.[a-z]{2,6}(/[-\\w@\\+\\.~#\\?&/=%]*)?$";
    var url = new RegExp(regexQuery,"i");
    if (url.test(userInput)) {
        return true;
    }
    return false;
}
});
