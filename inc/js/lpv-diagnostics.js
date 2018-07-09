jQuery(document).ready(function($) {
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
	var thisCookie = getCookie('pmpro_lpv_count');
	if (thisCookie == null) {
		count = 0;
		limit = 5;
	} else {
		var cookieArray = thisCookie.split("|");
		var count = Number(cookieArray[1]);
		var limit = cookieArray[2];
	}
	if ( isNaN(count) ) {
		count = 0;
	}

	var obj = {
		'phpexpire' : lpv_diagnostics_object.lpv_diagnostics_php_expire,
		'lpv_limit' : lpv_diagnostics_object.lpv_diagnostics_lpv_limit.views,
		'userlevel' : lpv_diagnostics_object.lpv_diagnostics_user_level,
		'redirect'  : lpv_diagnostics_object.lpv_diagnostics_redirect,
		'response'  : lpv_diagnostics_object.lpv_diagnostics_response,
	}
	var d = new Date(obj.phpexpire);
	var exp = d.toUTCString();

	elem = $('body');
	if (elem.hasClass('single')){
		var upcount = Number(count) + Number(1);
	} else {
		var upcount = count;
	}
	if ( obj.lpv_limit <= upcount ) {
		var upcount = 0;
	}
	var lpv_array = obj.userlevel + '|' + upcount + '|' + obj.lpv_limit;

	var remaining = obj.lpv_limit - upcount;
	document.cookie="pmpro_lpv_count=" + lpv_array + '; expires=' + exp + ';path=/';

	$('#some-other-paste').html('Need to calculate expiration<br>Set cookie at 0, separately increment<br>Cookie Set ' + "pmpro_lpv_count=" + upcount + '; expires=' + exp );
	// if ( upcount == 0 ) {
	if ( upcount > 0 ) {
		$('#lpv_count').html(remaining);
		$('#lpv_limit').html(obj.lpv_limit);
	}  else {
		$('#lpv_count').html(obj.lpv_limit);
		$('#lpv_limit').html(obj.lpv_limit);
	}
	if ( 1 == Number(remaining) && 'footer' == obj.response ) {
		$('#lpv-footer').css({'padding':'10rem 0','font-size':'3rem',}).append('response ' + obj.response);
		$('#footer-break').css({'display':'block'}).delay(1500);
	}
	if ( 1 == Number(remaining) && 'popup' == obj.response ) {
		$('#lpv-modal').css({'display':'block'});
		$('#foter-text').html('Yo modal = LPV love ' + remaining + ' remaining');
	}
	if ( 1 == Number(remaining) && 'redirect' == obj.response ) {
		$('#foter-text').html( 'We\'ll use "window.location = obj.redirect;" to send to ' + obj.redirect );
		window.location = obj.redirect;
	} else  {
		$('#foter-text').html('no modal LPV we love ' + remaining + ' remaining ' + ' response == ' +  obj.response );
	}
});
