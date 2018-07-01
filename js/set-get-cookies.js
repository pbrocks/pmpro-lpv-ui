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