<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(isset($APPLICATION->arAuthResult))
	$arResult['ERROR_MESSAGE'] = $APPLICATION->arAuthResult;?>
<?$bEqual = (\Bitrix\Main\Config\Option::get("aspro.optimus", "LOGIN_EQUAL_EMAIL", "Y") == "Y");?>
<div class="border_block">
	<div class="module-form-block-wr lk-page">
		<?ShowMessage($arResult['ERROR_MESSAGE']);?>
		<?if(!isset($arResult['ERROR_MESSAGE']['TYPE']) || $arResult['ERROR_MESSAGE']['TYPE'] != 'OK'):?>
		<div class="form-block">
			<form name="bform" method="post" target="_top" class="bf" action="<?=SITE_DIR?>auth/forgot-password/">
				<?if (strlen($arResult["BACKURL"]) > 0){?><input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" /><?}?>
				<input type="hidden" name="AUTH_FORM" value="Y">
				<input type="hidden" name="TYPE" value="SEND_PWD">
				<?=GetMessage("AUTH_FORGOT_PASSWORD_1")?>
				<br /><br />
				<?
				$name = "AUTH_EMAIL";
				if(!$bEqual)
				{
					$name = "AUTH_LOGIN";
				}?>
				<div class="r form-control">
					<label><?=GetMessage($name);?> <span class="star">*</span></label>
					<?if($bEqual):?>
						<input type="email" name="USER_EMAIL" required="required"  maxlength="255" />
					<?else:?>
						<input type="text" name="USER_LOGIN" required="required"  maxlength="255" />
					<?endif;?>
				</div>
				<?if ($arResult["USE_CAPTCHA"]):?>
					<div class="form-control captcha-row clearfix">
						<label><span><?=GetMessage("FORM_CAPRCHE_TITLE")?>&nbsp;<span class="star">*</span></span></label>
						<div class="captcha_image">
							<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
							<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
							<div class="captcha_reload"></div>
						</div>
						<div class="captcha_input">
							<input type="text" class="inputtext captcha" name="captcha_word" size="30" maxlength="50" value="" required />
						</div>
					</div>
				<?endif?>
				<div class="but-r">
					<button class="button vbig_btn wides" type="submit" name="send_account_info" value=""><span><?=GetMessage("RETRIEVE")?></span></button>
					<?/*<div class="prompt"><span class="star">*</span> &mdash;&nbsp; <?=GetMessage("REQUIRED_FIELDS")?></div>
					<div class="clearboth"></div>*/?>
				</div>
			</form>
		</div>
		<script type="text/javascript">
			<?if(!$bEqual):?>
				document.bform.USER_LOGIN.focus();
			<?else:?>
				document.bform.USER_EMAIL.focus();
			<?endif;?>
		</script>
		<?endif;?>
	</div>
	<script type="text/javascript">
	$(document).ready(function(){
		$('form[name="bform"]').validate({
			highlight: function( element ){
				$(element).parent().addClass('error');
			},
			unhighlight: function( element ){
				$(element).parent().removeClass('error');
			},
			submitHandler: function( form ){
				if( $('form[name=bform]').valid() ){
					var eventdata = {type: 'form_submit', form: form, form_name: 'FORGOT'};
					BX.onCustomEvent('onSubmitForm', [eventdata]);
				}
			},
			errorPlacement: function( error, element ){
				error.insertBefore(element);
			}
		});
	});
	</script>
</div>