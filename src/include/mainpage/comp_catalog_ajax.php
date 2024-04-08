<?$bAjaxMode = (isset($_POST["AJAX_POST"]) && $_POST["AJAX_POST"] == "Y");
if($bAjaxMode)
{
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	global $APPLICATION;
}?>
<?if((isset($arParams["IBLOCK_ID"]) && $arParams["IBLOCK_ID"]) || $bAjaxMode):?>
	<?
	$arIncludeParams = ($bAjaxMode ? $_POST["AJAX_PARAMS"] : $arParamsTmp);
	$arGlobalFilter = ($bAjaxMode ? unserialize(urldecode($_POST["GLOBAL_FILTER"])) : array());
	$arComponentParams = unserialize(urldecode($arIncludeParams));
	$arComponentParams['TYPE_SKU'] = \Bitrix\Main\Config\Option::get('aspro.optimus', 'TYPE_SKU', 'TYPE_1', SITE_ID);
	?>

	<?
	if($bAjaxMode && (is_array($arGlobalFilter) && $arGlobalFilter))
		$GLOBALS[$arComponentParams["FILTER_NAME"]] = $arGlobalFilter;

	if($bAjaxMode && $_POST["FILTER_HIT_PROP"])
		$arComponentParams["FILTER_HIT_PROP"] = $_POST["FILTER_HIT_PROP"];

	/* hide compare link from module options */
	/*if (CNext::GetFrontParametrValue('CATALOG_COMPARE') == 'N') {
		$arComponentParams["DISPLAY_COMPARE"] = 'N';
	}*/
	/**/

	if ($_POST["ajax_get"] && $_POST["ajax_get"] === 'Y') {
		$arComponentParams["AJAX_REQUEST"] = 'Y';
	}
	// print_r($arComponentParams);
	?>

	<?$APPLICATION->IncludeComponent(
		"bitrix:catalog.section",
		"catalog_block_front",
		$arComponentParams,
		false, array("HIDE_ICONS"=>"Y")
	);?>

<?endif;?>