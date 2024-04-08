<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>

<?if($arParams["TITLE_BLOCK"] && (!empty($arParams["VK"]) || !empty($arParams["ODN"]) || !empty($arParams["FACE"]) || !empty($arParams["TWIT"]) || !empty($arParams["INST"]) || !empty($arParams["MAIL"]) || !empty($arParams["YOUTUBE"]) || !empty($arParams["GOOGLE_PLUS"]) || !empty($arParams["TELEGRAM"]) || !empty($arParams["WHATSAPP"]))){?>
	<div class="small_title"><?=$arParams["TITLE_BLOCK"];?></div>
<?}?>
<div class="links rows_block soc_icons">
	<?foreach($arResult['ORDER_SOC'] as $value):?>
		<?if($value == "vk" && $arParams["VK"]){?>
			<div class="item_block">
				<a href="<?=$arParams["VK"]?>" target="_blank" title="<?=GetMessage("VKONTAKTE")?>" class="<?=$value;?>"></a>
			</div>
		<?}?>
		<?if($value == "odn" && $arParams["ODN"]){?>
			<div class="item_block">
				<a href="<?=$arParams["ODN"]?>" target="_blank" title="<?=GetMessage("ODN")?>" class="<?=$value;?>"></a>
			</div>
		<?}?>
		<?if($value == "fb" && $arParams["FACE"]){?>
			<div class="item_block">
				<a href="<?=$arParams["FACE"]?>" target="_blank" title="<?=GetMessage("FACEBOOK")?>" class="<?=$value;?>"></a>
			</div>
		<?}?>
		<?if($value == "tw" && $arParams["TWIT"]){?>
			<div class="item_block">
				<a href="<?=$arParams["TWIT"]?>" target="_blank" title="<?=GetMessage("TWITTER")?>" class="<?=$value;?>"></a>
			</div>
		<?}?>
		<?if($value == "inst" && $arParams["INST"]){?>
			<div class="item_block">
				<a href="<?=$arParams["INST"]?>" target="_blank" title="<?=GetMessage("INST")?>" class="<?=$value;?>"></a>
			</div>
		<?}?>
		<?if($value == "mail" && $arParams["MAIL"]){?>
			<div class="item_block">
				<a href="<?=$arParams["MAIL"]?>" target="_blank" title="<?=GetMessage("MAIL")?>" class="<?=$value;?>"></a>
			</div>
		<?}?>
		<?if($value == "youtube" && $arParams["YOUTUBE"]){?>
			<div class="item_block">
				<a href="<?=$arParams["YOUTUBE"]?>" target="_blank" title="<?=GetMessage("YOUTUBE")?>" class="<?=$value;?>"></a>
			</div>
		<?}?>
		<?if($value == "telegram" && $arParams["TELEGRAM"]){?>
			<div class="item_block">
				<a href="<?=$arParams["TELEGRAM"]?>" target="_blank" title="<?=GetMessage("TELEGRAM")?>" class="<?=$value;?>"></a>
			</div>
		<?}?>
		<?if($value == "viber" && $arParams["VIBER"]){?>
			<div class="item_block">
				<a href="<?=$arParams["VIBER"]?>" target="_blank" title="<?=GetMessage("VIBER")?>" class="<?=$value;?>"></a>
			</div>
		<?}?>
		<?if($value == "whatsapp" && $arParams["WHATSAPP"]){?>
			<div class="item_block">
				<a href="<?=$arParams["WHATSAPP"]?>" target="_blank" title="<?=GetMessage("WHATSAPP")?>" class="<?=$value;?>"></a>
			</div>
		<?}?>
		<?if($value == "skype" && $arParams["SKYPE"]){?>
			<div class="item_block">
				<a href="<?=$arParams["SKYPE"]?>" target="_blank" title="<?=GetMessage("SKYPE")?>" class="<?=$value;?>"></a>
			</div>
		<?}?>
		<?if($value == "pinterest" && $arParams["PINTEREST"]){?>
			<div class="item_block">
				<a href="<?=$arParams["PINTEREST"]?>" target="_blank" title="<?=GetMessage("PINTEREST")?>" class="<?=$value;?>"></a>
			</div>
		<?}?>
	<?endforeach;?>
</div>