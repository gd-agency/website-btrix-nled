<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?use \Bitrix\Main\Localization\Loc,
	  \Bitrix\Main\Web\Json;?>
<?if( count( $arResult["ITEMS"] ) >= 1 ){?>
	<?if($fast_view_text_tmp = \Bitrix\Main\Config\Option::get('aspro.optimus', 'EXPRESSION_FOR_FAST_VIEW', GetMessage('FAST_VIEW')))
		$fast_view_text = $fast_view_text_tmp;
	else
		$fast_view_text = GetMessage('FAST_VIEW');?>
	<?if(($arParams["AJAX_REQUEST"]=="N") || !isset($arParams["AJAX_REQUEST"])){?>
		<div class="top_wrapper rows_block <?=($arParams["SHOW_UNABLE_SKU_PROPS"] != "N" ? "show_un_props" : "unshow_un_props");?>">
			<div class="catalog_block items block_list">
	<?}?>
		<?
		$currencyList = '';
		if (!empty($arResult['CURRENCIES'])){
			$templateLibrary[] = 'currency';
			$currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
		}
		$templateData = array(
			'TEMPLATE_LIBRARY' => $templateLibrary,
			'CURRENCIES' => $currencyList
		);
		unset($currencyList, $templateLibrary);

		$arParams["BASKET_ITEMS"]=($arParams["BASKET_ITEMS"] ? $arParams["BASKET_ITEMS"] : array());
		$arOfferProps = implode(';', (array)$arParams['OFFERS_CART_PROPERTIES']);


		switch ($arParams["LINE_ELEMENT_COUNT"]){
			case '2':
				$col=2;
				break;
			case '3':
				$col=3;
				break;
			default:
				$col=4;
				break;
		}?>
		<?foreach($arResult["ITEMS"] as $arItem){?>
			<div class="item_block item-parent col-<?=$col;?>">
				<div class="catalog_item_wrapp item">
					<div class="basket_props_block" id="bx_basket_div_<?=$arItem["ID"];?>" style="display: none;">
						<?if (!empty($arItem['PRODUCT_PROPERTIES_FILL'])){
							foreach ($arItem['PRODUCT_PROPERTIES_FILL'] as $propID => $propInfo){?>
								<input type="hidden" name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]" value="<? echo htmlspecialcharsbx($propInfo['ID']); ?>">
								<?if (isset($arItem['PRODUCT_PROPERTIES'][$propID]))
									unset($arItem['PRODUCT_PROPERTIES'][$propID]);
							}
						}
						$arItem["EMPTY_PROPS_JS"]="Y";
						$emptyProductProperties = empty($arItem['PRODUCT_PROPERTIES']);
						if (!$emptyProductProperties){
							$arItem["EMPTY_PROPS_JS"]="N";?>
							<div class="wrapper">
								<table>
									<?foreach ($arItem['PRODUCT_PROPERTIES'] as $propID => $propInfo){?>
										<tr>
											<td><? echo $arItem['PROPERTIES'][$propID]['NAME']; ?></td>
											<td>
												<?if('L' == $arItem['PROPERTIES'][$propID]['PROPERTY_TYPE']	&& 'C' == $arItem['PROPERTIES'][$propID]['LIST_TYPE']){
													foreach($propInfo['VALUES'] as $valueID => $value){?>
														<label>
															<input type="radio" name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]" value="<? echo $valueID; ?>" <? echo ($valueID == $propInfo['SELECTED'] ? '"checked"' : ''); ?>><? echo $value; ?>
														</label>
													<?}
												}else{?>
													<select name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]"><?
														foreach($propInfo['VALUES'] as $valueID => $value){?>
															<option value="<? echo $valueID; ?>" <? echo ($valueID == $propInfo['SELECTED'] ? '"selected"' : ''); ?>><? echo $value; ?></option>
														<?}?>
													</select>
												<?}?>
											</td>
										</tr>
									<?}?>
								</table>
							</div>
							<?
						}?>
					</div>
					<?$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));

					$arCurrentSKU = array();

					$arItem["strMainID"] = $this->GetEditAreaId($arItem['ID']);
					$arItemIDs=COptimus::GetItemsIDs($arItem);

					$totalCount = COptimus::GetTotalCount($arItem);
					$arQuantityData = COptimus::GetQuantityArray($totalCount, $arItemIDs["ALL_ITEM_IDS"]);

					$bLinkedItems = (isset($arParams["LINKED_ITEMS"]) && $arParams["LINKED_ITEMS"]);
					if($bLinkedItems)
						$arItem["FRONT_CATALOG"]="Y";

					$item_id = $arItem["ID"];
					$strMeasure = '';
					
					$elementName = ((isset($arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) ? $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] : $arItem['NAME']);

					if(!$arItem["OFFERS"] || $arParams['TYPE_SKU'] !== 'TYPE_1'){
						if($arParams["SHOW_MEASURE"] == "Y" && $arItem["CATALOG_MEASURE"]){
							$arMeasure = CCatalogMeasure::getList(array(), array("ID" => $arItem["CATALOG_MEASURE"]), false, false, array())->GetNext();
							$strMeasure = $arMeasure["SYMBOL_RUS"];
						}
						$arAddToBasketData = COptimus::GetAddToBasketArray($arItem, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], ($arItem["FRONT_CATALOG"] ? true: false), $arItemIDs["ALL_ITEM_IDS"], 'small', $arParams);
					}
					elseif($arItem["OFFERS"]){
						$strMeasure = $arItem["MIN_PRICE"]["CATALOG_MEASURE_NAME"];
						if ($arItem['OFFERS_PROP']) {
							$currentSKUIBlock = $arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["IBLOCK_ID"];
							$currentSKUID = $arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["ID"];
	
							$strMeasure = $arItem["MIN_PRICE"]["CATALOG_MEASURE_NAME"];
							$totalCount = COptimus::GetTotalCount($arItem["OFFERS"][$arItem["OFFERS_SELECTED"]], $arParams);
							$arQuantityData = COptimus::GetQuantityArray($totalCount, array('ID' => $currentSKUID), 'N', (($arItem['CATALOG_TYPE'] == CCatalogProduct::TYPE_SET || $bSlide || !$arResult['STORES_COUNT']) ? false : true), 'ce_cmp_hidden');
	
							if($arItem["OFFERS"])
							{
								$totalCountCMP = COptimus::GetTotalCount($arItem, $arParamsCE_CMP);
								$arQuantityDataCMP = COptimus::GetQuantityArray($totalCountCMP, array('ID' => $item_id), 'N', false, 'ce_cmp_visible');
							}
	
	
							$arItem["DETAIL_PAGE_URL"] = $arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["DETAIL_PAGE_URL"];
							
							if($arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["PREVIEW_PICTURE"])
								$arItem["PREVIEW_PICTURE"] = $arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["PREVIEW_PICTURE"];
							if($arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["PREVIEW_PICTURE"])
								$arItem["DETAIL_PICTURE"] = $arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["DETAIL_PICTURE"];
	
							if($arParams["SET_SKU_TITLE"] == "Y")
								$arItem["NAME"] = $elementName = $arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["NAME"];
							$item_id = $currentSKUID;
	
							// ARTICLE
							if($arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["DISPLAY_PROPERTIES"]["ARTICLE"]["VALUE"])
							{
								$arItem["ARTICLE"]["NAME"] = $arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["DISPLAY_PROPERTIES"]["ARTICLE"]["NAME"];
								$arItem["ARTICLE"]["VALUE"] = (is_array($arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["DISPLAY_PROPERTIES"]["ARTICLE"]["VALUE"]) ? reset($arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["DISPLAY_PROPERTIES"]["ARTICLE"]["VALUE"]) : $arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["DISPLAY_PROPERTIES"]["ARTICLE"]["VALUE"]);
								unset($arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["DISPLAY_PROPERTIES"]["ARTICLE"]);
							}
	
							$arCurrentSKU = $arItem["JS_OFFERS"][$arItem["OFFERS_SELECTED"]];
							$strMeasure = $arCurrentSKU["MEASURE"];
	
							$arAddToBasketData = COptimus::GetAddToBasketArray($arItem["OFFERS"][$arItem["OFFERS_SELECTED"]], $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false, $arItemIDs["ALL_ITEM_IDS"], 'small', $arParams);
						}
					}
					?>
					<div class="catalog_item item_wrap <?=(($_GET['q'])) ? 's' : ''?>" id="<?=$arItemIDs["strMainID"];?>">
						<div>
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
											<?if(!$arItem["OFFERS"] && $arItem['CAN_BUY'] && $arAddToBasketData["ACTION"] === "ADD"):?>
												<div class="wish_item_button">
													<span title="<?=GetMessage('CATALOG_WISH')?>" class="wish_item to" data-item="<?=$arItem["ID"]?>" data-iblock="<?=$arItem["IBLOCK_ID"]?>"><i></i></span>
													<span title="<?=GetMessage('CATALOG_WISH_OUT')?>" class="wish_item in added" style="display: none;" data-item="<?=$arItem["ID"]?>" data-iblock="<?=$arItem["IBLOCK_ID"]?>"><i></i></span>
												</div>
											<?elseif($arItem["OFFERS"] && !empty($arItem['OFFERS_PROP'])):?>
												<div class="wish_item_button" <?= !$arCurrentSKU['CAN_BUY'] && $arAddToBasketData["ACTION"] !== "ADD" ? 'style="display: none"' : null; ?>>													<span title="<?=GetMessage('CATALOG_WISH')?>" class="wish_item to <?=$arParams["TYPE_SKU"];?>" data-item="<?=$arCurrentSKU['ID']?>" data-iblock="<?=$arItem["IBLOCK_ID"]?>" data-offers="Y" data-props="<?=$arOfferProps?>"><i></i></span>
													<span title="<?=GetMessage('CATALOG_WISH_OUT')?>" class="wish_item in added <?=$arParams["TYPE_SKU"];?>" style="display: none;" data-item="<?=$arCurrentSKU['ID']?>" data-iblock="<?=$arOffer["IBLOCK_ID"]?>"><i></i></span>
												</div>
											<?endif;?>
										<?endif;?>
										<?if($arParams["DISPLAY_COMPARE"] == "Y"):?>
											<?if(!$arItem["OFFERS"] || ($arParams["TYPE_SKU"] !== 'TYPE_1' || ($arParams["TYPE_SKU"] == 'TYPE_1' && !$arItem["OFFERS_PROP"]))):?>
												<div class="compare_item_button">
													<span title="<?=GetMessage('CATALOG_COMPARE')?>" class="compare_item to" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arItem["ID"]?>" ><i></i></span>
													<span title="<?=GetMessage('CATALOG_COMPARE_OUT')?>" class="compare_item in added" style="display: none;" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arItem["ID"]?>"><i></i></span>
												</div>
											<?elseif($arItem["OFFERS"]):?>
												<div class="compare_item_button">
													<span title="<?=GetMessage('CATALOG_COMPARE')?>" class="compare_item to <?=$arParams["TYPE_SKU"];?>" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arCurrentSKU['ID']?>" ><i></i></span>
													<span title="<?=GetMessage('CATALOG_COMPARE_OUT')?>" class="compare_item in added <?=$arParams["TYPE_SKU"];?>" style="display: none;" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arCurrentSKU['ID']?>"><i></i></span>
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
											<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
										<?elseif( !empty($arItem["DETAIL_PICTURE"])):?>
											<?$img = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], array( "width" => 170, "height" => 170 ), BX_RESIZE_IMAGE_PROPORTIONAL,true );?>
											<img src="<?=$img["src"]?>" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
										<?else:?>
											<img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
										<?endif;?>
									</a>
									<div class="fast_view_block" data-event="jqm" data-param-form_id="fast_view" data-param-iblock_id="<?=$arParams["IBLOCK_ID"];?>" data-param-id="<?=$arItem["ID"];?>" data-param-item_href="<?=urlencode($arItem["DETAIL_PAGE_URL"]);?>" data-name="fast_view"><?=$fast_view_text;?></div>
								</div>
							</div>
							<div class="item_info main_item_wrapper <?=$arParams["TYPE_SKU"]?>">
								<div class="item-title">
									<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><span><?=$elementName;?></span></a>
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
								<div class="article_block"> 
									<?if(isset($arItem['ARTICLE']) && $arItem['ARTICLE']['VALUE']){?> 
										<span class="article_block__title"><?=$arItem['ARTICLE']['NAME'];?></span><span class="article_block__value">: <?=$arItem['ARTICLE']['VALUE'];?></span>
									<?}?> 
								</div>
								<div class="cost prices clearfix">
									<?if($arItem['OFFERS']):?>
										<div class="js_price_wrapper">
											<?if($arCurrentSKU):?>
												<?$item_id = $arCurrentSKU["ID"];
												$arCurrentSKU['PRICE_MATRIX'] = $arCurrentSKU['PRICE_MATRIX_RAW'];
												$arCurrentSKU['CATALOG_MEASURE_NAME'] = $arCurrentSKU['MEASURE'];
												if(isset($arCurrentSKU['PRICE_MATRIX']) && $arCurrentSKU['PRICE_MATRIX']): // USE_PRICE_COUNT?>
													<?if($arCurrentSKU['ITEM_PRICE_MODE'] == 'Q' && count($arCurrentSKU['PRICE_MATRIX']['ROWS']) > 1):?>
														<?=COptimus::showPriceRangeTop($arCurrentSKU, $arParams, Loc::getMessage("CATALOG_ECONOMY"));?>
													<?endif;?>
													<?=COptimus::showPriceMatrix($arCurrentSKU, $arParams, $strMeasure, $arAddToBasketData);?>
												<?else:?>
													<?\Aspro\Functions\CAsproOptimusItem::showItemPrices($arParams, $arCurrentSKU["PRICES"], $strMeasure, $min_price_id, ($arParams["SHOW_DISCOUNT_PERCENT_NUMBER"] !== "N" ? "N" : "Y"));?>
												<?endif;?>
											<?else:?>
												<?\Aspro\Functions\CAsproOptimusSku::showItemPrices($arParams, $arItem, $item_id, $min_price_id, array(), ($arParams["SHOW_DISCOUNT_PERCENT_NUMBER"] !== "N" ? "N" : "Y"));?>
											<?endif;?>
										</div>
									<?else:?>
										<?
										$item_id = $arItem["ID"];
										if(isset($arItem['PRICE_MATRIX']) && $arItem['PRICE_MATRIX']) // USE_PRICE_COUNT
										{?>
											<?if($arItem['ITEM_PRICE_MODE'] == 'Q' && count($arItem['PRICE_MATRIX']['ROWS']) > 1):?>
												<?=COptimus::showPriceRangeTop($arItem, $arParams, GetMessage("CATALOG_ECONOMY"));?>
											<?endif;?>
											<?=COptimus::showPriceMatrix($arItem, $arParams, $strMeasure, $arAddToBasketData);?>
											<?$arMatrixKey = array_keys($arItem['PRICE_MATRIX']['MATRIX']);
											$min_price_id=current($arMatrixKey);?>
										<?	
										}
										else
										{
											$arCountPricesCanAccess = 0;
											$min_price_id=0;?>
											<?\Aspro\Functions\CAsproOptimusItem::showItemPrices($arParams, $arItem["PRICES"], $strMeasure, $min_price_id);?>
										<?}?>
									<?endif;?>
								</div>
								<?if($arParams["SHOW_DISCOUNT_TIME"]=="Y" && $arParams['SHOW_COUNTER_LIST'] != 'N'){?>
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
																<span class="value"><?=$totalCount;?></span>
																<span class="text"><?=GetMessage("TITLE_QUANTITY");?></span>
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
								<div class="hover_block1 footer_button inner_content js_offers__<?=$arItem['ID'];?>_block">
									<?if($arItem["OFFERS"]){?>
										<?if(!empty($arItem['OFFERS_PROP'])){?>
											<div class="sku_props">
												<div class="bx_catalog_item_scu wrapper_sku" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PROP_DIV']; ?>" data-site_id="<?=SITE_ID;?>" data-id="<?=$arItem["ID"];?>" data-offer_id="<?=$arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["ID"];?>" data-propertyid="<?=$arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["PROPERTIES"]["CML2_LINK"]["ID"];?>" data-offer_iblockid="<?=$arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["IBLOCK_ID"];?>">
													<?$arSkuTemplate = array();?>
													<?$arSkuTemplate=COptimus::GetSKUPropsArray($arItem['OFFERS_PROPS_JS'], $arResult["SKU_IBLOCK_ID"], $arParams["DISPLAY_TYPE"], $arParams["OFFER_HIDE_NAME_PROPS"], "N", $arItem, $arParams['OFFER_SHOW_PREVIEW_PICTURE_PROPS']);													
													?>
													<?foreach ($arSkuTemplate as $code => $strTemplate){
														if (!isset($arItem['OFFERS_PROP'][$code]))
															continue;
														echo '<div class="item_wrapper">', str_replace('#ITEM#_prop_', $arItemIDs["ALL_ITEM_IDS"]['PROP'], $strTemplate), '</div>';
													}?>
												</div>
												<?$arItemJSParams=COptimus::GetSKUJSParams($arResult, $arParams, $arItem);?>
											</div>
										<?}?>
									<?}?>
									<?if(!$arItem["OFFERS"] || $arParams['TYPE_SKU'] !== 'TYPE_1'):?>
										<div class="counter_wrapp <?=($arItem["OFFERS"] && $arParams["TYPE_SKU"] == "TYPE_1" ? 'woffers' : '')?>">
											<?if(($arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_LIST"] && $arAddToBasketData["ACTION"] == "ADD") && $arItem["CAN_BUY"]):?>
												<div class="counter_block" data-item="<?=$arItem["ID"];?>">
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
									<?elseif($arItem["OFFERS"]):?>
										<?if(empty($arItem['OFFERS_PROP'])){?>
											<div class="offer_buy_block buys_wrapp woffers">
												<?
												$arItem["OFFERS_MORE"] = "Y";
												$arAddToBasketData = COptimus::GetAddToBasketArray($arItem, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false, $arItemIDs["ALL_ITEM_IDS"], 'small read_more1', $arParams);?>
												<!--noindex-->
													<?=$arAddToBasketData["HTML"]?>
												<!--/noindex-->
											</div>
										<?}else{?>
											<div class="offer_buy_block">
												<div class="counter_wrapp">
													<?
													$arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]['IS_OFFER'] = 'Y';
													$arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]['IBLOCK_ID'] = $arParams['IBLOCK_ID'];
													?>
													<?$arAddToBasketData = COptimus::GetAddToBasketArray($arItem["OFFERS"][$arItem["OFFERS_SELECTED"]], $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false, $arItemIDs["ALL_ITEM_IDS"], 'small', $arParams);?>
											
													<?if(($arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_LIST"] && $arAddToBasketData["ACTION"] == "ADD") && $arAddToBasketData["CAN_BUY"]):?>
														<div class="counter_block" data-item="<?=$arCurrentSKU["ID"];?>">
															<span class="minus" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['QUANTITY_DOWN']; ?>">-</span>
															<input type="text" class="text" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['QUANTITY']; ?>" name="<? echo $arParams["PRODUCT_QUANTITY_VARIABLE"]; ?>" value="<?=$arAddToBasketData["MIN_QUANTITY_BUY"]?>" />
															<span class="plus" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['QUANTITY_UP']; ?>" <?=($arAddToBasketData["MAX_QUANTITY_BUY"] ? "data-max='".$arAddToBasketData["MAX_QUANTITY_BUY"]."'" : "")?>>+</span>
														</div>
													<?endif;?>
													<div id="<?=$arItemIDs["ALL_ITEM_IDS"]['BASKET_ACTIONS']; ?>" class="button_block <?=(($arAddToBasketData["ACTION"] == "ORDER"/*&& !$arItem["CAN_BUY"]*/)  || !$arAddToBasketData["CAN_BUY"] || !$arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_LIST"] || $arAddToBasketData["ACTION"] == "SUBSCRIBE" ? "wide" : "");?>">
														<!--noindex-->
															<?=$arAddToBasketData["HTML"]?>
														<!--/noindex-->
													</div>

												</div>
											</div>
										<?}?>
									<?endif;?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?}?>
	<?if(($arParams["AJAX_REQUEST"]=="N") || !isset($arParams["AJAX_REQUEST"])){?>
			</div>
		</div>
	<?}?>
	<?if($arParams["AJAX_REQUEST"]=="Y"){?>
		<div class="wrap_nav">
	<?}?>
	<div class="bottom_nav <?=$arParams["DISPLAY_TYPE"];?>" <?=($arParams["AJAX_REQUEST"]=="Y" ? "style='display: none; '" : "");?>>
		<?if( $arParams["DISPLAY_BOTTOM_PAGER"] == "Y" ){?><?=$arResult["NAV_STRING"]?><?}?>
	</div>
	<?if($arParams["AJAX_REQUEST"]=="Y"){?>
		</div>
	<?}?>
<?}else{?>
	<script>
		// $(document).ready(function(){
			$('.sort_header').animate({'opacity':'1'}, 500);
		// })
	</script>
	<div class="no_goods catalog_block_view">
		<div class="no_products">
			<div class="wrap_text_empty">
				<?if($_REQUEST["set_filter"]){?>
					<?$APPLICATION->IncludeFile(SITE_DIR."include/section_no_products_filter.php", Array(), Array("MODE" => "html",  "NAME" => GetMessage('EMPTY_CATALOG_DESCR')));?>
				<?}else{?>
					<?$APPLICATION->IncludeFile(SITE_DIR."include/section_no_products.php", Array(), Array("MODE" => "html",  "NAME" => GetMessage('EMPTY_CATALOG_DESCR')));?>
				<?}?>
			</div>
		</div>
		<?if($_REQUEST["set_filter"]){?>
			<span class="button wide"><?=GetMessage('RESET_FILTERS');?></span>
		<?}?>
	</div>
<?}?>

<script>
	BX.message({
		QUANTITY_AVAILIABLE: '<? echo COption::GetOptionString("aspro.optimus", "EXPRESSION_FOR_EXISTS", GetMessage("EXPRESSION_FOR_EXISTS_DEFAULT"), SITE_ID); ?>',
		QUANTITY_NOT_AVAILIABLE: '<? echo COption::GetOptionString("aspro.optimus", "EXPRESSION_FOR_NOTEXISTS", GetMessage("EXPRESSION_FOR_NOTEXISTS"), SITE_ID); ?>',
		ADD_ERROR_BASKET: '<? echo GetMessage("ADD_ERROR_BASKET"); ?>',
		ADD_ERROR_COMPARE: '<? echo GetMessage("ADD_ERROR_COMPARE"); ?>',
	})
</script>