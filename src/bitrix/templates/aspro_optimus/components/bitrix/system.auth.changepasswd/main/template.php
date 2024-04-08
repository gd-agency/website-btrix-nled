<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="module-form-block-wr lk-page border_block">
<?
	if(isset($APPLICATION->arAuthResult)) {
		$arResult['ERROR_MESSAGE'] = $APPLICATION->arAuthResult;

		if( !($arResult['ERROR_MESSAGE']['TYPE'] === 'OK' && !empty($_POST['change_pwd'])) )
			ShowMessage($arResult['ERROR_MESSAGE']);

		if( $arResult['ERROR_MESSAGE']['TYPE'] === 'OK' ){
			unset($_SESSION['system.auth.changepasswd']);
		}
	}
	$lastLogin = $arResult['LAST_LOGIN'] ?: (isset($_SESSION['lastLoginSave']) && !empty($_SESSION['lastLoginSave']) ? $_SESSION['lastLoginSave'] : null);
	$bIsLoginEqualEmail = \Bitrix\Main\Config\Option::get('aspro.optimus', 'LOGIN_EQUAL_EMAIL', 'Y', SITE_ID) === 'Y';
	$loginFieldName = $bIsLoginEqualEmail ? "AUTH_EMAIL" : "AUTH_LOGIN";
?>
<?
	if( isset($_POST["LAST_LOGIN"]) && empty( $_POST["LAST_LOGIN"] ) ){
		$arResult["ERRORS"]["LAST_LOGIN"] = GetMessage("REQUIRED_FIELD");
	}
	if( isset($_POST["USER_PASSWORD"]) && strlen( $_POST["USER_PASSWORD"] ) < 6 ){
		$arResult["ERRORS"]["USER_PASSWORD"] = GetMessage("PASSWORD_MIN_LENGTH_2");
	}
	if( isset($_POST["USER_PASSWORD"]) && empty( $_POST["USER_PASSWORD"] ) ){
		$arResult["ERRORS"]["USER_PASSWORD"] = GetMessage("REQUIRED_FIELD");
	}
	if( isset($_POST["USER_CONFIRM_PASSWORD"]) && strlen( $_POST["USER_CONFIRM_PASSWORD"] ) < 6 ){
		$arResult["ERRORS"]["USER_CONFIRM_PASSWORD"] = GetMessage("PASSWORD_MIN_LENGTH_2");
	}
	if( isset($_POST["USER_CONFIRM_PASSWORD"]) && empty( $_POST["USER_CONFIRM_PASSWORD"] ) ){
		$arResult["ERRORS"]["USER_CONFIRM_PASSWORD"] = GetMessage("REQUIRED_FIELD");
	}
	if( $_POST["USER_PASSWORD"] != $_POST["USER_CONFIRM_PASSWORD"] ){
		$arResult["ERRORS"]["USER_CONFIRM_PASSWORD"] = GetMessage("WRONG_PASSWORD_CONFIRM");
	}
	if($arResult['ERROR_MESSAGE'])
		$arResult["ERRORS"] = $arResult['ERROR_MESSAGE'];
	if ($arResult['SHOW_ERRORS'] == 'Y' ){
		ShowMessage($arResult['ERROR_MESSAGE']);?>
		<p><font class="errortext"><?=GetMessage("WRONG_LOGIN_OR_PASSWORD")?></font></p>
	<?}?>
	<? 	
		if( 
			( empty($arResult['ERRORS']) || $arResult['ERROR_MESSAGE']['TYPE'] !== 'ERROR' ) && 
			( !empty($_POST['change_pwd']) || $_POST['TYPE'] ) 
		):
	?>
		<p><?=GetMessage("CHANGE_SUCCESS")?></p>
		<div class="but-r"><a href="/auth/" class="button vbig_btn wides"><span><?=GetMessage("LOGIN")?></span></a></div>
	<? else: ?>
		<script>
		$(document).ready(function(){
			$(".form-block form").validate({
				rules:{
					USER_CONFIRM_PASSWORD: {equalTo: '#pass'},
					<? if( $bIsLoginEqualEmail ): ?>
					USER_LOGIN: {
						email: true,
					}
					<? endif; ?>
				},
				messages:{
					USER_CONFIRM_PASSWORD: {
						equalTo: '<?=GetMessage("PASSWORDS_DONT_MATCH")?>'
					}
				},
				submitHandler: function(form){
					if( $('form[name=bform]').valid() ){
						const eventdata = {
							type: 'form_submit',
							form: form,
							form_name: 'FORGOT',
						}

						BX.onCustomEvent('onSubmitForm', [ eventdata ])
					}
				}
			});
		})
		</script>
		<div class="form-block">
			<form method="post" action="/auth/change-password/" name="bform" class="bf">
				<?if (strlen($arResult["BACKURL"]) > 0): ?><input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" /><?endif;?>
				<input type="hidden" name="AUTH_FORM" value="Y">
				<input type="hidden" name="TYPE" value="CHANGE_PWD">	
				<div class="r form-control">
					<label><?= GetMessage($loginFieldName); ?> <span class="star">*</span></label>
					<input type="text" maxlength="50" value="<?=$lastLogin?>" class="bx-auth-input <?=($_POST && empty($_POST["USER_LOGIN"]) ? "error": '')?>" disabled required />
					<input type="hidden" name="USER_LOGIN" value="<?=$lastLogin?>" />
				</div>
				
				<? if($arResult["USE_PASSWORD"]): ?>
					<div class="r form-control">
						<div class="label_block">
							<label for="USER_CURRENT_PASSWORD"><?=GetMessage("AUTH_CURRENT_PASSWORD")?> <span class="star">*</span></label>
							<input type="password" name="USER_CURRENT_PASSWORD" id="USER_CURRENT_PASSWORD" maxlength="50" required value="<?=$arResult["USER_CURRENT_PASSWORD"]?>" class="form-control bg-color current_password <?=( isset($arResult["ERRORS"]) && array_key_exists( "USER_CURRENT_PASSWORD", $arResult["ERRORS"] ))? "error": ''?>" />
						</div>
					</div>
				<? else: ?>
					<input type="hidden" name="USER_CHECKWORD" maxlength="50" value="<?=$arResult["USER_CHECKWORD"]?>" class="bx-auth-input"  />
				<? endif; ?>

				<div class="form-control">
					<div class="wrap_md">
						<div class="iblock label_block">
							<label><?=GetMessage("AUTH_NEW_PASSWORD_REQ")?> <span class="star">*</span></label>
							<input type="password" name="USER_PASSWORD" maxlength="50" id="pass" required value="<?=$arResult["USER_PASSWORD"]?>" class="bx-auth-input <?=( isset($arResult["ERRORS"]) && array_key_exists( "USER_PASSWORD", $arResult["ERRORS"] ))? "error": ''?>" />
						</div>
						<div class="iblock text_block">
							<?=GetMessage("PASSWORD_MIN_LENGTH")?>
						</div>
					</div>
				</div>	
				<div class="r form-control">
					<label><?=GetMessage("AUTH_NEW_PASSWORD_CONFIRM")?> <span class="star">*</span></label>
					<input type="password" name="USER_CONFIRM_PASSWORD" maxlength="50" required value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>" class="bx-auth-input <?=(isset($arResult["ERRORS"]) && array_key_exists( "USER_CONFIRM_PASSWORD", $arResult["ERRORS"] ))? "error": ''?>"  />
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
				<input type="hidden" name="USER_CHECKWORD" maxlength="50" value="<?=$arResult["USER_CHECKWORD"]?>" class="bx-auth-input"  />
				<div class="form-control">
					<span class="star">*</span>
					<?= GetMessage("FORM_REQUIRED_FIELDS"); ?>
				</div>
				<div class="but-r">
					<button class="button vbig_btn wides" type="submit" name="change_pwd" value="<?=GetMessage("AUTH_CHANGE")?>"><span><?=GetMessage("CHANGE_PASSWORD")?></span></button>				
					<?/*<div class="prompt"><span class="star">*</span> &mdash;&nbsp; <?=GetMessage("REQUIRED_FIELDS")?></div>		
					<div class="clearboth"></div>*/?>
				</div> 		
			</form> 
		</div>
		<script type="text/javascript">document.bform.USER_LOGIN.focus();</script>
	<? endif; ?>
</div>