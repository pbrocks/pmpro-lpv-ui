function operate_on_data1( data1 ) {
	console.log( "Operating on data1: " + data1 );
}

function operate_on_data2( data2 ) {
	console.log( "Operating on data2: " + data2 );
}

jQuery(document).ready(function($) {
    $('#two_responses_submit').click(function() {
        $.ajax({
            type: "POST",
            url: two_responses_script.two_responses_ajaxurl,
            data: {
                // Variables defined from form
                action       : 'two_responses_action',
                serialize    : $('#two-responses-form').serialize(),
                custjson     : $('#custjson').val(),
                custnumber   : $('#custnumber').val(),
                response     : $('#response-two').val(),

				// Admin stuff
				script_name : 'if-two-responses.js',
				ajaxurl     : two_responses_script.two_responses_ajaxurl,
				nonce       : two_responses_script.two_responses_nonce,
              },
			dataType: "json",
            success:function( data ) {
                operate_on_data1( data.data1 );
				operate_on_data2( data.data2 );
            },
            error: function( jqXHR, textStatus, errorThrown ){
                console.log( errorThrown );
            }
        });
    });
});