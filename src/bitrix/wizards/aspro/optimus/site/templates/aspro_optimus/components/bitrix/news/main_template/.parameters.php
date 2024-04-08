<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */
/** @global CUserTypeManager $USER_FIELD_MANAGER */
global $USER_FIELD_MANAGER;
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
Loader::includeModule('iblock');

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arProperty = array();
$arIblocksFilter  = array();
if((IntVal($arCurrentValues["CATALOG_IBLOCK_ID1"]) > 0)||(IntVal($arCurrentValues["CATALOG_IBLOCK_ID2"]) > 0)||(IntVal($arCurrentValues["CATALOG_IBLOCK_ID3"]) > 0)||(IntVal($arCurrentValues["CATALOG_IBLOCK_ID4"]) > 0))
{
	if (IntVal($arCurrentValues["CATALOG_IBLOCK_ID1"]) > 0) $arIblocksFilter[] = $arCurrentValues["CATALOG_IBLOCK_ID1"];
	if (IntVal($arCurrentValues["CATALOG_IBLOCK_ID2"]) > 0) $arIblocksFilter[] = $arCurrentValues["CATALOG_IBLOCK_ID2"];
	if (IntVal($arCurrentValues["CATALOG_IBLOCK_ID3"]) > 0) $arIblocksFilter[] = $arCurrentValues["CATALOG_IBLOCK_ID3"];
}



//if ($arIblocksFilter)
//{
	$arIBlock = array();
	$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_CATALOG_TYPE"], "ACTIVE"=>"Y"));
	while($arr=$rsIBlock->Fetch())
		$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
	
	foreach($arIblocksFilter as $key=>$value)
	{
		$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"], "ACTIVE"=>"Y"));
		while ($arr=$rsProp->Fetch())
		{
			if($arr["PROPERTY_TYPE"] != "F")
				$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
		}
	}
	$arProperty_LNS = $arProperty;
//}

$arListView = array(
	'slider' => GetMessage("SLIDER_VIEW"),
	'block' => GetMessage("BLOCK_VIEW"),
);	

$arTemplateParameters = array(
	"CATALOG_FILTER_NAME" => Array(
		"NAME" => GetMessage("FILTER_NAME"),
		"TYPE" => "STRING",
		"DEFAULT" => "arrProductsFilter",
	),
	"IS_VERTICAL" => array(
		"NAME" => GetMessage("IS_VERTICAL"),
		"PARENT" => "LIST_SETTINGS",
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	),
	'SHOW_CHILD_SECTIONS' => array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 700,
		'NAME' => GetMessage('SHOW_CHILD_SECTIONS'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	"DISPLAY_DATE" => array(
		"NAME" => GetMessage("DISPLAY_DATE"),
		"PARENT" => "VISUAL",
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	),
	
	"SHOW_FAQ_BLOCK" => array(
		"NAME" => GetMessage("SHOW_FAQ_BLOCK"),
		"PARENT" => "VISUAL",
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		//"REFRESH" => "Y",
	),
	"GET_YEAR" => array(
		"NAME" => GetMessage("GET_YEAR"),
		"PARENT" => "FILTER_SETTINGS",
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		//"REFRESH" => "Y",
	),
	"SHOW_SERVICES_BLOCK" => array(
		"NAME" => GetMessage("SHOW_SERVICES_BLOCK"),
		"PARENT" => "DETAIL_SETTINGS",
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		//"REFRESH" => "Y",
	),
	"SHOW_BACK_LINK" => array(
		"NAME" => GetMessage("SHOW_BACK_LINK"),
		"PARENT" => "DETAIL_SETTINGS",
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	),
	"GALLERY_PROPERTY" => array(
		"NAME" => GetMessage("GALLERY_PROPERTY"),
		"TYPE" => "LIST",
		"PARENT" => "DETAIL_SETTINGS",
		"VALUES" => $arProperty_LNS,
		"ADDITIONAL_VALUES" => "Y",
		"DEFAULT" => "MORE_PHOTO",
	),
	"SHOW_GALLERY" => array(
		"NAME" => GetMessage("SHOW_GALLERY"),
		"PARENT" => "DETAIL_SETTINGS",
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"LINKED_PRODUCTS_PROPERTY" => array(
		"NAME" => GetMessage("LINKED_PRODUCTS_PROPERTY"),
		"TYPE" => "LIST",
		"PARENT" => "DETAIL_SETTINGS",
		"VALUES" => $arProperty_LNS,
		"ADDITIONAL_VALUES" => "Y",
		"DEFAULT" => "LINK"
	),
	"SHOW_LINKED_PRODUCTS" => array(
		"NAME" => GetMessage("SHOW_LINKED_PRODUCTS"),
		"TYPE" => "CHECKBOX",
		"PARENT" => "DETAIL_SETTINGS",
		"DEFAULT" => "N",
	),
	"PRICE_PROPERTY" => array(
		"NAME" => GetMessage("PRICE_PROPERTY"),
		"TYPE" => "LIST",
		"PARENT" => "DETAIL_SETTINGS",
		"VALUES" => $arProperty_LNS,
		"ADDITIONAL_VALUES" => "Y",
		"DEFAULT" => "PRICE"
	),
	'LINKED_ELEMENST_PAGE_COUNT' => array(
		'SORT' => 704,
		'NAME' => GetMessage('LINKED_ELEMENST_PAGE_COUNT'),
		'TYPE' => 'TEXT',
		"PARENT" => "DETAIL_SETTINGS",
		'DEFAULT' => '20',
	),
	"LIST_VIEW" => array(
		"NAME" => GetMessage("LIST_VIEW"),
		"TYPE" => "LIST",
		"PARENT" => "DETAIL_SETTINGS",
		"VALUES" => $arListView,
		"ADDITIONAL_VALUES" => "N",
		"DEFAULT" => "slider"
	),
	"USE_SHARE" => array(
		"NAME" => GetMessage("USE_SHARE_TITLE"),
		"TYPE" => "CHECKBOX",
		"PARENT" => "DETAIL_SETTINGS",
		"DEFAULT" => "Y",
	),
	"SHOW_ITEM_SECTION" => array(
		"NAME" => GetMessage("SHOW_ITEM_SECTION"),
		"TYPE" => "CHECKBOX",
		"PARENT" => "DETAIL_SETTINGS",
		"DEFAULT" => "N",
	),
	"DEPTH_LEVEL_BRAND" => array(
		"NAME" => GetMessage("DEPTH_LEVEL_BRAND"),
		"TYPE" => "STRING",
		"PARENT" => "DETAIL_SETTINGS",
		"DEFAULT" => "3"
	),
	/*"PERIOD_PROPERTY" => array(
		"NAME" => GetMessage("PERIOD_PROPERTY"),
		"TYPE" => "LIST",
		"PARENT" => "DETAIL_SETTINGS",
		"VALUES" => $arProperty_LNS,
		"ADDITIONAL_VALUES" => "Y",
		"DEFAULT" => "PERIOD"
	),
	"SHOW_PERIOD" => array(
		"NAME" => GetMessage("SHOW_PERIOD"),
		"TYPE" => "CHECKBOX",
		"PARENT" => "DETAIL_SETTINGS",
		"DEFAULT" => "N",
	),*/
	
);
/*if($arCurrentValues["SHOW_FAQ_BLOCK"]=="Y"){
	$arTemplateParameters["FAQ_FORM_ID"] = Array(
		"NAME" => GetMessage("FAQ_FORM_ID"),
		"TYPE" => "STRING",
		"DEFAULT" => "1",
		"PARENT" => "VISUAL",
	);
}*/

$arPrice = array();
if (\Bitrix\Main\Loader::includeModule('catalog'))
{
	$arPrice = CCatalogIBlockParameters::getPriceTypesList();
	$arTemplateParameters['PRICE_CODE'] = array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => GetMessage('PRICE_CODE_TITLE'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'Y',
		'VALUES' => $arPrice,
		'ADDITIONAL_VALUES' => 'Y'
	);
	$arTemplateParameters['SHOW_OLD_PRICE'] = array(
		"PARENT" => "DETAIL_SETTINGS",
		'NAME' => GetMessage('CP_BC_TPL_SHOW_OLD_PRICE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);
	$arTemplateParameters['PRICE_VAT_INCLUDE'] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("IBLOCK_PRICE_VAT_INCLUDE"),
		"TYPE" => "CHECKBOX",
		"REFRESH" => "N",
		"DEFAULT" => "Y",
	);

	$arTemplateParameters['USE_PRICE_COUNT'] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("IBLOCK_USE_PRICE_COUNT"),
		"TYPE" => "CHECKBOX",
		"REFRESH" => "N",
		"DEFAULT" => "N",
	);
	$arTemplateParameters['SHOW_MEASURE'] = Array(
		"NAME" => GetMessage("SHOW_MEASURE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"PARENT" => "DETAIL_SETTINGS",
	);
	$arTemplateParameters["SHOW_DISCOUNT_PERCENT"] = array(
		"PARENT" => "DETAIL_SETTINGS",
		'NAME' => GetMessage('CP_BC_TPL_SHOW_DISCOUNT_PERCENT'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);
	$arTemplateParameters["SHOW_DISCOUNT_PERCENT_NUMBER"] = array(
		"PARENT" => "DETAIL_SETTINGS",
		'NAME' => GetMessage('CP_BC_TPL_SHOW_DISCOUNT_PERCENT_NUMBER'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);

	$arTemplateParameters['CONVERT_CURRENCY'] = array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => GetMessage('CP_BC_CONVERT_CURRENCY'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y',
	);

	if (isset($arCurrentValues['CONVERT_CURRENCY']) && $arCurrentValues['CONVERT_CURRENCY'] == 'Y')
	{
		$arTemplateParameters['CURRENCY_ID'] = array(
			'PARENT' => 'DETAIL_SETTINGS',
			'NAME' => GetMessage('CP_BC_CURRENCY_ID'),
			'TYPE' => 'LIST',
			'VALUES' => Bitrix\Currency\CurrencyManager::getCurrencyList(),
			'DEFAULT' => Bitrix\Currency\CurrencyManager::getBaseCurrency(),
			"ADDITIONAL_VALUES" => "Y",
		);
	}

	$arStore = array();
	global $USER_FIELD_MANAGER;
	$storeIterator = CCatalogStore::GetList(
		array(),
		array('ISSUING_CENTER' => 'Y'),
		false,
		false,
		array('ID', 'TITLE')
	);
	while ($store = $storeIterator->GetNext())
		$arStore[$store['ID']] = "[".$store['ID']."] ".$store['TITLE'];

	$userFields = $USER_FIELD_MANAGER->GetUserFields("CAT_STORE", 0, LANGUAGE_ID);
	$propertyUF = array();

	foreach($userFields as $fieldName => $userField)
		$propertyUF[$fieldName] = $userField["LIST_COLUMN_LABEL"] ? $userField["LIST_COLUMN_LABEL"] : $fieldName;

	$arTemplateParameters['STORES'] = array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => GetMessage('STORES'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'Y',
		'VALUES' => $arStore,
		'ADDITIONAL_VALUES' => 'Y'
	);
}
?>