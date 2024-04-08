<?
global $arTheme, $arRegion;
$logoClass = ($arTheme['COLORED_LOGO']['VALUE'] !== 'Y' ? '' : ' colored');
?>
<div class="wrapper_inner">
	<div class="logo-row row margin0">
		<div class="col">
			<div class="inner-table-block sep-left nopadding logo-block">
				<div class="logo<?=$logoClass?>">
					<? COptimus::ShowLogo(); ?>
				</div>
			</div>
		</div>
		<div class="col">
			<div class="inner-table-block menu-block rows sep-left">
				<div class="title" >
					<?= \COptimus::showIconSvg([
						'CLASS_ICON' => "svg-burger", 
						'PATH' => SITE_TEMPLATE_PATH."/images/svg/burger_black.svg"
					]); ?>
					<span class="menu-block__title-text"><?= mb_strtoupper(GetMessage("S_MOBILE_MENU")); ?></span><i class="fa fa-angle-down"></i>
				</div>
				<div class="navs table-menu js-nav">
					<? if( COptimus::nlo('menu-fixed') ): ?>
						<!-- noindex -->
						<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
							array(
								"COMPONENT_TEMPLATE" => ".default",
								"PATH" => SITE_DIR."include/menu/fixed/menu.top_fixed_field.php",
								"AREA_FILE_SHOW" => "file",
								"AREA_FILE_SUFFIX" => "",
								"AREA_FILE_RECURSIVE" => "Y",
								"EDIT_TEMPLATE" => "include_area.php"
							),
							false, array("HIDE_ICONS" => "Y")
						);?>
						<!-- /noindex -->
					<? endif; ?>
					<? COptimus::nlo('menu-fixed'); ?>
				</div>
			</div>
		</div>

		<div class="nopadding hidden-sm hidden-xs search animation-width pull-right-flex">
			<div class="inner-table-block">
				<div class="search">
					<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
						array(
							"COMPONENT_TEMPLATE" => ".default",
							"PATH" => SITE_DIR."include/top_page/fixed/search.title.catalog_fixed.php",
							"AREA_FILE_SHOW" => "file",
							"AREA_FILE_SUFFIX" => "",
							"AREA_FILE_RECURSIVE" => "Y",
							"EDIT_TEMPLATE" => "standard.php"
						),
						false
					);?>
				</div>
			</div>
		</div>
		<?
			$phonesBlock = "";
			ob_start();
			$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
				array(
					"COMPONENT_TEMPLATE" => ".default",
					"PATH" => SITE_DIR."include/phone.php",
					"AREA_FILE_SHOW" => "file",
					"AREA_FILE_SUFFIX" => "",
					"AREA_FILE_RECURSIVE" => "Y",
					"EDIT_TEMPLATE" => "standard.php"
				),
				false
			);
			$phonesBlock = trim(ob_get_contents());
			ob_end_clean();
		?>
		<? if( $phonesBlock ): ?>
			<div class="col logo_and_menu-row">
				<div class="inner-table-block inner-table-block__phones">
					<div class="middle_phone">
						<div class="phones">
							<span class="phone_wrap">
								<span class="phone">
									<span class="icons fa fa-phone"></span>
									<span class="phone_text">
										<?= $phonesBlock; ?>
									</span>
								</span>
							</span>
						</div>
					</div>
				</div>
			</div>
		<? endif; ?>

		<? if($TEMPLATE_OPTIONS["BASKET"]["CURRENT_VALUE"] === "NORMAL"): ?>
			<div class="col">
				<div class="inner-table-block inner-table-block__icons">
					<div class="basket_wrapp">
						<div class="wrapp_all_icons page-header__fixed__icons">
							<?= \Aspro\Functions\CAsproOptimus::ShowCabinetLink(true, false, 'big');?>
							<?= COptimus::ShowBasketWithCompareLink('', 'big', false, false, true); ?>
						</div>
					</div>
				</div>
			</div>
		<? else: ?>
			<div class="col pull-right-flex">
				<div class="inner-table-block inner-table-block__callback nopadding">
					<span class="order_wrap_btn button transparent">
						<span class="callback_btn"><?=GetMessage("CALLBACK")?></span>
					</span>
				</div>
			</div>
		<? endif; ?>
	</div>
</div>