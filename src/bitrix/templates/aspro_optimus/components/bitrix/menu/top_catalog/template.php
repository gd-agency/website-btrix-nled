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

	<ul class="menu top menu_top_block catalogfirst <?=($bShowImagesInLeftMenu ? 'left_icons' : '')?>">
		<?foreach( $arResult as $key => $arItem ){?>
			<?
				// Secure logout
				if( strpos($arItem['LINK'], 'logout') && Option::get('main', 'secure_logout') === 'Y' ){
					$arItem['LINK'] .= "&sessid=".bitrix_sessid();
				}
			?>
			<li class="catalog icons_fa <?=($arItem["CHILD"] ? "has-child" : "");?> <?=($arItem["SELECTED"] ? "current" : "");?>">
				<a class="<?=($arItem["CHILD"] ? "parent" : "");?>" href="<?=$arItem["LINK"]?>" ><?=$arItem["TEXT"]?></a>
				<?if($arItem["CHILD"] && !$bIndexBot){?>
					<ul class="dropdown">
					<?
					$nloMenu = ( COptimus::IsMainPage() || COptimus::IsCatalogPage() ) && $TEMPLATE_OPTIONS['MENU_POSITION_MAIN']['CURRENT_VALUE'] == 'SHOW';
					?>
						<?if($nloMenu && COptimus::nlo('menu-catalog') || !$nloMenu):?>
							<?if($nloMenu) {?>
								<!-- noindex -->
							<?}?>
							<?foreach($arItem["CHILD"] as $arChildItem){?>
								<li class="full <?=($arChildItem["CHILD"] ? "has-child" : "");?> <?if($arChildItem["SELECTED"]){?> current opened <?}?> m_<?=strtolower($TEMPLATE_OPTIONS["MENU_POSITION"]["CURRENT_VALUE"]);?> v_<?=strtolower($TEMPLATE_OPTIONS["MENU_TYPE_VIEW"]["CURRENT_VALUE"]);?>">
									<a class="icons_fa <?=($arChildItem["CHILD"] ? "parent" : "");?>" href="<?=$arChildItem["SECTION_PAGE_URL"];?>">
										<?if($arChildItem["IMAGES"] && $bShowImagesInLeftMenu){?>
											<span class="image"><img src="<?=$arChildItem["IMAGES"]["src"];?>" alt="<?=$arChildItem["NAME"];?>" /></span>
										<?}?>
										<span class="name"><?=$arChildItem["NAME"];?></span>
										<div class="toggle_block"></div></a>
									<?if($arChildItem["CHILD"]){?>
										<ul class="dropdown">
											<?foreach($arChildItem["CHILD"] as $arChildItem1){?>
												<li class="menu_item <?if($arChildItem1["SELECTED"]){?> current <?}?> <?=($showImagesInMenu=="Y" ? "hide-images" : "");?>">
													<?if($arChildItem1["IMAGES"] && $TEMPLATE_OPTIONS["MENU_TYPE_VIEW"]["CURRENT_VALUE"] != 'BOTTOM' && $showImagesInMenu!=="Y"){?>
														<span class="image"><a href="<?=$arChildItem1["SECTION_PAGE_URL"];?>"><img src="<?=$arChildItem1["IMAGES"]["src"];?>" alt="<?=$arChildItem1["NAME"];?>"/></a></span>
													<?}?>
													<a class="section dark_link" href="<?=$arChildItem1["SECTION_PAGE_URL"];?>"><span><?=$arChildItem1["NAME"];?></span></a>
													<?if($arChildItem1["CHILD"]){?>
														<ul class="dropdown">
															<?foreach($arChildItem1["CHILD"] as $arChildItem2){?>
																<li class="menu_item <?if($arChildItem2["SELECTED"]){?> current <?}?>">
																	<a class="section1" href="<?=$arChildItem2["SECTION_PAGE_URL"];?>"><span><?=$arChildItem2["NAME"];?></span></a>
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
							<?if($nloMenu) {?>
								<!-- /noindex -->
							<?}?>
						<?endif;?>
						<?if($nloMenu) {
							COptimus::nlo('menu-catalog');
						}?>
					</ul>
				<?}?>
			</li>
		<?}?>
	</ul>
<?}?>