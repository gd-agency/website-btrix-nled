<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<div class="mail_soc_wrapper" style="text-align:center;padding-top:20px;font-size:0px;">
	<?if( !empty( $arParams["VK"] ) && $arParams["VK"] != "\"" ){?>
		<a href="<?=$arParams["VK"]?>" target="_blank" class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.optimus/images/vk.png" alt="<?=GetMessage("VKONTAKTE")?>" title="<?=GetMessage("VKONTAKTE")?>" />
		</a>
	<?}?>
	<?if( !empty( $arParams["ODN"] ) && $arParams["ODN"] != "\""  ){?>
		<a href="<?=$arParams["ODN"]?>" target="_blank"  class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.optimus/images/odn.png" alt="<?=GetMessage("ODN")?>" title="<?=GetMessage("ODN")?>" />
		</a>
	<?}?>
	<?if( !empty( $arParams["FACE"] ) && $arParams["FACE"] != "\""  ){?>
		<a href="<?=$arParams["FACE"]?>" target="_blank" class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.optimus/images/facebook.png" alt="<?=GetMessage("FACEBOOK")?>" title="<?=GetMessage("FACEBOOK")?>" />
		</a>
	<?}?>
	<?if( !empty( $arParams["TWIT"] ) && $arParams["TWIT"] != "\""  ){?>
		<a href="<?=$arParams["TWIT"]?>" target="_blank" class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.optimus/images/twitter.png" alt="<?=GetMessage("TWITTER")?>" title="<?=GetMessage("TWITTER")?>" /> 
		</a>
	<?}?>
	<?if( !empty( $arParams["INST"] ) && $arParams["INST"] != "\""  ){?>
		<a href="<?=$arParams["INST"]?>" target="_blank" class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.optimus/images/inst.png" alt="<?=GetMessage("INST")?>" title="<?=GetMessage("INST")?>" />
		</a>
	<?}?>
	<?if( !empty( $arParams["MAIL"] ) && $arParams["MAIL"] != "\""  ){?>
		<a href="<?=$arParams["MAIL"]?>" target="_blank" class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.optimus/images/mail.png" alt="<?=GetMessage("MAIL")?>" title="<?=GetMessage("MAIL")?>" />
		</a>
	<?}?>
	<?if( !empty( $arParams["YOUTUBE"] ) && $arParams["YOUTUBE"] != "\""  ){?>
		<a href="<?=$arParams["YOUTUBE"]?>" target="_blank" class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.optimus/images/youtube.png" alt="<?=GetMessage("YOUTUBE")?>" title="<?=GetMessage("YOUTUBE")?>" /> 
		</a>
	<?}?>
	<?if( !empty( $arParams["TELEGRAM"] ) && $arParams["TELEGRAM"] != "\""  ){?>
		<a href="<?=$arParams["TELEGRAM"]?>" target="_blank" class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.optimus/images/telegram.png" alt="<?=GetMessage("TELEGRAM")?>" title="<?=GetMessage("TELEGRAM")?>" /> 
		</a>
	<?}?>
	<?if( !empty( $arParams["VIBER"] ) && $arParams["VIBER"] != "\""  ){?>
		<a href="<?=$arParams["VIBER"]?>" target="_blank" class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.optimus/images/viber.png" alt="<?=GetMessage("VIBER")?>" title="<?=GetMessage("VIBER")?>" /> 
		</a>
	<?}?>
	<?if( !empty( $arParams["WHATSAPP"] ) && $arParams["WHATSAPP"] != "\""  ){?>
		<a href="<?=$arParams["WHATSAPP"]?>" target="_blank" class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.optimus/images/whatsapp.png" alt="<?=GetMessage("WHATSAPP")?>" title="<?=GetMessage("WHATSAPP")?>" /> 
		</a>
	<?}?>
	<?if( !empty( $arParams["SKYPE"] ) && $arParams["SKYPE"] != "\""  ){?>
		<a href="<?=$arParams["SKYPE"]?>" target="_blank" class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.optimus/images/skype.png" alt="<?=GetMessage("SKYPE")?>" title="<?=GetMessage("SKYPE")?>" /> 
		</a>
	<?}?>
	<?if( !empty( $arParams["PINTEREST"] ) && $arParams["PINTEREST"] != "\""  ){?>
		<a href="<?=$arParams["PINTEREST"]?>" target="_blank" class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.optimus/images/pinterest.png" alt="<?=GetMessage("PINTEREST")?>" title="<?=GetMessage("PINTEREST")?>" /> 
		</a>
	<?}?>
</div>