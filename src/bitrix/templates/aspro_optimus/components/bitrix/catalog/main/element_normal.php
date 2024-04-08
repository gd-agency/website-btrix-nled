<?$sViewElementTemplate = ($arParams["ELEMENT_TYPE_VIEW"] == "FROM_MODULE" ? strtolower($TEMPLATE_OPTIONS["CATALOG_PAGE_DETAIL"]["CURRENT_VALUE"]) : ($arParams["ELEMENT_TYPE_VIEW"] ? $arParams["ELEMENT_TYPE_VIEW"] : "element_1"));?>
<?//set detail view type
$typeTmpDetail = 0;
if($arSection['UF_ELEMENT_DETAIL'])
	$typeTmpDetail = $arSection['UF_ELEMENT_DETAIL'];
else
{
	if($arSection["DEPTH_LEVEL"] > 2)
	{
		$sectionParent = COptimusCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => COptimusCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "ID" => $arSection["IBLOCK_SECTION_ID"], "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "NAME", "UF_ELEMENT_DETAIL"));
		if($sectionParent['UF_ELEMENT_DETAIL'] && !$typeTmpDetail)
			$typeTmpDetail = $sectionParent['UF_ELEMENT_DETAIL'];

		if(!$typeTmpDetail)
		{
			$sectionRoot = COptimusCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => COptimusCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "<=LEFT_BORDER" => $arSection["LEFT_MARGIN"], ">=RIGHT_BORDER" => $arSection["RIGHT_MARGIN"], "DEPTH_LEVEL" => 1, "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "NAME", "UF_ELEMENT_DETAIL"));
			if($sectionRoot['UF_ELEMENT_DETAIL'] && !$typeTmpDetail)
				$typeTmpDetail = $sectionRoot['UF_ELEMENT_DETAIL'];
		}
	}
	else
	{
		$sectionRoot = COptimusCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => COptimusCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "<=LEFT_BORDER" => $arSection["LEFT_MARGIN"], ">=RIGHT_BORDER" => $arSection["RIGHT_MARGIN"], "DEPTH_LEVEL" => 1, "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "NAME", "UF_ELEMENT_DETAIL"));
		if($sectionRoot['UF_ELEMENT_DETAIL'] && !$typeTmpDetail)
			$typeTmpDetail = $sectionRoot['UF_ELEMENT_DETAIL'];
	}
}
if($typeTmpDetail)
{
	$rsTypes = CUserFieldEnum::GetList(array(), array("ID" => $typeTmpDetail));
	if($arType = $rsTypes->GetNext())
		$typeDetail = $arType['XML_ID'];
	if($typeDetail)
		$sViewElementTemplate = $typeDetail;
}
?>
<?@include_once('page_blocks/'.$sViewElementTemplate.'.php');?>
