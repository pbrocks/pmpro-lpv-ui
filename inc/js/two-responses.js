function operate_on_data1( data1 ) {
	console.log( "Operating on data1: " + data1 );
}

function operate_on_data2( data2 ) {
	console.log( "Operating on data2: " + data2 );

	var getCookie = function (name) {
		var value = "; " + document.cookie;
		var parts = value.split("; " + name + "=");
		if (parts.length == 2) return parts.pop().split(";").shift();
	};
	var thisCookie = getCookie('pmpro_lpv_count');
	var cookieArray = thisCookie.split("|");
	var level = cookieArray[0];
	var count = Number(cookieArray[1]);
	var limit = cookieArray[2];
	document.getElementById("clear-button-paste").innerHTML = 'The variable, thisCookie, gets split into its constituent parts of 0=level ' + level + ', 1=count ' + count + ', 2=limit ' + limit + '!!<br>';
}

// var getCookie = function (name) {
// 	var value = "; " + document.cookie;
// 	var parts = value.split("; " + name + "=");
// 	if (parts.length == 2) return parts.pop().split(";").shift();
// };

// var thisCookie = getCookie('pmpro_lpv_count');

jQuery(document).ready(function($) {
	$('#two-responses-submit').click(function(e) {
		e.preventDefault();
		$.ajax({
			type: "POST",
			url: two_responses_object.two_responses_ajaxurl,
			data: {
				// Variables defined from form
				action       : 'two_responses_action',
				serialize    : $('#two-responses-form').serialize(),
				hidden       : $('#two-responses').val(),

				// Localized stuff lpv_diagnostics_action
				userlevel    : two_responses_object.two_responses_user_level,
				cookie_values: two_responses_object.two_responses_cookie_values,
				limit        : two_responses_object.two_responses_limit,
				redirect     : two_responses_object.two_responses_redirect,
				phpexpire    : two_responses_object.two_responses_php_expire,
				response     : two_responses_object.two_responses_action,

				// Admin stuff
				script_name : 'two-responses.js',
				ajaxurl     : two_responses_object.two_responses_ajaxurl,
				nonce       : two_responses_object.two_responses_nonce,
			},
			// dataType: "json",
			success:function( data ) {
				$('#clear-button-paste').html('Some array, hopefully' + data);
				// operate_on_data1( data.data1 );
				// operate_on_data2( data.data2 );
			},
			error: function( jqXHR, textStatus, errorThrown ){
				console.log( errorThrown );
			}
		});
	});
});