<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

$frame = $this->createFrame()->begin();

$injectId = 'sale_gift_product_'.rand();

$currentProductId = (int)$arResult['POTENTIAL_PRODUCT_TO_BUY']['ID'];

if (isset($arResult['REQUEST_ITEMS']))
{
	CJSCore::Init(array('ajax'));

	// component parameters
	$signer = new \Bitrix\Main\Security\Sign\Signer;
	$signedParameters = $signer->sign(
		base64_encode(serialize($arResult['_ORIGINAL_PARAMS'])),
		'bx.sale.gift.product'
	);
	$signedTemplate = $signer->sign($arResult['RCM_TEMPLATE'], 'bx.sale.gift.product');

	?>

	<span id="<?=$injectId?>" class="sale_gift_product_container"></span>

	<script type="text/javascript">
		BX.ready(function(){

			var currentProductId = <?=CUtil::JSEscape($currentProductId)?>;
			var giftAjaxData = {
				'parameters':'<?=CUtil::JSEscape($signedParameters)?>',
				'template': '<?=CUtil::JSEscape($signedTemplate)?>',
				'site_id': '<?=CUtil::JSEscape(SITE_ID)?>'
			};

			bx_sale_gift_product_load(
				'<?=CUtil::JSEscape($injectId)?>',
				giftAjaxData
			);

			BX.addCustomEvent('onCatalogStoreProductChange', function(offerId){
				if(currentProductId == offerId)
				{
					return;
				}
				currentProductId = offerId;
				bx_sale_gift_product_load(
					'<?=CUtil::JSEscape($injectId)?>',
					giftAjaxData,
					{offerId: offerId}
				);
			});
		});
	</script>

	<?
	$frame->end();
	return;
}

if (!empty($arResult['ITEMS'])){
	$templateData = array(
		'TEMPLATE_CLASS' => 'bx_'.$arParams['TEMPLATE_THEME']
	);
	$arParams['IS_GIFT'] = 'Y';
	$arParams["SHOW_DISCOUNT_PERCENT"] = "N";
	$arSkuTemplate = array();
	if (!empty($arResult['SKU_PROPS'])){
		$arSkuTemplate=COptimus::GetSKUPropsArray($arResult['SKU_PROPS'], $arResult["SKU_IBLOCK_ID"], "block", $arParams["OFFER_HIDE_NAME_PROPS"], "Y");
	}?>
	<script type="text/javascript">
		BX.message({
			CVP_MESS_BTN_BUY: '<? echo ('' != $arParams['MESS_BTN_BUY'] ? CUtil::JSEscape($arParams['MESS_BTN_BUY']) : GetMessageJS('CVP_TPL_MESS_BTN_BUY_GIFT')); ?>',
			CVP_MESS_BTN_ADD_TO_BASKET: '<? echo ('' != $arParams['MESS_BTN_ADD_TO_BASKET'] ? CUtil::JSEscape($arParams['MESS_BTN_ADD_TO_BASKET']) : GetMessageJS('CVP_TPL_MESS_BTN_ADD_TO_BASKET')); ?>',

			CVP_MESS_BTN_DETAIL: '<? echo ('' != $arParams['MESS_BTN_DETAIL'] ? CUtil::JSEscape($arParams['MESS_BTN_DETAIL']) : GetMessageJS('CVP_TPL_MESS_BTN_DETAIL')); ?>',

			CVP_MESS_NOT_AVAILABLE: '<? echo ('' != $arParams['MESS_BTN_DETAIL'] ? CUtil::JSEscape($arParams['MESS_BTN_DETAIL']) : GetMessageJS('CVP_TPL_MESS_BTN_DETAIL')); ?>',
			CVP_BTN_MESSAGE_BASKET_REDIRECT: '<? echo GetMessageJS('CVP_CATALOG_BTN_MESSAGE_BASKET_REDIRECT'); ?>',
			CVP_BASKET_URL: '<? echo $arParams["BASKET_URL"]; ?>',
			CVP_ADD_TO_BASKET_OK: '<? echo GetMessageJS('CVP_ADD_TO_BASKET_OK'); ?>',
			CVP_TITLE_ERROR: '<? echo GetMessageJS('CVP_CATALOG_TITLE_ERROR') ?>',
			CVP_TITLE_BASKET_PROPS: '<? echo GetMessageJS('CVP_CATALOG_TITLE_BASKET_PROPS') ?>',
			CVP_TITLE_SUCCESSFUL: '<? echo GetMessageJS('CVP_ADD_TO_BASKET_OK'); ?>',
			CVP_BASKET_UNKNOWN_ERROR: '<? echo GetMessageJS('CVP_CATALOG_BASKET_UNKNOWN_ERROR') ?>',
			CVP_BTN_MESSAGE_SEND_PROPS: '<? echo GetMessageJS('CVP_CATALOG_BTN_MESSAGE_SEND_PROPS'); ?>',
			CVP_BTN_MESSAGE_CLOSE: '<? echo GetMessageJS('CVP_CATALOG_BTN_MESSAGE_CLOSE') ?>'
		});
	</script>

	<div class="bx_item_list_you_looked_horizontal <? echo $templateData['TEMPLATE_CLASS']; ?>">
		<?if($fast_view_text_tmp = \Bitrix\Main\Config\Option::get('aspro.optimus', 'EXPRESSION_FOR_FAST_VIEW', GetMessage('FAST_VIEW')))
			$fast_view_text = $fast_view_text_tmp;
		else
			$fast_view_text = GetMessage('FAST_VIEW');?>
		<div class="common_product wrapper_block s_<?=$injectId;?> <?=($arParams["SHOW_UNABLE_SKU_PROPS"] != "N" ? "show_un_props" : "unshow_un_props");?>">
			<?if(empty($arParams['HIDE_BLOCK_TITLE']) || $arParams['HIDE_BLOCK_TITLE'] == 'N'){?>
				<div class="top_block">
					<div class="title_block"><?=($arParams['BLOCK_TITLE'] ? htmlspecialcharsbx($arParams['BLOCK_TITLE']) : GetMessage('SGP_TPL_BLOCK_TITLE_DEFAULT'));?></div>
				</div>
			<?}?>
			<ul class="viewed_navigation slider_navigation top_big custom_flex border"></ul>
			<div class="all_wrapp">
				<div class="content_inner tab">
					<ul class="tabs_slider slides wr">
						<?
						$elementEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
						$elementDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
						$elementDeleteParams = array('CONFIRM' => GetMessage('CVP_TPL_ELEMENT_DELETE_CONFIRM'));
						?>
						<?foreach($arResult['ITEMS'] as $key => $arItem){
							$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $elementEdit);
							$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $elementDelete, $elementDeleteParams);
							$arItem["strMainID"] = $this->GetEditAreaId($arItem['ID']);
							$arItemIDs=COptimus::GetItemsIDs($arItem);

							$strMeasure = '';
							$totalCount = COptimus::GetTotalCount($arItem);
							$arQuantityData = COptimus::GetQuantityArray($totalCount, $arItemIDs["ALL_ITEM_IDS"]);
							if(!$arItem["OFFERS"]){
								if($arParams["SHOW_MEASURE"] == "Y" && $arItem["CATALOG_MEASURE"]){
									$arMeasure = CCatalogMeasure::getList(array(), array("ID" => $arItem["CATALOG_MEASURE"]), false, false, array())->GetNext();
									$strMeasure = $arMeasure["SYMBOL_RUS"];
								}
								$arAddToBasketData = COptimus::GetAddToBasketArray($arItem, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false, $arItemIDs["ALL_ITEM_IDS"], 'small', $arParams);
							}
							elseif($arItem["OFFERS"]){
								$strMeasure = $arItem["MIN_PRICE"]["CATALOG_MEASURE_NAME"];
								if(!$arItem['OFFERS_PROP']){

									$arAddToBasketData = COptimus::GetAddToBasketArray($arItem["OFFERS"][0], $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false, $arItemIDs["ALL_ITEM_IDS"], 'small', $arParams);
								}
							}
							?>
							<li class="catalog_item main_item_wrapper" id="<?=$arItem["strMainID"];?>">
								<div class="image_wrapper_block">
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
									<?if($arParams["DISPLAY_WISH_BUTTONS"] != "N" || $arParams["DISPLAY_COMPARE"] == "Y"):?>
										<div class="like_icons">
											<?if($arParams["DISPLAY_WISH_BUTTONS"] != "N"):?>
												<?if(!$arItem["OFFERS"]):?>
													<div class="wish_item_button">
														<span title="<?=GetMessage('CATALOG_WISH')?>" class="wish_item to" data-item="<?=$arItem["ID"]?>" data-iblock="<?=$arItem["IBLOCK_ID"]?>"><i></i></span>
														<span title="<?=GetMessage('CATALOG_WISH_OUT')?>" class="wish_item in added" style="display: none;" data-item="<?=$arItem["ID"]?>" data-iblock="<?=$arItem["IBLOCK_ID"]?>"><i></i></span>
													</div>
												<?elseif($arItem["OFFERS"]):?>
													<div class="wish_item_button" style="display: none;">
														<span title="<?=GetMessage('CATALOG_WISH')?>" class="wish_item to <?=$arParams["TYPE_SKU"];?>" data-item="" data-iblock="<?=$arItem["IBLOCK_ID"]?>" data-offers="Y" data-props="<?=$arOfferProps?>"><i></i></span>
														<span title="<?=GetMessage('CATALOG_WISH_OUT')?>" class="wish_item in added <?=$arParams["TYPE_SKU"];?>" style="display: none;" data-item="" data-iblock="<?=$arItem["IBLOCK_ID"]?>"><i></i></span>
													</div>
												<?endif;?>
											<?endif;?>
											<?if($arParams["DISPLAY_COMPARE"] == "Y"):?>
												<?if(!$arItem["OFFERS"] || ($arParams["TYPE_SKU"] !== 'TYPE_1' || ($arParams["TYPE_SKU"] == 'TYPE_1' && !$arItem["OFFERS_PROP"]))):?>
													<div class="compare_item_button">
														<span title="<?=GetMessage('CATALOG_COMPARE')?>" class="compare_item to" data-iblock="<?=$arItem["IBLOCK_ID"]?>" data-item="<?=$arItem["ID"]?>" ><i></i></span>
														<span title="<?=GetMessage('CATALOG_COMPARE_OUT')?>" class="compare_item in added" style="display: none;" data-iblock="<?=$arItem["IBLOCK_ID"]?>" data-item="<?=$arItem["ID"]?>"><i></i></span>
													</div>
												<?elseif($arItem["OFFERS"]):?>
													<div class="compare_item_button">
														<span title="<?=GetMessage('CATALOG_COMPARE')?>" class="compare_item to <?=$arParams["TYPE_SKU"];?>" data-iblock="<?=$arItem["IBLOCK_ID"]?>" data-item="" ><i></i></span>
														<span title="<?=GetMessage('CATALOG_COMPARE_OUT')?>" class="compare_item in added <?=$arParams["TYPE_SKU"];?>" style="display: none;" data-iblock="<?=$arItem["IBLOCK_ID"]?>" data-item=""><i></i></span>
													</div>
												<?endif;?>
											<?endif;?>
										</div>
									<?endif;?>
									<div class="wrapper_fw">
										<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="thumb" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PICT']; ?>">
											<?
											$a_alt=($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $arItem["NAME"] );
											$a_title=($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] : $arItem["NAME"] );
											?>
											<?if( !empty($arItem["PREVIEW_PICTURE"]) ):?>
												<img border="0" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
											<?elseif( !empty($arItem["DETAIL_PICTURE"])):?>
												<?$img = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], array( "width" => 170, "height" => 170 ), BX_RESIZE_IMAGE_PROPORTIONAL,true );?>
												<img border="0" src="<?=$img["src"]?>" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
											<?else:?>
												<img border="0" src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
											<?endif;?>
										</a>
										<div class="fast_view_block" data-event="jqm" data-param-form_id="fast_view" data-param-iblock_id="<?=$arParams["IBLOCK_ID"];?>" data-param-id="<?=$arItem["ID"];?>" data-param-item_href="<?=urlencode($arItem["DETAIL_PAGE_URL"]);?>" data-name="fast_view"><?=$fast_view_text;?></div>
									</div>
								</div>
								<div class="item_info <?=$arParams["TYPE_SKU"]?>">
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
									<div class="cost prices clearfix">
										<?if( $arItem["OFFERS"]){?>
											<?\Aspro\Functions\CAsproOptimusSku::showItemPrices($arParams, $arItem, $item_id, $min_price_id, $arItemIDs);?>
										<?}else{?>
											<?
											$item_id = $arItem["ID"];
											if(isset($arItem['PRICE_MATRIX']) && $arItem['PRICE_MATRIX']) // USE_PRICE_COUNT
											{?>
												<?if($arItem['ITEM_PRICE_MODE'] == 'Q' && count($arItem['PRICE_MATRIX']['ROWS']) > 1):?>
													<?=COptimus::showPriceRangeTop($arItem, $arParams, GetMessage("CATALOG_ECONOMY"));?>
												<?endif;?>
												<?=COptimus::showPriceMatrix($arItem, $arParams, $strMeasure, $arAddToBasketData);?>
											<?	
											}
											elseif ( $arItem["PRICES"] )
											{?>
												<?foreach($arItem["PRICES"] as $priceCode => $arTmpPrice)
												{
													$arItem["PRICES"][$priceCode]["DISCOUNT_VALUE"] = $arItem["PRICES"][$priceCode]["DISCOUNT_DIFF"];
													$arItem["PRICES"][$priceCode]["PRINT_DISCOUNT_VALUE"] = $arItem["PRICES"][$priceCode]["PRINT_DISCOUNT_DIFF"];
												}?>
												<?\Aspro\Functions\CAsproOptimusItem::showItemPrices($arParams, $arItem["PRICES"], $strMeasure, $min_price_id);?>
											<?}?>
										<?}?>
									</div>
									<?if($arParams["SHOW_DISCOUNT_TIME"]=="Y"){?>
										<?$arUserGroups = $USER->GetUserGroupArray();?>
										<?if($arParams['SHOW_DISCOUNT_TIME_EACH_SKU'] != 'Y' || ($arParams['SHOW_DISCOUNT_TIME_EACH_SKU'] == 'Y' && !$arItem['OFFERS'])):?>
											<?$arDiscounts = CCatalogDiscount::GetDiscountByProduct($item_id, $arUserGroups, "N", $min_price_id, SITE_ID);
											$arDiscount=array();
											if($arDiscounts)
												$arDiscount=current($arDiscounts);
											if($arDiscount["ACTIVE_TO"]){?>
												<div class="view_sale_block <?=($arQuantityData["HTML"] ? '' : 'wq');?>">
													<div class="count_d_block">
														<span class="active_to hidden"><?=$arDiscount["ACTIVE_TO"];?></span>
														<div class="title"><?=GetMessage("UNTIL_AKC");?></div>
														<span class="countdown values"></span>
													</div>
													<?if($arQuantityData["HTML"]):?>
														<div class="quantity_block">
															<div class="title"><?=GetMessage("TITLE_QUANTITY_BLOCK");?></div>
															<div class="values">
																<span class="item">
																	<span  <?=( count( $arItem["OFFERS"] ) > 0 && $arItem["OFFERS_PROP"] ? 'style="opacity:0;"' : '')?>><?=$totalCount;?></span>
																	<div class="text"><?=GetMessage("TITLE_QUANTITY");?></div>
																</span>
															</div>
														</div>
													<?endif;?>
												</div>
											<?}?>
										<?else:?>
											<?if($arItem['JS_OFFERS'])
											{
												foreach($arItem['JS_OFFERS'] as $keyOffer => $arTmpOffer2)
												{
													$active_to = '';
													$arDiscounts = CCatalogDiscount::GetDiscountByProduct( $arTmpOffer2['ID'], $arUserGroups, "N", array(), SITE_ID );
													if($arDiscounts)
													{
														foreach($arDiscounts as $arDiscountOffer)
														{
															if($arDiscountOffer['ACTIVE_TO'])
															{
																$active_to = $arDiscountOffer['ACTIVE_TO'];
																break;
															}
														}
													}
													$arItem['JS_OFFERS'][$keyOffer]['DISCOUNT_ACTIVE'] = $active_to;
												}
											}?>
											<div class="view_sale_block" style="display:none;">
												<div class="count_d_block">
														<span class="active_to_<?=$arItem["ID"]?> hidden"><?=$arDiscount["ACTIVE_TO"];?></span>
														<div class="title"><?=GetMessage("UNTIL_AKC");?></div>
														<span class="countdown countdown_<?=$arItem["ID"]?> values"></span>
												</div>
												<?if($arQuantityData["HTML"]):?>
													<div class="quantity_block">
														<div class="title"><?=GetMessage("TITLE_QUANTITY_BLOCK");?></div>
														<div class="values">
															<span class="item">
																<span class="value"><?=$totalCount;?></span>
																<span class="text"><?=GetMessage("TITLE_QUANTITY");?></span>
															</span>
														</div>
													</div>
												<?endif;?>
											</div>
										<?endif;?>
									<?}?>
								</div>
									<div class="buttons_block">
										<?if(!empty($arItem['OFFERS']) && isset($arSkuTemplate[$arItem['IBLOCK_ID']])){?>
											<?if(!empty($arItem['OFFERS_PROP'])){?>
												<div class="sku_props">
													<div class="bx_catalog_item_scu wrapper_sku" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PROP_DIV']; ?>">
														<?foreach ($arSkuTemplate[$arItem['IBLOCK_ID']] as $code => $strTemplate){
															if (!isset($arItem['OFFERS_PROP'][$code]))
																continue;
															echo '<div>', str_replace('#ITEM#_prop_', $arItemIDs["ALL_ITEM_IDS"]['PROP'], $strTemplate), '</div>';
														}?>
													</div>
													<?$arItemJSParams=COptimus::GetSKUJSParams($arResult, $arParams, $arItem, "N", "Y");?>
													
													<script type="text/javascript">
														var <? echo $arItemIDs["strObName"]; ?> = new JCSaleGiftProduct(<? echo CUtil::PhpToJSObject($arItemJSParams, false, true); ?>);
													</script>
												</div>
											<?}?>
										<?}?>
										<?if(!$arItem["OFFERS"] || ($arItem["OFFERS"] && !$arItem['OFFERS_PROP'])):?>
											<div class="counter_wrapp <?=($arItem["OFFERS"] && $arParams["TYPE_SKU"] == "TYPE_1" ? 'woffers' : '')?>">
												<?if(($arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_LIST"] && $arAddToBasketData["ACTION"] == "ADD") && $arItem["CAN_BUY"]):?>
													<div class="counter_block" data-offers="<?=($arItem["OFFERS"] ? "Y" : "N");?>" data-item="<?=$arItem["ID"];?>">
														<span class="minus" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['QUANTITY_DOWN']; ?>">-</span>
														<input type="text" class="text" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['QUANTITY']; ?>" name="<? echo $arParams["PRODUCT_QUANTITY_VARIABLE"]; ?>" value="<?=$arAddToBasketData["MIN_QUANTITY_BUY"]?>" />
														<span class="plus" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['QUANTITY_UP']; ?>" <?=($arAddToBasketData["MAX_QUANTITY_BUY"] ? "data-max='".$arAddToBasketData["MAX_QUANTITY_BUY"]."'" : "")?>>+</span>
													</div>
												<?endif;?>
												<div id="<?=$arItemIDs["ALL_ITEM_IDS"]['BASKET_ACTIONS']; ?>" class="button_block <?=(($arAddToBasketData["ACTION"] == "ORDER"/*&& !$arItem["CAN_BUY"]*/)  || !$arItem["CAN_BUY"] || !$arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_LIST"] || $arAddToBasketData["ACTION"] == "SUBSCRIBE" ? "wide" : "");?>">
													<!--noindex-->
														<?=$arAddToBasketData["HTML"]?>
													<!--/noindex-->
												</div>
											</div>
											<?
											if(isset($arItem['PRICE_MATRIX']) && $arItem['PRICE_MATRIX']) // USE_PRICE_COUNT
											{?>
												<?if($arItem['ITEM_PRICE_MODE'] == 'Q' && count($arItem['PRICE_MATRIX']['ROWS']) > 1):?>
													<?$arOnlyItemJSParams = array(
														"ITEM_PRICES" => $arItem["ITEM_PRICES"],
														"ITEM_PRICE_MODE" => $arItem["ITEM_PRICE_MODE"],
														"ITEM_QUANTITY_RANGES" => $arItem["ITEM_QUANTITY_RANGES"],
														"MIN_QUANTITY_BUY" => $arAddToBasketData["MIN_QUANTITY_BUY"],
														"ID" => $arItemIDs["strMainID"],
													)?>
													<script type="text/javascript">
														var <? echo $arItemIDs["strObName"]; ?>el = new JCCatalogSectionOnlyElement(<? echo CUtil::PhpToJSObject($arOnlyItemJSParams, false, true); ?>);
													</script>
												<?endif;?>
											<?}?>
										<?elseif($arItem["OFFERS"] && $arItem['OFFERS_PROP']):?>
											<div class="offer_buy_block buys_wrapp woffers" style="display:none;">
												<div class="counter_wrapp"></div>
											</div>
										<?endif;?>
									</div>
							</li>
						<?}?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<script>
		$(document).ready(function(){
			var flexsliderItemWidth = 220;
			var flexsliderItemMargin = 12;
			$('.s_<?=$injectId;?> .content_inner').flexslider({
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
				//minItems: flexsliderMinItems,
				controlsContainer: '.viewed_navigation',
				start: function(slider){
					slider.find('li').css('opacity', 1);
				}
			});

			var itemsButtons = $('.s_<?=$injectId;?> .wr > li .buttons_block'),
				itemsButtonsHeight = itemsButtons.getMaxHeights();
			$('.s_<?=$injectId;?> .wr .buttons_block').hide();
			
			var tabsContentUnhover = ($('.s_<?=$injectId;?> .all_wrapp').height() * 1)+20;
			var tabsContentHover = tabsContentUnhover + itemsButtonsHeight+50;

			$('.s_<?=$injectId;?> .slides').equalize({children: '.item-title'}); 
			$('.s_<?=$injectId;?> .slides').equalize({children: '.item_info'}); 
			$('.s_<?=$injectId;?> .slides').equalize({children: '.catalog_item'});

			$('.s_<?=$injectId;?> .all_wrapp .content_inner').attr('data-unhover', tabsContentUnhover);
			$('.s_<?=$injectId;?> .all_wrapp .content_inner').attr('data-hover', tabsContentHover);
			$('.s_<?=$injectId;?> .all_wrapp').height(tabsContentUnhover);
			$('.s_<?=$injectId;?> .all_wrapp .content_inner').addClass('absolute');			

			$('.s_<?=$injectId;?> .wr > li').hover(
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
<?}?>
<?$frame->beginStub();?>
<?$frame->end();?>