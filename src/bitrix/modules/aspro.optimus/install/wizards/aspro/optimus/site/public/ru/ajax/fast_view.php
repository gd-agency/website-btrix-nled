<?define("STATISTIC_SKIP_ACTIVITY_CHECK", "true");?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<a href="#" class="close jqmClose"><i></i></a>
<?global $APPLICATION, $TEMPLATE_OPTIONS;?>
<div class="form">
	<div class="form_head">
		<h2><?=\Bitrix\Main\Localization\Loc::getMessage('FAST_VIEW');?></h2>
	</div>
	<?
	if(!$TEMPLATE_OPTIONS)
	$TEMPLATE_OPTIONS = COptimus::GetTemplateOptions(SITE_ID);
	$url = htmlspecialcharsbx(urldecode($_GET['item_href']));

	\Bitrix\Main\Loader::includeModule('sale');
	\Bitrix\Main\Loader::includeModule('currency');
	\Bitrix\Main\Loader::includeModule('catalog');?>

	<script>
		var objUrl = parseUrlQuery(),
			add_url = '?FAST_VIEW=Y';
		if('clear_cache' in objUrl)
		{
			if(objUrl.clear_cache == 'Y')
				add_url += '&clear_cache=Y';
		}
		BX.ajax({
			url: '<?=$url;?>'+add_url,
			method: 'POST',
			data: BX.ajax.prepareData({'FAST_VIEW':'Y'}),
			dataType: 'html',
			processData: false,
			start: true,
			headers: [{'name': 'X-Requested-With', 'value': 'XMLHttpRequest'}],
			onfailure: function(data) {
				alert('Error connecting server');
			},
			onsuccess: function(html){
				var ob = BX.processHTML(html);
				// inject
				BX('fast_view_item').innerHTML = ob.HTML;
				BX.ajax.processScripts(ob.SCRIPT);
				$('#fast_view_item').closest('.form').addClass('init');
				$('.fast_view_frame h2').html($('#fast_view_item .title.hidden').html());

				initCountdown();
				InitZoomPict($('#fast_view_item .zoom_picture'));

				// init calculate delivery with preview
				
				if (typeof initCalculatePreview == 'function') {
					if($('#fast_view_item .item_main_info.noffer').length){
						initCalculatePreview();
						$(".animate-load").on("click", function () {
							$(this).parent().addClass("loadings");
						});

					}
				}
				setStatusButton();
				InitFlexSlider();

				setTimeout(function(){
					showTotalSummItem('Y');
				}, 100);
				
				$(window).scroll();
			}
		})
		$('.jqmClose').on('click', function(e){
			e.preventDefault();

			if( $('.jqmOverlay').length === $('.popup.show').length ) 
				$(this).closest('.jqmWindow').jqmHide();
			else
				$(this).closest('.popup').remove()
		})
	</script>
	<div id="fast_view_item"><div class="loading_block"></div></div>
</div>
