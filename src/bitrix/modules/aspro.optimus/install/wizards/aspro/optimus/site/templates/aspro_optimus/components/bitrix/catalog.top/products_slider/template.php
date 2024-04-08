<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?use COptimus as Solution;?>
<? $this->setFrameMode( true ); ?>
<?
$sliderID  = "specials_slider_wrapp_".$this->randString();
$notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
$arNotify = Solution::unserialize($notifyOption);
?>
<?if($arResult["ITEMS"]):?>
	<?if($fast_view_text_tmp = \Bitrix\Main\Config\Option::get('aspro.optimus', 'EXPRESSION_FOR_FAST_VIEW', GetMessage('FAST_VIEW')))
		$fast_view_text = $fast_view_text_tmp;
	else
		$fast_view_text = GetMessage('FAST_VIEW');?>
	<div class="common_product wrapper_block top_border1" id="<?=$sliderID?>">
		
		<ul class="slider_navigation top_big custom_flex border"></ul>
		<div class="all_wrapp">
			<div class="content_inner tab">
				<ul class="tabs_slider tabs_slider--specials wr">
					<?foreach($arResult["ITEMS"] as $key => $arItem):?>
						<?
						$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
						$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
						$totalCount = COptimus::GetTotalCount($arItem);
						$arQuantityData = COptimus::GetQuantityArray($totalCount);
						$tmpArAddToBasketData = COptimus::GetAddToBasketArray($arItem, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], true);
						$arItem["FRONT_CATALOG"]="Y";
						$arItemIDs = COptimus::GetItemsIDs($arItem);
						
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
						<li id="<?=$this->GetEditAreaId($arItem['ID']);?>" class="catalog_item">
							<div class="inner_wrap">
								<div class="image_wrapper_block">
									<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="thumb">
										<?if($arItem["DISPLAY_PROPERTIES"]["HIT"]){?>
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
										<?if( ($arParams["DISPLAY_WISH_BUTTONS"] != "N" || $arParams["DISPLAY_COMPARE"] == "Y")):?>
											<div class="like_icons">
												<?if($arItem["CAN_BUY"] && empty($arItem["OFFERS"]) && $arParams["DISPLAY_WISH_BUTTONS"] != "N"  && $tmpArAddToBasketData["ACTION"] == "ADD"):?>
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
										<?if(!empty($arItem["PREVIEW_PICTURE"])):?>
											<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=($arItem["PREVIEW_PICTURE"]["ALT"]?$arItem["PREVIEW_PICTURE"]["ALT"]:$arItem["NAME"]);?>" title="<?=($arItem["PREVIEW_PICTURE"]["TITLE"]?$arItem["PREVIEW_PICTURE"]["TITLE"]:$arItem["NAME"]);?>" />
										<?elseif(!empty($arItem["DETAIL_PICTURE"])):?>
											<?$img = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], array("width" => 170, "height" => 170), BX_RESIZE_IMAGE_PROPORTIONAL, true );?>
											<img src="<?=$img["src"]?>" alt="<?=($arItem["PREVIEW_PICTURE"]["ALT"]?$arItem["PREVIEW_PICTURE"]["ALT"]:$arItem["NAME"]);?>" title="<?=($arItem["PREVIEW_PICTURE"]["TITLE"]?$arItem["PREVIEW_PICTURE"]["TITLE"]:$arItem["NAME"]);?>" />
										<?else:?>
											<img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=($arItem["PREVIEW_PICTURE"]["ALT"]?$arItem["PREVIEW_PICTURE"]["ALT"]:$arItem["NAME"]);?>" title="<?=($arItem["PREVIEW_PICTURE"]["TITLE"]?$arItem["PREVIEW_PICTURE"]["TITLE"]:$arItem["NAME"]);?>" />
										<?endif;?>
									</a>
									<div class="fast_view_block" data-event="jqm" data-param-form_id="fast_view" data-param-iblock_id="<?=$arParams["IBLOCK_ID"];?>" data-param-id="<?=$arItem["ID"];?>" data-param-item_href="<?=urlencode($arItem["DETAIL_PAGE_URL"]);?>" data-name="fast_view"><?=$fast_view_text;?></div>
								</div>
								<div class="item_info">
									<div class="item_info__top">
										<div class="item-title">
											<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><span><?=$arItem["NAME"]?></span></a>
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
									</div>

									<div class="item_info__bottom">
										<?$arAddToBasketData = COptimus::GetAddToBasketArray($arItem, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], !empty($arItem['OFFERS']), $arItemIDs["ALL_ITEM_IDS"], 'small', $arParams);?>
										<div class="cost prices clearfix">
											<?if($arItem["OFFERS"]):?>
												<?\Aspro\Functions\CAsproOptimusSku::showItemPrices($arParams, $arItem, $item_id, $min_price_id);?>
											<?else:?>
												<?
												if(isset($arItem['PRICE_MATRIX']) && $arItem['PRICE_MATRIX']) // USE_PRICE_COUNT
												{?>
													<?if($arItem['ITEM_PRICE_MODE'] == 'Q' && count($arItem['PRICE_MATRIX']['ROWS']) > 1):?>
														<?=COptimus::showPriceRangeTop($arItem, $arParams, GetMessage("CATALOG_ECONOMY"));?>
													<?endif;?>
													<?=COptimus::showPriceMatrix($arItem, $arParams, $strMeasure, $arAddToBasketData);?>
												<?	
												}
												elseif($arItem["PRICES"])
												{?>
													<?\Aspro\Functions\CAsproOptimusItem::showItemPrices($arParams, $arItem["PRICES"], $strMeasure, $min_price_id);?>
												<?}?>
											<?endif;?>
										</div>
									</div>
								</div>

								<div class="footer_button">
									<div class="counter_wrapp">
										<?if (($arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_LIST"] && $arAddToBasketData["ACTION"] == "ADD") && $arItem["CAN_BUY"]):?>
											<div class="counter_block" data-item="<?=$arItem["ID"];?>">
												<span class="minus" id="<?=$arItemIDs["ALL_ITEM_IDS"]['QUANTITY_DOWN'];?>">-</span>
												<input type="text" class="text" id="<?=$arItemIDs["ALL_ITEM_IDS"]['QUANTITY'];?>" name="<?=$arParams["PRODUCT_QUANTITY_VARIABLE"];?>" value="<?=$arAddToBasketData["MIN_QUANTITY_BUY"]?>" />
												<span class="plus" id="<?=$arItemIDs["ALL_ITEM_IDS"]['QUANTITY_UP'];?>" <?=($arAddToBasketData["MAX_QUANTITY_BUY"] ? "data-max='".$arAddToBasketData["MAX_QUANTITY_BUY"]."'" : "")?>>+</span>
											</div>
										<?endif;?>

										<?=$arAddToBasketData["HTML"];?>
									</div>
								</div>
							</div>
						</li>
					<?endforeach;?>
				</ul>
			</div>
		</div>
		<?if($arParams["INIT_SLIDER"] == "Y"):?>
			<script type="text/javascript">
				$(document).ready(function(){
					var flexsliderItemWidth = 220;
					var flexsliderItemMargin = 12;
					
				
					$('#<?=$sliderID?> .content_inner').flexslider({
						animation: 'slide',
						selector: '.tabs_slider--specials > li',
						slideshow: false,
						animationSpeed: 600,
						directionNav: true,
						controlNav: false,
						pauseOnHover: true,
						animationLoop: true, 
						itemWidth: flexsliderItemWidth,
						itemMargin: flexsliderItemMargin,
						controlsContainer: '#<?=$sliderID?> .slider_navigation',
						start: function(slider){
							slider.find('li').css('opacity', 1);
						}
					});
					
					// var itemsButtonsHeight = $('.wrapper_block#<?=$sliderID;?> .wr > li .buttons_block').height();
					// $('.wrapper_block#<?=$sliderID;?> .wr .buttons_block').hide();
					// if($('.wrapper_block#<?=$sliderID;?> .all_wrapp .content_inner').attr('data-hover') ==undefined){
					// 	var tabsContentUnhover = ($('.wrapper_block#<?=$sliderID;?> .all_wrapp').height() * 1)+20;
					// 	var tabsContentHover = tabsContentUnhover + itemsButtonsHeight+50;

					// 	// $('.wrapper_block#<?=$sliderID;?> .slides').equalize({children: '.item-title'}); 
					// 	// $('.wrapper_block#<?=$sliderID;?> .slides').equalize({children: '.item_info'}); 
					// 	// $('.wrapper_block#<?=$sliderID;?> .slides').equalize({children: '.catalog_item'});

					// 	// $('.wrapper_block#<?=$sliderID;?> .all_wrapp .content_inner').attr('data-unhover', tabsContentUnhover);
					// 	// $('.wrapper_block#<?=$sliderID;?> .all_wrapp .content_inner').attr('data-hover', tabsContentHover);
					// 	// $('.wrapper_block#<?=$sliderID;?> .all_wrapp').height(tabsContentUnhover);
					// 	// $('.wrapper_block#<?=$sliderID;?> .all_wrapp .content_inner').addClass('absolute');


					// }

					if($('#<?=$sliderID?> .slider_navigation .flex-disabled').length > 1){
						$('#<?=$sliderID?> .slider_navigation').hide();
					}
				});
			</script>
			
		<?endif;?>
	</div>
<?else:?>
	<?$this->setFrameMode(true);?>
	<script type="text/javascript">
	$(document).ready(function(){
		$(".news_detail_wrapp .similar_products_wrapp").remove();
	}); /* dirty hack, remove this code */
	</script>
<?endif;?>