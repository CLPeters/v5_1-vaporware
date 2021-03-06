function changeChapter() {
	var chap_change = document.getElementById("chap_change");
	var selectedValue = chap_change.options[chap_change.selectedIndex].value;
window.location=url+selectedValue;
}

$('#story-container').on("click", ".openform", function(e)
{
	$("a.opener").removeAttr('href');
	var form = $(this);
	var review_container = document.getElementById('review-container');

	if ( form.nextAll('.ajaxform:first').html() == '' )
	{
		//clear all old forms
		$(review_container).find('.ajaxform').hide('slow').html('');
		var ajaxurl = base + '/story/ajax/review_comment_form';
		var data = { captcha: $(this).data("captcha"), structure : { childof: $(this).attr('id'), level: $(this).data("level"), element: $(this).data("story"), subelement: $(this).data("chapter") } };
		review_data ( ajaxurl, data, form );
	}
});

function review_data ( ajax_url, ajax_data, ajax_form )
{
	$.ajax({
        type: "POST",
        url: ajax_url,
        data: ajax_data,
		success: function (html) {	
			html = html['BODY'];
			number = 0;
			//create the new form and make it visible
			ajax_form.nextAll('.ajaxform:first').hide().html(html[1]).show('slow');
			if ( html[2] )
			{
				// will currently redirect to the currently showing review, even if a comment was written for a different one
				var redirect = $(location).attr('protocol') + '//' + $(location).attr('hostname') + html[2];
				// new call would fix this, but requires code change in story model
				//var redirect = $(location).attr('href');
				$.redirectPost(redirect, {s_data: ajax_data});
			}
			$('.reviewButton').click(function(review) {
				review.preventDefault();
				review_act = ($(this).data("action"));
				
				if(review_act=="submit")
				{
					review_data ( ajax_url, $('#write_review').serialize(), ajax_form );
				}
			});

		}		
    });
	if ( ajax_data.captcha == 1 ) getCaptchaImage();
}
