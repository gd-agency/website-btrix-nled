<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$frame = $this->createFrame()->begin("");
$templateData = array(
	//'TEMPLATE_THEME' => $this->GetFolder().'/themes/'.$arParams['TEMPLATE_THEME'].'/style.css',
	'TEMPLATE_CLASS' => 'bx_'.$arParams['TEMPLATE_THEME']
);
$injectId = 'bigdata_recommeded_products_'.rand();?>
<script type="application/javascript">
	BX.cookie_prefix = '<?=CUtil::JSEscape(COption::GetOptionString("main", "cookie_name", "BITRIX_SM"))?>';
	BX.cookie_domain = '<?=$APPLICATION->GetCookieDomain()?>';
	BX.current_server_time = '<?=time()?>';

	BX.ready(function(){
		bx_rcm_recommendation_event_attaching(BX('<?=$injectId?>_items'));
	});
</script>
<?if (isset($arResult['REQUEST_ITEMS'])){
	CJSCore::Init(array('ajax'));
	// component parameters
	$signer = new \Bitrix\Main\Security\Sign\Signer;
	$signedParameters = $signer->sign(
		base64_encode(serialize($arResult['_ORIGINAL_PARAMS'])),
		'bx.bd.products.recommendation'
	);
	$signedTemplate = $signer->sign($arResult['RCM_TEMPLATE'], 'bx.bd.products.recommendation');?>

	<div id="<?=$injectId?>" class="bigdata_recommended_products_container"></div>
	<script type="application/javascript">
		BX.ready(function(){
			bx_rcm_get_from_cloud(
				'<?=CUtil::JSEscape($injectId)?>',
				<?=CUtil::PhpToJSObject($arResult['RCM_PARAMS'])?>,
				{
					'parameters':'<?=CUtil::JSEscape($signedParameters)?>',
					'template': '<?=CUtil::JSEscape($signedTemplate)?>',
					'site_id': '<?=CUtil::JSEscape(SITE_ID)?>',
					'rcm': 'yes'
				}
			);
		});
	</script>

	<?$frame->end();
	return;
}
if($arResult['ITEMS']){?>
	<input type="hidden" name="bigdata_recommendation_id" value="<?=htmlspecialcharsbx($arResult['RID'])?>">
	<div id="<?=$injectId?>_items" class="bigdata_recommended_products_items">
		<?if($fast_view_text_tmp = \Bitrix\Main\Config\Option::get('aspro.optimus', 'EXPRESSION_FOR_FAST_VIEW', GetMessage('FAST_VIEW')))
			$fast_view_text = $fast_view_text_tmp;
		else
			$fast_view_text = GetMessage('FAST_VIEW');?>
		<?$class_block="s_".$this->randString();?>
		<div class="viewed_slider common_product wrapper_block recomendation <?=$class_block;?>">
			<div class="top_block">
				<?$title_block=($arParams["TITLE_BLOCK"] ? $arParams["TITLE_BLOCK"] : GetMessage('RECOMENDATION_TITLE'));?>
				<div class="title_block"><?=$title_block;?></div>
			</div>
			<ul class="viewed_navigation slider_navigation top_big  custom_flex border"></ul>
			<div class="all_wrapp basket">
				<div class="content_inner tab">
					<ul class="tabs_slider slides wr">
						<?foreach ($arResult['ITEMS'] as $key => $arItem){?>
							<?$strMainID = $this->GetEditAreaId($arItem['ID'] . $key);?>
							<li class="catalog_item" id="<?=$strMainID;?>">
								<?$strTitle = (
									isset($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]) && '' != isset($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"])
									? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]
									: $arItem['NAME']
								);
								$totalCount = COptimus::GetTotalCount($arItem);
								$arQuantityData = COptimus::GetQuantityArray($totalCount);
								$arItem["FRONT_CATALOG"]="Y";
								$arItem["RID"]=$arResult["RID"];
								$arAddToBasketData = COptimus::GetAddToBasketArray($arItem, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], true);
								
								$strMeasure='';
								if($arItem["OFFERS"]){
									$strMeasure=$arItem["MIN_PRICE"]["CATALOG_MEASURE_NAME"];
								}else{
									if (($arParams["SHOW_MEASURE"]=="Y")&&($arItem["CATALOG_MEASURE"])){
										$arMeasure = CCatalogMeasure::getList(array(), array("ID"=>$arItem["CATALOG_MEASURE"]), false, false, array())->GetNext();
										$strMeasure=$arMeasure["SYMBOL_RUS"];
									}
								}
								?>
								<?if($arParams["DISPLAY_WISH_BUTTONS"] != "N" || $arParams["DISPLAY_COMPARE"] == "Y"):?>
									<div class="like_icons">
										<?if($arItem["CAN_BUY"] && empty($arItem["OFFERS"]) && $arParams["DISPLAY_WISH_BUTTONS"] != "N"):?>
											<div class="wish_item_button">
												<span title="<?=GetMessage('CATALOG_WISH')?>" class="wish_item to" data-item="<?=$arItem["ID"]?>"><i></i></span>
												<span title="<?=GetMessage('CATALOG_WISH_OUT')?>" class="wish_item in added" style="display: none;" data-item="<?=$arItem["ID"]?>"><i></i></span>
											</div>
										<?endif;?>
										<?if($arParams["DISPLAY_COMPARE"] == "Y"):?>
											<div class="compare_item_button">
												<span title="<?=GetMessage('CATALOG_COMPARE')?>" class="compare_item to" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arItem["ID"]?>" ><i></i></span>
												<span title="<?=GetMessage('CATALOG_COMPARE_OUT')?>" class="compare_item in added" style="display: none;" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arItem["ID"]?>"><i></i></span>
											</div>
										<?endif;?>
									</div>
								<?endif;?>
								<div class="image_wrapper_block">
									<a href="<?=$arItem["DETAIL_PAGE_URL"]?>?RID=<?=$arResult["RID"]?>" class="thumb">
										<?if($arItem["PROPERTIES"]["HIT"]["VALUE_XML_ID"]){?>
											<div class="stickers">
												<?if (is_array($arItem["PROPERTIES"]["HIT"]["VALUE_XML_ID"])):?>
													<?foreach($arItem["PROPERTIES"]["HIT"]["VALUE_XML_ID"] as $key=>$class){?>
														<div><div class="sticker_<?=strtolower($class);?>"><?=$arItem["PROPERTIES"]["HIT"]["VALUE"][$key]?></div></div>
													<?}?>
												<?endif;?>
												<?if($arParams["SALE_STIKER"] && $arItem["PROPERTIES"][$arParams["SALE_STIKER"]]["VALUE"]){?>
													<div><div class="sticker_sale_text"><?=$arItem["PROPERTIES"][$arParams["SALE_STIKER"]]["VALUE"];?></div></div>
												<?}?>
											</div>
										<?}?>
										<?if(!empty($arItem["PREVIEW_PICTURE"])):?>
											<img border="0" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$strTitle;?>" title="<?=$strTitle;?>" />
										<?elseif(!empty($arItem["DETAIL_PICTURE"])):?>
											<?$img = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], array("width" => 170, "height" => 170), BX_RESIZE_IMAGE_PROPORTIONAL, true );?>
											<img border="0" src="<?=$img["src"]?>" alt="<?=$strTitle;?>" title="<?=$strTitle;?>" />
										<?else:?>
											<img border="0" src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=$strTitle;?>" title="<?=$strTitle;?>" />
										<?endif;?>
									</a>
									<div class="fast_view_block" data-event="jqm" data-param-form_id="fast_view" data-param-iblock_id="<?=$arParams["IBLOCK_ID"];?>" data-param-id="<?=$arItem["ID"];?>" data-param-item_href="<?=urlencode($arItem["DETAIL_PAGE_URL"]);?>" data-name="fast_view"><?=$fast_view_text;?></div>
								</div>
								<div class="item_info">
									<div class="item-title">
										<a href="<?=$arItem["DETAIL_PAGE_URL"]?>?RID=<?=$arResult["RID"]?>"><span><?=$arItem["NAME"]?></span></a>
									</div>
									<?if($arParams["SHOW_RATING"] == "Y"):?>
										<div class="rating">
											<?$APPLICATION->IncludeComponent(
											   "bitrix:iblock.vote",
											   "element_rating_front",
											   Array(
												  "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
												  "IBLOCK_ID" => $arItem["IBLOCK_ID"],
												  "ELEMENT_ID" =>$arItem["ID"],
												  "MAX_VOTE" => 5,
												  "VOTE_NAMES" => array(),
												  "CACHE_TYPE" => $arParams["CACHE_TYPE"],
												  "CACHE_TIME" => $arParams["CACHE_TIME"],
												  "DISPLAY_AS_RATING" => 'vote_avg'
											   ),
											   $component, array("HIDE_ICONS" =>"Y")
											);?>
										</div>
									<?endif;?>
									<?=$arQuantityData["HTML"];?>
									<div class="cost prices clearfix">
										<?if($arItem["OFFERS"]):?>
											<?\Aspro\Functions\CAsproOptimusSku::showItemPrices($arParams, $arItem, $item_id, $min_price_id);?>
										<?elseif($arItem["PRICES"]):?>
											<?\Aspro\Functions\CAsproOptimusItem::showItemPrices($arParams, $arItem["PRICES"], $strMeasure, $min_price_id);?>
										<?endif;?>
									</div>
									<div class="buttons_block clearfix">
										<?=$arAddToBasketData["HTML"]?>
									</div>
								</div>
							</li>
						<?}?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	
	<script type="text/javascript">
		$(document).ready(function(){
			var flexsliderItemWidth = 220;
			var flexsliderItemMargin = 15;
			var sliderWidth = $('.specials_slider_wrapp').outerWidth();
			$('.viewed_slider.<?=$class_block;?> .content_inner').flexslider({
				animation: 'slide',
				selector: '.slides > li',
				slideshow: false,
				animationSpeed: 600,
				directionNav: true,
				controlNav: false,
				pauseOnHover: true,
				animationLoop: true, 
				itemWidth: flexsliderItemWidth,
				itemMargin: flexsliderItemMargin,
				controlsContainer: '.viewed_navigation',
				start: function(slider){
					slider.find('li').css('opacity', 1);
				}
			});
			var itemsButtonsHeight = $('.wrapper_block.<?=$class_block;?> .wr > li .buttons_block').height();
			$('.wrapper_block.<?=$class_block;?> .wr .buttons_block').hide();
			if($('.wrapper_block.<?=$class_block;?> .all_wrapp .content_inner').attr('data-hover') ==undefined){
				var tabsContentUnhover = ($('.wrapper_block.<?=$class_block;?> .all_wrapp').height() * 1)+20;
				var tabsContentHover = tabsContentUnhover + itemsButtonsHeight+50;

				$('.wrapper_block.<?=$class_block;?> .slides').equalize({children: '.item-title'}); 
				$('.wrapper_block.<?=$class_block;?> .slides').equalize({children: '.item_info'}); 
				$('.wrapper_block.<?=$class_block;?> .slides').equalize({children: '.catalog_item'});
				
				$('.wrapper_block.<?=$class_block;?> .all_wrapp .content_inner').attr('data-unhover', tabsContentUnhover);
				$('.wrapper_block.<?=$class_block;?> .all_wrapp .content_inner').attr('data-hover', tabsContentHover);
				$('.wrapper_block.<?=$class_block;?> .all_wrapp').height(tabsContentUnhover);
				$('.wrapper_block.<?=$class_block;?> .all_wrapp .content_inner').addClass('absolute');
			}

			$('.wrapper_block.<?=$class_block;?> .wr > li').hover(
				function(){
					var tabsContentHover = $(this).closest('.content_inner').attr('data-hover') * 1;
					$(this).closest('.content_inner').fadeTo(100, 1);
					$(this).closest('.content_inner').stop().css({'height': tabsContentHover});
					$(this).find('.buttons_block').fadeIn(450, 'easeOutCirc');
				},
				function(){
					var tabsContentUnhoverHover = $(this).closest('.content_inner').attr('data-unhover') * 1;
					$(this).closest('.content_inner').stop().animate({'height': tabsContentUnhoverHover}, 100);
					$(this).find('.buttons_block').stop().fadeOut(233);
				}
			);			
		})
	</script>
<?}
$frame->end();?>