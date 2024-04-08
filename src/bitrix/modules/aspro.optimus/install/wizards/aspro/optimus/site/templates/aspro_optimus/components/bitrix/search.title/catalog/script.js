$(document).ready(function(){
	$('.top-btn.inline-search-show').click(
		function() {

			event.stopPropagation();// for not open mobile menu

			var _this = $(this);			
			var searchBlock = _this.closest('header').find('.middle-h-row .search .stitle_form');
			var searchOverlay =  _this.closest('header').find('.middle-h-row .search .search-overlay');
			var panel = $('body > #panel #bx-panel');
			var switcher = $('body > .style-switcher');

			searchBlock.addClass('fixed-search');
			panel.addClass('fixed-search-over');
			switcher.addClass('fixed-search-over');
			searchOverlay.show();

			
		}
	);

	$('.search-overlay, .close-block.inline-search-hide').click(
		function() {
			var _this = $(this);
			var searchBlock = _this.closest('header').find('.middle-h-row .search .stitle_form');
			var searchOverlay =  _this.closest('header').find('.middle-h-row .search .search-overlay');
			var panel = $('body > #panel #bx-panel');
			var switcher = $('body > .style-switcher');

			searchBlock.removeClass('fixed-search');
			panel.removeClass('fixed-search-over');
			switcher.removeClass('fixed-search-over');

			searchOverlay.hide();

		}
	);

}
);