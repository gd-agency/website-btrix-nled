<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?if( count( $arResult["ITEMS"] ) >= 1 ){?>
	<div class="viewed_block">
		<div class="title_block"><?=($arParams["TITLE_BLOCK"] ? $arParams["TITLE_BLOCK"] : GetMessage("TITLE_BLOCK_NAME"))?></div>
		<?if($fast_view_text_tmp = \Bitrix\Main\Config\Option::get('aspro.optimus', 'EXPRESSION_FOR_FAST_VIEW', GetMessage('FAST_VIEW')))
			$fast_view_text = $fast_view_text_tmp;
		else
			$fast_view_text = GetMessage('FAST_VIEW');?>
		<div class="outer_wrap flexslider shadow items border custom_flex viewed" data-plugin-options='{"animation": "slide", "directionNav": true, "itemMargin":10, "controlNav" :false, "animationLoop": true, "slideshow": false, "counts": [8,4,3,2,1]}'>
			<ul class="rows_block items slides">
				<?foreach($arResult["ITEMS"] as $arItem){
					$isItem=(isset($arItem['ID']) ? true : false);?>
					<li class="item_block">
						<div class="item_wrap item <?=($isItem ? 'has-item' : '' );?>" <?=($isItem ? "id='".$this->GetEditAreaId($arItem['ID'])."'" : "")?>>
							<?if($isItem){?>
								<?$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
								$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));

								$item_id = $arItem["ID"];
								$strMeasure = '';
								if($arParams["SHOW_MEASURE"] == "Y" && $arItem["CATALOG_MEASURE"]){
									$arMeasure = CCatalogMeasure::getList(array(), array("ID" => $arItem["CATALOG_MEASURE"]), false, false, array())->GetNext();
									$strMeasure = $arMeasure["SYMBOL_RUS"];
								}?>
								<div class="inner_wrap">
									<div class="image_wrapper_block">
										<div class="wrapper_fw">
											<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="thumb">								
												<?
												$a_alt=($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $arItem["NAME"] );
												$a_title=($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] : $arItem["NAME"] );
												?>
												<?if( !empty($arItem["PREVIEW_PICTURE"]) ):?>
													<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
												<?elseif( !empty($arItem["DETAIL_PICTURE"])):?>
													<?$img = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], array( "width" => 100, "height" => 90 ), BX_RESIZE_IMAGE_PROPORTIONAL,true );?>
													<img src="<?=$img["src"]?>" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
												<?else:?>
													<img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
												<?endif;?>
											</a>
											<div class="fast_view_block" data-event="jqm" data-param-form_id="fast_view" data-param-iblock_id="<?=$arParams["IBLOCK_ID"];?>" data-param-id="<?=$arItem["ID"];?>" data-param-item_href="<?=urlencode($arItem["DETAIL_PAGE_URL"]);?>" data-name="fast_view"><?=$fast_view_text;?></div>
										</div>
									</div>
									<div class="item_info">
										<div class="item-title">
											<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="dark_link"><span><?=$arItem["NAME"]?></span></a>
										</div>
										<div class="cost prices clearfix ">
											<?if( $arItem["OFFERS"]){?>
												<?$minPrice = false;
												if (isset($arItem['MIN_PRICE']) || isset($arItem['RATIO_PRICE'])){
													// $minPrice = (isset($arItem['RATIO_PRICE']) ? $arItem['RATIO_PRICE'] : $arItem['MIN_PRICE']);
													$minPrice = $arItem['MIN_PRICE'];
												}
												$offer_id=0;
												if($arParams["TYPE_SKU"]=="N"){
													$offer_id=$minPrice["MIN_ITEM_ID"];
												}
												$min_price_id=$minPrice["MIN_PRICE_ID"];
												if(!$min_price_id)
													$min_price_id=$minPrice["PRICE_ID"];
												if($minPrice["MIN_ITEM_ID"])
													$item_id=$minPrice["MIN_ITEM_ID"];
												$prefix = '';
												if('N' == $arParams['TYPE_SKU'] || $arParams['DISPLAY_TYPE'] !== 'block'){
													$prefix = GetMessage("CATALOG_FROM");
												}?>
												<div class="price only_price">
													<?if(strlen($minPrice["PRINT_VALUE"])):?>
														<?=$prefix;?> <span class="values_wrapper"><?=($minPrice['PRINT_DISCOUNT_VALUE'] ? $minPrice['PRINT_DISCOUNT_VALUE'] : $minPrice['PRINT_VALUE']);?></span><?if (($arParams["SHOW_MEASURE"]=="Y") && $strMeasure){?><span class="measure">/<?=$strMeasure?></span><?}?>
													<?endif;?>
												</div>
											<?}elseif ( $arItem["PRICES"] ){?>
												<? $arCountPricesCanAccess = 0;
												$min_price_id=0;												
												foreach( $arItem["PRICES"] as $key => $arPrice ) { if($arPrice["CAN_ACCESS"]){$arCountPricesCanAccess++;} } ?>
												<?foreach($arItem["PRICES"] as $key => $arPrice){?>
													<?if($arPrice["CAN_ACCESS"]){
														$percent=0;
														if($arPrice["MIN_PRICE"]=="Y"){
															$min_price_id=$arPrice["PRICE_ID"];
														}?>
														<?$price = CPrice::GetByID($arPrice["ID"]);?>
														<?if($arCountPricesCanAccess > 1):?>
															<div class="price_name"><?=$price["CATALOG_GROUP_NAME"];?></div>
														<?endif;?>
														<div class="price only_price">
															<?if(strlen($arPrice["PRINT_VALUE"])):?>
																<span class="values_wrapper"><?=\Aspro\Functions\CAsproOptimusItem::getCurrentPrice("DISCOUNT_VALUE", $arPrice);?></span><?if (($arParams["SHOW_MEASURE"]=="Y") && $strMeasure){?><span class="measure">/<?=$strMeasure?></span><?}?>
															<?endif;?>
														</div>
													<?}?>
												<?}?>
											<?}?>
										</div>
									</div>
								</div>
							<?}?>
						</div>
					</li>
				<?}?>
			</ul>
		</div>
	</div>
	<script>
		//$('.viewed_block .rows_block .item .item-title').dotdotdot();
		$('.viewed_block .rows_block .item .item-title').sliceHeightNoResize({outer:true, slice:8, autoslicecount:false});
		$('.viewed_block .rows_block .item').sliceHeightNoResize({outer:true, slice:8, autoslicecount:false});
	</script>
<?}?>
