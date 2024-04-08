<?
$arResult = COptimus::getChilds($arResult);
global $arRegion, $arTheme;

if($arResult){
	foreach($arResult as $key => &$arItem){
		if(isset($arItem["PARAMS"]["ONLY_MOBILE"]) && $arItem["PARAMS"]["ONLY_MOBILE"]=="Y") {
		    unset($arResult[$key]);
		    continue;
		}

		if( isset($arItem['PARAMS']['IS_CATALOG']) ){
			$catalog_id = \Bitrix\Main\Config\Option::get("aspro.optimus", "CATALOG_IBLOCK_ID", COptimusCache::$arIBlocks[SITE_ID]['aspro_optimus_catalog']['aspro_optimus_catalog'][0]);
			$arSections = COptimusCache::CIBlockSection_GetList(
				['SORT' => 'ASC', 'ID' => 'ASC', 'CACHE' => ['TAG' => COptimusCache::GetIBlockCacheTag($catalog_id), 'GROUP' => ['ID']]], 
				['IBLOCK_ID' => $catalog_id, 'ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y', 'ACTIVE_DATE' => 'Y', '<DEPTH_LEVEL' => $arParams['MAX_LEVEL']], 
				false, 
				["ID", "NAME", "LEFT_MARGIN", "RIGHT_MARGIN", "DEPTH_LEVEL", "SECTION_PAGE_URL", "IBLOCK_SECTION_ID"]
			);

			
			if( $arSections ){
				$arTmpResult = array();

				foreach($arSections as $ID => &$arSection){
					$arSection['TEXT'] = $arSection['NAME'];
					$arSection['LINK'] = $arSection['SECTION_PAGE_URL'];
					unset($arSection['NAME'], $arSection['SECTION_PAGE_URL']);

					if($arSection['IBLOCK_SECTION_ID']){
						if(!isset($arSections[$arSection['IBLOCK_SECTION_ID']]['CHILD'])){
							$arSections[$arSection['IBLOCK_SECTION_ID']]['CHILD'] = array();
						}

						$arSections[$arSection['IBLOCK_SECTION_ID']]['CHILD'][] = &$arSections[$arSection['ID']];
					}

					if($arSection['DEPTH_LEVEL'] == 1){
						$arTmpResult[] = &$arSections[$arSection['ID']];
					}
				}

				$arItem['CHILD'] = $arTmpResult;
			}
		}

		if(isset($arItem['CHILD'])){
			foreach($arItem['CHILD'] as $key2=>$arItemChild){
				if(isset($arItemChild['PARAMS']) && $arRegion && $arTheme['USE_REGIONALITY']['VALUE'] === 'Y' && $arTheme['USE_REGIONALITY']['DEPENDENT_PARAMS']['REGIONALITY_FILTER_ITEM']['VALUE'] === 'Y'){
					// filter items by region
					if(isset($arItemChild['PARAMS']['LINK_REGION'])){
						if($arItemChild['PARAMS']['LINK_REGION']){
							if(!in_array($arRegion['ID'], $arItemChild['PARAMS']['LINK_REGION']))
								unset($arResult[$key]['CHILD'][$key2]);
						}
						else
							unset($arResult[$key]['CHILD'][$key2]);
					}
				}
			}
		}
	}
}?>