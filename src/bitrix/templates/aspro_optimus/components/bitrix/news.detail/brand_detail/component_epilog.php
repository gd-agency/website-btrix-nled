<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?=$GLOBALS['sectionsContent']?>
<?if ($arParams["SHOW_LINKED_PRODUCTS"] == "Y" && strlen($arParams["LINKED_PRODUCTS_PROPERTY"])):?>
	<?IncludeTemplateLangFile(__FILE__);?>
	<?
	$iblockID = \Bitrix\Main\Config\Option::get("aspro.optimus", "CATALOG_IBLOCK_ID", COptimusCache::$arIBlocks[SITE_ID]["aspro_optimus_catalog"]["aspro_optimus_catalog"][0]);
	$arItems = COptimusCache::CIBLockElement_GetList(array('CACHE' => array("MULTI" =>"Y", "TAG" => COptimusCache::GetIBlockCacheTag($iblockID))), array("IBLOCK_ID" => $iblockID, "ACTIVE"=>"Y", "PROPERTY_".$arParams["LINKED_PRODUCTS_PROPERTY"] => $arResult["ID"] ), false, false, array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID"));
	$arParams["DEPTH_LEVEL_BRAND"] = ($arParams["DEPTH_LEVEL_BRAND"] ? $arParams["DEPTH_LEVEL_BRAND"] : 3);
	$bFilter = $arParams['SHOW_LINKED_PRODUCTS'] === 'Y' && $arParams['SHOW_FILTER_LEFT'] === 'Y';
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
					<h3><?=GetMessage("BRAND_PRODUCTS_SECTION", Array ("#BRAND_NAME#" => $arResult["NAME"]));?></h3>
					<?$GLOBALS["arBrandSections"] = array("ID" => $arSectionsID);?>
					<?include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/news.detail.products_section.php');?>
				</div>
			<?endif;?>
		<?endif;?>
		<?if($arItems):?>
			<hr class="long"/>
			<div class="similar_products_wrapp">
				<h3><?=GetMessage("BRAND_PRODUCTS", Array ("#BRAND_NAME#" => $arResult["NAME"]));?></h3>
				<?
				if($GLOBALS[$arParams["CATALOG_FILTER_NAME"]]){
					$GLOBALS[$arParams["CATALOG_FILTER_NAME"]] = array_merge($GLOBALS[$arParams["CATALOG_FILTER_NAME"]], array( "PROPERTY_".$arParams["LINKED_PRODUCTS_PROPERTY"] => $arResult["ID"] ));
				}
				else{
					$GLOBALS[$arParams["CATALOG_FILTER_NAME"]] = array( "PROPERTY_".$arParams["LINKED_PRODUCTS_PROPERTY"] => $arResult["ID"] );
				}

				$list_view = ($arParams['LIST_VIEW'] ? $arParams['LIST_VIEW'] : 'slider');
				//$bAjaxFilter = ((isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") && (isset($_GET["ajax_get"]) && $_GET["ajax_get"] === "Y"));
				//$bAjax = $bAjaxFilter;
				$bAjaxFilter = ((isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") && (isset($_GET["ajax_get"]) && $_GET["ajax_get"] === "Y"));
				$bAjax = ((isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") && $_GET["ajax_get_filter"] !== "Y" && (isset($_GET["ajax_get"]) && $_GET["ajax_get"] === "Y"));
				
				?>
				<div class="wraps goods-block with-padding block catalog vertical">
					<div class="js_filter filter_horizontal">
						<div class="bx_filter bx_filter_vertical"></div>
					</div>

					<?if($bFilter):?>
						<div class="adaptive_filter">
							<a class="filter_opener"><i></i><span><?=GetMessage("CATALOG_SMART_FILTER_TITLE")?></span></a>
						</div>
						<script type="text/javascript">
						$(".filter_opener").click(function(){
							checkVerticalMobileFilter();
							$(this).toggleClass("opened");
							$(".bx_filter_vertical, .bx_filter").slideToggle(333);
						});
						</script>
					<?endif;?>

					<?if($bAjaxFilter):?>
						<?$APPLICATION->RestartBuffer();?>
					<?else:?>
						<?$arTransferParams = array(
							"SHOW_ABSENT" => $arParams["SHOW_ABSENT"],
							"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
							"PRICE_CODE" => $arParams["PRICE_CODE"],
							"OFFER_TREE_PROPS" => $arParams["OFFER_TREE_PROPS"],
							"OFFER_SHOW_PREVIEW_PICTURE_PROPS" => $arParams["OFFER_SHOW_PREVIEW_PICTURE_PROPS"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
							"CURRENCY_ID" => $arParams["CURRENCY_ID"],
							"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
							"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
							"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
							"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
							"LIST_OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
							"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
							"LIST_OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
							"SHOW_DISCOUNT_TIME" => $arParams["SHOW_DISCOUNT_TIME"],
							"SHOW_COUNTER_LIST" => $arParams["SHOW_COUNTER_LIST"],
							"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
							"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
							"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
							"SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"],
							"SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"],
							"SHOW_DISCOUNT_PERCENT_NUMBER" => $arParams["SHOW_DISCOUNT_PERCENT_NUMBER"],
							"USE_REGION" => $arParams["USE_REGION"],
							"STORES" => $arParams["STORES"],
							"DEFAULT_COUNT" => $arParams["DEFAULT_COUNT"],
							"BASKET_URL" => $arParams["BASKET_URL"],
							"SHOW_GALLERY" => $arParams["SHOW_GALLERY"],
							"MAX_GALLERY_ITEMS" => $arParams["MAX_GALLERY_ITEMS"],
							"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
							"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
							"PARTIAL_PRODUCT_PROPERTIES" => $arParams["PARTIAL_PRODUCT_PROPERTIES"],
							"ADD_PROPERTIES_TO_BASKET" => $arParams["ADD_PROPERTIES_TO_BASKET"],
							"SHOW_ONE_CLICK_BUY" => $arParams["SHOW_ONE_CLICK_BUY"],
							"SHOW_DISCOUNT_TIME_EACH_SKU" => $arParams["SHOW_DISCOUNT_TIME_EACH_SKU"],
							"SHOW_ARTICLE_SKU" => $arParams["SHOW_ARTICLE_SKU"],
							"ADD_PICT_PROP" => $arParams["ADD_PICT_PROP"],
							"ADD_DETAIL_TO_SLIDER" => $arParams["DETAIL_ADD_DETAIL_TO_SLIDER"],
							"OFFER_ADD_PICT_PROP" => $arParams["OFFER_ADD_PICT_PROP"],
							"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
							"IBINHERIT_TEMPLATES" => $arSeoItem ? $arIBInheritTemplates : array(),
							"DISPLAY_WISH_BUTTONS" => $arParams["DISPLAY_WISH_BUTTONS"],
						);?>

						<div class="js-wrapper-block" data-params='<?=str_replace('\'', '"', CUtil::PhpToJSObject($arTransferParams, false))?>'>
							<div id="right_block_ajax_wrapper" class="ajax_load cur clear" data-code="block">
					<?endif;?>
						    
					<?include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/news.detail.products_'.$list_view.'.php');?>

					<?if($bAjaxFilter):?>
						<?die();?>
					<?else:?>
							</div>
						</div>
					<?endif;?>
				</div>
			</div>
		<?endif;?>
	<?}?>
<?endif;?>
<?if ($arParams["SHOW_BACK_LINK"]=="Y"):?>
	<?$refer=$_SERVER['HTTP_REFERER'];
	if (strpos($refer, $arResult["LIST_PAGE_URL"])!==false) {?>
		<div class="back"><a class="back" href="javascript:history.back();"><span><?=GetMessage("BACK");?></span></a></div>
	<?}else{?>
		<div class="back"><a class="back" href="<?=$arResult["LIST_PAGE_URL"]?>"><span><?=GetMessage("BACK");?></span></a></div>
	<?}?>
<?endif;?>