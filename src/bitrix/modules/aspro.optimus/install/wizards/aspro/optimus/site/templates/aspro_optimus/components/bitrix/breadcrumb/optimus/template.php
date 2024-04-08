<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$strReturn = '';
if($arResult){
	CModule::IncludeModule("iblock");
	global $TEMPLATE_OPTIONS, $OptimusSectionID, $noAddElementToChain;
	$cnt = count($arResult);
	$lastindex = $cnt - 1;
	$bShowCatalogSubsections = COption::GetOptionString("aspro.optimus", "SHOW_BREADCRUMBS_CATALOG_SUBSECTIONS", "Y", SITE_ID) == "Y";
	$bMobileBreadcrumbs = ($TEMPLATE_OPTIONS["MOBILE_CATALOG_BREADCRUMBS"]["CURRENT_VALUE"] == "Y" && $OptimusSectionID);

	/*if ($bMobileBreadcrumbs) {
		$visibleMobile = $lastindex - 1;
	}*/

	if ($bMobileBreadcrumbs) {
		if ($noAddElementToChain) {
			$visibleMobile = $lastindex;
		} else {
			$visibleMobile = $lastindex - 1;
		}
	}

	for($index = 0; $index < $cnt; ++$index){
		$arSubSections = array();
		$bShowMobileArrow = false;
		$arItem = $arResult[$index];
		$title = htmlspecialcharsex($arItem["TITLE"]);
		$bLast = $index == $lastindex;

		if($OptimusSectionID){
			if ($bMobileBreadcrumbs && $visibleMobile == $index) {
				$bShowMobileArrow = true;
			}

			if($bShowCatalogSubsections){
				$arSubSections = COptimus::getChainNeighbors($OptimusSectionID, $arItem['LINK']);
			}
		}
			

		
		if($index){
			$strReturn .= '<span class="separator">-</span>';
		}
		if($arItem["LINK"] <> "" && $arItem['LINK'] != GetPagePath() && $arItem['LINK']."index.php" != GetPagePath() || $arSubSections){
			$strReturn .= '<div class="bx-breadcrumb-item'.($bMobileBreadcrumbs ? ' bx-breadcrumb-item--mobile' : '').($bShowMobileArrow ? ' bx-breadcrumb-item--visible-mobile colored_theme_hover_bg-block' : '').($arSubSections ? ' drop' : '').'" id="bx_breadcrumb_'.$index.'" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
			if($arSubSections){
				if($index == ($cnt-1) && GetPagePath() === $arItem["LINK"]):
					$strReturn .= '<link href="'.GetPagePath().'" itemprop="item" /><span class="number">';
				else:
					$strReturn .= '<a class="number" href="'.$arItem["LINK"].'" itemprop="item">';
				endif;

				if ($bShowMobileArrow) {
					$strReturn .= COptimus::showIconSvg(array('CLASS' => 'breadcrumb-arrow', 'CLASS_ICON' => 'colored_theme_hover_bg-el-svg', 'PATH' => SITE_TEMPLATE_PATH.'/images/svg/catalog/arrow_breadcrumbs.svg'));
				}

				$strReturn .=($arSubSections ? '<span itemprop="name">'.$title.'</span><b class="space"></b><span class="separator'.($bLast ? ' cat_last' : '').'"></span>' : '<span>'.$title.'</span>');
				$strReturn .= '<meta itemprop="position" content="'.($index + 1).'">';
				if($index == ($cnt-1)):
					$strReturn .= '</span>';
				else:
					$strReturn .= '</a>';
				endif;
				$strReturn .= '<div class="dropdown_wrapp"><div class="dropdown">';
					foreach($arSubSections as $arSubSection){
						$strReturn .= '<a href="'.$arSubSection["LINK"].'">'.$arSubSection["NAME"].'</a>';
					}
				$strReturn .= '</div></div>';
			}
			else{
				$strReturn .= '<a href="'.$arItem["LINK"].'" title="'.$title.'" itemprop="item">';
				if ($bShowMobileArrow) {
					$strReturn .= COptimus::showIconSvg(array('CLASS' => 'breadcrumb-arrow', 'CLASS_ICON' => 'colored_theme_hover_bg-el-svg', 'PATH' => SITE_TEMPLATE_PATH.'/images/svg/catalog/arrow_breadcrumbs.svg'));
				}
				$strReturn .= '<span itemprop="name">'.$title.'</span><meta itemprop="position" content="'.($index + 1).'"></a>';
			}
			$strReturn .= '</div>';
		}
		else{
			$strReturn .= '<span class="'.($bMobileBreadcrumbs ? ' bx-breadcrumb-item--mobile' : '').'" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><link href="'.GetPagePath().'" itemprop="item" /><span itemprop="name">'.$title.'</span><meta itemprop="position" content="'.($index + 1).'"></span>';
		}
	}

	return '<div class="breadcrumbs" itemscope="" itemtype="http://schema.org/BreadcrumbList">'.$strReturn.'</div>';
}
else{
	return $strReturn;
}
?>