$(document).ready(function(){
	$('.articles-list.lists_block.sections .item .button_opener').on('click', function(e){
	
        const btnOpen = $(this);
        const btnOpenTitle = btnOpen.find('.opener');
        const slideBlock = btnOpen.closest('.item').find('.text.childs');
        const bOpen = slideBlock.is(':visible');
        const dur = bOpen ? 200 : 400;
        const func = (bOpen ? 'slideUp' : 'slideDown');
        const openText = (typeof(btnOpen.data('open_text')) !== 'undefined' ? btnOpen.data('open_text') : '');
        const closeText = (typeof(btnOpen.data('close_text')) !== 'undefined' ? btnOpen.data('close_text') : '');

		if(slideBlock.length){
            slideBlock.velocity(func, {
                duration: dur, 
                easing: 'easeOutQuart'
            });
            slideBlock.toggleClass('opened');
			
			if(slideBlock.hasClass('opened')){
                btnOpen.addClass('active');

				if(openText.length)
                    btnOpenTitle.text(openText);

			}else if(!slideBlock.hasClass('opened')){
                btnOpen.removeClass('active');

				if(closeText.length)
                    btnOpenTitle.text(closeText);
			}
		}
	})
});