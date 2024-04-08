<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?use \Bitrix\Main\Web\Json;?>
<div class="basket_props_block" id="bx_basket_div_<?=$arResult["ID"];?>" style="display: none;">
	<?if (!empty($arResult['PRODUCT_PROPERTIES_FILL'])){
		foreach ($arResult['PRODUCT_PROPERTIES_FILL'] as $propID => $propInfo){?>
			<input type="hidden" name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]" value="<? echo htmlspecialcharsbx($propInfo['ID']); ?>">
			<?if (isset($arResult['PRODUCT_PROPERTIES'][$propID]))
				unset($arResult['PRODUCT_PROPERTIES'][$propID]);
		}
	}
	$arResult["EMPTY_PROPS_JS"]="Y";
	$emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
	if (!$emptyProductProperties){
		$arResult["EMPTY_PROPS_JS"]="N";?>
		<div class="wrapper">
			<table>
				<?foreach ($arResult['PRODUCT_PROPERTIES'] as $propID => $propInfo){?>
					<tr>
						<td><? echo $arResult['PROPERTIES'][$propID]['NAME']; ?></td>
						<td>
							<?if('L' == $arResult['PROPERTIES'][$propID]['PROPERTY_TYPE'] && 'C' == $arResult['PROPERTIES'][$propID]['LIST_TYPE']){
								foreach($propInfo['VALUES'] as $valueID => $value){?>
									<label>
										<input type="radio" name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]" value="<? echo $valueID; ?>" <? echo ($valueID == $propInfo['SELECTED'] ? '"checked"' : ''); ?>><? echo $value; ?>
									</label>
								<?}
							}else{?>
								<select name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]">
									<?foreach($propInfo['VALUES'] as $valueID => $value){?>
										<option value="<? echo $valueID; ?>" <? echo ($valueID == $propInfo['SELECTED'] ? '"selected"' : ''); ?>><? echo $value; ?></option>
									<?}?>
								</select>
							<?}?>
						</td>
					</tr>
				<?}?>
			</table>
		</div>
	<?}?>
</div>
<?
$this->setFrameMode(true);
$currencyList = '';
if (!empty($arResult['CURRENCIES'])){
	$templateLibrary[] = 'currency';
	$currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
}
$templateData = array(
	'TEMPLATE_LIBRARY' => $templateLibrary,
	'CURRENCIES' => $currencyList,
	'STORES' => array(
		"USE_STORE_PHONE" => $arParams["USE_STORE_PHONE"],
		"SCHEDULE" => $arParams["SCHEDULE"],
		"USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
		"MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
		"ELEMENT_ID" => $arResult["ID"],
		"STORE_PATH"  =>  $arParams["STORE_PATH"],
		"MAIN_TITLE"  =>  $arParams["MAIN_TITLE"],
		"MAX_AMOUNT"=>$arParams["MAX_AMOUNT"],
		"USE_ONLY_MAX_AMOUNT" => $arParams["USE_ONLY_MAX_AMOUNT"],
		"SHOW_EMPTY_STORE" => $arParams['SHOW_EMPTY_STORE'],
		"SHOW_GENERAL_STORE_INFORMATION" => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
		"USE_ONLY_MAX_AMOUNT" => $arParams["USE_ONLY_MAX_AMOUNT"],
		"USER_FIELDS" => $arParams['USER_FIELDS'],
		"FIELDS" => $arParams['FIELDS'],
		"STORES" => $arParams['STORES'] = array_diff($arParams['STORES'], array('')),
	)
);
unset($currencyList, $templateLibrary);


$arSkuTemplate = array();
if (!empty($arResult['SKU_PROPS'])){
	$arSkuTemplate=COptimus::GetSKUPropsArray($arResult['SKU_PROPS'], $arResult["SKU_IBLOCK_ID"], "list", $arParams["OFFER_HIDE_NAME_PROPS"]);
}
$strMainID = $this->GetEditAreaId($arResult['ID']);

$strObName = 'ob'.preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);

$arResult["strMainID"] = $this->GetEditAreaId($arResult['ID'])."f";
$arItemIDs=COptimus::GetItemsIDs($arResult, "Y");
$totalCount = COptimus::GetTotalCount($arResult);
$arQuantityData = COptimus::GetQuantityArray($totalCount, $arItemIDs["ALL_ITEM_IDS"], "Y");

$arParams["BASKET_ITEMS"]=($arParams["BASKET_ITEMS"] ? $arParams["BASKET_ITEMS"] : array());
$useStores = $arParams["USE_STORE"] == "Y" && $arResult["STORES_COUNT"] && $arQuantityData["RIGHTS"]["SHOW_QUANTITY"];
$showCustomOffer=(($arResult['OFFERS'] && $arParams["TYPE_SKU"] !="N") ? true : false);
if($showCustomOffer){
	$templateData['JS_OBJ'] = $strObName;
}
$strMeasure='';
$arAddToBasketData = array();
if($arResult["OFFERS"]){
	$strMeasure=$arResult["MIN_PRICE"]["CATALOG_MEASURE_NAME"];
	$templateData["STORES"]["OFFERS"]="Y";
	foreach($arResult["OFFERS"] as $key=>$arOffer){
		$templateData["STORES"]["OFFERS_ID"][]=$arOffer["ID"];
	}
}else{
	if (($arParams["SHOW_MEASURE"]=="Y")&&($arResult["CATALOG_MEASURE"])){
		$arMeasure = CCatalogMeasure::getList(array(), array("ID"=>$arResult["CATALOG_MEASURE"]), false, false, array())->GetNext();
		$strMeasure=$arMeasure["SYMBOL_RUS"];
	}
	$arAddToBasketData = COptimus::GetAddToBasketArray($arResult, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false, $arItemIDs["ALL_ITEM_IDS"], 'big_btn', $arParams);
}
$arOfferProps = implode(';', (array)$arParams['OFFERS_CART_PROPERTIES']);

// save item viewed
$arFirstPhoto = reset($arResult['MORE_PHOTO']);
$arItemPrices = $arResult['MIN_PRICE'];
if(isset($arResult['PRICE_MATRIX']) && $arResult['PRICE_MATRIX'])
{
	$rangSelected = $arResult['ITEM_QUANTITY_RANGE_SELECTED'];
	$priceSelected = $arResult['ITEM_PRICE_SELECTED'];
	if(isset($arResult['FIX_PRICE_MATRIX']) && $arResult['FIX_PRICE_MATRIX'])
	{
		$rangSelected = $arResult['FIX_PRICE_MATRIX']['RANGE_SELECT'];
		$priceSelected = $arResult['FIX_PRICE_MATRIX']['PRICE_SELECT'];
	}
	$arItemPrices = $arResult['ITEM_PRICES'][$priceSelected];
	$arItemPrices['VALUE'] = $arItemPrices['BASE_PRICE'];
	$arItemPrices['PRINT_VALUE'] = \Aspro\Functions\CAsproOptimusItem::getCurrentPrice('BASE_PRICE', $arItemPrices);
	$arItemPrices['DISCOUNT_VALUE'] = $arItemPrices['PRICE'];
	$arItemPrices['PRINT_DISCOUNT_VALUE'] = \Aspro\Functions\CAsproOptimusItem::getCurrentPrice('PRICE', $arItemPrices);
}
$arViewedData = array(
	'PRODUCT_ID' => $arResult['ID'],
	'IBLOCK_ID' => $arResult['IBLOCK_ID'],
	'NAME' => $arResult['NAME'],
	'DETAIL_PAGE_URL' => $arResult['DETAIL_PAGE_URL'],
	'PICTURE_ID' => $arResult['PREVIEW_PICTURE'] ? $arResult['PREVIEW_PICTURE']['ID'] : ($arFirstPhoto ? $arFirstPhoto['ID'] : false),
	'CATALOG_MEASURE_NAME' => $arResult['CATALOG_MEASURE_NAME'],
	'MIN_PRICE' => $arItemPrices,
	'CAN_BUY' => $arResult['CAN_BUY'] ? 'Y' : 'N',
	'IS_OFFER' => 'N',
	'WITH_OFFERS' => $arResult['OFFERS'] ? 'Y' : 'N',
);
$elementName = ((isset($arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) ? $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] : $arResult['NAME']);

$actualItem = $arResult["OFFERS"] ? (isset($arResult['OFFERS'][$arResult['OFFERS_SELECTED']]) ? $arResult['OFFERS'][$arResult['OFFERS_SELECTED']] : reset($arResult['OFFERS'])) : $arResult;
?>
<script type="text/javascript">
setViewedProduct(<?=$arResult['ID']?>, <?=CUtil::PhpToJSObject($arViewedData, false)?>);
</script>
<div class="item_main_info <?=(!$showCustomOffer ? "noffer" : "");?> <?=($arParams["SHOW_UNABLE_SKU_PROPS"] != "N" ? "show_un_props" : "unshow_un_props");?>" id="<?=$arItemIDs["strMainID"];?>">
	<div class="img_wrapper">
		<div class="item_slider">
			<div class="stickers">
				<?if (is_array($arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"])):?>
					<?foreach($arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"] as $key=>$class){?>
						<div><div class="sticker_<?=strtolower($class);?>"><?=$arResult["PROPERTIES"]["HIT"]["VALUE"][$key]?></div></div>
					<?}?>
				<?endif;?>
				<?if($arParams["SALE_STIKER"] && $arResult["PROPERTIES"][$arParams["SALE_STIKER"]]["VALUE"]){?>
					<div><div class="sticker_sale_text"><?=$arResult["PROPERTIES"][$arParams["SALE_STIKER"]]["VALUE"];?></div></div>
				<?}?>
			</div>
			<?reset($arResult['MORE_PHOTO']);
			$arFirstPhoto = current($arResult['MORE_PHOTO']);
			$viewImgType=$arParams["DETAIL_PICTURE_MODE"];?>
			<div class="slides">
				<?if($showCustomOffer && !empty($arResult['OFFERS_PROP'])){?>
					<div class="offers_img wof">
						<?$alt=$arFirstPhoto["ALT"];
						$title=$arFirstPhoto["TITLE"];?>
						<?if($arFirstPhoto["BIG"]["src"]){?>
							<a href="<?=($viewImgType=="POPUP" ? $arFirstPhoto["BIG"]["src"] : "javascript:void(0)");?>" class="<?=($viewImgType=="POPUP" ? "popup_link" : "line_link");?>" title="<?=$title;?>">
								<img id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PICT']; ?>" src="<?=$arFirstPhoto['SMALL']['src']; ?>" <?=($viewImgType=="MAGNIFIER" ? 'data-large="" xpreview="" xoriginal=""': "");?> alt="<?=$alt;?>" title="<?=$title;?>" itemprop="image">
							</a>
						<?}else{?>
							<a href="javascript:void(0)" class="" title="<?=$title;?>">
								<img id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PICT']; ?>" src="<?=$arFirstPhoto['SRC']; ?>" alt="<?=$alt;?>" title="<?=$title;?>" itemprop="image">
							</a>
						<?}?>
					</div>
				<?}else{
					if($arResult["MORE_PHOTO"]){
						$bMagnifier = ($viewImgType=="MAGNIFIER");?>
						<ul>
							<?foreach($arResult["MORE_PHOTO"] as $i => $arImage){
								if($i && $bMagnifier):?>
									<?continue;?>
								<?endif;?>
								<?$isEmpty=($arImage["SMALL"]["src"] ? false : true );?>
								<?
								$alt=$arImage["ALT"];
								$title=$arImage["TITLE"];
								?>
								<li id="photo-<?=$i?>" <?=(!$i ? 'class="current"' : 'style="display: none;"')?>>
									<?if(!$isEmpty){?>
										<a href="<?=($viewImgType=="POPUP" ? $arImage["BIG"]["src"] : "javascript:void(0)");?>" <?=($bIsOneImage ? '' : 'data-fancybox-group="item_slider"')?> class="<?=($viewImgType=="POPUP" ? "popup_link fancy" : "line_link");?>" title="<?=$title;?>">
											<img  src="<?=$arImage["SMALL"]["src"]?>" <?=($viewImgType=="MAGNIFIER" ? "class='zoom_picture'" : "");?> <?=($viewImgType=="MAGNIFIER" ? 'xoriginal="'.$arImage["BIG"]["src"].'" xpreview="'.$arImage["THUMB"]["src"].'"' : "");?> alt="<?=$alt;?>" title="<?=$title;?>"<?=(!$i ? ' itemprop="image"' : '')?>/>
										</a>
									<?}else{?>
										<img  src="<?=$arImage["SRC"]?>" alt="<?=$alt;?>" title="<?=$title;?>" />
									<?}?>
								</li>
							<?}?>
						</ul>
					<?}
				}?>
			</div>
			<?/*thumbs*/?>
			<?if(!$showCustomOffer || empty($arResult['OFFERS_PROP'])){
				if(count($arResult["MORE_PHOTO"]) > 1):?>
					<div class="wrapp_thumbs xzoom-thumbs">
						<div class="thumbs flexslider" data-plugin-options='{"animation": "slide", "selector": ".slides_block > li", "directionNav": true, "itemMargin":10, "itemWidth": 54, "controlsContainer": ".thumbs_navigation", "controlNav" :false, "animationLoop": true, "slideshow": false}' style="max-width:<?=ceil(((count($arResult['MORE_PHOTO']) <= 3 ? count($arResult['MORE_PHOTO']) : 3) * 64) - 10)?>px;">
							<ul class="slides_block" id="thumbs">
								<?foreach($arResult["MORE_PHOTO"]as $i => $arImage):?>
									<li <?=(!$i ? 'class="current"' : '')?> data-big_img="<?=$arImage["BIG"]["src"]?>" data-small_img="<?=$arImage["SMALL"]["src"]?>">
										<span><img class="xzoom-gallery" width="50" xpreview="<?=$arImage["THUMB"]["src"];?>" src="<?=$arImage["THUMB"]["src"]?>" alt="<?=$arImage["ALT"];?>" title="<?=$arImage["TITLE"];?>" /></span>
									</li>
								<?endforeach;?>
							</ul>
							<span class="thumbs_navigation custom_flex"></span>
						</div>
					</div>
					<script>
						$(document).ready(function(){
							$('.item_slider .thumbs li').first().addClass('current');
							$('.item_slider .thumbs .slides_block').delegate('li:not(.current)', 'click', function(){
								var slider_wrapper = $(this).parents('.item_slider'),
									index = $(this).index();
								$(this).addClass('current').siblings().removeClass('current')
								if(arOptimusOptions['THEME']['DETAIL_PICTURE_MODE'] == 'MAGNIFIER')
								{
									var li = $(this).parents('.item_slider').find('.slides li');
									li.find('img').attr('src', $(this).data('small_img'));
									li.find('img').attr('xoriginal', $(this).data('big_img'));
								}
								else
								{
									slider_wrapper.find('.slides li').removeClass('current').hide();
									slider_wrapper.find('.slides li:eq('+index+')').addClass('current').show();
								}
							});
						})
					</script>
				<?endif;?>
			<?}else{?>
				<div class="wrapp_thumbs">
					<div class="sliders">
						<div class="thumbs" style="">
						</div>
					</div>
				</div>
			<?}?>
		</div>
		<?/*mobile*/?>
		<?if(!$showCustomOffer || empty($arResult['OFFERS_PROP'])){?>
			<div class="item_slider flex flexslider" data-plugin-options='{"animation": "slide", "directionNav": false, "controlNav": true, "animationLoop": false, "slideshow": true, "slideshowSpeed": 10000, "animationSpeed": 600}'>
				<ul class="slides">
					<?if($arResult["MORE_PHOTO"]){
						foreach($arResult["MORE_PHOTO"] as $i => $arImage){?>
							<?$isEmpty=($arImage["SMALL"]["src"] ? false : true );?>
							<li id="mphoto-<?=$i?>" <?=(!$i ? 'class="current"' : 'style="display: none;"')?>>
								<?
								$alt=$arImage["ALT"];
								$title=$arImage["TITLE"];
								?>
								<?if(!$isEmpty){?>
									<a href="<?=$arImage["BIG"]["src"]?>" data-fancybox-group="item_slider_flex" class="fancy" title="<?=$title;?>" >
										<img src="<?=$arImage["SMALL"]["src"]?>" alt="<?=$alt;?>" title="<?=$title;?>" />
									</a>
								<?}else{?>
									<img  src="<?=$arImage["SRC"];?>" alt="<?=$alt;?>" title="<?=$title;?>" />
								<?}?>
							</li>
						<?}
					}?>
				</ul>
			</div>
		<?}else{?>
			<div class="item_slider flex"></div>
		<?}?>
	</div>
	<div class="prices_item_block scrollbar">
		<div class="middle_info main_item_wrapper list_item">
			<?$frame = $this->createFrame()->begin();?>
			<div class="prices_block">
				<div class="cost prices clearfix">
					<?if( count( (array)$arResult["OFFERS"] ) > 0 ){?>
						<div class="with_matrix" style="display:none;">
							<div class="price price_value_block"><span class="values_wrapper"></span></div>
							<?if($arParams["SHOW_OLD_PRICE"]=="Y"):?>
								<div class="price discount"></div>
							<?endif;?>
							<?if($arParams["SHOW_DISCOUNT_PERCENT"]=="Y"){?>
								<div class="sale_block matrix" style="display:none;">
									<div class="sale_wrapper">
									<div class="value">-<span></span>%</div>
									<div class="text"><span class="title"><?=GetMessage("CATALOG_ECONOMY");?></span>
									<span class="values_wrapper"></span></div>
									<div class="clearfix"></div></div>
								</div>
							<?}?>
						</div>
						<?\Aspro\Functions\CAsproOptimusSku::showItemPrices($arParams, $arResult, $item_id, $min_price_id, $arItemIDs);?>
					<?}else{?>
						<?
						$item_id = $arResult["ID"];
						if(isset($arResult['PRICE_MATRIX']) && $arResult['PRICE_MATRIX']) // USE_PRICE_COUNT
						{
							if($arResult['PRICE_MATRIX']['COLS'])
							{
								$arCurPriceType = current($arResult['PRICE_MATRIX']['COLS']);
								$arCurPrice = current($arResult['PRICE_MATRIX']['MATRIX'][$arCurPriceType['ID']]);
								$min_price_id = $arCurPriceType['ID'];?>
								<div class="" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
									<meta itemprop="price" content="<?=($arResult['MIN_PRICE']['DISCOUNT_VALUE'] ? $arResult['MIN_PRICE']['DISCOUNT_VALUE'] : $arResult['MIN_PRICE']['VALUE'])?>" />
									<meta itemprop="priceCurrency" content="<?=$arResult['MIN_PRICE']['CURRENCY']?>" />
									<link itemprop="availability" href="http://schema.org/<?=($arResult['PRICE_MATRIX']['AVAILABLE'] == 'Y' ? 'InStock' : 'OutOfStock')?>" />
								</div>
							<?}?>
							<?if($arResult['ITEM_PRICE_MODE'] == 'Q' && count((array)$arResult['PRICE_MATRIX']['ROWS']) > 1):?>
								<?=COptimus::showPriceRangeTop($arResult, $arParams, GetMessage("CATALOG_ECONOMY"));?>
							<?endif;?>
							<?=COptimus::showPriceMatrix($arResult, $arParams, $strMeasure, $arAddToBasketData);?>
						<?
						}
						else
						{?>
							<?\Aspro\Functions\CAsproOptimusItem::showItemPrices($arParams, $arResult["PRICES"], $strMeasure, $min_price_id);?>
						<?}?>
					<?}?>
				</div>
				<?if($arParams["SHOW_DISCOUNT_TIME"]=="Y"){?>
					<?$arUserGroups = $USER->GetUserGroupArray();?>
					<?if($arParams['SHOW_DISCOUNT_TIME_EACH_SKU'] != 'Y' || ($arParams['SHOW_DISCOUNT_TIME_EACH_SKU'] == 'Y' && !$arResult['OFFERS'])):?>
						<?$arDiscounts = CCatalogDiscount::GetDiscountByProduct($item_id, $arUserGroups, "N", $min_price_id, SITE_ID);
						$arDiscount=array();
						if($arDiscounts)
							$arDiscount=current($arDiscounts);
						if($arDiscount["ACTIVE_TO"]){?>
							<div class="view_sale_block <?=($arQuantityData["HTML"] ? '' : 'wq');?>"">
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
												<span class="value" <?=((count( (array)$arResult["OFFERS"] ) > 0 && $arParams["TYPE_SKU"] == 'TYPE_1' && $arResult["OFFERS_PROP"]) ? 'style="opacity:0;"' : '')?>><?=$totalCount;?></span>
												<span class="text"><?=GetMessage("TITLE_QUANTITY");?></span>
											</span>
										</div>
									</div>
								<?endif;?>
							</div>
						<?}?>
					<?else:?>
						<?if($arResult['JS_OFFERS'])
						{

							foreach($arResult['JS_OFFERS'] as $keyOffer => $arTmpOffer2)
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
								$arResult['JS_OFFERS'][$keyOffer]['DISCOUNT_ACTIVE'] = $active_to;
							}
						}?>
						<div class="view_sale_block" style="display:none;">
							<div class="count_d_block">
									<span class="active_to_<?=$arResult["ID"]?> hidden"><?=$arDiscount["ACTIVE_TO"];?></span>
									<div class="title"><?=GetMessage("UNTIL_AKC");?></div>
									<span class="countdown countdown_<?=$arResult["ID"]?> values"></span>
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
			<div class="buy_block">
				<?if(!$arResult["OFFERS"]):?>
					<script>
						$(document).ready(function() {
							$('.catalog_detail .tabs_section .tabs_content .form.inline input[data-sid="PRODUCT_NAME"]').attr('value', $('h1').text());
						});
					</script>
					<div class="counter_wrapp">
						<?if(($arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_DETAIL"] && $arAddToBasketData["ACTION"] == "ADD") && $arResult["CAN_BUY"]):?>
							<div class="counter_block big_basket" data-offers="<?=($arResult["OFFERS"] ? "Y" : "N");?>" data-item="<?=$arResult["ID"];?>" <?=(($arResult["OFFERS"] && $arParams["TYPE_SKU"]=="N") ? "style='display: none;'" : "");?>>
								<span class="minus" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['QUANTITY_DOWN']; ?>">-</span>
								<input type="text" class="text" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['QUANTITY']; ?>" name="<? echo $arParams["PRODUCT_QUANTITY_VARIABLE"]; ?>" value="<?=$arAddToBasketData["MIN_QUANTITY_BUY"]?>" />
								<span class="plus" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['QUANTITY_UP']; ?>" <?=($arAddToBasketData["MAX_QUANTITY_BUY"] ? "data-max='".$arAddToBasketData["MAX_QUANTITY_BUY"]."'" : "")?>>+</span>
							</div>
						<?endif;?>
						<div id="<? echo $arItemIDs["ALL_ITEM_IDS"]['BASKET_ACTIONS']; ?>" class="button_block <?=(($arAddToBasketData["ACTION"] == "ORDER" /*&& !$arResult["CAN_BUY"]*/) || !$arResult["CAN_BUY"] || !$arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_DETAIL"] || ($arAddToBasketData["ACTION"] == "SUBSCRIBE" && $arResult["CATALOG_SUBSCRIBE"] == "Y")  ? "wide" : "");?>">
							<!--noindex-->
								<?=$arAddToBasketData["HTML"]?>
							<!--/noindex-->
						</div>
					</div>
					<?if($arAddToBasketData["ACTION"] !== "NOTHING"):?>
						<?if($arAddToBasketData["ACTION"] == "ADD" && $arResult["CAN_BUY"] && $arParams["SHOW_ONE_CLICK_BUY"]!="N"):?>
							<div class="wrapp_one_click">
								<span class="transparent big_btn type_block button transition_bg one_click" data-item="<?=$arResult["ID"]?>" data-iblockID="<?=$arParams["IBLOCK_ID"]?>" data-quantity="<?=$arAddToBasketData["MIN_QUANTITY_BUY"];?>" onclick="oneClickBuy('<?=$arResult["ID"]?>', '<?=$arParams["IBLOCK_ID"]?>', this)">
									<span><?=GetMessage('ONE_CLICK_BUY')?></span>
								</span>
							</div>
						<?endif;?>
					<?endif;?>

					<?if(isset($arResult['PRICE_MATRIX']) && $arResult['PRICE_MATRIX']) // USE_PRICE_COUNT
					{?>
						<?if($arResult['ITEM_PRICE_MODE'] == 'Q' && count((array)$arResult['PRICE_MATRIX']['ROWS']) > 1):?>
							<?$arOnlyItemJSParams = array(
								"ITEM_PRICES" => $arResult["ITEM_PRICES"],
								"ITEM_PRICE_MODE" => $arResult["ITEM_PRICE_MODE"],
								"ITEM_QUANTITY_RANGES" => $arResult["ITEM_QUANTITY_RANGES"],
								"MIN_QUANTITY_BUY" => $arAddToBasketData["MIN_QUANTITY_BUY"],
								"ID" => $arItemIDs["strMainID"],
							)?>
							<script type="text/javascript">
								var <? echo $arItemIDs["strObName"]; ?>el = new JCCatalogOnlyElement(<? echo CUtil::PhpToJSObject($arOnlyItemJSParams, false, true); ?>);
							</script>
						<?endif;?>
					<?}?>
				<?elseif($arResult["OFFERS"] && $arParams['TYPE_SKU'] == 'TYPE_1'):?>
					<div class="offer_buy_block buys_wrapp" style="display:none;">
						<div class="counter_wrapp"></div>
					</div>
				<?elseif($arResult["OFFERS"] && $arParams['TYPE_SKU'] != 'TYPE_1'):?>
					<span class="big_btn slide_offer button transition_bg type_block"><i></i><span><?=GetMessage("MORE_TEXT_BOTTOM");?></span></span>
				<?endif;?>
			</div>
			<?//delivery calculate?>
			<?if((!$arResult["OFFERS"] &&
					$arAddToBasketData["ACTION"] == "ADD" &&
					$arResult["CAN_BUY"]) ||
					(
						$arResult["OFFERS"] &&
						$arParams['TYPE_SKU'] === 'TYPE_1'
					)):?>
				<?=\Aspro\Functions\CAsproOptimus::showCalculateDeliveryBlock($arResult['ID'], $arParams);?>
			<?endif;?>
			<?if($arParams["DISPLAY_WISH_BUTTONS"] != "N" || $arParams["DISPLAY_COMPARE"] == "Y"):?>
				<div class="description_wrapp top_info">
					<div class="like_icons">
						<?if($arParams["DISPLAY_WISH_BUTTONS"] != "N" && $arResult['CAN_BUY'] && $arAddToBasketData["ACTION"] == "ADD"):?>
							<?if(!$arResult["OFFERS"]):?>
								<div class="wish_item_button">
									<span class="wish_item to" data-item="<?=$arResult["ID"]?>" data-iblock="<?=$arResult["IBLOCK_ID"]?>"><i></i><div><?=GetMessage('CATALOG_WISH')?></div></span>
									<span class="wish_item in added" style="display: none;" data-item="<?=$arResult["ID"]?>" data-iblock="<?=$arResult["IBLOCK_ID"]?>"><i></i><div><?=GetMessage('CATALOG_WISH_OUT2')?></div></span>
								</div>
							<?elseif($arResult["OFFERS"] && !empty($arResult['OFFERS_PROP'])):?>
								<div class="wish_item_button">
									<div class="wish_item text " data-item="" data-iblock="<?=$arResult["IBLOCK_ID"]?>" data-offers="Y" data-props="<?=$arOfferProps?>">
										<span class="value <?=$arParams["TYPE_SKU"];?>"><i></i><div><?=GetMessage('CATALOG_WISH')?></div></span>
										<span class="value added <?=$arParams["TYPE_SKU"];?>"><i></i><div><?=GetMessage('CATALOG_WISH_OUT2')?></div></span>
									</div>
								</div>
							<?endif;?>
						<?endif;?>
						<?if($arParams["DISPLAY_COMPARE"] == "Y"):?>
							<?if(!$arResult["OFFERS"] || ($arParams["TYPE_SKU"] !== 'TYPE_1' || ($arParams["TYPE_SKU"] == 'TYPE_1' && !$arResult["OFFERS_PROP"]))):?>
								<div class="compare_item_button">
									<span class="compare_item to" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arResult["ID"]?>" ><i></i><div><?=GetMessage('CATALOG_COMPARE')?></div></span>
									<span class="compare_item in added" style="display: none;" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arResult["ID"]?>"><i></i><div><?=GetMessage('CATALOG_COMPARE_OUT')?></div></span>
								</div>
							<?elseif($arResult["OFFERS"]):?>
								<div class="compare_item_button">
									<span class="compare_item to <?=$arParams["TYPE_SKU"];?>" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="" ><i></i><div><?=GetMessage('CATALOG_COMPARE')?></div></span>
									<span class="compare_item in added <?=$arParams["TYPE_SKU"];?>" style="display: none;" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item=""><i></i><div><?=GetMessage('CATALOG_COMPARE_OUT')?></div></span>
								</div>
							<?endif;?>
						<?endif;?>
					</div>
				</div>
			<?endif;?>
			<?$frame->end();?>
		</div>
	</div>
	<div class="right_info">
		<div class="info_item scrollbar">
			<?
			$showProps = false;
			if($arResult["DISPLAY_PROPERTIES"]){
				foreach($arResult["DISPLAY_PROPERTIES"] as $arProp){
					if(!in_array($arProp["CODE"], array("SERVICES", "BRAND", "HIT", "RECOMMEND", "NEW", "STOCK", "VIDEO", "VIDEO_YOUTUBE", "CML2_ARTICLE"))){
						if(!is_array($arProp["DISPLAY_VALUE"])){
							$arProp["DISPLAY_VALUE"] = array($arProp["DISPLAY_VALUE"]);
						}
					}
					if(is_array($arProp["DISPLAY_VALUE"])){
						foreach($arProp["DISPLAY_VALUE"] as $value){
							if(strlen($value)){
								$showProps = true;
								break 2;
							}
						}
					}
				}
			}
			if(!$showProps && $arResult['OFFERS']){
				foreach($arResult['OFFERS'] as $arOffer){
					foreach($arOffer['DISPLAY_PROPERTIES'] as $arProp){
						if(!is_array($arProp["DISPLAY_VALUE"])){
							$arProp["DISPLAY_VALUE"] = array($arProp["DISPLAY_VALUE"]);
						}

						foreach($arProp["DISPLAY_VALUE"] as $value){
							if(strlen($value)){
								$showProps = true;
								break 3;
							}
						}
					}
				}
			}
			?>
			<div class="title hidden"><a href="<?=$arResult["DETAIL_PAGE_URL"];?>" class="dark_link"><?=$elementName;?></a></div>
			<div class="top_info">
				<?if($arParams["USE_RATING"] == "Y"):?>
					<?$frame = $this->createFrame('dv_'.$arResult["ID"])->begin('');?>
						<div class="rating">
							<?$APPLICATION->IncludeComponent(
							   "bitrix:iblock.vote",
							   "element_rating",
							   Array(
								  "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
								  "IBLOCK_ID" => $arResult["IBLOCK_ID"],
								  "ELEMENT_ID" =>$arResult["ID"],
								  "MAX_VOTE" => 5,
								  "VOTE_NAMES" => array(),
								  "CACHE_TYPE" => $arParams["CACHE_TYPE"],
								  "CACHE_TIME" => $arParams["CACHE_TIME"],
								  "DISPLAY_AS_RATING" => 'vote_avg'
							   ),
							   $component, array("HIDE_ICONS" =>"Y")
							);?>
						</div>
					<?$frame->end();?>
				<?endif;?>
				<div class="rows_block">
					<div class="item_block">
						<?if(strlen($arResult["DISPLAY_PROPERTIES"]["CML2_ARTICLE"]["VALUE"]) || ($arResult['SHOW_OFFERS_PROPS'] && $showCustomOffer)):?>
							<div class="article iblock" itemprop="additionalProperty" itemscope itemtype="http://schema.org/PropertyValue" <?if($arResult['SHOW_OFFERS_PROPS']){?>id="<? echo $arItemIDs["ALL_ITEM_IDS"]['DISPLAY_PROP_ARTICLE_DIV'] ?>" style="display: none;"<?}?>>
								<span class="block_title" itemprop="name"><?=GetMessage("ARTICLE");?></span>
								<span class="value" itemprop="value"><?=$arResult["DISPLAY_PROPERTIES"]["CML2_ARTICLE"]["VALUE"]?></span>
							</div>
						<?endif;?>
						<?if($useStores){?>
							<div class="p_block">
						<?}?>
						<?=$arQuantityData["HTML"];?>
						<?if($useStores){?>
							</div>
						<?}?>
						<?if($arParams["SHOW_CHEAPER_FORM"] == "Y"):?>
							<div class="cheaper_form">
								<span class="animate-load cheaper" data-name="<?=COptimus::formatJsName($arResult["NAME"]);?>" data-item="<?=$arResult['ID'];?>"><?=($arParams["CHEAPER_FORM_NAME"] ? $arParams["CHEAPER_FORM_NAME"] : GetMessage("CHEAPER"));?></span>
							</div>
						<?endif;?>
					</div>
				</div>
				<div class="sku_block">
					<?if($arResult["OFFERS"] && $showCustomOffer){?>
						<div class="sku_props">
							<?if (!empty($arResult['OFFERS_PROP'])){?>
								<div class="bx_catalog_item_scu wrapper_sku" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PROP_DIV']; ?>">
									<?foreach ($arSkuTemplate as $code => $strTemplate){
										if (!isset($arResult['OFFERS_PROP'][$code]))
											continue;
										echo str_replace('#ITEM#_prop_', $arItemIDs["ALL_ITEM_IDS"]['PROP'], $strTemplate);
									}?>
								</div>
							<?}?>
							<?$arItemJSParams=COptimus::GetSKUJSParams($arResult, $arParams, $arResult, "Y");?>
							<script type="text/javascript">
								var <? echo $arItemIDs["strObName"]; ?> = new JCCatalogElementFast(<? echo CUtil::PhpToJSObject($arItemJSParams, false, true); ?>);
							</script>
						</div>
					<?}?>
				</div>
				<?if(strlen($arResult["PREVIEW_TEXT"])):?>
					<div class="preview_text"><?=$arResult["PREVIEW_TEXT"]?></div>
				<?endif;?>
				<?$strGrupperType = $arParams["GRUPPER_PROPS"];?>
				<div class="iblock char_block" <?=(!$showProps ? 'style="display:none;"' : '');?>>
					<div class="title_tab"><?=GetMessage("PROPERTIES_TAB");?></div>
					<table class="props_list">
						<?foreach($arResult["DISPLAY_PROPERTIES"] as $arProp):?>
							<?if(!in_array($arProp["CODE"], array("SERVICES", "HIT", "RECOMMEND", "NEW", "STOCK", "VIDEO", "VIDEO_YOUTUBE", "POPUP_VIDEO", "CML2_ARTICLE"))):?>
								<?if((!is_array($arProp["DISPLAY_VALUE"]) && strlen($arProp["DISPLAY_VALUE"])) || (is_array($arProp["DISPLAY_VALUE"]) && implode('', $arProp["DISPLAY_VALUE"]))):?>
									<tr itemprop="additionalProperty" itemscope itemtype="http://schema.org/PropertyValue">
										<td class="char_name">
											<?if($arProp["HINT"] && $arParams["SHOW_HINTS"]=="Y"):?><div class="hint"><span class="icon"><i>?</i></span><div class="tooltip"><?=$arProp["HINT"]?></div></div><?endif;?>
											<span class="props_item <?if($arProp["HINT"] && $arParams["SHOW_HINTS"] == "Y"){?>whint<?}?>">
												<span itemprop="name"><?=$arProp["NAME"]?></span>
											</span>
										</td>
										<td class="char_value">
											<span itemprop="value">
												<?if(count((array)$arProp["DISPLAY_VALUE"]) > 1):?>
													<?=implode(', ', (array)$arProp["DISPLAY_VALUE"]);?>
												<?else:?>
													<?=$arProp["DISPLAY_VALUE"];?>
												<?endif;?>
											</span>
										</td>
									</tr>
								<?endif;?>
							<?endif;?>
						<?endforeach;?>
					</table>
					<table class="props_list" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['DISPLAY_PROP_DIV']; ?>"></table>
				</div>
			</div>
		</div>
	</div>
	<?
	if($arResult['CATALOG'] && $actualItem['CAN_BUY'] && \Bitrix\Main\ModuleManager::isModuleInstalled('sale') && $arParams['USE_DETAIL_PREDICTION'] !== 'N'){
		$APPLICATION->IncludeComponent(
			'bitrix:sale.prediction.product.detail',
			'main',
			array(
				'BUTTON_ID' => false,
				'CUSTOM_SITE_ID' => isset($arParams['CUSTOM_SITE_ID']) ? $arParams['CUSTOM_SITE_ID'] : null,
				'POTENTIAL_PRODUCT_TO_BUY' => array(
					'ID' => $arResult['ID'],
					'MODULE' => isset($arResult['MODULE']) ? $arResult['MODULE'] : 'catalog',
					'PRODUCT_PROVIDER_CLASS' => isset($arResult['PRODUCT_PROVIDER_CLASS']) ? $arResult['PRODUCT_PROVIDER_CLASS'] : 'CCatalogProductProvider',
					'QUANTITY' => isset($arResult['QUANTITY']) ? $arResult['QUANTITY'] : null,
					'IBLOCK_ID' => $arResult['IBLOCK_ID'],
					'PRIMARY_OFFER_ID' => isset($arResult['OFFERS'][0]['ID']) ? $arResult['OFFERS'][0]['ID'] : null,
					'SECTION' => array(
						'ID' => isset($arResult['SECTION']['ID']) ? $arResult['SECTION']['ID'] : null,
						'IBLOCK_ID' => isset($arResult['SECTION']['IBLOCK_ID']) ? $arResult['SECTION']['IBLOCK_ID'] : null,
						'LEFT_MARGIN' => isset($arResult['SECTION']['LEFT_MARGIN']) ? $arResult['SECTION']['LEFT_MARGIN'] : null,
						'RIGHT_MARGIN' => isset($arResult['SECTION']['RIGHT_MARGIN']) ? $arResult['SECTION']['RIGHT_MARGIN'] : null,
					),
				),
				'REQUEST_ITEMS' => true,
				'RCM_TEMPLATE' => 'main',
			),
			$component,
			array('HIDE_ICONS' => 'Y')
		);
	}
	?>
</div>

<script type="text/javascript">
	BX.message({
		QUANTITY_AVAILIABLE: '<? echo COption::GetOptionString("aspro.next", "EXPRESSION_FOR_EXISTS", GetMessage("EXPRESSION_FOR_EXISTS_DEFAULT"), SITE_ID); ?>',
		QUANTITY_NOT_AVAILIABLE: '<? echo COption::GetOptionString("aspro.next", "EXPRESSION_FOR_NOTEXISTS", GetMessage("EXPRESSION_FOR_NOTEXISTS"), SITE_ID); ?>',
		ADD_ERROR_BASKET: '<? echo GetMessage("ADD_ERROR_BASKET"); ?>',
		ADD_ERROR_COMPARE: '<? echo GetMessage("ADD_ERROR_COMPARE"); ?>',
		ONE_CLICK_BUY: '<? echo GetMessage("ONE_CLICK_BUY"); ?>',
		SITE_ID: '<? echo SITE_ID; ?>'
	})
</script>