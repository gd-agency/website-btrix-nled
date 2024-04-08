<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?
// get section items count and subsections
$arSectionFilter = COptimus::GetCurrentSectionFilter($arResult["VARIABLES"], $arParams);
$arSection = COptimusCache::CIblockSection_GetList(array("CACHE" => array("TAG" => COptimusCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]), "CACHE_GROUP" => array($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()), "MULTI" => "N")), $arSectionFilter, false, array('ID', 'DESCRIPTION', 'PICTURE', 'DETAIL_PICTURE', 'IBLOCK_ID', 'UF_TOP_SEO'));
$arItemFilter = [];

if($arSection){
	$arItemFilter = COptimus::GetCurrentSectionElementFilter($arResult["VARIABLES"], $arParams);
	$arItemFilter['SECTION_ID'] = $arSection['ID'];
}
$itemsCnt = COptimusCache::CIblockElement_GetList(array("CACHE" => array("TAG" => COptimusCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]), "CACHE_GROUP" => array($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()))), $arItemFilter, array());
COptimus::AddMeta(
	array(
		'og:description' => $arSection['DESCRIPTION'],
		'og:image' => (($arSection['PICTURE'] || $arSection['DETAIL_PICTURE']) ? CFile::GetPath(($arSection['PICTURE'] ? $arSection['PICTURE'] : $arSection['DETAIL_PICTURE'])) : false),
	)
);
$arSubSectionFilter = COptimus::GetCurrentSectionSubSectionFilter($arResult["VARIABLES"], $arParams, $arSection['ID']);
$arSubSections = COptimusCache::CIblockSection_GetList(array("CACHE" => array("TAG" => COptimusCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]), "MULTI" => "Y")), $arSubSectionFilter, false, array("ID", "DEPTH_LEVEL"));
?>
<?if($arParams["USE_RSS"]=="Y"):?>
	<?
		if(method_exists($APPLICATION, 'addheadstring'))
		$APPLICATION->AddHeadString('<link rel="alternate" type="application/rss+xml" title="'.$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["rss"].'" href="'.$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["rss"].'" />');
	?>
	<a href="<?=$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["rss"]?>" target="_blank" title="RSS" class="rss_feed_icon"><?=GetMessage("RSS_TITLE")?></a>
<?endif;?>
<?if ($arParams["SHOW_FAQ_BLOCK"]=="Y"):?>
	<div class="right_side wide">
		<div class="ask_small_block">
			<div class="ask_btn_block">
				<a class="button vbig_btn wides ask_btn"><span><?=GetMessage("ASK_QUESTION")?></span></a>
			</div>
			<div class="description">
				<?$APPLICATION->IncludeFile(SITE_DIR."include/ask_block_description.php", Array(), Array("MODE" => "html", "NAME" => GetMessage("ASK_QUESTION_TEXT"), ));?>
			</div>
		</div>
	</div>
	<div class="left_side wide">
		<? if(isset($arSection['DESCRIPTION']) && strlen($arSection['DESCRIPTION'])): ?>
			<?= $arSection['DESCRIPTION']; ?>
			<hr class="long">
		<? endif; ?>
<?endif;?>
<?
if($arParams["SET_STATUS_404"] == "Y")
{
	if(strpos(CHTTP::GetLastStatus(), "404") !== false)
		CHTTP::SetStatus(200);
}
?>
<?if($arParams["USE_FILTER"]=="Y"){
	$arYears=COptimus::GetYearsItems($arParams["IBLOCK_ID"]);
	arsort($arYears);

	if(isset($arResult["VARIABLES"]["SECTION_ID"]))
	{
		$iYear = $arResult["VARIABLES"]["SECTION_ID"];
		if($arYears && ($iYear && in_array($iYear, $arYears))){?>
			<div class="filter_block border_block">
				<ul>
					<li class="prop <?=( $item["ACTIVE"] == "Y" ? 'active' : '' );?>">
						<a href="<?=$arParams["SEF_FOLDER"]?>"><?=GetMessage("ALL");?></a>
					</li>
					<?foreach( $arYears as $year ){?>
						<li class="prop <?=( $item["ACTIVE"] == "Y" ? 'active' : '' );?>">
							<?if( $iYear == $year ){?>
								<span>
							<?}else{?>
								<a href="<?=$arParams["SEF_FOLDER"]?><?=$year?>/">
							<?}?>
								<?=$year?>
							<?if( $iYear == $year ){?>
								</span>
							<?}else{?>
								</a>
							<?}?>
						</li>
					<?}?>
				</ul>
				<div class="cls"></div>
			</div>
			<?$GLOBALS['arrFilter'] = array(
				">DATE_ACTIVE_FROM" => ConvertDateTime("01.01.".$iYear, FORMAT_DATETIME),
				"<=DATE_ACTIVE_FROM" => ConvertDateTime("01.01.".($iYear+1), FORMAT_DATETIME),
			);?>
			<?unset($arResult["VARIABLES"]["SECTION_ID"]);?>
		<?}
		else
		{
			echo '<p><font class="errortext">'.GetMessage('SECTION_NOT_FOUND').'</font></p>';
			CHTTP::SetStatus(404);
			return;
		}?>
	<?}?>
<?}?>

<?if($arSubSections):?>
	<?// sections list?>
	<?@include_once('page_blocks/section_1.php');?>
<?endif;?>
<?// section elements?>
<?if(strlen($arParams["FILTER_NAME"])):?>
	<?$arTmpFilter = $GLOBALS[$arParams["FILTER_NAME"]];?>
	<?$GLOBALS[$arParams["FILTER_NAME"]] = array_merge((array)$GLOBALS[$arParams["FILTER_NAME"]], $arItemFilter);?>
<?else:?>
	<?$arParams["FILTER_NAME"] = "arrFilterServ";?>
	<?$GLOBALS[$arParams["FILTER_NAME"]] = $arItemFilter;?>
<?endif;?>
<?@include_once('page_blocks/list_elements_1.php');?>

<?if ($arParams["SHOW_FAQ_BLOCK"]=="Y"):?></div><?endif;?>