<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<? 
    global $TEMPLATE_OPTIONS;

    $sViewElementTemplate = $arParams["SECTIONS_TYPE_VIEW"] === "FROM_MODULE" 
        ? strtolower($TEMPLATE_OPTIONS["CATALOG_PAGE_SECTIONS"]["CURRENT_VALUE"]) 
        : (
            $arParams["SECTIONS_TYPE_VIEW"] 
                ? $arParams["SECTIONS_TYPE_VIEW"] 
                : "sections_1"
        );
?>
<?@include_once('page_blocks/'.$sViewElementTemplate.'.php');?>