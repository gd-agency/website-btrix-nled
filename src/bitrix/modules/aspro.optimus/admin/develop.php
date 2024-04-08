<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

global $APPLICATION;
IncludeModuleLangFile(__FILE__);

$moduleClass = "COptimus";
$moduleID = "aspro.optimus";
\Bitrix\Main\Loader::includeModule($moduleID);

use \Bitrix\Main\Config\Option;

$RIGHT = $APPLICATION->GetGroupRight($moduleID);
if($RIGHT >= "R"){

	$by = "id";
	$sort = "asc";

	$arSites = array();
	$db_res = CSite::GetList($by, $sort, array("ACTIVE"=>"Y"));
	while($res = $db_res->Fetch()){
		$arSites[] = $res;
	}

	$arParametrsList = array(
		'CUSTOMIZE' => array(
			'TITLE' => GetMessage('CUSTOMIZE_OPTIONS'),
			'THEME' => 'N',
			'OPTIONS' => array(
				'TEMPLATE_CUSTOM_CSS' => array(
					'TITLE' => GetMessage('TEMPLATE_CUSTOM_CSS_TITLE'),
					'TYPE' => 'includefile',
					'INCLUDEFILE' => '#SITE_DIR#css/custom.css',
				),
				'TEMPLATE_CUSTOM_JS' => array(
					'TITLE' => GetMessage('TEMPLATE_CUSTOM_JS_TITLE'),
					'TYPE' => 'includefile',
					'INCLUDEFILE' => '#SITE_DIR#js/custom.js',
				),
			),
		),
	);


	$arTabs = array();
	foreach($arSites as $key => $arSite){
		$arBackParametrs = array();
		$arTabs[] = array(
			"DIV" => "edit".($key+1),
			"TAB" => GetMessage("MAIN_OPTIONS_SITE_TITLE", array("#SITE_NAME#" => $arSite["NAME"], "#SITE_ID#" => $arSite["ID"])),
			"ICON" => "settings",
			// "TITLE" => GetMessage("MAIN_OPTIONS_TITLE"),
			"PAGE_TYPE" => "site_settings",
			"SITE_ID" => $arSite["ID"],
			"SITE_DIR" => $arSite["DIR"],
			"TEMPLATE" => COptimus::GetSiteTemplate($arSite["ID"]),
			"OPTIONS" => $arBackParametrs,
		);
	}

	$tabControl = new CAdminTabControl("tabControl", $arTabs);

	if($REQUEST_METHOD == "POST" && strlen($Update.$Apply.$RestoreDefaults) > 0 && $RIGHT >= "W" && check_bitrix_sessid()){
		global $APPLICATION, $CACHE_MANAGER;

		if(strlen($RestoreDefaults) > 0){

		}
		else{

		}

		// clear composite cache
		if($compositeMode = $moduleClass::IsCompositeEnabled()){
			$arHTMLCacheOptions = $moduleClass::GetCompositeOptions();
			$obCache = new CPHPCache();
			$obCache->CleanDir('', 'html_pages');
			$moduleClass::EnableComposite($compositeMode === 'AUTO_COMPOSITE', $arHTMLCacheOptions);
		}
		
		$APPLICATION->RestartBuffer();
	}

	CJSCore::Init(array("jquery"));?>
	<?if(!count($arTabs)):?>
		<div class="adm-info-message-wrap adm-info-message-red">
			<div class="adm-info-message">
				<div class="adm-info-message-title"><?=GetMessage("ASPRO_OPTIMUS_NO_SITE_INSTALLED", array("#SESSION_ID#"=>bitrix_sessid_get()))?></div>
				<div class="adm-info-message-icon"></div>
			</div>
		</div>
	<?else:?>
		<div class="adm-info-message"><?=GetMessage("OPTIMUS_MODULE_DEVELOP_INFO");?></div>
		<br>
		<br>
		<?$tabControl->Begin();?>
		<style type="text/css">
		*[id^=wait_window_div],.waitwindow{display:none;}
		</style>
		<form method="post" class="optimus_options" enctype="multipart/form-data" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>">
		<?=bitrix_sessid_post();?>
		<?
		foreach($arTabs as $key => $arTab){
			$tabControl->BeginNextTab();
			if($arTab["SITE_ID"]){
				$optionsSiteID = $arTab["SITE_ID"];
				foreach($arParametrsList as $blockCode => $arBlock)
				{?>
					<tr class="heading"><td colspan="2"><?=$arBlock["TITLE"]?></td></tr>
					<?
					foreach($arBlock["OPTIONS"] as $optionCode => $arOption)
					{
						$arOption[0] = $optionCode."_".$arTab["SITE_ID"];
						$arOption[1] = $arOption["TITLE"];
						$arTab["TEMPLATE"]["PATH"] = str_replace($_SERVER["DOCUMENT_ROOT"], "", $arTab["TEMPLATE"]["PATH"]);
						$arOption[3] = array($arOption["TYPE"], 1 => array("INCLUDEFILE" => $arOption["INCLUDEFILE"]));
						?>
						<?=$moduleClass::__AdmSettingsDrawRow_EX($moduleID, $arOption, $optionsSiteID, $arTab["TEMPLATE"]["PATH"]);?>
						<?
					}
				}
			}
		}
		?>
		<?
		if($REQUEST_METHOD == "POST" && strlen($Update.$Apply.$RestoreDefaults) && check_bitrix_sessid())
		{
			if(strlen($Update) && strlen($_REQUEST["back_url_settings"]))
				LocalRedirect($_REQUEST["back_url_settings"]);
			else
				LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
		}?>
			<?$tabControl->Buttons();?>
			<?/*<input <?if($RIGHT < "W") echo "disabled"?> type="submit" name="Apply" class="submit-btn" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">*/?>
			<?if(strlen($_REQUEST["back_url_settings"]) > 0): ?>
				<input type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?=htmlspecialcharsbx(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
				<input type="hidden" name="back_url_settings" value="<?=htmlspecialcharsbx($_REQUEST["back_url_settings"])?>">
			<?endif;?>
		</form>
		<?$tabControl->End();?>
	<?endif;?>
<?}
else
{
	echo CAdminMessage::ShowMessage(GetMessage('NO_RIGHTS_FOR_VIEWING'));
}?>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');?>