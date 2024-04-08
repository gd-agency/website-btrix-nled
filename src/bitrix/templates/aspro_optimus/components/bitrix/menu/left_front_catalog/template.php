<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? 
	use \Bitrix\Main\Config\Option;
	$this->setFrameMode( true );
?>
<?if( !empty( $arResult ) ){
	global $TEMPLATE_OPTIONS;?>
	<?$bIndexBot = (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Lighthouse') !== false);?>
	<?$showImagesInMenu = COption::GetOptionString("aspro.optimus", "HIDE_IMAGES_IN_MENU", 'N', SITE_ID);?>
	<?
	$showImagesInLeftMenu = $TEMPLATE_OPTIONS["LEFT_BLOCK_CATALOG_ICONS"]["CURRENT_VALUE"];
	$bShowImagesInLeftMenu = $showImagesInLeftMenu == 'Y';
	?>
	<div class="menu_top_block catalog_block <?=($bShowImagesInLeftMenu ? 'left_icons' : '')?>">
		<ul class="menu dropdown">
			<?foreach( $arResult as $key => $arItem ){?>
				<?
					// Secure logout
					if( strpos($arItem['LINK'], 'logout') && Option::get('main', 'secure_logout') === 'Y' ){
						$arItem['LINK'] .= "&sessid=".bitrix_sessid();
					}
				?>
				<li class="full <?=($arItem["CHILD"] ? "has-child" : "");?> <?=($arItem["SELECTED"] ? "current opened" : "");?> m_<?=strtolower($TEMPLATE_OPTIONS["MENU_POSITION"]["CURRENT_VALUE"]);?> v_<?=strtolower($TEMPLATE_OPTIONS["MENU_TYPE_VIEW"]["CURRENT_VALUE"]);?>">
					<a class="icons_fa <?=($arItem["CHILD"] ? "parent" : "");?>" href="<?=$arItem["SECTION_PAGE_URL"]?>" >
						<?if($arItem["IMAGES"] && $bShowImagesInLeftMenu){?>
							<span class="image"><img src="<?=$arItem["IMAGES"]["src"];?>" alt="<?=$arItem["NAME"];?>" /></span>
						<?}?>
						<span class="name"><?=$arItem["NAME"]?></span>
						<div class="toggle_block"></div>
					</a>
					<?if($arItem["CHILD"] && !$bIndexBot){?>
						<ul class="dropdown">
							<?foreach($arItem["CHILD"] as $arChildItem){?>
								<li class="<?=($arChildItem["CHILD"] ? "has-childs" : "");?> <?if($arChildItem["SELECTED"]){?> current <?}?> <?=($showImagesInMenu=="Y" ? "hide-images" : "");?>">
									<?if($arChildItem["IMAGES"] && $TEMPLATE_OPTIONS["MENU_TYPE_VIEW"]["CURRENT_VALUE"] != 'BOTTOM' && $showImagesInMenu!=="Y"){?>
										<span class="image"><a href="<?=$arChildItem["SECTION_PAGE_URL"];?>"><img src="<?=$arChildItem["IMAGES"]["src"];?>" alt="<?=$arChildItem["NAME"];?>" /></a></span>
									<?}?>
									<a class="section dark_link" href="<?=$arChildItem["SECTION_PAGE_URL"];?>"><span><?=$arChildItem["NAME"];?></span></a>
									<?if($arChildItem["CHILD"]){?>
										<ul class="dropdown">
											<?foreach($arChildItem["CHILD"] as $arChildItem1){?>
												<li class="menu_item <?if($arChildItem1["SELECTED"]){?> current <?}?>">
													<a class="parent1 section1" href="<?=$arChildItem1["SECTION_PAGE_URL"];?>"><span><?=$arChildItem1["NAME"];?></span></a>
												</li>
											<?}?>
										</ul>
									<?}?>
									<div class="clearfix"></div>
								</li>
							<?}?>
						</ul>
					<?}?>
				</li>
			<?}?>
		</ul>
	</div>
<?}?>