<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? 
	use \Bitrix\Main\Config\Option;
	$this->setFrameMode( true ); 
?>
<ul class="submenu">
	<?if (is_array($arResult) && !empty($arResult)):?>
	<?foreach( $arResult as $arItem ){?>
		<?
			// Secure logout
			if( strpos($arItem['LINK'], 'logout') && Option::get('main', 'secure_logout') === 'Y' ){
				$arItem['LINK'] .= "&sessid=".bitrix_sessid();
			}
		?>
		<li class="menu_item<?=($arItem["SELECTED"]?" selected":"");?>"><a href="<?=$arItem["LINK"]?>" class="dark_link"><?=$arItem["TEXT"]?></a></li>
		<?if (is_array($arResult["ITEMS"]) && !empty($arResult["ITEMS"])):?>
			<?foreach( $arItem["ITEMS"] as $arSubItem ){?>
				<li class="menu_subitem<?=($arItem["SELECTED"]?" selected":"");?>"><a href="<?=$arSubItem["LINK"]?>"><?=$arSubItem["TEXT"]?></a></li>
			<?}?>
		<?endif;?>
	<?}?>
	<?endif;?>
</ul>