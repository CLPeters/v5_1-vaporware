$('.toggle').click(function(event) {
    var div = $(this).nextAll('.toggle_container');
		div.toggle("slow");
    $(this).children('.toggle_less').toggle();
    $(this).children('.toggle_more').toggle();
    event.preventDefault()
});

$("#hideMessage").change( function(c){
	var date = new Date();
	if (this.checked) {
		date.setTime(date.getTime()+(1000*24*60*60*1000));
		expires="; expires="+date.toGMTString();
	}
	else
	{
		date.setTime(date.getTime()-1);
		expires="; expires="+date.toGMTString();
	}
	document.cookie = 'skip_redirect_message=1'+expires+'; path=/'
});