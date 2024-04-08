<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */
/** @global CUserTypeManager $USER_FIELD_MANAGER */
global $USER_FIELD_MANAGER;
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
Loader::includeModule('iblock');

$arTemplateParameters = array(
	"GOOGLE_API_KEY" => Array(
		"NAME" => GetMessage("GOOGLE_API_KEY"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	),
	"MAP_TYPE_FROM_MODULE" => array(
		'NAME' => GetMessage("MAP_TYPE_FROM_MODULE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
);

?>