<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контакты");
?>
<div class="contacts_map">
	<?
		$arMapTypes = ['YANDEX', 'GOOGLE'];
		$sMapType = $arMapTypes[\Bitrix\Main\Config\Option::get("aspro.optimus", "MAP_TYPE", "0", SITE_ID)];
	?>
	<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", 
		array(
			"COMPONENT_TEMPLATE" => ".default",
			"PATH" => SITE_DIR."include/contacts-map-".strtolower($sMapType).".php",
			"AREA_FILE_SHOW" => "file",
			"AREA_FILE_SUFFIX" => "",
			"AREA_FILE_RECURSIVE" => "Y",
			"EDIT_TEMPLATE" => "standard.php"
		),
		false
	);?>
</div>
<div class="wrapper_inner">
	<div class="contacts_left">
		<div class="store_description">
			<div class="store_property">
				<div class="title">Адрес</div>
				<div class="value">
					<?$APPLICATION->IncludeFile(SITE_DIR."include/address.php", Array(), Array("MODE" => "html", "NAME" => "Адрес"));?>
				</div>
			</div>
			<div class="store_property">
				<div class="title">Телефон</div>
				<div class="value">
					<?$APPLICATION->IncludeFile(SITE_DIR."include/contact_phone.php", Array(), Array("MODE" => "html", "NAME" => "Телефон"));?>
				</div>
			</div>
			<div class="store_property">
				<div class="title">Email</div>
				<div class="value">
					<?$APPLICATION->IncludeFile(SITE_DIR."include/email.php", Array(), Array("MODE" => "html", "NAME" => "Email"));?>
				</div>
			</div>
			<div class="store_property">
				<div class="title">Режим работы</div>
				<div class="value">
					<?$APPLICATION->IncludeFile(SITE_DIR."include/schedule.php", Array(), Array("MODE" => "html", "NAME" => "Время работы"));?>
				</div>
			</div>
		</div>
	</div>
	<div class="contacts_right">
		<blockquote><?$APPLICATION->IncludeFile(SITE_DIR."include/contacts_text.php", Array(), Array("MODE" => "html", "NAME" => GetMessage("CONTACTS_TEXT")));?></blockquote>
		<?Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("form-feedback-block");?>
		<?$APPLICATION->IncludeComponent("bitrix:form.result.new", "inline",
			Array(
				"WEB_FORM_ID" => "#FORM_FEEDBACK_ID#",
				"IGNORE_CUSTOM_TEMPLATE" => "N",
				"USE_EXTENDED_ERRORS" => "Y",
				"SEF_MODE" => "N",
				"CACHE_TYPE" => "A",
				"CACHE_TIME" => "3600000",
				"LIST_URL" => "",
				"EDIT_URL" => "",
				"SUCCESS_URL" => "?send=ok",
				"CHAIN_ITEM_TEXT" => "",
				"CHAIN_ITEM_LINK" => "",
				"VARIABLE_ALIASES" => Array(
					"WEB_FORM_ID" => "WEB_FORM_ID",
					"RESULT_ID" => "RESULT_ID"
				)
			)
		);?>
		<?Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("form-feedback-block", "");?>
	</div>
</div>
<div class="clearboth"></div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>