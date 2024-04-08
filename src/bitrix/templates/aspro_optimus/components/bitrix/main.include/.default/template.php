<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
$text='';
$bIndexBot = (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Lighthouse') !== false);

if((strpos($arResult["FILE"], "comp_catalog_hit") !== false ||
	strpos($arResult["FILE"], "comp_banners_float") !== false ||
	strpos($arResult["FILE"], "comp_brands") !== false) && $bIndexBot)
	return;
if($arResult["FILE"] <> '')
{
	include($arResult["FILE"]);
}
