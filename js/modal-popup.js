jQuery(document).ready(function($) {
	$('#modal-button').click(function(){
        $("#this-modal").toggle();
    });
	$('.close').click(function(){
        $("#this-modal").toggle('900');
    });
});