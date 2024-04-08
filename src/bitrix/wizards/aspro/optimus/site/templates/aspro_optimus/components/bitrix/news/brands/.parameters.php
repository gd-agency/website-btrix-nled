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

$arPrice = array();
$arSort = CIBlockParameters::GetElementSortFields(
	array('SHOWS', 'SORT', 'TIMESTAMP_X', 'NAME', 'ID', 'ACTIVE_FROM', 'ACTIVE_TO'),
	array('KEY_LOWERCASE' => 'Y')
);
if (\Bitrix\Main\Loader::includeModule("catalog"))
{
	$arSort = array_merge($arSort, CCatalogIBlockParameters::GetCatalogSortFields(), array("PROPERTY_MINIMUM_PRICE"=>GetMessage("SORT_PRICES_MINIMUM_PRICE"), "PROPERTY_MAXIMUM_PRICE"=>GetMessage("SORT_PRICES_MAXIMUM_PRICE"), "REGION_PRICE"=>GetMessage("SORT_PRICES_REGION_PRICE")));
		if (isset($arSort['CATALOG_AVAILABLE'])) {
			unset($arSort['CATALOG_AVAILABLE']);
		}

	$rsPrice=CCatalogGroup::GetList($v1="sort", $v2="asc");
	while($arr=$rsPrice->Fetch())
	{
		$arPrice[$arr["NAME"]] = "[".$arr["NAME"]."] ".$arr["NAME_LANG"];
	}
	if ((isset($arCurrentValues['CATALOG_IBLOCK_ID1']) && (int)$arCurrentValues['CATALOG_IBLOCK_ID1']) > 0)
	{
		$arSKU = CCatalogSKU::GetInfoByProductIBlock($arCurrentValues['CATALOG_IBLOCK_ID1']);
		$boolSKU = !empty($arSKU) && is_array($arSKU);
	}
}
else
{
	$arPrice = $arProperty_N;
}
$arAscDesc = array(
	"asc" => GetMessage("IBLOCK_SORT_ASC"),
	"desc" => GetMessage("IBLOCK_SORT_DESC"),
);

// get offers iblock properties and group by types
if ($boolSKU)
{
	$arAllOfferPropList = array();
	$arFileOfferPropList = array(
		'-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
	);
	$arTreeOfferPropList = $arShowPreviewPictuteTreeOfferPropList = array(
		'-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
	);
	$rsProps = CIBlockProperty::GetList(
		array('SORT' => 'ASC', 'ID' => 'ASC'),
		array('IBLOCK_ID' => $arSKU['IBLOCK_ID'], 'ACTIVE' => 'Y')
	);
	while ($arProp = $rsProps->Fetch())
	{
		if ($arProp['ID'] == $arSKU['SKU_PROPERTY_ID'])
			continue;
		$arProp['USER_TYPE'] = (string)$arProp['USER_TYPE'];
		$strPropName = '['.$arProp['ID'].']'.('' != $arProp['CODE'] ? '['.$arProp['CODE'].']' : '').' '.$arProp['NAME'];
		if ('' == $arProp['CODE'])
			$arProp['CODE'] = $arProp['ID'];

		$arProperty_Offers[$arProp['CODE']] = $strPropName;

		if ('F' == $arProp['PROPERTY_TYPE'])
			$arFileOfferPropList[$arProp['CODE']] = $strPropName;
		if ('N' != $arProp['MULTIPLE'])
			continue;
		if (
			'L' == $arProp['PROPERTY_TYPE']
			|| 'E' == $arProp['PROPERTY_TYPE']
			|| ('S' == $arProp['PROPERTY_TYPE'] && 'directory' == $arProp['USER_TYPE'] && CIBlockPriceTools::checkPropDirectory($arProp))
		)
			$arTreeOfferPropList[$arProp['CODE']] = $strPropName;

		if ('S' == $arProp['PROPERTY_TYPE'] && 'directory' == $arProp['USER_TYPE'] && CIBlockPriceTools::checkPropDirectory($arProp) && strlen($arProp['USER_TYPE_SETTINGS']['TABLE_NAME'])){
			$arShowPreviewPictuteTreeOfferPropList[$arProp['CODE']] = $strPropName;
		}
	}
}

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
	"IBLOCK_CATALOG_TYPE" => array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("IBLOCK_CATALOG_TYPE"),
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "Y",
		"VALUES" => $arIBlockType,
		"REFRESH" => "Y",
	),
	"CATALOG_IBLOCK_ID1" => array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("IBLOCK_IBLOCK1"),
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "Y",
		"VALUES" => $arIBlock,
		"REFRESH" => "Y",
	),
	"CATALOG_IBLOCK_ID2" => array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("IBLOCK_IBLOCK2"),
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "Y",
		"VALUES" => $arIBlock,
		"REFRESH" => "Y",
	),
	"CATALOG_IBLOCK_ID3" => array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("IBLOCK_IBLOCK3"),
		"TYPE" => "LIST",
		"VALUES" => $arIBlock,
		"ADDITIONAL_VALUES" => "Y",
		"REFRESH" => "Y",
	),
	"SHOW_BACK_LINK" => array(
		"NAME" => GetMessage("SHOW_BACK_LINK"),
		"PARENT" => "DETAIL_SETTINGS",
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	),
	"SHOW_GALLERY" => array(
		"NAME" => GetMessage("SHOW_GALLERY"),
		"PARENT" => "DETAIL_SETTINGS",
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"REFRESH" => "Y",
	),
);

if($arCurrentValues["SHOW_GALLERY"] !== 'N'){
	$arTemplateParameters = array_merge($arTemplateParameters, array(
		"GALLERY_PROPERTY" => array(
			"NAME" => GetMessage("GALLERY_PROPERTY"),
			"TYPE" => "LIST",
			"PARENT" => "DETAIL_SETTINGS",
			"VALUES" => $arProperty_LNS,
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "MORE_PHOTO",
		),
	));
}

$arTemplateParameters = array_merge($arTemplateParameters, array(
	"SHOW_LINKED_PRODUCTS" => array(
		"NAME" => GetMessage("SHOW_LINKED_PRODUCTS"),
		"TYPE" => "CHECKBOX",
		"PARENT" => "DETAIL_SETTINGS",
		"DEFAULT" => "N",
		"REFRESH" => "Y",
	),
));

if($arCurrentValues["SHOW_LINKED_PRODUCTS"] === 'Y'){
	$arTemplateParameters = array_merge($arTemplateParameters, array(
		"LIST_VIEW" => array(
			"NAME" => GetMessage("LIST_VIEW"),
			"TYPE" => "LIST",
			"PARENT" => "DETAIL_SETTINGS",
			"VALUES" => $arListView,
			"ADDITIONAL_VALUES" => "N",
			"DEFAULT" => "slider"
		),
		"LINKED_PRODUCTS_PROPERTY" => array(
			"NAME" => GetMessage("LINKED_PRODUCTS_PROPERTY"),
			"TYPE" => "LIST",
			"PARENT" => "DETAIL_SETTINGS",
			"VALUES" => $arProperty_LNS,
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "BRAND"
		),
		'LINKED_ELEMENST_PAGE_COUNT' => array(
			'SORT' => 704,
			'NAME' => GetMessage('LINKED_ELEMENST_PAGE_COUNT'),
			'TYPE' => 'TEXT',
			"PARENT" => "DETAIL_SETTINGS",
			'DEFAULT' => '20',
		),
		'SHOW_FILTER_LEFT' => array(
			"NAME" => GetMessage("SHOW_FILTER_LEFT"),
			"TYPE" => "CHECKBOX",
			"PARENT" => "DETAIL_SETTINGS",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		),
	));

	if ($boolSKU)
	{
		$arTemplateParameters["OFFER_TREE_PROPS"] = Array(
			'PARENT' => 'DETAIL_SETTINGS',
			'NAME' => GetMessage('OFFERS_SETTINGS'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'Y',
			'ADDITIONAL_VALUES' => 'N',
			'REFRESH' => 'N',
			'DEFAULT' => '-',
			'VALUES' => $arTreeOfferPropList
		);
		$arTemplateParameters['OFFER_SHOW_PREVIEW_PICTURE_PROPS']=array(
			'PARENT' => 'DETAIL_SETTINGS',
			'NAME' => GetMessage('OFFER_SHOW_PREVIEW_PICTURE_PROPS_TITLE'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'Y',
			'ADDITIONAL_VALUES' => 'N',
			'REFRESH' => 'N',
			'DEFAULT' => '-',
			'VALUES' => $arShowPreviewPictuteTreeOfferPropList
		);
		$arTemplateParameters["LIST_OFFERS_FIELD_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("CP_BC_LIST_OFFERS_FIELD_CODE"), "DETAIL_SETTINGS");
		$arTemplateParameters["LIST_OFFERS_PROPERTY_CODE"] = array(
			"PARENT" => "DETAIL_SETTINGS",
			"NAME" => GetMessage("CP_BC_LIST_OFFERS_PROPERTY_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty_Offers,
			"ADDITIONAL_VALUES" => "Y",
		);
		$arTemplateParameters["OFFERS_CART_PROPERTIES"] = array(
			"PARENT" => "DETAIL_SETTINGS",
			"NAME" => GetMessage("CP_BC_OFFERS_PROPERTIES"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty_Offers,
			"HIDDEN" => (isset($arCurrentValues['ADD_PROPERTIES_TO_BASKET']) && $arCurrentValues['ADD_PROPERTIES_TO_BASKET'] == 'N' ? 'Y' : 'N')
		);
		$arTemplateParameters["OFFERS_SORT_FIELD"] = array(
			"PARENT" => "DETAIL_SETTINGS",
			"NAME" => GetMessage("CP_BC_OFFERS_SORT_FIELD"),
			"TYPE" => "LIST",
			"VALUES" => $arSort,
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "sort",
		);
		$arTemplateParameters["OFFERS_SORT_ORDER"] = array(
			"PARENT" => "DETAIL_SETTINGS",
			"NAME" => GetMessage("CP_BC_OFFERS_SORT_ORDER"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "asc",
			"ADDITIONAL_VALUES" => "Y",
		);
		$arTemplateParameters["OFFERS_SORT_FIELD2"] = array(
			"PARENT" => "DETAIL_SETTINGS",
			"NAME" => GetMessage("CP_BC_OFFERS_SORT_FIELD2"),
			"TYPE" => "LIST",
			"VALUES" => $arSort,
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "id",
		);
		$arTemplateParameters["OFFERS_SORT_ORDER2"] = array(
			"PARENT" => "DETAIL_SETTINGS",
			"NAME" => GetMessage("CP_BC_OFFERS_SORT_ORDER2"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "desc",
			"ADDITIONAL_VALUES" => "Y",
		);
	}

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
}

if($arCurrentValues["SHOW_FILTER_LEFT"] === 'Y'){
	$arTemplateParameters['AJAX_FILTER_CATALOG'] = array(
		"NAME" => GetMessage("AJAX_FILTER_CATALOG"),
		"TYPE" => "CHECKBOX",
		"PARENT" => "DETAIL_SETTINGS",
		"DEFAULT" => "N",
	);
}

$arTemplateParameters = array_merge($arTemplateParameters, array(
	"SHOW_ITEM_SECTION" => array(
		"NAME" => GetMessage("SHOW_ITEM_SECTION"),
		"TYPE" => "CHECKBOX",
		"PARENT" => "DETAIL_SETTINGS",
		"DEFAULT" => "N",
		"REFRESH" => "Y",
	),
	"SHOW_ITEM_SECTION_LEFT" => array(
		"NAME" => GetMessage("SHOW_ITEM_SECTION_LEFT"),
		"TYPE" => "CHECKBOX",
		"PARENT" => "DETAIL_SETTINGS",
		"DEFAULT" => "N",
		"REFRESH" => "Y",
	),
));

if($arCurrentValues["SHOW_ITEM_SECTION"] === 'Y' || $arCurrentValues["SHOW_ITEM_SECTION_LEFT"] === 'Y'){
	$arTemplateParameters['DEPTH_LEVEL_BRAND'] = array(
		"NAME" => GetMessage("DEPTH_LEVEL_BRAND"),
		"TYPE" => "STRING",
		"PARENT" => "DETAIL_SETTINGS",
		"DEFAULT" => "3"
	);
}
?>