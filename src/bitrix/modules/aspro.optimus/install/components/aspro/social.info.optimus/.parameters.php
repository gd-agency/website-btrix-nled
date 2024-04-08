<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Web\Json;

if (!Loader::includeModule('iblock'))
	return;

CBitrixComponent::includeComponentClass('bitrix:catalog.section');

$arComponentParameters = array(
	"PARAMETERS" => array(
		"CACHE_TIME"  =>  Array("DEFAULT"=>36000000),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_BND_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"TITLE_BLOCK" => array(
			"NAME" => GetMessage("TITLE_BLOCK"),
			"TYPE" => "STRING",
			"DEFAULT" => ""
		),
		"VK" => array(
			"NAME" => GetMessage("VKONTAKTE"),
			"TYPE" => "STRING",
			"DEFAULT" => "https://vk.com/aspro74"
		),
		"ODN" => array(
			"NAME" => GetMessage("ODN"),
			"TYPE" => "STRING",
			"DEFAULT" => "#"
		),
		"FACE" => array(
			"NAME" => "Facebook",
			"TYPE" => "STRING",
			"DEFAULT" => "http://www.facebook.com/aspro74"
		),
		"TWIT" => array(
			"NAME" => "Twitter",
			"TYPE" => "STRING",
			"DEFAULT" => "http://twitter.com/aspro_ru"
		),
		"INST" => array(
			"NAME" => GetMessage("INST"),
			"TYPE" => "STRING",
			"DEFAULT" => "#"
		),
		"TELEGRAM" => array(
			"NAME" => GetMessage("TELEGRAM"),
			"TYPE" => "STRING",
			"DEFAULT" => "#"
		),
		"MAIL" => array(
			"NAME" => GetMessage("MAIL"),
			"TYPE" => "STRING",
			"DEFAULT" => "#"
		),
		"YOUTUBE" => array(
			"NAME" => GetMessage("YOUTUBE"),
			"TYPE" => "STRING",
			"DEFAULT" => "#"
		),
		"VIBER" => array(
			"NAME" => GetMessage("VIBER"),
			"TYPE" => "STRING",
			"DEFAULT" => "#"
		),
		"WHATSAPP" => array(
			"NAME" => GetMessage("WHATSAPP"),
			"TYPE" => "STRING",
			"DEFAULT" => "#"
		),
		"SKYPE" => array(
			"NAME" => GetMessage("SKYPE"),
			"TYPE" => "STRING",
			"DEFAULT" => "#"
		),
		"PINTEREST" => array(
			"NAME" => GetMessage("PINTEREST"),
			"TYPE" => "STRING",
			"DEFAULT" => "#"
		),
	),
);
if(class_exists('CatalogSectionComponent'))
{
	$arComponentParameters['PARAMETERS']['ORDER_SOC'] = array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => GetMessage('CP_BC_TPL_PRODUCT_BLOCKS_ORDER'),
		'TYPE' => 'CUSTOM',
		'JS_FILE' => CatalogSectionComponent::getSettingsScript('/bitrix/components/bitrix/catalog.section', 'dragdrop_order'),
		'JS_EVENT' => 'initDraggableOrderControl',
		'JS_DATA' => Json::encode(array(
			'vk' => GetMessage('VKONTAKTE'),
			'odn' => GetMessage('ODN'),
			'fb' => 'Facebook',
			'tw' => 'Twitter',
			'inst' => GetMessage('INST'),
			'telegram' => GetMessage('TELEGRAM'),
			'viber' => GetMessage('VIBER'),
			'whatsapp' => GetMessage('WHATSAPP'),
			'skype' => GetMessage('SKYPE'),
			'mail' => GetMessage('MAIL'),
			'youtube' => GetMessage('YOUTUBE'),
			'pinterest' => GetMessage('PINTEREST'),
		)),
		'DEFAULT' => 'vk,odn,fb,tw,inst,mail,youtube,telegram,viber,whatsapp,skype,pinterest'
	);
}
?>
