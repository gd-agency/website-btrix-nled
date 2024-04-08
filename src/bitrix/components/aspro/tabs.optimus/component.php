<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?\Bitrix\Main\Loader::includeModule('iblock');
$arTabs=$arFilterProp=$arShowProp=array();

$arResult["SHOW_SLIDER_PROP"] = false;
if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"])){
	$arrFilter = array();
}
else{
	$arrFilter = $GLOBALS[$arParams["FILTER_NAME"]];
	if(!is_array($arrFilter))
		$arrFilter = array();
}

$arFilter = array( "ACTIVE" => "Y", "IBLOCK_ID" => $arParams["IBLOCK_ID"]);
if($arParams["SECTION_ID"]){
	$arFilter[]=array("SECTION_ID"=>$arParams["SECTION_ID"],"INCLUDE_SUBSECTIONS"=>"Y" );
}elseif($arParams["SECTION_CODE"]){
	$arFilter[]=array("SECTION_CODE"=>$arParams["SECTION_CODE"],"INCLUDE_SUBSECTIONS"=>"Y" );
}
$rsProp = CIBlockPropertyEnum::GetList(Array("sort"=>"asc", "id"=>"desc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arParams["IBLOCK_ID"], "CODE"=>$arParams["TABS_CODE"]));
while($arProp=$rsProp->Fetch()){
	$arShowProp[$arProp["EXTERNAL_ID"]]=$arProp["VALUE"];
}

if($arShowProp){
	foreach($arShowProp as $key=>$prop){
		$arItems=array();
		$arItems=COptimusCache::CIBLockElement_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => COptimusCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array_merge( $arFilter, $arrFilter, array( "PROPERTY_".$arParams["TABS_CODE"]."_VALUE" => array($prop) ) ), false, array("nTopCount"=>1), array("ID"));
		if( $arItems ){
			$arTabs[$key]=$prop;
			$arResult["SHOW_SLIDER_PROP"] = true;
		}
	}
}else{
	return;
}
$arParams["PROP_CODE"] = $arParams["TABS_CODE"];
$arResult["TABS"] = $arTabs;

$arParams['PAGER_TEMPLATE'] = 'main';

$arParams["DISPLAY_WISH_BUTTONS"] = \Bitrix\Main\Config\Option::get('aspro.optimus', 'SHOW_DELAY', 'Y');
$arParams["DISPLAY_COMPARE"] = \Bitrix\Main\Config\Option::get('aspro.optimus', 'SHOW_COMPARE', 'Y');
?>
<?$arTransferParams = array(
	"SHOW_ABSENT" => $arParams["SHOW_ABSENT"],
	"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
	"PRICE_CODE" => $arParams["PRICE_CODE"],
	"OFFER_TREE_PROPS" => $arParams["OFFER_TREE_PROPS"],
	"OFFER_SHOW_PREVIEW_PICTURE_PROPS" => $arParams["OFFER_SHOW_PREVIEW_PICTURE_PROPS"],
	"CACHE_TIME" => $arParams["CACHE_TIME"],
	"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
	"CURRENCY_ID" => $arParams["CURRENCY_ID"],
	"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
	"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
	"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
	"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
	"LIST_OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
	"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
	"LIST_OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
	"SHOW_DISCOUNT_TIME" => $arParams["SHOW_DISCOUNT_TIME"],
	"SHOW_COUNTER_LIST" => $arParams["SHOW_COUNTER_LIST"],
	"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
	"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
	"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
	"SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"],
	"SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"],
	"SHOW_DISCOUNT_PERCENT_NUMBER" => $arParams["SHOW_DISCOUNT_PERCENT_NUMBER"],
	"USE_REGION" => $arParams["USE_REGION"],
	"STORES" => $arParams["STORES"],
	"DEFAULT_COUNT" => $arParams["DEFAULT_COUNT"],
	"BASKET_URL" => $arParams["BASKET_URL"],
	"SHOW_GALLERY" => $arParams["SHOW_GALLERY"],
	"MAX_GALLERY_ITEMS" => $arParams["MAX_GALLERY_ITEMS"],
	"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
	"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
	"PARTIAL_PRODUCT_PROPERTIES" => $arParams["PARTIAL_PRODUCT_PROPERTIES"],
	"ADD_PROPERTIES_TO_BASKET" => $arParams["ADD_PROPERTIES_TO_BASKET"],
	"SHOW_ONE_CLICK_BUY" => $arParams["SHOW_ONE_CLICK_BUY"],
	"SHOW_DISCOUNT_TIME_EACH_SKU" => $arParams["SHOW_DISCOUNT_TIME_EACH_SKU"],
	"SHOW_ARTICLE_SKU" => $arParams["SHOW_ARTICLE_SKU"],
	"ADD_PICT_PROP" => $arParams["ADD_PICT_PROP"],
	"ADD_DETAIL_TO_SLIDER" => $arParams["DETAIL_ADD_DETAIL_TO_SLIDER"],
	"OFFER_ADD_PICT_PROP" => $arParams["OFFER_ADD_PICT_PROP"],
	"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
	"IBINHERIT_TEMPLATES" => $arSeoItem ? $arIBInheritTemplates : array(),
	"DISPLAY_WISH_BUTTONS" => $arParams["DISPLAY_WISH_BUTTONS"],
	"DISPLAY_COMPARE" => $arParams["DISPLAY_WISH_BUTTONS"],
);?>
<div class="js-wrapper-block" data-params='<?=str_replace('\'', '"', CUtil::PhpToJSObject($arTransferParams, false))?>'>
	<?$this->IncludeComponentTemplate();?>
</div>