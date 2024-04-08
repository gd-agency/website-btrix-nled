$(document).ready(function() {
	$.ajax({
		url: arOptimusOptions['SITE_DIR']+'include/mainpage/comp_instagram.php',
		data: {'AJAX_REQUEST_INSTAGRAM': 'Y', 'SHOW_INSTAGRAM': arOptimusOptions['THEME']['INSTAGRAMM_INDEX']},
		type: 'POST',
		success: function(html){
                        if($(html).find(".image").length){
                            $('.instagram_ajax').html(html).addClass('loaded');
                            var eventdata = {action:'instagrammLoaded'};
                            BX.onCustomEvent('onCompleteAction', [eventdata]);
                            $('.instagram_ajax').height('auto');
                            $('.instagram_ajax').css({"min-height" : "295px"});
                        }
                        else {
                            $('.instagram_ajax').remove();
                        }
		}
	});
});