<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;
if(!isset($arParams["ORDER_SOC"]) || !$arParams["ORDER_SOC"])
	$arParams["ORDER_SOC"] = 'vk,odn,fb,tw,inst,mail,youtube,google_plus,telegram,viber,whatsapp,skype';
$arResult['ORDER_SOC'] = explode(',', $arParams["ORDER_SOC"]);
if( $this->StartResultCache(false, array(($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()),$bUSER_HAVE_ACCESS, $arNavigation)))
{
	$this->SetResultCacheKeys(array(
		"ID",
		"IBLOCK_ID",
		"NAV_CACHED_DATA",
		"NAME",
		"IBLOCK_SECTION_ID",
		"IBLOCK",
		"LIST_PAGE_URL", "~LIST_PAGE_URL",
		"SECTION_URL",
		"SECTION",
		"ORDER_SOC",
		"PROPERTIES",
	));
	$this->IncludeComponentTemplate();
}
?>
