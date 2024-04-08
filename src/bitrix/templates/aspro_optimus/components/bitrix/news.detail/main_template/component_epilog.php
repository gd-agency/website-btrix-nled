<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
	<?if($arParams["SHOW_LINKED_PRODUCTS"]=="Y" && $arResult["DISPLAY_PROPERTIES"][$arParams["LINKED_PRODUCTS_PROPERTY"]]["VALUE"]):?>
		<?

		if(is_array($arParams["LIST_OFFERS_FIELD_CODE"])){
			if(!in_array('DETAIL_PAGE_URL', $arParams["LIST_OFFERS_FIELD_CODE"])){
				$arParams["LIST_OFFERS_FIELD_CODE"][] = 'DETAIL_PAGE_URL';
			}
		} else {
			$arParams["LIST_OFFERS_FIELD_CODE"] = array("DETAIL_PAGE_URL");
		}

		$iblockID = \Bitrix\Main\Config\Option::get("aspro.optimus", "CATALOG_IBLOCK_ID", COptimusCache::$arIBlocks[SITE_ID]["aspro_optimus_catalog"]["aspro_optimus_catalog"][0]);
		$arItems = COptimusCache::CIBLockElement_GetList(array('CACHE' => array("MULTI" =>"Y", "TAG" => COptimusCache::GetIBlockCacheTag($iblockID))), array("IBLOCK_ID" => $iblockID, "ACTIVE"=>"Y", "ID" => $arResult["DISPLAY_PROPERTIES"][$arParams["LINKED_PRODUCTS_PROPERTY"]]["VALUE"] ), false, false, array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID"));
		$arParams["DEPTH_LEVEL_BRAND"] = ($arParams["DEPTH_LEVEL_BRAND"] ? $arParams["DEPTH_LEVEL_BRAND"] : 3);
		if($arItems)
		{
			$arSectionsID = array();
			foreach($arItems as $arItem)
			{
				if($arItem["IBLOCK_SECTION_ID"])
				{
					if(is_array($arItem["IBLOCK_SECTION_ID"]))
						$arSectionsID = array_merge($arSectionsID, $arItem["IBLOCK_SECTION_ID"]);
					else
						$arSectionsID[] = $arItem["IBLOCK_SECTION_ID"];
				}
			}
			if($arSectionsID)
				$arSectionsID = array_unique($arSectionsID);?>
			<?if($arSectionsID):?>
				<?if($arParams["SHOW_ITEM_SECTION"] == "Y"):?>
					<div class="sections_wrapper">
						<hr class="long"/>
						<h3><?=GetMessage("ACTION_PRODUCTS_SECTION");?></h3>
						<?$GLOBALS["arBrandSections"] = array("ID" => $arSectionsID);?>
						<?include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/news.detail.products_section.php');?>
					</div>
				<?endif;?>
			<?endif;?>
			<?if($arItems):?>
				<hr class="long"/>
				<div class="similar_products_wrapp main_temp clearfix">
					<?if(CSite::InDir(SITE_DIR."sale")){?>
						<h3><?=GetMessage("ACTION_PRODUCTS");?></h3>
					<?}else{?>
						<h3><?=GetMessage("ACTION_PRODUCTS_LINK");?></h3>
					<?}?>
					<?if(!$arParams["CATALOG_FILTER_NAME"]){
						$arParams["CATALOG_FILTER_NAME"]="arrProductsFilter";
					}?>
					<div class="module-products-corusel product-list-items catalog">
						<?$GLOBALS[$arParams["CATALOG_FILTER_NAME"]] = array("ID" => $arResult["DISPLAY_PROPERTIES"][$arParams["LINKED_PRODUCTS_PROPERTY"]]["VALUE"] );?>
						<?$list_view = ($arParams['LIST_VIEW'] ? $arParams['LIST_VIEW'] : 'slider');?>

						<div class="wraps goods-block with-padding block ajax_load catalog">
							<?$bAjax = ((isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest")  && (isset($_GET["ajax_get"]) && $_GET["ajax_get"] == "Y"));?>
							<?if($bAjax):?>
								<?$APPLICATION->RestartBuffer();?>
							<?endif;?>

							<?include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/news.detail.products_'.$list_view.'.php');?>

							<?if($bAjax):?>
								<?die();?>
							<?endif;?>
						</div>
					</div>
				</div>
			<?endif;?>
		<?}?>
	<?endif;?>
	<?if ($arParams["SHOW_SERVICES_BLOCK"]=="Y"):?>
		<div class="ask_big_block">
			<div class="ask_btn_block">
				<a class="button vbig_btn wides services_btn" data-title="<?=$arResult["NAME"];?>"><span><?=GetMessage("SERVICES_CALL")?></span></a>
			</div>
			<div class="description">
				<div class="desc">
					<?$APPLICATION->IncludeFile(SITE_DIR."include/services_block_description.php", Array(), Array("MODE" => "html", "NAME" => GetMessage("ASK_QUESTION_DETAIL_TEXT"), ));?>
				</div>
				<div class="price services">
					<div class="price_new">
						<?=$templateData['PRICE'];?>
					</div>
				</div>
			</div>
			<div class="clear"></div>
		</div>
	<?endif;?>
<?if ($arParams["SHOW_BACK_LINK"]=="Y"):?>
	<?$refer=$_SERVER['HTTP_REFERER'];
	if (strpos($refer, $arResult["LIST_PAGE_URL"])!==false) {?>
		<div class="back"><a class="back" href="javascript:history.back();"><span><?=GetMessage("BACK");?></span></a></div>
	<?}else{?>
		<div class="back"><a class="back" href="<?=$arResult["LIST_PAGE_URL"]?>"><span><?=GetMessage("BACK");?></span></a></div>
	<?}?>
<?endif;?>