<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<?

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

Loader::includeModule("iblock");
Loader::includeModule("highloadblock");

// get current section & element
global $OptimusSectionID;
$arSection = $arElement = array();

$bFastViewMode = (isset($_REQUEST['FAST_VIEW']) && $_REQUEST['FAST_VIEW'] == 'Y');

$arSections = [];
if($arResult["VARIABLES"]["SECTION_ID"] > 0){
	$arSections = COptimusCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"Y", "TAG" => COptimusCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "ID" => $arResult["VARIABLES"]["SECTION_ID"], "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "UF_TIZERS", "SECTION_PAGE_URL", "NAME", "IBLOCK_SECTION_ID", "DEPTH_LEVEL", "LEFT_MARGIN", "RIGHT_MARGIN", "UF_OFFERS_TYPE", "UF_ELEMENT_DETAIL"));
}
elseif(strlen(trim($arResult["VARIABLES"]["SECTION_CODE"])) > 0){
	$arSections = COptimusCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"Y", "TAG" => COptimusCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "=CODE" => $arResult["VARIABLES"]["SECTION_CODE"], "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "UF_TIZERS", "SECTION_PAGE_URL", "NAME", "IBLOCK_SECTION_ID", "DEPTH_LEVEL", "LEFT_MARGIN", "RIGHT_MARGIN", "UF_OFFERS_TYPE", "UF_ELEMENT_DETAIL"));
}

if(count($arSections) > 1)
{
	foreach($arSections as $key => $arTmpSection)
	{
		if(str_replace($arParams['SEF_FOLDER'], '', $arTmpSection['SECTION_PAGE_URL']) == $arResult['VARIABLES']['SECTION_CODE_PATH'].'/')
		{
			$arSection = $arTmpSection;
		}
		
	}
}
else
{
	$arSection = current($arSections);
}

if($arResult["VARIABLES"]["ELEMENT_ID"] > 0){
	$arElement = COptimusCache::CIBLockElement_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => COptimusCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE"=>"Y", "ID" => $arResult["VARIABLES"]["ELEMENT_ID"]), false, false, array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME", "PREVIEW_TEXT", "PREVIEW_PICTURE", "DETAIL_PICTURE"));
}
elseif(strlen(trim($arResult["VARIABLES"]["ELEMENT_CODE"])) > 0){
	$arElement = COptimusCache::CIBLockElement_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => COptimusCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE"=>"Y", "=CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"]), false, false, array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME", "PREVIEW_TEXT", "PREVIEW_PICTURE", "DETAIL_PICTURE"));

}

if(!$arSection){
	if($arElement["IBLOCK_SECTION_ID"]){
		$sid = ((isset($arElement["IBLOCK_SECTION_ID_SELECTED"]) && $arElement["IBLOCK_SECTION_ID_SELECTED"]) ? $arElement["IBLOCK_SECTION_ID_SELECTED"] : $arElement["IBLOCK_SECTION_ID"]);
		$arSection = COptimusCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => COptimusCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "ID" => $sid, "IBLOCK_ID" => $arElement["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "UF_TIZERS", "NAME"));
	}
}

$OptimusSectionID = $arSection["ID"];

$arParams["DISPLAY_WISH_BUTTONS"] = \Bitrix\Main\Config\Option::get('aspro.optimus', 'SHOW_DELAY', 'Y');

if(\Bitrix\Main\Config\Option::get('aspro.optimus', 'SHOW_COMPARE', 'Y') == 'N')
	$arParams["USE_COMPARE"] = 'N';

global $TEMPLATE_OPTIONS, $noAddElementToChain;
$noAddElementToChain = $arParams['ADD_ELEMENT_CHAIN'] !== 'Y';

$typeSKU = '';
//set offer view type
$typeTmpSKU = 0;
if($arSection['UF_OFFERS_TYPE'])
	$typeTmpSKU = $arSection['UF_OFFERS_TYPE'];
else
{
	if($arSection["DEPTH_LEVEL"] > 2)
	{
		$arSectionParent = COptimusCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => COptimusCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "ID" => $arSection["IBLOCK_SECTION_ID"], "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "NAME", "UF_OFFERS_TYPE"));
		if($arSectionParent['UF_OFFERS_TYPE'] && !$typeTmpSKU)
			$typeTmpSKU = $arSectionParent['UF_OFFERS_TYPE'];

		if(!$typeTmpSKU)
		{
			$arSectionRoot = COptimusCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => COptimusCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "<=LEFT_BORDER" => $arSection["LEFT_MARGIN"], ">=RIGHT_BORDER" => $arSection["RIGHT_MARGIN"], "DEPTH_LEVEL" => 1, "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "NAME", "UF_OFFERS_TYPE"));
			if($arSectionRoot['UF_OFFERS_TYPE'] && !$typeTmpSKU)
				$typeTmpSKU = $arSectionRoot['UF_OFFERS_TYPE'];
		}
	}
	else
	{
		$arSectionRoot = COptimusCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => COptimusCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "<=LEFT_BORDER" => $arSection["LEFT_MARGIN"], ">=RIGHT_BORDER" => $arSection["RIGHT_MARGIN"], "DEPTH_LEVEL" => 1, "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "NAME", "UF_OFFERS_TYPE"));
		if($arSectionRoot['UF_OFFERS_TYPE'] && !$typeTmpSKU)
			$typeTmpSKU = $arSectionRoot['UF_OFFERS_TYPE'];
	}
}
if($typeTmpSKU)
{
	$rsTypes = CUserFieldEnum::GetList(array(), array("ID" => $typeTmpSKU));
	if($arType = $rsTypes->GetNext())
		$typeSKU = $arType['XML_ID'];
}
?>

<?COptimus::AddMeta(
	array(
		'og:description' => $arElement['PREVIEW_TEXT'],
		'og:image' => (($arElement['PREVIEW_PICTURE'] || $arElement['DETAIL_PICTURE']) ? CFile::GetPath(($arElement['PREVIEW_PICTURE'] ? $arElement['PREVIEW_PICTURE'] : $arElement['DETAIL_PICTURE'])) : false),
	)
);?>

<?if($bFastViewMode):?>
	<?include_once('element_fast_view.php');?>
<?else:?>
	<?include_once('element_normal.php');?>
<?endif;?>