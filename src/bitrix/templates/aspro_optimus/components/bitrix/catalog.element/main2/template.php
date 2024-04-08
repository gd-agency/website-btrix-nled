<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$shema = array();?>
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
	'OFFERS' => $arResult['OFFERS'],
	'OFFERS_SELECTED' => $arResult['OFFERS_SELECTED'],
	'CATALOG' => $arResult['CATALOG'],
	'OFFERS_IBLOCK' => $arResult['OFFERS_IBLOCK'],
	'MODULE' => $arResult['MODULE'],
	'PRODUCT_PROVIDER_CLASS' => $arResult['PRODUCT_PROVIDER_CLASS'],
	'QUANTITY' => $arResult['QUANTITY'],
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

$arResult["strMainID"] = $this->GetEditAreaId($arResult['ID']);
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
	$arAddToBasketData = COptimus::GetAddToBasketArray($arResult, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false, $arItemIDs["ALL_ITEM_IDS"], 'big_btn w_icons', $arParams);
}
$arOfferProps = implode(';', $arParams['OFFERS_CART_PROPERTIES']);

$templateData["ITEM_IDS"] = $arItemIDs;
$templateData["USE_STORE"] = $useStores;
$templateData["SHOW_CUSTOM_OFFER"] = $showCustomOffer;
$templateData["HAS_OFFERS"] = (!empty($arResult["OFFERS"]));

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
	$arItemPrices['VALUE'] = $arItemPrices['PRICE'];
	$arItemPrices['PRINT_VALUE'] = \Aspro\Functions\CAsproOptimusItem::getCurrentPrice('PRICE', $arItemPrices);
	$arItemPrices['DISCOUNT_VALUE'] = $arItemPrices['BASE_PRICE'];
	$arItemPrices['PRINT_DISCOUNT_VALUE'] = \Aspro\Functions\CAsproOptimusItem::getCurrentPrice('BASE_PRICE', $arItemPrices);
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

$actualItem = $arResult["OFFERS"] ? (isset($arResult['OFFERS'][$arResult['OFFERS_SELECTED']]) ? $arResult['OFFERS'][$arResult['OFFERS_SELECTED']] : reset($arResult['OFFERS'])) : $arResult;
?>
<script type="text/javascript">
setViewedProduct(<?=$arResult['ID']?>, <?=CUtil::PhpToJSObject($arViewedData, false)?>);
</script>
<meta itemprop="name" content="<?=$name = strip_tags(!empty($arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) ? $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] : $arResult['NAME'])?>" />
<meta itemprop="category" content="<?=$arResult['CATEGORY_PATH']?>" />
<meta itemprop="description" content="<?=(strlen(strip_tags($arResult['PREVIEW_TEXT'])) ? strip_tags($arResult['PREVIEW_TEXT']) : (strlen(strip_tags($arResult['DETAIL_TEXT'])) ? strip_tags($arResult['DETAIL_TEXT']) : $name))?>" />
<div class="item_main_info <?=(!$showCustomOffer ? "noffer" : "");?> <?=($arParams["SHOW_UNABLE_SKU_PROPS"] != "N" ? "show_un_props" : "unshow_un_props");?>" id="<?=$arItemIDs["strMainID"];?>">
	<div class="img_wrapper">
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
		<div class="item_slider">
			<?if(($arParams["DISPLAY_WISH_BUTTONS"] != "N" || $arParams["DISPLAY_COMPARE"] == "Y") || (strlen($arResult["DISPLAY_PROPERTIES"]["CML2_ARTICLE"]["VALUE"]) || ($arResult['SHOW_OFFERS_PROPS'] && $showCustomOffer))):?>
				<div class="like_wrapper">
					<?if($arParams["DISPLAY_WISH_BUTTONS"] != "N" || $arParams["DISPLAY_COMPARE"] == "Y"):?>
						<div class="like_icons iblock">
							<?if($arParams["DISPLAY_WISH_BUTTONS"] != "N"):?>
								<?if(!$arResult["OFFERS"] && $arAddToBasketData["ACTION"] == "ADD"):?>
									<div class="wish_item text" data-item="<?=$arResult["ID"]?>" data-iblock="<?=$arResult["IBLOCK_ID"]?>">
										<span class="value" title="<?=GetMessage('CT_BCE_CATALOG_IZB')?>" ><i></i></span>
										<span class="value added" title="<?=GetMessage('CT_BCE_CATALOG_IZB_ADDED')?>"><i></i></span>
									</div>
								<?elseif($arResult["OFFERS"] && $arParams["TYPE_SKU"] === 'TYPE_1' && !empty($arResult['OFFERS_PROP'])):?>
									<div class="wish_item text " data-item="" data-iblock="<?=$arResult["IBLOCK_ID"]?>" <?=(!empty($arResult['OFFERS_PROP']) ? 'data-offers="Y"' : '');?> data-props="<?=$arOfferProps?>">
										<span class="value <?=$arParams["TYPE_SKU"];?>" title="<?=GetMessage('CT_BCE_CATALOG_IZB')?>"><i></i></span>
										<span class="value added <?=$arParams["TYPE_SKU"];?>" title="<?=GetMessage('CT_BCE_CATALOG_IZB_ADDED')?>"><i></i></span>
									</div>
								<?endif;?>
							<?endif;?>
							<?if($arParams["DISPLAY_COMPARE"] == "Y"):?>
								<?if(!$arResult["OFFERS"] || ($arResult["OFFERS"] && $arParams["TYPE_SKU"] === 'TYPE_1' && !$arResult["OFFERS_PROP"])):?>
									<div data-item="<?=$arResult["ID"]?>" data-iblock="<?=$arResult["IBLOCK_ID"]?>" data-href="<?=$arResult["COMPARE_URL"]?>" class="compare_item text <?=($arResult["OFFERS"] ? $arParams["TYPE_SKU"] : "");?>" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['COMPARE_LINK']; ?>">
										<span class="value" title="<?=GetMessage('CT_BCE_CATALOG_COMPARE')?>"><i></i></span>
										<span class="value added" title="<?=GetMessage('CT_BCE_CATALOG_COMPARE_ADDED')?>"><i></i></span>
									</div>
								<?elseif($arResult["OFFERS"] && $arParams["TYPE_SKU"] === 'TYPE_1'):?>
									<div data-item="" data-iblock="<?=$arResult["IBLOCK_ID"]?>" data-href="<?=$arResult["COMPARE_URL"]?>" class="compare_item text <?=$arParams["TYPE_SKU"];?>">
										<span class="value" title="<?=GetMessage('CT_BCE_CATALOG_COMPARE')?>"><i></i></span>
										<span class="value added" title="<?=GetMessage('CT_BCE_CATALOG_COMPARE_ADDED')?>"><i></i></span>
									</div>
								<?endif;?>
							<?endif;?>
						</div>
					<?endif;?>
				</div>
			<?endif;?>

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
						<div class="thumbs flexslider" data-plugin-options='{"animation": "slide", "selector": ".slides_block > li", "directionNav": true, "itemMargin":10, "itemWidth": 54, "controlsContainer": ".thumbs_navigation", "controlNav" :false, "animationLoop": true, "slideshow": false}' style="max-width:<?=ceil(((count($arResult['MORE_PHOTO']) <= 4 ? count($arResult['MORE_PHOTO']) : 4) * 64) - 10)?>px;">
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
	<div class="right_info">
		<div class="info_item">
			<?$isArticle=(strlen($arResult["DISPLAY_PROPERTIES"]["CML2_ARTICLE"]["VALUE"]) || ($arResult['SHOW_OFFERS_PROPS'] && $showCustomOffer));?>
			<?if($isArticle || $arResult["BRAND_ITEM"] || $arParams["SHOW_RATING"] == "Y" || strlen($arResult["PREVIEW_TEXT"])){?>
				<div class="top_info">
					<div class="rows_block">
						<?$col=1;
						if($isArticle && $arResult["BRAND_ITEM"] && $arParams["SHOW_RATING"] == "Y"){
							$col=3;
						}elseif(($isArticle && $arResult["BRAND_ITEM"]) || ($isArticle && $arParams["SHOW_RATING"] == "Y") || ($arResult["BRAND_ITEM"] && $arParams["SHOW_RATING"] == "Y")){
							$col=2;
						}?>
						<?if($arParams["SHOW_RATING"] == "Y"):?>
							<div class="item_block col-<?=$col;?>">
								<?$frame = $this->createFrame('dv_'.$arResult["ID"])->begin('');?>
									<div class="rating">
										<?$APPLICATION->IncludeComponent(
										   "bitrix:iblock.vote",
										   "element_rating",
										   Array(
											  "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
											  "IBLOCK_ID" => $arResult["IBLOCK_ID"],
											  "ELEMENT_ID" => $arResult["ID"],
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
							</div>
						<?endif;?>
						<?if($isArticle):?>
							<div class="item_block col-<?=$col;?>">
								<div class="article iblock" itemprop="additionalProperty" itemscope itemtype="http://schema.org/PropertyValue" <?if($arResult['SHOW_OFFERS_PROPS']){?>id="<? echo $arItemIDs["ALL_ITEM_IDS"]['DISPLAY_PROP_ARTICLE_DIV'] ?>" style="display: none;"<?}?>>
									<span class="block_title" itemprop="name"><?=GetMessage("ARTICLE");?>:</span>
									<span class="value" itemprop="value"><?=$arResult["DISPLAY_PROPERTIES"]["CML2_ARTICLE"]["VALUE"]?></span>
								</div>
							</div>
						<?endif;?>

						<?if($arResult["BRAND_ITEM"]){?>
							<div class="item_block col-<?=$col;?>">
								<div class="brand">
									<meta itemprop="brand" content="<?=$arResult["BRAND_ITEM"]["NAME"]?>" />
									<?if(!$arResult["BRAND_ITEM"]["IMAGE"]):?>
										<b class="block_title"><?=GetMessage("BRAND");?>:</b>
										<a href="<?=$arResult["BRAND_ITEM"]["DETAIL_PAGE_URL"]?>"><?=$arResult["BRAND_ITEM"]["NAME"]?></a>
									<?else:?>
										<a class="brand_picture" href="<?=$arResult["BRAND_ITEM"]["DETAIL_PAGE_URL"]?>">
											<img  src="<?=$arResult["BRAND_ITEM"]["IMAGE"]["src"]?>" alt="<?=$arResult["BRAND_ITEM"]["NAME"]?>" title="<?=$arResult["BRAND_ITEM"]["NAME"]?>" />
										</a>
									<?endif;?>
								</div>
							</div>
						<?}?>
					</div>
					<?if(strlen($arResult["PREVIEW_TEXT"])):?>
						<div class="preview_text"><?=$arResult["PREVIEW_TEXT"]?></div>
						<?if(strlen($arResult["DETAIL_TEXT"])):?>
							<div class="more_block icons_fa color_link"><span><?=GetMessage('MORE_TEXT_BOTTOM');?></span></div>
						<?endif;?>
					<?endif;?>
				</div>
			<?}?>
			<div class="middle_info main_item_wrapper">
				<?$frame = $this->createFrame()->begin();?>
				<div class="prices_block">
					<div class="cost prices clearfix">
						<?if( count( $arResult["OFFERS"] ) > 0 ){?>
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
										<?$price = ($arResult['MIN_PRICE']['DISCOUNT_VALUE'] ? $arResult['MIN_PRICE']['DISCOUNT_VALUE'] : $arResult['MIN_PRICE']['VALUE'] )?>
										<?$currency = $arResult['MIN_PRICE']['CURRENCY'] ? $arResult['MIN_PRICE']['CURRENCY'] : $arParams['CURRENCY_ID']?>
										<meta itemprop="price" content="<?=($price ? $price : 0)?>" />
										<meta itemprop="priceCurrency" content="<?=$currency ? $currency : CSaleLang::GetLangCurrency(SITE_ID)?>" />
										
										<link itemprop="availability" href="http://schema.org/<?=($arResult['PRICE_MATRIX']['AVAILABLE'] == 'Y' ? 'InStock' : 'OutOfStock')?>" />
									</div>
								<?}?>
								<?if($arResult['ITEM_PRICE_MODE'] == 'Q' && count($arResult['PRICE_MATRIX']['ROWS']) > 1):?>
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
													<span class="value" <?=((count( $arResult["OFFERS"] ) > 0 && $arParams["TYPE_SKU"] == 'TYPE_1' && $arResult["OFFERS_PROP"]) ? 'style="opacity:0;"' : '')?>><?=$totalCount;?></span>
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
					<?if($useStores){?>
						<div class="p_block">
					<?}?>
						<?=$arQuantityData["HTML"];?>
					<?if($useStores){?>
						</div>
					<?}?>
					<?if($arParams["SHOW_CHEAPER_FORM"] == "Y"):?>
						<div class="cheaper_form">
							<span class="animate-load cheaper" data-name="<?=$arResult["NAME"]?>" data-item="<?=$arResult['ID'];?>"><?=($arParams["CHEAPER_FORM_NAME"] ? $arParams["CHEAPER_FORM_NAME"] : GetMessage("CHEAPER"));?></span>
						</div>
					<?endif;?>
				</div>
				<div class="buy_block">
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
								var <? echo $arItemIDs["strObName"]; ?> = new JCCatalogElement(<? echo CUtil::PhpToJSObject($arItemJSParams, false, true); ?>);
							</script>
						</div>
					<?}?>
					<?if(!$arResult["OFFERS"]):?>
						<script>
							$(document).ready(function() {
								$('.catalog_detail .tabs_section .form.inline input[data-sid="PRODUCT_NAME"]').attr('value', $('h1').text());
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
							<?if($arResult['ITEM_PRICE_MODE'] == 'Q' && count($arResult['PRICE_MATRIX']['ROWS']) > 1):?>
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
				<?if((!$arResult["OFFERS"] &&
					$arAddToBasketData["ACTION"] == "ADD" &&
					$arResult["CAN_BUY"]) ||
					(
						$arResult["OFFERS"] &&
						$arParams['TYPE_SKU'] === 'TYPE_1'
					)):?>
					<?//delivery calculate?>
						<?=\Aspro\Functions\CAsproOptimus::showCalculateDeliveryBlock($arResult['ID'], $arParams);?>
				<?endif;?>
				<?$frame->end();?>
			</div>

			<?if(is_array($arResult["STOCK"]) && $arResult["STOCK"]):?>
				<?foreach($arResult["STOCK"] as $key => $arStockItem):?>
					<div class="stock_board">
						<div class="title"><?=GetMessage("CATALOG_STOCK_TITLE")?></div>
						<div class="txt"><?=$arStockItem["PREVIEW_TEXT"]?></div>
						<a class="read_more" href="<?=$arStockItem["DETAIL_PAGE_URL"]?>"><?=GetMessage("CATALOG_STOCK_VIEW")?></a>
					</div>
				<?endforeach;?>
			<?endif;?>
			<div class="element_detail_text wrap_md">
				<div class="sh">
					<?$APPLICATION->IncludeFile(SITE_DIR."include/share_buttons.php", Array(), Array("MODE" => "html", "NAME" => GetMessage('CT_BCE_CATALOG_SOC_BUTTON')));?>
				</div>
				<div class="price_txt">
					<?$APPLICATION->IncludeFile(SITE_DIR."include/element_detail_text.php", Array(), Array("MODE" => "html",  "NAME" => GetMessage('CT_BCE_CATALOG_DOP_DESCR')));?>
				</div>
			</div>
		</div>
	</div>
	<?if($arResult['OFFERS']):?>
		<span itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer" style="display:none;">
			<?$lowPrice = ($arResult['MIN_PRICE']['DISCOUNT_VALUE'] ? $arResult['MIN_PRICE']['DISCOUNT_VALUE'] : $arResult['MIN_PRICE']['VALUE'] )?>
			<?$currency = $arResult['MIN_PRICE']['CURRENCY'] ? $arResult['MIN_PRICE']['CURRENCY'] : $arParams['CURRENCY_ID']?>
			<meta itemprop="lowPrice" content="<?=($lowPrice ? $lowPrice : 0)?>" />
			<meta itemprop="priceCurrency" content="<?=$currency ? $currency : CSaleLang::GetLangCurrency(SITE_ID)?>" />
			
		  <meta itemprop="offerCount" content="<?=count($arResult['OFFERS'])?>" />
			<?foreach($arResult['OFFERS'] as $arOffer):?>
				<?$currentOffersList = array();?>
				<?foreach($arOffer['TREE'] as $propName => $skuId):?>
					<?$propId = (int)substr($propName, 5);?>
					<?foreach($arResult['SKU_PROPS'] as $prop):?>
						<?if($prop['ID'] == $propId):?>
							<?foreach($prop['VALUES'] as $propId => $propValue):?>
								<?if($propId == $skuId):?>
									<?$currentOffersList[] = $propValue['NAME'];?>
									<?break;?>
								<?endif;?>
							<?endforeach;?>
						<?endif;?>
					<?endforeach;?>
				<?endforeach;?>
				<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
					<meta itemprop="sku" content="<?=implode('/', $currentOffersList)?>" />
					<a href="<?=$arOffer['DETAIL_PAGE_URL']?>" itemprop="url"></a>
					<meta itemprop="price" content="<?=($arOffer['MIN_PRICE']['DISCOUNT_VALUE']) ? $arOffer['MIN_PRICE']['DISCOUNT_VALUE'] : $arOffer['MIN_PRICE']['VALUE']?>" />
					<meta itemprop="priceCurrency" content="<?=$arOffer['MIN_PRICE']['CURRENCY']?>" />
					<link itemprop="availability" href="http://schema.org/<?=($arOffer['CAN_BUY'] ? 'InStock' : 'OutOfStock')?>" />

					<?
					if($arDiscount["ACTIVE_TO"]){?>
						<meta itemprop="priceValidUntil" content="<?=date("Y-m-d", MakeTimeStamp($arDiscount["ACTIVE_TO"]))?>" />
					<?}?>
					<link itemprop="url" href="<?=$arResult["DETAIL_PAGE_URL"]?>" />
				</span>
			<?endforeach;?>
		</span>
		<?unset($arOffer, $currentOffersList);?>
	<?else:?>
		<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
			<?$price = ($arResult['MIN_PRICE']['DISCOUNT_VALUE'] ? $arResult['MIN_PRICE']['DISCOUNT_VALUE'] : $arResult['MIN_PRICE']['VALUE'] )?>
			<?$currency = $arResult['MIN_PRICE']['CURRENCY'] ? $arResult['MIN_PRICE']['CURRENCY'] : $arParams['CURRENCY_ID']?>
			<meta itemprop="price" content="<?=($price ? $price : 0)?>" />
			<meta itemprop="priceCurrency" content="<?=$currency ? $currency : CSaleLang::GetLangCurrency(SITE_ID)?>" />
			
			<link itemprop="availability" href="http://schema.org/<?=($arResult['MIN_PRICE']['CAN_BUY'] ? 'InStock' : 'OutOfStock')?>" />
			<?
			if($arDiscount["ACTIVE_TO"]){?>
				<meta itemprop="priceValidUntil" content="<?=date("Y-m-d", MakeTimeStamp($arDiscount["ACTIVE_TO"]))?>" />
			<?}?>
			<link itemprop="url" href="<?=$arResult["DETAIL_PAGE_URL"]?>" />
		</span>
	<?endif;?>
	<div class="clearleft"></div>
	<?if($arResult["TIZERS_ITEMS"]){?>
		<div class="tizers_block_detail">
			<div class="rows_block">
				<?$count_t_items=count($arResult["TIZERS_ITEMS"]);?>
				<?foreach($arResult["TIZERS_ITEMS"] as $arItem){?>
					<div class="item_block tizer col-<?=$count_t_items;?>">
						<div class="inner_wrapper">
							<?if($arItem["UF_LINK"]){?>
								<a href="<?=$arItem["UF_LINK"];?>" <?=(strpos($arItem["UF_LINK"], "http") !== false ? "target='_blank' rel='nofollow'" : '')?>>
							<?}?>
							<?if($arItem["UF_FILE"]){?>
								<div class="image">
									<img src="<?=$arItem["PREVIEW_PICTURE"]["src"];?>" alt="<?=$arItem["UF_NAME"];?>" title="<?=$arItem["UF_NAME"];?>">
								</div>
							<?}?>
							<div class="text">
								<?=$arItem["UF_NAME"];?>
							</div>
							<div class="clearfix"></div>
							<?if($arItem["UF_LINK"]){?>
								</a>
							<?}?>
						</div>
					</div>
				<?}?>
			</div>
		</div>
	<?}?>

	<?if($arParams["SHOW_KIT_PARTS"] == "Y" && $arResult["SET_ITEMS"]):?>
		<div class="set_wrapp set_block">
			<div class="title"><?=GetMessage("GROUP_PARTS_TITLE")?></div>
			<ul>
				<?foreach($arResult["SET_ITEMS"] as $iii => $arSetItem):?>
					<li class="item">
						<div class="item_inner">
							<div class="image">
								<a href="<?=$arSetItem["DETAIL_PAGE_URL"]?>">
									<?if($arSetItem["PREVIEW_PICTURE"]):?>
										<?$img = CFile::ResizeImageGet($arSetItem["PREVIEW_PICTURE"], array("width" => 140, "height" => 140), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
										<img  src="<?=$img["src"]?>" alt="<?=$arSetItem["NAME"];?>" title="<?=$arSetItem["NAME"];?>" />
									<?elseif($arSetItem["DETAIL_PICTURE"]):?>
										<?$img = CFile::ResizeImageGet($arSetItem["DETAIL_PICTURE"], array("width" => 140, "height" => 140), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
										<img  src="<?=$img["src"]?>" alt="<?=$arSetItem["NAME"];?>" title="<?=$arSetItem["NAME"];?>" />
									<?else:?>
										<img  src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_small.png" alt="<?=$arSetItem["NAME"];?>" title="<?=$arSetItem["NAME"];?>" />
									<?endif;?>
								</a>
								<?if($arResult["SET_ITEMS_QUANTITY"]):?>
									<div class="quantity">x<?=$arSetItem["QUANTITY"];?></div>
								<?endif;?>
							</div>
							<div class="item_info">
								<div class="item-title">
									<a href="<?=$arSetItem["DETAIL_PAGE_URL"]?>"><span><?=$arSetItem["NAME"]?></span></a>
								</div>
								<?if($arParams["SHOW_KIT_PARTS_PRICES"] == "Y"):?>
									<div class="cost prices clearfix">
										<?
										$arCountPricesCanAccess = 0;
										foreach($arSetItem["PRICES"] as $key => $arPrice){
											if($arPrice["CAN_ACCESS"]){
												$arCountPricesCanAccess++;
											}
										}?>
										<?if($arSetItem["MEASURE"][$arSetItem["ID"]]["MEASURE"]["SYMBOL_RUS"])
											$strMeasure = $arSetItem["MEASURE"][$arSetItem["ID"]]["MEASURE"]["SYMBOL_RUS"];?>
										<?foreach($arSetItem["PRICES"] as $key => $arPrice):?>
											<?if($arPrice["CAN_ACCESS"]):?>
												<?$price = CPrice::GetByID($arPrice["ID"]);?>
												<?if($arCountPricesCanAccess > 1):?>
													<div class="price_name"><?=$price["CATALOG_GROUP_NAME"];?></div>
												<?endif;?>
												<?if($arPrice["VALUE"] > $arPrice["DISCOUNT_VALUE"]):?>
													<div class="price">
														<?=$arPrice["PRINT_DISCOUNT_VALUE"];?><?if(($arParams["SHOW_MEASURE"] == "Y") && $strMeasure):?><small>/<?=$strMeasure?></small><?endif;?>
													</div>
													<?if($arParams["SHOW_OLD_PRICE"]=="Y"):?>
														<div class="price discount">
															<span><?=$arPrice["PRINT_VALUE"]?></span>
														</div>
													<?endif;?>
												<?else:?>
													<div class="price">
														<?=$arPrice["PRINT_VALUE"];?><?if(($arParams["SHOW_MEASURE"] == "Y") && $strMeasure):?><small>/<?=$strMeasure?></small><?endif;?>
													</div>
												<?endif;?>
											<?endif;?>
										<?endforeach;?>
									</div>
								<?endif;?>
							</div>
						</div>
					</li>
					<?if($arResult["SET_ITEMS"][$iii + 1]):?>
						<li class="separator"></li>
					<?endif;?>
				<?endforeach;?>
			</ul>
		</div>
	<?endif;?>
	<?if($arResult['OFFERS']):?>
		<?if($arResult['OFFER_GROUP']):?>
			<?foreach($arResult['OFFERS'] as $arOffer):?>
				<?if(!$arOffer['OFFER_GROUP']) continue;?>
				<span id="<?=$arItemIDs['ALL_ITEM_IDS']['OFFER_GROUP'].$arOffer['ID']?>" style="display: none;">
					<?$APPLICATION->IncludeComponent("bitrix:catalog.set.constructor", "",
						array(
							"IBLOCK_ID" => $arResult["OFFERS_IBLOCK"],
							"ELEMENT_ID" => $arOffer['ID'],
							"PRICE_CODE" => $arParams["PRICE_CODE"],
							"BASKET_URL" => $arParams["BASKET_URL"],
							"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
							"SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"],
							"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
							"SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"],
							"CONVERT_CURRENCY" => $arParams['CONVERT_CURRENCY'],
							"CURRENCY_ID" => $arParams["CURRENCY_ID"]
						), $component, array("HIDE_ICONS" => "Y")
					);?>
				</span>
			<?endforeach;?>
		<?endif;?>
	<?else:?>
		<?$APPLICATION->IncludeComponent("bitrix:catalog.set.constructor", "",
			array(
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"ELEMENT_ID" => $arResult["ID"],
				"PRICE_CODE" => $arParams["PRICE_CODE"],
				"BASKET_URL" => $arParams["BASKET_URL"],
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"],
				"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
				"SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"],
				"CONVERT_CURRENCY" => $arParams['CONVERT_CURRENCY'],
				"CURRENCY_ID" => $arParams["CURRENCY_ID"]
			), $component, array("HIDE_ICONS" => "Y")
		);?>
	<?endif;?>
</div>

<div class="tabs_section notab">
	<?
	$iTab = 0;
	$showProps = false;
	if($arResult["DISPLAY_PROPERTIES"])
	{
		foreach($arResult["DISPLAY_PROPERTIES"] as $arProp)
		{
			if(!in_array($arProp["CODE"], array("SERVICES", "BRAND", "HIT", "RECOMMEND", "NEW", "STOCK", "VIDEO", "VIDEO_YOUTUBE", "CML2_ARTICLE")))
			{
				if(!is_array($arProp["DISPLAY_VALUE"]))
					$arProp["DISPLAY_VALUE"] = array($arProp["DISPLAY_VALUE"]);
				if(is_array($arProp["DISPLAY_VALUE"]))
				{
					foreach($arProp["DISPLAY_VALUE"] as $value)
					{
						if(strlen($value))
						{
							$showProps = true;
							break 2;
						}
					}
				}
			}
		}
	}
	if(!$showProps && $arOffer['DISPLAY_PROPERTIES'])
	{
		foreach($arResult['OFFERS'] as $arOffer)
		{
			foreach($arOffer['DISPLAY_PROPERTIES'] as $arProp)
			{
				if(!is_array($arProp["DISPLAY_VALUE"]))
					$arProp["DISPLAY_VALUE"] = array($arProp["DISPLAY_VALUE"]);

				foreach($arProp["DISPLAY_VALUE"] as $value)
				{
					if(strlen($value))
					{
						$showProps = true;
						break 3;
					}
				}
			}
		}
	}
	$arVideo = array();
	if(strlen($arResult["DISPLAY_PROPERTIES"]["VIDEO"]["VALUE"])){
		$arVideo[] = $arResult["DISPLAY_PROPERTIES"]["VIDEO"]["~VALUE"];
	}
	if(isset($arResult["DISPLAY_PROPERTIES"]["VIDEO_YOUTUBE"]["VALUE"])){
		if(is_array($arResult["DISPLAY_PROPERTIES"]["VIDEO_YOUTUBE"]["VALUE"])){
			$arVideo = $arVideo + $arResult["DISPLAY_PROPERTIES"]["VIDEO_YOUTUBE"]["~VALUE"];
		}
		elseif(strlen($arResult["DISPLAY_PROPERTIES"]["VIDEO_YOUTUBE"]["VALUE"])){
			$arVideo[] = $arResult["DISPLAY_PROPERTIES"]["VIDEO_YOUTUBE"]["~VALUE"];
		}
	}
	if(strlen($arResult["SECTION_FULL"]["UF_VIDEO"])){
		$arVideo[] = $arResult["SECTION_FULL"]["~UF_VIDEO"];
	}
	if(strlen($arResult["SECTION_FULL"]["UF_VIDEO_YOUTUBE"])){
		$arVideo[] = $arResult["SECTION_FULL"]["~UF_VIDEO_YOUTUBE"];
	}
	?>
	<?$docs_prop_code = (($arParams["DETAIL_DOCS_PROP"] && $arParams["DETAIL_DOCS_PROP"] != "-") ? $arParams["DETAIL_DOCS_PROP"] : "INSTRUCTIONS" );?>

	<?//block sku?>
	<?$this->SetViewTarget('offers_show_block');?>
		<?if($arResult["OFFERS"] && $arParams["TYPE_SKU"]=="N"):?>
			<div class="drag_block">
				<div class="wrap_md wrap_md_row">
					<div class="iblock md-100">
						<h4><?=GetMessage("OFFER_PRICES")?></h4>
						<?
						$showSkUName = ((in_array('NAME', $arParams['OFFERS_FIELD_CODE'])));
						$showSkUImages = false;
						if(((in_array('PREVIEW_PICTURE', $arParams['OFFERS_FIELD_CODE']) || in_array('DETAIL_PICTURE', $arParams['OFFERS_FIELD_CODE'])))){
							foreach ($arResult["OFFERS"] as $key => $arSKU){
								if($arSKU['PREVIEW_PICTURE'] || $arSKU['DETAIL_PICTURE']){
									$showSkUImages = true;
									break;
								}
							}
						}?>
						<?if($arResult["OFFERS"] && $arParams["TYPE_SKU"] !== "TYPE_1"):?>
							<script>
								$(document).ready(function() {
									$('.catalog_detail .tabs_section .form.inline input[data-sid="PRODUCT_NAME"]').attr('value', $('h1').text());
								});
							</script>
						<?endif;?>
						<?if($arResult["OFFERS"] && $arParams["TYPE_SKU"] !== "TYPE_1"):?>
							<div class="prices_tab ">
							<div class="bx_sku_props" style="display:none;">
								<?$arSkuKeysProp='';
								$propSKU=$arParams["OFFERS_CART_PROPERTIES"];
								if($propSKU){
									$arSkuKeysProp=base64_encode(serialize(array_keys($propSKU)));
								}?>
								<input type="hidden" value="<?=$arSkuKeysProp;?>"></input>
							</div>

							<div class="list-offers ajax_load">
								<div class="bx_sku_props" style="display:none;">
									<?$arSkuKeysProp='';
									$propSKU=$arParams["OFFERS_CART_PROPERTIES"];
									if($propSKU){
										$arSkuKeysProp=base64_encode(serialize(array_keys($propSKU)));
									}?>
									<input type="hidden" value="<?=$arSkuKeysProp;?>" />
								</div>
								<div class="table-view flexbox flexbox--row">
									<?foreach($arResult["OFFERS"] as $key => $arSKU):?>
										<?
										if($arResult["PROPERTIES"]["CML2_BASE_UNIT"]["VALUE"])
											$sMeasure = $arResult["PROPERTIES"]["CML2_BASE_UNIT"]["VALUE"];
										else
											$sMeasure = GetMessage("MEASURE_DEFAULT");
										$skutotalCount = COptimus::GetTotalCount($arSKU, $arParams);
										$arskuQuantityData = COptimus::GetQuantityArray($skutotalCount, array('quantity-wrapp', 'quantity-indicators'));

										$arSKU["IBLOCK_ID"]=$arResult["IBLOCK_ID"];
										$arSKU["IS_OFFER"]="Y";
										$arskuAddToBasketData = COptimus::GetAddToBasketArray($arSKU, $skutotalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], true, array(), 'small', $arParams);
										$arskuAddToBasketData["HTML"] = str_replace('data-item', 'data-props="'.$arOfferProps.'" data-item', $arskuAddToBasketData["HTML"]);
										?>
										<div class="table-view__item item bordered box-shadow main_item_wrapper <?=($useStores ? "table-view__item--has-stores" : "");?>">
											<div class="table-view__item-wrapper item_info catalog-adaptive flexbox flexbox--row">
												<?if($showSkUImages):?>
													<?//image-block?>
													<div class="item-foto">
														<div class="item-foto__picture">
															<?
															$srcImgPreview = $srcImgDetail = false;
															$imgPreviewID = ($arResult['OFFERS'][$key]['PREVIEW_PICTURE'] ? (is_array($arResult['OFFERS'][$key]['PREVIEW_PICTURE']) ? $arResult['OFFERS'][$key]['PREVIEW_PICTURE']['ID'] : $arResult['OFFERS'][$key]['PREVIEW_PICTURE']) : false);
															$imgDetailID = ($arResult['OFFERS'][$key]['DETAIL_PICTURE'] ? (is_array($arResult['OFFERS'][$key]['DETAIL_PICTURE']) ? $arResult['OFFERS'][$key]['DETAIL_PICTURE']['ID'] : $arResult['OFFERS'][$key]['DETAIL_PICTURE']) : false);
															$imgPreviewID;
															if($imgPreviewID || $imgDetailID){
																$arImgPreview = CFile::ResizeImageGet($imgPreviewID ? $imgPreviewID : $imgDetailID, array('width' => 350, 'height' => 350), BX_RESIZE_IMAGE_PROPORTIONAL, true);
																$srcImgPreview = $arImgPreview['src'];
															}
															if($imgDetailID){
																$srcImgDetail = CFile::GetPath($imgDetailID);
															}
															?>
															<?if($srcImgPreview || $srcImgDetail):?>
																<img src="<?=$srcImgPreview?>" alt="<?=$arSKU['NAME']?>" />
															<?else:?>
																<img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=$arSKU['NAME']?>"/>
															<?endif;?>
														</div>
														<div class="adaptive">
															<div class="like_icons block">
																<div class="like_icons list static icons">
																	<?if($arParams["DISPLAY_WISH_BUTTONS"] != "N"):?>
																		<?if($arSKU['CAN_BUY'] && $arskuAddToBasketData["ACTION"] === "ADD"):?>
																			<div class="wish_item_button o_<?=$arSKU["ID"];?>">
																				<span title="<?=GetMessage('CATALOG_WISH')?>" class="wish_item text to <?=$arParams["TYPE_SKU"];?>" data-item="<?=$arSKU["ID"]?>" data-iblock="<?=$arResult["IBLOCK_ID"]?>" data-offers="Y" data-props="<?=$arOfferProps?>"><i></i></span>
																				<span title="<?=GetMessage('CATALOG_WISH_OUT')?>" class="wish_item text in added <?=$arParams["TYPE_SKU"];?>" style="display: none;" data-item="<?=$arSKU["ID"]?>" data-iblock="<?=$arSKU["IBLOCK_ID"]?>"><i></i></span>
																			</div>
																		<?endif;?>
																	<?endif;?>
																	<?if($arParams["DISPLAY_COMPARE"] == "Y"):?>
																		<div class="compare_item_button o_<?=$arSKU["ID"];?>">
																			<span title="<?=GetMessage('CATALOG_COMPARE')?>" class="compare_item to text <?=$arParams["TYPE_SKU"];?>" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arSKU["ID"]?>" ><i></i></span>
																			<span title="<?=GetMessage('CATALOG_COMPARE_OUT')?>" class="compare_item in added text <?=$arParams["TYPE_SKU"];?>" style="display: none;" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arSKU["ID"]?>"><i></i></span>
																		</div>
																	<?endif;?>
																</div>
															</div>
														</div>
													</div>
												<?endif;?>

												<?//text-block?>
												<div class="item-info">
													<div class="item-title"><?=$arSKU['NAME']?></div>
													<div class="quantity_block_wrapper">
														<?if($useStores){?>
															<div class="p_block">
														<?}?>
															<?=$arskuQuantityData["HTML"];?>
														<?if($useStores){?>
															</div>
														<?}?>
														<?if($arSKU['PROPERTIES']['ARTICLE']['VALUE']):?>
															<div class="muted article">
																<span class="name"><?/*=Loc::getMessage('ARTICLE_COMPACT');*/?></span><span class="value"><?=$arSKU['PROPERTIES']['ARTICLE']['VALUE'];?></span>
															</div>
														<?endif;?>
													</div>
													<?if($arResult["SKU_PROPERTIES"]):?>
														<div class="properties list">
															<div class="properties__container properties props_list">
																<?foreach ($arResult["SKU_PROPERTIES"] as $key => $arProp){?>
																	<?if(!$arProp["IS_EMPTY"] && $key != 'ARTICLE'):?>
																		<div class="properties__item properties__item--compact ">
																			<?if($arResult["TMP_OFFERS_PROP"][$arProp["CODE"]]){
																				echo $arResult["TMP_OFFERS_PROP"][$arProp["CODE"]]["VALUES"][$arSKU["TREE"]["PROP_".$arProp["ID"]]]["NAME"];?>
																				<?}else{
																					if (is_array($arSKU["PROPERTIES"][$arProp["CODE"]]["VALUE"])){
																						echo implode("/", $arSKU["PROPERTIES"][$arProp["CODE"]]["VALUE"]);
																					}else{
																						if($arSKU["PROPERTIES"][$arProp["CODE"]]["USER_TYPE"]=="directory" && isset($arSKU["PROPERTIES"][$arProp["CODE"]]["USER_TYPE_SETTINGS"]["TABLE_NAME"])){
																							$rsData = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('=TABLE_NAME'=>$arSKU["PROPERTIES"][$arProp["CODE"]]["USER_TYPE_SETTINGS"]["TABLE_NAME"])));
																							if ($arData = $rsData->fetch()){
																								$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData);
																								$entityDataClass = $entity->getDataClass();
																								$arFilter = array(
																									'limit' => 1,
																									'filter' => array(
																										'=UF_XML_ID' => $arSKU["PROPERTIES"][$arProp["CODE"]]["VALUE"]
																									)
																								);
																								$arValue = $entityDataClass::getList($arFilter)->fetch();
																								if(isset($arValue["UF_NAME"]) && $arValue["UF_NAME"]){
																									$SkuProperti = $arValue["UF_NAME"];
																								}else{
																									$SkuProperti = $arSKU["PROPERTIES"][$arProp["CODE"]]["VALUE"];
																								}
																							}
																						}else{
																							$SkuProperti =  $arSKU["PROPERTIES"][$arProp["CODE"]]["VALUE"];
																						}
																					}
																				}?>
																			<?if($SkuProperti):?>
																				<div class="properties__title muted properties__item--inline char_name">
																					<?if($arProp["HINT"] && $arParams["SHOW_HINTS"]=="Y"):?><div class="hint"><span class="icon"><i>?</i></span><div class="tooltip"><?=$arProp["HINT"]?></div></div><?endif;?>
																					<span class="props_item"><?=$arProp["NAME"]?>:</span>
																				</div>
																				<div class="properties__value darken properties__item--inline char_value font_xs"><?=$SkuProperti;?></div>
																			<?endif;?>
																		</div>
																	<?endif;?>
																<?}?>
															</div>
														</div>
													<?endif;?>
												</div>

												<div class="item-actions flexbox flexbox--row">
													<?//prices-block?>
													<div class="item-price">
														<div class="cost prices clearfix">
															<?
															$collspan++;
															$arCountPricesCanAccess = 0;
															if(isset($arSKU['PRICE_MATRIX']) && $arSKU['PRICE_MATRIX'] && count($arSKU['PRICE_MATRIX']['ROWS']) > 1) // USE_PRICE_COUNT
															{?>
																<?=COptimus::showPriceRangeTop($arSKU, $arParams, GetMessage("CATALOG_ECONOMY"));?>
																<?echo COptimus::showPriceMatrix($arSKU, $arParams, $arSKU["CATALOG_MEASURE_NAME"]);
															}
															else
															{?>
																<?\Aspro\Functions\CAsproOptimusItem::showItemPrices($arParams, $arSKU["PRICES"], $arSKU["CATALOG_MEASURE_NAME"], $min_price_id, ($arParams["SHOW_DISCOUNT_PERCENT_NUMBER"] != "N" ? "N" : "Y"));?>
															<?}?>
														</div>

														<div class="basket_props_block" id="bx_basket_div_<?=$arSKU["ID"];?>" style="display: none;">
															<?if (!empty($arSKU['PRODUCT_PROPERTIES_FILL'])){
																foreach ($arSKU['PRODUCT_PROPERTIES_FILL'] as $propID => $propInfo){?>
																	<input type="hidden" name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]" value="<? echo htmlspecialcharsbx($propInfo['ID']); ?>">
																	<?if (isset($arSKU['PRODUCT_PROPERTIES'][$propID]))
																		unset($arSKU['PRODUCT_PROPERTIES'][$propID]);
																}
															}
															$arSKU["EMPTY_PROPS_JS"]="Y";
															$emptyProductProperties = empty($arSKU['PRODUCT_PROPERTIES']);
															if (!$emptyProductProperties){
																$arSKU["EMPTY_PROPS_JS"]="N";?>
																<div class="wrapper">
																	<table>
																		<?foreach ($arSKU['PRODUCT_PROPERTIES'] as $propID => $propInfo){?>
																			<tr>
																				<td><? echo $arSKU['PROPERTIES'][$propID]['NAME']; ?></td>
																				<td>
																					<?if('L' == $arSKU['PROPERTIES'][$propID]['PROPERTY_TYPE']	&& 'C' == $arSKU['PROPERTIES'][$propID]['LIST_TYPE']){
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
													</div>

													<?//buttons-block?>
													<div class="item-buttons item_<?=$arSKU["ID"]?> buy_block counter_block_wr">
														<div class="counter_wrapp list clearfix n-mb small-block">
															<?if($arskuAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_DETAIL"] && !count($arSKU["OFFERS"]) && $arskuAddToBasketData["ACTION"] == "ADD" && $arSKU["CAN_BUY"]):?>
																<div class="counter_block_inner">
																	<div class="counter_block" data-item="<?=$arSKU["ID"];?>">
																		<span class="minus">-</span>
																		<input type="text" class="text" name="quantity" value="<?=$arskuAddToBasketData["MIN_QUANTITY_BUY"];?>" />
																		<span class="plus">+</span>
																	</div>
																</div>
															<?endif;?>
															<div class="button_block <?=(($arskuAddToBasketData["ACTION"] == "ORDER" /*&& !$arResult["CAN_BUY"]*/) || !$arSKU["CAN_BUY"] || !$arskuAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_DETAIL"] || ($arskuAddToBasketData["ACTION"] == "SUBSCRIBE" && $arSKU["CATALOG_SUBSCRIBE"] == "Y")  ? "wide" : "");?>">
																<?=$arskuAddToBasketData["HTML"]?>
															</div>
														</div>
														<?if($arskuAddToBasketData["ACTION"] !== "NOTHING"):?>
															<?if($arskuAddToBasketData["ACTION"] == "ADD" && $arSKU["CAN_BUY"] && $arParams["SHOW_ONE_CLICK_BUY"]!="N"):?>
																<div class="one_click_buy">
																	<span class="button small transparent one_click" data-item="<?=$arSKU["ID"]?>" data-offers="Y" data-iblockID="<?=$arParams["IBLOCK_ID"]?>" data-quantity="<?=$arskuAddToBasketData["MIN_QUANTITY_BUY"];?>" data-props="<?=$arOfferProps?>" onclick="oneClickBuy('<?=$arSKU["ID"]?>', '<?=$arParams["IBLOCK_ID"]?>', this)">
																		<span><?=GetMessage('ONE_CLICK_BUY')?></span>
																	</span>
																</div>
															<?endif;?>
														<?endif;?>

														<?//delivery calculate?>
														<?if(
															$arskuAddToBasketData["ACTION"] == "ADD" 
															&& $arSKU["CAN_BUY"]
														):?>
															
															<?=\Aspro\Functions\CAsproOptimus::showCalculateDeliveryBlock($arSKU['ID'], $arParams, $arParams['TYPE_SKU'] !== 'TYPE_1');?>
														<?endif;?>

														<?
														if(isset($arSKU['PRICE_MATRIX']) && $arSKU['PRICE_MATRIX']) // USE_PRICE_COUNT
														{?>
															<?if($arSKU['ITEM_PRICE_MODE'] == 'Q' && count($arSKU['PRICE_MATRIX']['ROWS']) > 1):?>
																<?$arOnlyItemJSParams = array(
																	"ITEM_PRICES" => $arSKU["ITEM_PRICES"],
																	"ITEM_PRICE_MODE" => $arSKU["ITEM_PRICE_MODE"],
																	"ITEM_QUANTITY_RANGES" => $arSKU["ITEM_QUANTITY_RANGES"],
																	"MIN_QUANTITY_BUY" => $arskuAddToBasketData["MIN_QUANTITY_BUY"],
																	"SHOW_DISCOUNT_PERCENT_NUMBER" => $arParams["SHOW_DISCOUNT_PERCENT_NUMBER"],
																	"ID" => $this->GetEditAreaId($arSKU["ID"]),
																)?>
																<script type="text/javascript">
																	var ob<? echo $this->GetEditAreaId($arSKU["ID"]); ?>el = new JCCatalogSectionOnlyElement(<? echo CUtil::PhpToJSObject($arOnlyItemJSParams, false, true); ?>);
																</script>
															<?endif;?>
														<?}?>
													</div>
												</div>

												<?//icons-block?>
												<div class="item-icons s_2">
													<div class="like_icons list static icons">
														<?if($arParams["DISPLAY_WISH_BUTTONS"] != "N"):?>
															<?if($arSKU['CAN_BUY'] && $arskuAddToBasketData["ACTION"] == "ADD"):?>
																<div class="wish_item_button o_<?=$arSKU["ID"];?>">
																	<span title="<?=GetMessage('CATALOG_WISH')?>" class="wish_item text to <?=$arParams["TYPE_SKU"];?>" data-item="<?=$arSKU["ID"]?>" data-iblock="<?=$arResult["IBLOCK_ID"]?>" data-offers="Y" data-props="<?=$arOfferProps?>"><i></i></span>
																	<span title="<?=GetMessage('CATALOG_WISH_OUT')?>" class="wish_item text in added <?=$arParams["TYPE_SKU"];?>" style="display: none;" data-item="<?=$arSKU["ID"]?>" data-iblock="<?=$arSKU["IBLOCK_ID"]?>"><i></i></span>
																</div>
															<?endif;?>
														<?endif;?>
														<?if($arParams["DISPLAY_COMPARE"] == "Y"):?>
															<div class="compare_item_button o_<?=$arSKU["ID"];?>">
																<span title="<?=GetMessage('CATALOG_COMPARE')?>" class="compare_item to text <?=$arParams["TYPE_SKU"];?>" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arSKU["ID"]?>" ><i></i></span>
																<span title="<?=GetMessage('CATALOG_COMPARE_OUT')?>" class="compare_item in added text <?=$arParams["TYPE_SKU"];?>" style="display: none;" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arSKU["ID"]?>"><i></i></span>
															</div>
														<?endif;?>
													</div>
												</div>

												<?//stores icon?>
												<?if($useStores):?>
													<div class="opener top">
														<?$collspan++;?>
														<span class="opener_icon"><i></i></span>
													</div>
												<?endif;?>
											</div>
											<div class="offer_stores">
												<?$APPLICATION->IncludeComponent("bitrix:catalog.store.amount", "main", array(
														"PER_PAGE" => "10",
														"USE_STORE_PHONE" => $arParams["USE_STORE_PHONE"],
														"SCHEDULE" => $arParams["SCHEDULE"],
														"USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
														"MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
														"ELEMENT_ID" => $arSKU["ID"],
														"STORE_PATH"  =>  $arParams["STORE_PATH"],
														"MAIN_TITLE"  =>  $arParams["MAIN_TITLE"],
														"MAX_AMOUNT"=>$arParams["MAX_AMOUNT"],
														"SHOW_EMPTY_STORE" => $arParams['SHOW_EMPTY_STORE'],
														"SHOW_GENERAL_STORE_INFORMATION" => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
														"USE_ONLY_MAX_AMOUNT" => $arParams["USE_ONLY_MAX_AMOUNT"],
														"USER_FIELDS" => $arParams['USER_FIELDS'],
														"FIELDS" => $arParams['FIELDS'],
														"STORES" => $arParams['STORES'],
														"CACHE_TYPE" => "A",
														"SET_ITEMS" => $arResult["SET_ITEMS"],
													),
													$component
												);?>
											</div>
										</div>
									<?endforeach;?>
								</div>
							</div>
							</div>
						<?endif;?>
					</div>
				</div>
			</div>
		<?endif;?>
	<?$this->EndViewTarget();?>

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
				)
			),
			$component,
			array('HIDE_ICONS' => 'Y')
		);
	}
	?>

	<?
	$arFiles = array();
	if($arResult["PROPERTIES"][$docs_prop_code]["VALUE"])
		$arFiles = $arResult["PROPERTIES"][$docs_prop_code]["VALUE"];
	else
		$arFiles = $arResult["SECTION_FULL"]["UF_FILES"];

	if(is_array($arFiles))
	{
		foreach($arFiles as $key => $value)
		{
			if(!intval($value))
				unset($arFiles[$key]);
		}
	}

	$templateData["DETAIL_TEXT"] = $arResult["DETAIL_TEXT"];
	$templateData["PROPERTIES"] = $arResult["PROPERTIES"];
	$templateData["DISPLAY_PROPERTIES"] = $arResult["DISPLAY_PROPERTIES"];
	$templateData["SHOW_PROPS"] = $showProps;
	$templateData["SERVICES"] = $arResult["SERVICES"];
	$templateData["FILES"] = $arFiles;
	$templateData["VIDEO"] = $arVideo;
	?>

	<script type="text/javascript">
		BX.message({
			QUANTITY_AVAILIABLE: '<? echo COption::GetOptionString("aspro.optimus", "EXPRESSION_FOR_EXISTS", GetMessage("EXPRESSION_FOR_EXISTS_DEFAULT"), SITE_ID); ?>',
			QUANTITY_NOT_AVAILIABLE: '<? echo COption::GetOptionString("aspro.optimus", "EXPRESSION_FOR_NOTEXISTS", GetMessage("EXPRESSION_FOR_NOTEXISTS"), SITE_ID); ?>',
			ADD_ERROR_BASKET: '<? echo GetMessage("ADD_ERROR_BASKET"); ?>',
			ADD_ERROR_COMPARE: '<? echo GetMessage("ADD_ERROR_COMPARE"); ?>',
			ONE_CLICK_BUY: '<? echo GetMessage("ONE_CLICK_BUY"); ?>',
			SITE_ID: '<? echo SITE_ID; ?>'
		})
	</script>
</div>