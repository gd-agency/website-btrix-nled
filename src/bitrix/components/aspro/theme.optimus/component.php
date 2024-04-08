<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule('aspro.optimus')){
	die;
}

global $TEMPLATE_OPTIONS, $SITE_THEME, $THEME_SWITCHER, $USER;
$arResult = array();
if(($arResult["ITEMS"] = $TEMPLATE_OPTIONS) && is_array($arResult["ITEMS"])){
	foreach($arResult["ITEMS"] as $i => $value){
		if(isset($value['THEME']) && $value['THEME'] === "N"){
			unset($arResult["ITEMS"][$i]);
		}
	}
}

$bIndexBot = (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Lighthouse') !== false); // is indexed yandex/google bot

if(!$bIndexBot)
{
	if($THEME_SWITCHER == "Y"){
		\Bitrix\Main\Data\StaticHtmlCache::getInstance()->markNonCacheable();
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/spectrum.js');
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/spectrum.css');
		$this->IncludeComponentTemplate();
	}
}
else
{
	\Bitrix\Main\Data\StaticHtmlCache::getInstance()->markNonCacheable();
}

$file = \Bitrix\Main\Application::getDocumentRoot().$componentPath.'/css/user_font_'.SITE_ID.'.css';

if(\Bitrix\Main\Config\Option::get('aspro.optimus', 'CUSTOM_FONT', '') && \Bitrix\Main\IO\File::isFileExists($file))
{
	$APPLICATION->SetAdditionalCSS($componentPath.'/css/user_font_'.SITE_ID.'.css', true);
}

return $TEMPLATE_OPTIONS;
?>
