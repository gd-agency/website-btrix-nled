<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$boolCatalog = CModule::IncludeModule("catalog");

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arProperty_LNS = array();
$arProperty_N = array();
$arProperty_X = array();
$arProperty_S = array();
if (0 < intval($arCurrentValues['IBLOCK_ID']))
{
	$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"], "ACTIVE"=>"Y"));
	while ($arr=$rsProp->Fetch())
	{
		if($arr["PROPERTY_TYPE"] != "F")
			$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];

		if($arr["PROPERTY_TYPE"]=="N")
			$arProperty_N[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
		if($arr["PROPERTY_TYPE"]=="S")
			$arProperty_S[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];

		if($arr["PROPERTY_TYPE"]!="F")
		{
			if($arr["MULTIPLE"] == "Y" && $arr["PROPERTY_TYPE"] == "L")
				$arProperty_XL[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
			elseif($arr["PROPERTY_TYPE"] == "L")
				$arProperty_X[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
			elseif($arr["PROPERTY_TYPE"] == "E" && $arr["LINK_IBLOCK_ID"] > 0)
				$arProperty_X[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
		}
	}
}

$arProperty_UF = array();
$arSProperty_LNS = array();
$arUserFields = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("IBLOCK_".$arCurrentValues["IBLOCK_ID"]."_SECTION");
foreach($arUserFields as $FIELD_NAME=>$arUserField)
{
	$arProperty_UF[$FIELD_NAME] = $arUserField["LIST_COLUMN_LABEL"]? $arUserField["LIST_COLUMN_LABEL"]: $FIELD_NAME;
	if($arUserField["USER_TYPE"]["BASE_TYPE"]=="string")
		$arSProperty_LNS[$FIELD_NAME] = $arProperty_UF[$FIELD_NAME];
}

$arOffers = CIBlockPriceTools::GetOffersIBlock($arCurrentValues["IBLOCK_ID"]);
$OFFERS_IBLOCK_ID = is_array($arOffers)? $arOffers["OFFERS_IBLOCK_ID"]: 0;
$arProperty_Offers = array();
$arShowPreviewPictuteTreeOfferPropList = array(
    '-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
);
if($OFFERS_IBLOCK_ID)
{
	$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>$OFFERS_IBLOCK_ID, "ACTIVE"=>"Y"));
	while($arr=$rsProp->Fetch())
	{
		$arr['USER_TYPE'] = (string)$arr['USER_TYPE'];
        $strPropName = '['.$arr['ID'].']'.('' != $arr['CODE'] ? '['.$arr['CODE'].']' : '').' '.$arr['NAME'];

		if($arr["PROPERTY_TYPE"] != "F")
			$arProperty_Offers[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];

		if ('S' == $arr['PROPERTY_TYPE'] && 'directory' == $arr['USER_TYPE'] && CIBlockPriceTools::checkPropDirectory($arr) && strlen($arr['USER_TYPE_SETTINGS']['TABLE_NAME'])){
			$arShowPreviewPictuteTreeOfferPropList[$arr['CODE']] = $strPropName;
		}
	}
	
}

$arSort = CIBlockParameters::GetElementSortFields(
	array('SHOWS', 'SORT', 'TIMESTAMP_X', 'NAME', 'ID', 'ACTIVE_FROM', 'ACTIVE_TO'),
	array('KEY_LOWERCASE' => 'Y')
);

$arPrice = array();
if ($boolCatalog)
{
	$arSort = array_merge($arSort, CCatalogIBlockParameters::GetCatalogSortFields());
	$rsPrice=CCatalogGroup::GetList($v1="sort", $v2="asc");
	while($arr=$rsPrice->Fetch()) $arPrice[$arr["NAME"]] = "[".$arr["NAME"]."] ".$arr["NAME_LANG"];
}
else
{
	$arPrice = $arProperty_N;
}

$arAscDesc = array(
	"asc" => GetMessage("IBLOCK_SORT_ASC"),
	"desc" => GetMessage("IBLOCK_SORT_DESC"),
);

$arComponentParameters = array(
	"GROUPS" => array(
		"PRICES" => array(
			"NAME" => GetMessage("IBLOCK_PRICES"),
		),
	),
	"PARAMETERS" => array(
		"AJAX_MODE" => array(),
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_IBLOCK"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"SECTION_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_SECTION_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["SECTION_ID"]}',
		),
		"SECTION_CODE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_SECTION_CODE"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
		"SECTION_USER_FIELDS" =>array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_BCS_SECTION_USER_FIELDS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arProperty_UF,
		),
		"ELEMENT_SORT_FIELD" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_ELEMENT_SORT_FIELD"),
			"TYPE" => "LIST",
			"VALUES" => $arSort,
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "sort",
		),
		"ELEMENT_SORT_ORDER" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_ELEMENT_SORT_ORDER"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "asc",
			"ADDITIONAL_VALUES" => "Y",
		),
		"ELEMENT_SORT_FIELD2" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_ELEMENT_SORT_FIELD2"),
			"TYPE" => "LIST",
			"VALUES" => $arSort,
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "id",
		),
		"ELEMENT_SORT_ORDER2" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_ELEMENT_SORT_ORDER2"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "desc",
			"ADDITIONAL_VALUES" => "Y",
		),
		"FILTER_NAME" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_FILTER_NAME_IN"),
			"TYPE" => "STRING",
			"DEFAULT" => "arrFilter",
		),
		"INCLUDE_SUBSECTIONS" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_BCS_INCLUDE_SUBSECTIONS"),
			"TYPE" => "LIST",
			"VALUES" => array(
				"Y" => GetMessage('CP_BCS_INCLUDE_SUBSECTIONS_ALL'),
				"A" => GetMessage('CP_BCS_INCLUDE_SUBSECTIONS_ACTIVE'),
				"N" => GetMessage('CP_BCS_INCLUDE_SUBSECTIONS_NO'),
			),
			"DEFAULT" => "Y",
		),
		"SHOW_ALL_WO_SECTION" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_BCS_SHOW_ALL_WO_SECTION"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		/*"SECTION_URL" => CIBlockParameters::GetPathTemplateParam(
			"SECTION",
			"SECTION_URL",
			GetMessage("IBLOCK_SECTION_URL"),
			"",
			"URL_TEMPLATES"
		),*/
		"DETAIL_URL" => CIBlockParameters::GetPathTemplateParam(
			"DETAIL",
			"DETAIL_URL",
			GetMessage("IBLOCK_DETAIL_URL"),
			"",
			"URL_TEMPLATES"
		),
		"BASKET_URL" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME" => GetMessage("IBLOCK_BASKET_URL"),
			"TYPE" => "STRING",
			"DEFAULT" => "/personal/basket.php",
		),
		"ACTION_VARIABLE" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME" => GetMessage("IBLOCK_ACTION_VARIABLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "action",
		),
		"PRODUCT_ID_VARIABLE" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME" => GetMessage("IBLOCK_PRODUCT_ID_VARIABLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "id",
		),
		"PRODUCT_QUANTITY_VARIABLE" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME" => GetMessage("CP_BCS_PRODUCT_QUANTITY_VARIABLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "quantity",
		),
		"PRODUCT_PROPS_VARIABLE" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME" => GetMessage("CP_BCS_PRODUCT_PROPS_VARIABLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "prop",
		),
		"SECTION_ID_VARIABLE" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME" => GetMessage("IBLOCK_SECTION_ID_VARIABLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "SECTION_ID",
		),
		"META_KEYWORDS" =>array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_DESC_KEYWORDS"),
			"TYPE" => "LIST",
			"DEFAULT" => "-",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => array_merge(Array("-"=>" "), $arSProperty_LNS),
		),
		"META_DESCRIPTION" =>array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_DESC_DESCRIPTION"),
			"TYPE" => "LIST",
			"DEFAULT" => "-",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => array_merge(Array("-"=>" "), $arSProperty_LNS),
		),
		"BROWSER_TITLE" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("CP_BCS_BROWSER_TITLE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"DEFAULT" => "-",
			"VALUES" => array_merge(Array("-"=>" ", "NAME" => GetMessage("IBLOCK_FIELD_NAME")), $arSProperty_LNS),
		),
		"ADD_SECTIONS_CHAIN" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("CP_BCS_ADD_SECTIONS_CHAIN"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"SHOW_MEASURE_WITH_RATIO" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SHOW_MEASURE_WITH_RATIO"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		// "DISPLAY_COMPARE" => Array(
		// 	"PARENT" => "ADDITIONAL_SETTINGS",
		// 	"NAME" => GetMessage("T_IBLOCK_DESC_DISPLAY_COMPARE"),
		// 	"TYPE" => "CHECKBOX",
		// 	"DEFAULT" => "Y",
		// ),
		"SHOW_MEASURE" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SHOW_MEASURE_NAME"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		/*"DISPLAY_WISH_BUTTONS" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("DISPLAY_WISH_BUTTONS_NAME"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),*/
		"SHOW_DISCOUNT_PERCENT" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SHOW_DISCOUNT_PERCENT_NAME"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"SHOW_DISCOUNT_TIME" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SHOW_DISCOUNT_TIME"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"SHOW_OLD_PRICE" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SHOW_OLD_PRICE_NAME"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"SET_TITLE" => Array(),
		"SET_STATUS_404" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("CP_BCS_SET_STATUS_404"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"PAGE_ELEMENT_COUNT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("IBLOCK_PAGE_ELEMENT_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "30",
		),
		"DISPLAY_BOTTOM_PAGER" => Array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("T_DISPLAY_BOTTOM_PAGER"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"LINE_ELEMENT_COUNT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("IBLOCK_LINE_ELEMENT_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "3",
		),
		"PROPERTY_CODE" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("IBLOCK_PROPERTY"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty,
			"ADDITIONAL_VALUES" => "Y",
		),
		"OFFERS_FIELD_CODE" => CIBlockParameters::GetFieldCode(GetMessage("CP_BCS_OFFERS_FIELD_CODE"), "VISUAL"),
		"OFFERS_PROPERTY_CODE" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("CP_BCS_OFFERS_PROPERTY_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty_Offers,
			"ADDITIONAL_VALUES" => "Y",
		),
		"OFFER_TREE_PROPS" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("CP_BCS_OFFER_TREE_PROPS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty_Offers,
			"ADDITIONAL_VALUES" => "Y",
		),
		"OFFERS_SORT_FIELD" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("CP_BCS_OFFERS_SORT_FIELD"),
			"TYPE" => "LIST",
			"VALUES" => $arSort,
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "sort",
		),
		"OFFERS_SORT_ORDER" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("CP_BCS_OFFERS_SORT_ORDER"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "asc",
			"ADDITIONAL_VALUES" => "Y",
		),
		"OFFERS_SORT_FIELD2" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("CP_BCS_OFFERS_SORT_FIELD2"),
			"TYPE" => "LIST",
			"VALUES" => $arSort,
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "id",
		),
		"OFFERS_SORT_ORDER2" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("CP_BCS_OFFERS_SORT_ORDER2"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "desc",
			"ADDITIONAL_VALUES" => "Y",
		),
		"OFFERS_LIMIT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage('CP_BCS_OFFERS_LIMIT'),
			"TYPE" => "STRING",
			"DEFAULT" => 5,
		),
		'OFFER_SHOW_PREVIEW_PICTURE_PROPS' => array(
            'PARENT' => 'VISUAL',
            'NAME' => GetMessage('OFFER_SHOW_PREVIEW_PICTURE_PROPS_TITLE'),
            'TYPE' => 'LIST',
            'MULTIPLE' => 'Y',
            'ADDITIONAL_VALUES' => 'N',
            'REFRESH' => 'N',
            'DEFAULT' => '-',
            'VALUES' => $arShowPreviewPictuteTreeOfferPropList
        ),
		"PRICE_CODE" => array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("IBLOCK_PRICE_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arPrice,
		),
		"USE_PRICE_COUNT" => array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("IBLOCK_USE_PRICE_COUNT"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			),
		"SHOW_PRICE_COUNT" => array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("IBLOCK_SHOW_PRICE_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "1",
		),
		"PRICE_VAT_INCLUDE" => array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("IBLOCK_VAT_INCLUDE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"PRODUCT_PROPERTIES" => array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("CP_BCS_PRODUCT_PROPERTIES"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty_X,
		),
		"USE_PRODUCT_QUANTITY" => array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("CP_BCS_USE_PRODUCT_QUANTITY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>36000000),
		"CACHE_FILTER" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("IBLOCK_CACHE_FILTER"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_BCS_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"TABS_CODE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("TABS_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => $arProperty,
			"ADDITIONAL_VALUES" => "Y",
		),
		"SHOW_RATING" => Array(
			"NAME" => GetMessage("SHOW_RATING"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"SALE_STIKER" =>array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SALE_STIKER"),
			"TYPE" => "LIST",
			"DEFAULT" => "-",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => array_merge(Array("-"=>" "), $arProperty_S),
		),
	),
);
// CIBlockParameters::AddPagerSettings($arComponentParameters, GetMessage("T_IBLOCK_DESC_PAGER_CATALOG"), true, true);

if ($boolCatalog)
{
	$arComponentParameters["PARAMETERS"]['HIDE_NOT_AVAILABLE'] = array(
		'PARENT' => 'DATA_SOURCE',
		'NAME' => GetMessage('CP_BCS_HIDE_NOT_AVAILABLE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);
	$arComponentParameters["PARAMETERS"]['HIDE_NOT_AVAILABLE_OFFERS'] = array(
		'PARENT' => 'DATA_SOURCE',
		'NAME' => GetMessage('CP_BCS_HIDE_NOT_AVAILABLE_OFFERS'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);
	if (CModule::IncludeModule('currency'))
	{
		$arComponentParameters["PARAMETERS"]['CONVERT_CURRENCY'] = array(
			'PARENT' => 'PRICES',
			'NAME' => GetMessage('CP_BCS_CONVERT_CURRENCY'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y',
		);

		if (isset($arCurrentValues['CONVERT_CURRENCY']) && 'Y' == $arCurrentValues['CONVERT_CURRENCY'])
		{
			$arCurrencyList = array();
			$rsCurrencies = CCurrency::GetList(($by = 'SORT'), ($order = 'ASC'));
			while ($arCurrency = $rsCurrencies->Fetch())
			{
				$arCurrencyList[$arCurrency['CURRENCY']] = $arCurrency['CURRENCY'];
			}
			$arComponentParameters['PARAMETERS']['CURRENCY_ID'] = array(
				'PARENT' => 'PRICES',
				'NAME' => GetMessage('CP_BCS_CURRENCY_ID'),
				'TYPE' => 'LIST',
				'VALUES' => $arCurrencyList,
				'DEFAULT' => CCurrency::GetBaseCurrency(),
				"ADDITIONAL_VALUES" => "Y",
			);
		}
	}
}

if(!$OFFERS_IBLOCK_ID)
{
	unset($arComponentParameters["PARAMETERS"]["OFFERS_FIELD_CODE"]);
	unset($arComponentParameters["PARAMETERS"]["OFFERS_PROPERTY_CODE"]);
	unset($arComponentParameters["PARAMETERS"]["OFFERS_SORT_FIELD"]);
	unset($arComponentParameters["PARAMETERS"]["OFFERS_SORT_ORDER"]);
	unset($arComponentParameters["PARAMETERS"]["OFFERS_SORT_FIELD2"]);
	unset($arComponentParameters["PARAMETERS"]["OFFERS_SORT_ORDER2"]);
	unset($arComponentParameters["PARAMETERS"]["HIDE_NOT_AVAILABLE_OFFERS"]);
}
else
{
	$arComponentParameters["PARAMETERS"]["OFFERS_CART_PROPERTIES"] = array(
		"PARENT" => "PRICES",
		"NAME" => GetMessage("CP_BCS_OFFERS_CART_PROPERTIES"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arProperty_Offers,
	);
}

if (isset($arCurrentValues["USE_PRODUCT_QUANTITY"]) && 'Y' == $arCurrentValues["USE_PRODUCT_QUANTITY"])
{
	$arComponentParameters["PARAMETERS"]['QUANTITY_FLOAT'] = array(
		'PARENT' => 'PRICES',
		'NAME' => GetMessage('CP_BCS_QUANTITY_FLOAT'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);
}
?>