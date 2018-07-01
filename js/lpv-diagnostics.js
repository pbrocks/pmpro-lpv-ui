jQuery(document).ready(function($) {
	// var pmpro_lpv_count;		//stores cookie
	// var parts;					//cookie convert to array of 2 parts
	// var count;					//part 0 is the view count
	// var month;					//part 1 is the month
	// var newticks = []; 			// this will hold our usage this month by level
	// var mylevel = lpv_diagnostics_object.lpv_diagnostics_limit;
	
	// //what is the current month?
	// var d = new Date();
	// var thismonth = d.getMonth();

	// //get cookie
	// pmpro_lpv_count = Get_Cookie('pmpro_lpv_count');

	// if (pmpro_lpv_count) {			
	// 	//get values from cookie
	// 	parts = pmpro_lpv_count.split(';');
	// 	month = parts[1];
	// 	if(month === undefined) { month = thismonth; parts[0] = "0,0"; } // just in case, and for cookie format migration
	// 	limitparts = parts[0].split(',');
	// 	var limitarrlength = limitparts.length;
	// 	var curkey = -1;
	// 	for (var i = 0; i < limitarrlength; i ++) {
	// 		if(i % 2 == 0) {
	// 			curkey = parseInt(limitparts[i], 10);
	// 		} else {
	// 			newticks[curkey] = parseInt(limitparts[i], 10);
	// 			curkey = -1;
	// 		}
	// 	}
	// 	if (month == thismonth && newticks[mylevel] !== undefined) {
	// 		count = newticks[mylevel] + 1;	// same month as other views
	// 		newticks[mylevel]++; 			// advance it for writing to the cookie
	// 	} else if(month == thismonth) { // it's the current month, but we haven't ticked yet.
	// 		count = 1;
	// 		newticks[mylevel] = 1;
	// 	} else {
	// 		count = 1;						//new month
	// 		newticks = [];					// new month, so we don't care about old ticks
	// 		newticks[mylevel] = 1;
	// 		month = thismonth;
	// 	}
	// }
	// else {
	// 	//defaults
	// 	count = 1;
	// 	newticks[mylevel] = 1;
	// 	month = thismonth;
	// }
	// document.getElementById('demo').innerHTML = 'This is the ' + count;
/**
 * Get the value of a cookie
 * Source: https://gist.github.com/wpsmith/6cf23551dd140fb72ae7
 * @param  {String} name  The name of the cookie
 * @return {String}       The cookie value
 */
var getCookie = function (name) {
	var value = "; " + document.cookie;
	var parts = value.split("; " + name + "=");
	if (parts.length == 2) return parts.pop().split(";").shift();
};
var thisCookie = getCookie('lpv_count');
var myarr = thisCookie.split("|");
var count = Number(myarr[1])+Number(1);
if ( count > 5 ) {
	count = 0;
	document.cookie = 'lpv_count=level|' + count + '|limit; expires=Fri, 31 Dec 2024 23:59:59 GMT';
	// window.location = 'https://google.com';
	document.getElementById("demo").innerHTML = 'lpv_count=level|' + count + '|limit; expires=Fri, 31 Dec 2024 23:59:59 GMT';
} else {
	document.cookie = 'lpv_count=level|' + count + '|limit; expires=Fri, 31 Dec 2024 23:59:59 GMT';
	document.getElementById("demo").innerHTML = 'lpv_count=level|' + count + '|limit; expires=Fri, 31 Dec 2024 23:59:59 GMT';
}
	// $('#lpv_diagnostics_submit').css({"font-size": "1.3rem","color": "rebeccapurple"}).click(function() {
		$.ajax({
			type: "POST",
			url: lpv_diagnostics_object.lpv_diagnostics_ajaxurl,
			data: {
				// Variables defined from form 
				action      : 'tie_into_lpv_diagnostics',
				serialize   : $('#lpv-diagnostics-form').serialize(),
				hidden      : $('input[name=hidden]').val(),
				token       : $('input[name=token]').val(),
				expires     : $('input[name=expires]').val(),

				// Localized stuff
				userlevel    : lpv_diagnostics_object.lpv_diagnostics_user_level,
				cookie_values: lpv_diagnostics_object.lpv_diagnostics_cookie_values,
				limit        : lpv_diagnostics_object.lpv_diagnostics_limit,
				current_view : lpv_diagnostics_object.lpv_diagnostics_current_view,
				phpexpire    : lpv_diagnostics_object.lpv_diagnostics_php_expire,

				// Admin stuff
				script_name  : 'lpv-diagnostics.js',
				ajaxurl      : lpv_diagnostics_object.lpv_diagnostics_ajaxurl,
				nonce        : lpv_diagnostics_object.lpv_diagnostics_nonce,
			},
			// dataType: "JSON",
			success:function( data ) {
				$('#data-returned').html(data);
				var obj = JSON.parse(data);
				var d = new Date(obj.phpexpire);
				var exp = d.toUTCString();
				console.log( data );
				document.cookie="pmp_lpv_count=" + obj.parts + '; expires=' + exp + ';path=/';
				$('#data-returned').html('Cookie Set ' + "pmp_lpv_count=" + obj.parts + '; expires=' + exp );
			},
			error: function( jqXHR, textStatus, errorThrown ){
				console.log( errorThrown );
			}
		});
	// });
});