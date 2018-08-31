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
	 var thisCookie = getCookie('pmpro_lpv_ct');
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

	 $.ajax({
	 	type: "POST",
	 	url: lpv_cookie_object.lpv_cookie_ajaxurl,
	 	data: {
			// Variables defined from form 
			action      : 'tie_into_lpv_cookie',
			serialize   : $('#lpv-cookie-form').serialize(),
			hidden      : $('input[name=hidden]').val(),
			token       : $('input[name=token]').val(),
			expires     : $('input[name=expires]').val(),

			// Localized stuff lpv_cookie_action
			userlevel    : lpv_cookie_object.lpv_cookie_user_level,
			cookie_values: lpv_cookie_object.lpv_cookie_cookie_values,
			limit        : lpv_cookie_object.lpv_cookie_lpv_limit,
			redirect     : lpv_cookie_object.lpv_cookie_redirect,
			phpexpire    : lpv_cookie_object.lpv_cookie_php_expire,
			response     : lpv_cookie_object.lpv_cookie_response,

			// Admin stuff
			script_name  : 'lpv-set-cookie.js',
			ajaxurl      : lpv_cookie_object.lpv_cookie_ajaxurl,
			nonce        : lpv_cookie_object.lpv_cookie_nonce,
		},
		// dataType: "JSON",
		success:function( data ) {
			// $('.modal-body').html(data).css({'text-align':'left'});

			var obj = JSON.parse(data);
			var d = new Date(obj.phpexpire);
			var exp = d.toUTCString();

			elem = $('body');
			if ( Number(count) >= obj.lpv_limit ) {
				alert('should not continue');
			}
			if (elem.hasClass('single')){
				var upcount = Number(count) - Number(1);
			} else {
				var upcount = count;
			}
			if ( obj.lpv_limit == upcount ) {
				var upcount = 0;
			}
			var lpv_array = obj.userlevel + '|' + upcount + '|' + obj.lpv_limit;


			var remaining = obj.lpv_limit - upcount;
			document.cookie="pmpro_lpv_ct=" + lpv_array + '; expires=' + exp + ';path=/';

			if ( Number(remaining) <= 0 ) {
				$('#lpv_count').html('0');
				$('#userlevel').html('userlevel=' + obj.userlevel);
				$('#upcount').html('upcount=' + upcount);
				$('#lpvlimit').html('lpvlimit=' + obj.lpv_limit);
				$('#lpv_limit').html(obj.lpv_limit);
				if ( 0 >= Number(remaining) && 'footer' == obj.response ) {
					$('#lpv-footer').css({'padding':'8rem 0'});
					$('.pmpro_level.medium-12').css({'padding':'2px','background':'rgba(22,22,22,.69)'});
					$('#lpv-footer-levels').css({'display':'block'}).delay(1500);
					$('#lpv-footer-display').css({'display':'none'}).delay(1500);
				}
				if ( 0 >= Number(remaining) && 'popup' == obj.response ) {
					$('#lpv-modal').css({'display':'block'});
					$('#header-text').html('Yo modal = LPV | love ' + remaining + ' remaining');
				}
				if ( 0 >= Number(remaining) && 'redirect' == obj.response ) {
					$('#header-text').html( 'We\'ll use "window.location = obj.redirect;" to send to ' + obj.redirect ); 
					window.location = obj.redirect;
				} else  {
					$('#header-text').html('no modal LPV | remaining= ' + remaining + ' remaining ' + '| response == ' +  obj.response );
				} 
			} else {
				$('#lpv_count').html(remaining);
				$('#lpv_limit').html(obj.lpv_limit);
			} 
		},
		error: function( jqXHR, textStatus, errorThrown ){
			console.log( errorThrown );
		}
	});
});

