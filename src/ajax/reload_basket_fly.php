<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use COptimus as Solution;
?>
<?if (isset($_REQUEST["PARAMS"]) && !empty($_REQUEST["PARAMS"])):?>	
	<?include_once("action_basket.php");?>
	<?$arParams = Solution::unserialize(urldecode($_REQUEST["PARAMS"]));?>
	<?$arParams['INNER']=true;?>
	<?$APPLICATION->IncludeComponent("bitrix:sale.basket.basket", "fly", $arParams, false, array("HIDE_ICONS" =>"Y"));?>	
<?endif;?>