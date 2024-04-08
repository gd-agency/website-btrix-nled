<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule("iblock")) return;

if(!defined("WIZARD_SITE_ID")) return;
if(!defined("WIZARD_SITE_DIR")) return;
if(!defined("WIZARD_SITE_PATH")) return;
if(!defined("WIZARD_TEMPLATE_ID")) return;
if(!defined("WIZARD_TEMPLATE_ABSOLUTE_PATH")) return;
if(!defined("WIZARD_THEME_ID")) return;

$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."/";
//$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"]."/local/templates/".WIZARD_TEMPLATE_ID."/";


if(isset($_SESSION["OPTIMUS_CATALOG_ID"]) && $_SESSION["OPTIMUS_CATALOG_ID"])
	COption::SetOptionString("aspro.optimus", "CATALOG_IBLOCK_ID", $_SESSION["OPTIMUS_CATALOG_ID"], "", WIZARD_SITE_ID);
COption::SetOptionString("aspro.optimus", "MAX_DEPTH_MENU", 4, "", WIZARD_SITE_ID);

$catalogIBlockID = COptimusCache::$arIBlocks[WIZARD_SITE_ID]["aspro_optimus_catalog"]["aspro_optimus_catalog"][0];
$skuIBlockID = COptimusCache::$arIBlocks[WIZARD_SITE_ID]["aspro_optimus_catalog"]["aspro_optimus_sku"][0];

$arUserFieldViewType = CUserTypeEntity::GetList(array(), array("ENTITY_ID" => "IBLOCK_".$catalogIBlockID."_SECTION", "FIELD_NAME" => "UF_OFFERS_TYPE"))->Fetch();
if(!$arUserFieldViewType)
{
	$arFields = array(
		"FIELD_NAME" => "UF_OFFERS_TYPE",
		"USER_TYPE_ID" => "enumeration",
		"XML_ID" => "UF_OFFERS_TYPE",
		"SORT" => 100,
		"MULTIPLE" => "N",
		"MANDATORY" => "N",
		"SHOW_FILTER" => "N",
		"SHOW_IN_LIST" => "Y",
		"EDIT_IN_LIST" => "Y",
		"IS_SEARCHABLE" => "N",
		"SETTINGS" => array(
			"DISPLAY" => "LIST",
			"LIST_HEIGHT" => 5,
		)
	);
	$arLangs = array(
		"EDIT_FORM_LABEL" => array(
			"ru" => GetMessage("OFFERS_TYPE"),
			"en" => "Offers type",
		),
		"LIST_COLUMN_LABEL" => array(
			"ru" => GetMessage("OFFERS_TYPE"),
			"en" => "Offers type",
		)
	);

	$ob = new CUserTypeEntity();
	$FIELD_ID = $ob->Add(array_merge($arFields, array("ENTITY_ID" => "IBLOCK_".$catalogIBlockID."_SECTION"), $arLangs));
	if($FIELD_ID)
	{
		$obEnum = new CUserFieldEnum;
		$obEnum->SetEnumValues($FIELD_ID, array(
			"n0" => array(
				"VALUE" => 1,
				"XML_ID" => "TYPE_1",
			),
			"n1" => array(
				"VALUE" => 2,
				"XML_ID" => "TYPE_2",
			),
		));
	}
}

$arUserFieldViewType = CUserTypeEntity::GetList(array(), array("ENTITY_ID" => "IBLOCK_".$catalogIBlockID."_SECTION", "FIELD_NAME" => "UF_ELEMENT_DETAIL"))->Fetch();
if(!$arUserFieldViewType)
{
	$arFields = array(
		"FIELD_NAME" => "UF_ELEMENT_DETAIL",
		"USER_TYPE_ID" => "enumeration",
		"XML_ID" => "UF_ELEMENT_DETAIL",
		"SORT" => 100,
		"MULTIPLE" => "N",
		"MANDATORY" => "N",
		"SHOW_FILTER" => "N",
		"SHOW_IN_LIST" => "Y",
		"EDIT_IN_LIST" => "Y",
		"IS_SEARCHABLE" => "N",
		"SETTINGS" => array(
			"DISPLAY" => "LIST",
			"LIST_HEIGHT" => 5,
		)
	);
	$arLangs = array(
		"EDIT_FORM_LABEL" => array(
			"ru" => GetMessage("DETAIL_PAGE"),
			"en" => "Catalog detail type",
		),
		"LIST_COLUMN_LABEL" => array(
			"ru" => GetMessage("DETAIL_PAGE"),
			"en" => "Catalog detail type",
		)
	);

	$ob = new CUserTypeEntity();
	$FIELD_ID = $ob->Add(array_merge($arFields, array("ENTITY_ID" => "IBLOCK_".$catalogIBlockID."_SECTION"), $arLangs));
	if($FIELD_ID)
	{
		$obEnum = new CUserFieldEnum;
		$obEnum->SetEnumValues($FIELD_ID, array(
			"n0" => array(
				"VALUE" => GetMessage("DETAIL_PAGE_TAB"),
				"XML_ID" => "element_1",
			),
			"n1" => array(
				"VALUE" => GetMessage("DETAIL_PAGE_NOTAB"),
				"XML_ID" => "element_2",
			),
		));
	}
}


// set features properties
use Bitrix\Iblock;

if( class_exists('Bitrix\Iblock\Model\PropertyFeature')  && method_exists('Bitrix\Iblock\Model\PropertyFeature', 'getPropertyFeatureList') && method_exists('Bitrix\Iblock\Model\PropertyFeature', 'getIndex') ) {

	$newFeatures = array(
		$skuIBlockID => array(
			'SIZES' => array(
				'LIST_PAGE_SHOW' => 'Y',
				'DETAIL_PAGE_SHOW' => 'Y',
				'IN_BASKET' => 'Y',
				'OFFER_TREE' => 'Y',
			),
			'COLOR_REF' => array(
				'LIST_PAGE_SHOW' => 'Y',
				'DETAIL_PAGE_SHOW' => 'Y',
				'IN_BASKET' => 'Y',
				'OFFER_TREE' => 'Y',
			),
		),
		$catalogIBlockID => array(
			'BRAND' => array(
				'LIST_PAGE_SHOW' => 'Y',
				'DETAIL_PAGE_SHOW' => 'Y',
			),
			"CML2_ARTICLE" => array(
				'LIST_PAGE_SHOW' => 'Y',
				'DETAIL_PAGE_SHOW' => 'Y',
			),
			"PROP_2033" => array(
				'LIST_PAGE_SHOW' => 'Y',
				'DETAIL_PAGE_SHOW' => 'Y',
			),
			"COLOR_REF2" => array(
				'LIST_PAGE_SHOW' => 'Y',
				'DETAIL_PAGE_SHOW' => 'Y',
			),
			"PROP_159" => array(
				'LIST_PAGE_SHOW' => 'Y',
				'DETAIL_PAGE_SHOW' => 'Y',
			),
			"PROP_2052" => array(
				'LIST_PAGE_SHOW' => 'Y',
				'DETAIL_PAGE_SHOW' => 'Y',
			),
			"PROP_2027" => array(
				'LIST_PAGE_SHOW' => 'Y',
				'DETAIL_PAGE_SHOW' => 'Y',
			),
			"PROP_2053" => array(
				'LIST_PAGE_SHOW' => 'Y',
				'DETAIL_PAGE_SHOW' => 'Y',
			),
			"PROP_2083" => array(
				'LIST_PAGE_SHOW' => 'Y',
				'DETAIL_PAGE_SHOW' => 'Y',
			),
			"PROP_2049" => array(
				'LIST_PAGE_SHOW' => 'Y',
				'DETAIL_PAGE_SHOW' => 'Y',
			),
			"PROP_2026" => array(
				'LIST_PAGE_SHOW' => 'Y',
				'DETAIL_PAGE_SHOW' => 'Y',
			),
			"PROP_2044" => array(
				'LIST_PAGE_SHOW' => 'Y',
				'DETAIL_PAGE_SHOW' => 'Y',
			),
			"PROP_162" => array(
				'LIST_PAGE_SHOW' => 'Y',
				'DETAIL_PAGE_SHOW' => 'Y',
			),
			"PROP_2065" => array(
				'LIST_PAGE_SHOW' => 'Y',
				'DETAIL_PAGE_SHOW' => 'Y',
			),
			"PROP_2054" => array(
				'LIST_PAGE_SHOW' => 'Y',
				'DETAIL_PAGE_SHOW' => 'Y',
			),
			"PROP_2017" => array(
				'LIST_PAGE_SHOW' => 'Y',
				'DETAIL_PAGE_SHOW' => 'Y',
			),
			"PROP_2055" => array(
				'LIST_PAGE_SHOW' => 'Y',
				'DETAIL_PAGE_SHOW' => 'Y',
			),
			"PROP_2069" => array(
				'LIST_PAGE_SHOW' => 'Y',
				'DETAIL_PAGE_SHOW' => 'Y',
			),
			"PROP_2062" => array(
				'LIST_PAGE_SHOW' => 'Y',
				'DETAIL_PAGE_SHOW' => 'Y',
			),
			"PROP_2061" => array(
				'LIST_PAGE_SHOW' => 'Y',
				'DETAIL_PAGE_SHOW' => 'Y',
			),
			"CML2_LINK" => array(
				'LIST_PAGE_SHOW' => 'Y',
				'DETAIL_PAGE_SHOW' => 'Y',
			),
			"VIDEO" => array(
				'DETAIL_PAGE_SHOW' => 'Y',
			),
			"VIDEO_YOUTUBE" => array(
				'DETAIL_PAGE_SHOW' => 'Y',
			),

		),
	);


	foreach ($newFeatures as $iblockId => $featuresProps) {

		foreach ($featuresProps as $propKey => $propValue) {
			$arPropsCodes[] = $propKey;
		}

		$arPropsFilter = array('IBLOCK_ID' => $iblockId);

		$resProps = CIBlockProperty::GetList( array(), $arPropsFilter );
		while ( $prop = $resProps->Fetch() ) {
			if( in_array($prop['CODE'], $arPropsCodes) ) {
				$arProps[] = $prop;
			}
		}

		if($arProps) {
			foreach ($arProps as $propKey => $propValue) {
				$arFields = array(
					'FEATURES' => array(),
				);

				$propertyFeatures = Iblock\Model\PropertyFeature::getPropertyFeatureList($propValue);

				foreach($propertyFeatures as $propFeat) {

					if( isset($featuresProps[ $propValue['CODE'] ][ $propFeat['FEATURE_ID'] ]) ) {
						$feautureIndex = Iblock\Model\PropertyFeature::getIndex($propFeat);
						$arFields['FEATURES'][$feautureIndex] = array(
							'MODULE_ID' => $propFeat['MODULE_ID'],
							'FEATURE_ID' => $propFeat['FEATURE_ID'],
							'IS_ENABLED' => $featuresProps[ $propValue['CODE'] ][ $propFeat['FEATURE_ID'] ],
						);
					}

				}

				$ibp = new CIBlockProperty;
				$res = $ibp->Update($propValue['ID'], $arFields, true);
			}
		}

	}


	// set iblock id for cross sales
	$arProps = array();
	$resProps = CIBlockProperty::GetList(array(), array('USER_TYPE' => 'SAsproCustomFilter'));
	while($prop = $resProps->Fetch()) {
		$arProps[] = $prop;
	} 

	if($arProps) {
		$CIBlockProperty = new CIBlockProperty();
		$arFields = array(
			'USER_TYPE' => 'SAsproCustomFilter',
			'USER_TYPE_SETTINGS' => array(
				'IBLOCK_TYPE_ID' => 'aspro_optimus_catalog',
				'IBLOCK_ID' => $catalogIBlockID,
			),
		);
		foreach($arProps as $prop){
			$CIBlockProperty->Update($prop['ID'], $arFields);
		}
	}
	

}

?>