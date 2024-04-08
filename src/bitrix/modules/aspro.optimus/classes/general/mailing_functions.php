<?
// include CMainPage
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/mainpage.php");
// get site_id by host
$obMainPage = new CMainPage();
$site_id = $obMainPage->GetSiteByHost();
if(!$site_id)
    $site_id = "s1";

$moduleID = "aspro.optimus";
\Bitrix\Main\Loader::includeModule($moduleID);
$arColoredHead = array("TYPE_2", "TYPE_6", "TYPE_7", "TYPE_8");

// get site info
$arSite = \CSite::GetByID($site_id)->Fetch();
$arSite['DIR'] = str_replace('//', '/', '/'.$arSite['DIR']);
if(!strlen($arSite['DOC_ROOT']))
{
    $arSite['DOC_ROOT'] = Bitrix\Main\Application::getDocumentRoot();
}
$site_charset = $arSite['CHARSET'];
$arSite['DOC_ROOT'] = str_replace('//', '/', $arSite['DOC_ROOT'].'/');
$siteDir = str_replace('//', '/', $arSite['DOC_ROOT'].$arSite['DIR']);
$siteProtocol = (\CMain::isHttps() ? "https" : "http");
$siteAddress = $arSite['SERVER_NAME'];
$siteAddressFull = $siteProtocol."://".$arSite['SERVER_NAME'];

//get iblock linked to site
\Bitrix\Main\Loader::includeModule('iblock');
global $arIblocks;
$arIblocks = array();
$dbIblock = \CIBlock::GetList(array(), array("ACTIVE" => "Y", "LID" => $site_id));
while($arIblock = $dbIblock->Fetch()){
	$arIblocks[$arIblock["IBLOCK_TYPE_ID"]][$arIblock["CODE"]][] = $arIblock["ID"];
}

//get vars
$arLogo = \Bitrix\Main\Config\Option::get($moduleID, "LOGO_IMAGE", false, $site_id);
$arLogoEmail = \Bitrix\Main\Config\Option::get($moduleID, "LOGO_IMAGE_EMAIL", false, $site_id);

$color_theme = \Bitrix\Main\Config\Option::get($moduleID, "COLOR_THEME", "BLUE", $site_id);
$custom_color_theme = \Bitrix\Main\Config\Option::get($moduleID, "CUSTOM_COLOR_THEME", false, $site_id);
$type_head = \Bitrix\Main\Config\Option::get($moduleID, "HEAD", "TYPE_1", $site_id);
$logo_color_bg = \Bitrix\Main\Config\Option::get($moduleID, "NO_LOGO_BG", "", $site_id);
$order_email = \Bitrix\Main\Config\Option::get("sale", "order_email", "sale@".$siteAddress);

$isColoredHead = (in_array($type_head, $arColoredHead) ? true : false);
$type_color = ($isColoredHead ? "colored" : "main");
$phone_color = ($type_color == "colored" ? "#fff" : "#1d2029");
$arModuleOptions = \COptimus::getModuleOptionsList(true);
$arThemeValues = array();
$bg_color = $bg_color_logo = "";
$phone = '+0 000 000-00-00';
global $copyright;
$copyright = '&copy; '.$arSite["NAME"];
global $social;
$social = '';

$bg_image = $siteAddressFull.'/bitrix/templates/'.str_replace(".", "_", $moduleID).'/images/phone_mail.png';
$bg_phone_position = '0px 0px';
$phonePath = $siteDir.'/include/phone.php';
$copyrightPath = $siteDir.'/include/footer/copy/copyright.php';
$socialPath = $siteDir.'/include/footer/social.info.optimus.default.php';

$bLogoEmail = false;

if ($arLogo == serialize(array()) || $arLogo == false) {
	if ($arLogoEmail != serialize(array()) && $arLogoEmail != false) {
		$arLogoValue = \COptimus::unserialize($arLogoEmail);
		$logo_src = \CFIle::GetPath(current($arLogoValue));
		$bLogoEmail = true;
	} else {
		$logo_src = "/include/logo.png";
	}
} else {
	if ($arLogoEmail != serialize(array()) && $arLogoEmail != false) {
		$arLogoValue = \COptimus::unserialize($arLogoEmail);
		$logo_src = \CFIle::GetPath(current($arLogoValue));
		$bLogoEmail = true;
	} else {
    $arLogoValue = \COptimus::unserialize($arLogo);
		$logo_src = \CFIle::GetPath(current($arLogoValue));
	}
}



foreach($arModuleOptions["TEMPLATE_OPTIONS"] as $arOptions)
{
    if($arOptions["ID"] == "COLOR_THEME")
    {
        $arThemeValues = $arOptions["VALUES"];
        break;
    }

}

foreach($arThemeValues as $arValue)
{
    if($arValue["VALUE"] == $color_theme)
    {
        $bg_color = $arValue["COMPONENT_VALUE"];
        break;
    }
}

if($color_theme == "CUSTOM")
{
    $bg_color = "#".$custom_color_theme;
}
$theme_color =  $bg_color;

$bg_color_logo = $bg_color;
if($logo_color_bg == "Y")
		$bg_color_logo = "";
		
if ($bLogoEmail) {
	$bg_color_logo = "";
}

if(Bitrix\Main\IO\File::isFileExists($phonePath))
{
    $phone = Bitrix\Main\IO\File::getFileContents($phonePath);
}
if(Bitrix\Main\IO\File::isFileExists($copyrightPath))
{
    $copyright = Bitrix\Main\IO\File::getFileContents($copyrightPath);
	// cut php
	$pattern = '/<\?(.*)\?>/is';
	preg_match($pattern, $copyright,$matches);
	if($matches[1])
	{
		$copyright = str_replace(array($matches[1], '<?', '?>'), "", $copyright);
	}
}
if(Bitrix\Main\IO\File::isFileExists($socialPath))
{
	$social = '';
	$social = Bitrix\Main\IO\File::getFileContents($socialPath);
	
	global $vk_social, $fb_social, $odn_social, $tw_social, $mail_social, $inst_social, $g_social, $y_social, $pinterest_social;
	$re = '@([\'" ]{1,3})VK([\'" ]{1,3})(.{1,8})[\'" ]{1,3}(.+)[\'" ]{1,3},@x';
	preg_match_all($re, $social, $arMatch);
	if($arMatch[4][0])
		$vk_social = str_replace(array("\"", "'"), "", $arMatch[4][0]);
	$re = '@([\'" ]{1,3})FACE([\'" ]{1,3})(.{1,8})[\'" ]{1,3}(.+)[\'" ]{1,3},@x';
	preg_match_all($re, $social, $arMatch);
	if($arMatch[4][0])
		$fb_social = str_replace(array("\"", "'"), "", $arMatch[4][0]);
	$re = '@([\'" ]{1,3})ODN([\'" ]{1,3})(.{1,8})[\'" ]{1,3}(.+)[\'" ]{1,3},@x';
	preg_match_all($re, $social, $arMatch);
	if($arMatch[4][0])
		$odn_social = str_replace(array("\"", "'"), "", $arMatch[4][0]);
	$re = '@([\'" ]{1,3})TWIT([\'" ]{1,3})(.{1,8})[\'" ]{1,3}(.+)[\'" ]{1,3},@x';
	preg_match_all($re, $social, $arMatch);
	if($arMatch[4][0])
		$tw_social = str_replace(array("\"", "'"), "", $arMatch[4][0]);
	$re = '@([\'" ]{1,3})MAIL([\'" ]{1,3})(.{1,8})[\'" ]{1,3}(.+)[\'" ]{1,3},@x';
	preg_match_all($re, $social, $arMatch);
	if($arMatch[4][0])
		$mail_social = str_replace(array("\"", "'"), "", $arMatch[4][0]);
	$re = '@([\'" ]{1,3})INST([\'" ]{1,3})(.{1,8})[\'" ]{1,3}(.+)[\'" ]{1,3},@x';
	preg_match_all($re, $social, $arMatch);
	if($arMatch[4][0])
		$inst_social = str_replace(array("\"", "'"), "", $arMatch[4][0]);
	$re = '@([\'" ]{1,3})GOOGLEPLUS([\'" ]{1,3})(.{1,8})[\'" ]{1,3}(.+)[\'" ]{1,3},@x';
	preg_match_all($re, $social, $arMatch);
	if($arMatch[4][0])
		$g_social = str_replace(array("\"", "'"), "", $arMatch[4][0]);
	$re = '@([\'" ]{1,3})YOUTUBE([\'" ]{1,3})(.{1,8})[\'" ]{1,3}(.+)[\'" ]{1,3},@x';
	preg_match_all($re, $social, $arMatch);
	if($arMatch[4][0])
		$y_social = str_replace(array("\"", "'"), "", $arMatch[4][0]);
	$re = '@([\'" ]{1,3})PINTEREST([\'" ]{1,3})(.{1,8})[\'" ]{1,3}(.+)[\'" ]{1,3},@x';
		preg_match_all($re, $social, $arMatch);
		if($arMatch[4][0])
			$pinterest_social = str_replace(array("\"", "'"), "", $arMatch[4][0]);
	
}
?>