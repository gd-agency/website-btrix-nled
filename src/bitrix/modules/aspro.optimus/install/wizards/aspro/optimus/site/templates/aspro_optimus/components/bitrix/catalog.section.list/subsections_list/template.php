<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode( true ); ?>
<?
	use \Bitrix\Main\Localization\Loc;
?>
<?if($arResult["SECTIONS"]){?>
<?
	$bBigBlock = $arParams['VIEW_TYPE'] === 'lg';
	$bCompactBlock = $arParams['VIEW_TYPE'] === 'compact';

	// item_block class list
	$itemBlockClasses = "";
	
	if( isset($arParams['VIEW_TYPE']) && $arParams['VIEW_TYPE'] )
		$itemBlockClasses .= " ".$arParams['VIEW_TYPE'];

	if( $bBigBlock ) {
		$itemBlockClasses .= " col-4";
	}elseif( $bCompactBlock ){
		$itemBlockClasses .= " col-3";
	}else{
		$itemBlockClasses .= " col-2";
	}
?>
<div class="catalog_section_list rows_block items section">	
	<?foreach( $arResult["SECTIONS"] as $arItems ){
		$this->AddEditAction($arItems['ID'], $arItems['EDIT_LINK'], CIBlock::GetArrayByID($arItems["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItems['ID'], $arItems['DELETE_LINK'], CIBlock::GetArrayByID($arItems["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
		<div class="item_block<?= $itemBlockClasses; ?>">
			<div class="section_item item" id="<?=$this->GetEditAreaId($arItems['ID']);?>">
				<table class="section_item_inner">	
					<tr>
						<?if ($arParams["SHOW_SECTION_LIST_PICTURES"]=="Y"):?>
							<?
								$collspan = 2;
								$imageSize = $bCompactBlock ? 60 : 120;
							?>
							<td class="image">
								<?if($arItems["PICTURE"]["SRC"]):?>
									<?$img = CFile::ResizeImageGet($arItems["PICTURE"]["ID"], array( "width" => $imageSize, "height" => $imageSize ), BX_RESIZE_IMAGE_EXACT, true );?>
									<a href="<?=$arItems["SECTION_PAGE_URL"]?>" class="thumb"><img src="<?=$img["src"]?>" alt="<?=($arItems["PICTURE"]["ALT"] ? $arItems["PICTURE"]["ALT"] : $arItems["NAME"])?>" title="<?=($arItems["PICTURE"]["TITLE"] ? $arItems["PICTURE"]["TITLE"] : $arItems["NAME"])?>" /></a>
								<?elseif($arItems["~PICTURE"]):?>
									<?$img = CFile::ResizeImageGet($arItems["~PICTURE"], array( "width" => $imageSize, "height" => $imageSize ), BX_RESIZE_IMAGE_EXACT, true );?>
									<a href="<?=$arItems["SECTION_PAGE_URL"]?>" class="thumb"><img src="<?=$img["src"]?>" alt="<?=($arItems["PICTURE"]["ALT"] ? $arItems["PICTURE"]["ALT"] : $arItems["NAME"])?>" title="<?=($arItems["PICTURE"]["TITLE"] ? $arItems["PICTURE"]["TITLE"] : $arItems["NAME"])?>" /></a>
								<?else:?>
									<a href="<?=$arItems["SECTION_PAGE_URL"]?>" class="thumb"><img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=$arItems["NAME"]?>" title="<?=$arItems["NAME"]?>" height="90" /></a>
								<?endif;?>
							</td>
						<?endif;?>
						<td class="section_info">
							<ul>
								<li class="name<?= $bBigBlock ? ' text-center': ''; ?>">
									<a href="<?=$arItems["SECTION_PAGE_URL"]?>"><span><?=$arItems["NAME"]?></span></a> 
									
									<? if( $arItems['ELEMENT_CNT'] ): ?>
										<span class="element-count2 muted"><?= \Aspro\Functions\CAsproOptimus::declOfNum($arItems["ELEMENT_CNT"], array(Loc::getMessage('COUNT_ELEMENTS_TITLE'), Loc::getMessage('COUNT_ELEMENTS_TITLE_2'), Loc::getMessage('COUNT_ELEMENTS_TITLE_3')))?></span>
									<? endif; ?>
								</li>
							</ul>
							<?if($arParams["SECTIONS_LIST_PREVIEW_DESCRIPTION"] != 'N'):?>
								<?$arSection = $section=COptimusCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => COptimusCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "ID" => $arItems["ID"], "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", $arParams["SECTIONS_LIST_PREVIEW_PROPERTY"]));?>
								<div class="desc" ><span class="desc_wrapp">
									<?if ($arSection[$arParams["SECTIONS_LIST_PREVIEW_PROPERTY"]]):?>
										<?=$arSection[$arParams["SECTIONS_LIST_PREVIEW_PROPERTY"]]?>
									<?else:?>
										<?=$arItems["DESCRIPTION"]?>
									<?endif;?>
								</span></div>
							<?endif;?>
						</td>
					</tr>
				</table>
			</div>
		</div>
	<?}?>
</div>
<script>
	/*$(document).ready(function(){
		$('.catalog_section_list.rows_block .item .section_info').sliceHeight();
		$('.catalog_section_list.rows_block .item').sliceHeight();
		setTimeout(function(){
			$(window).resize();
		},100)
	});*/
</script>
<?}?>