<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?
	// get section items count and subsections
	$arItemFilter = COptimus::GetCurrentSectionElementFilter($arResult["VARIABLES"], $arParams, false);
	$arSubSectionFilter = COptimus::GetCurrentSectionSubSectionFilter($arResult["VARIABLES"], $arParams, false);
	$itemsCnt = COptimusCache::CIBlockElement_GetList(array("CACHE" => array("TAG" => COptimusCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]), "CACHE_GROUP" => array($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()))), $arItemFilter, array());
	$arSubSections = COptimusCache::CIBlockSection_GetList(array("CACHE" => array("TAG" => COptimusCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]), "MULTI" => "Y", "CACHE_GROUP" => array($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()))), $arSubSectionFilter, false, array("ID"));
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
<?endif;?>

<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
	"AREA_FILE_SHOW" => "page",
	"AREA_FILE_SUFFIX" => "inc",
	"EDIT_TEMPLATE" => "standard.php"
	),
	false
);?>
<?
	if($arParams["USE_FILTER"]=="Y"){
		$arYears=COptimus::GetYearsItems($arParams["IBLOCK_ID"]);
		arsort($arYears);
		$bGetYear = ($arParams["GET_YEAR"] == "Y");
		if($arYears){
			$bHasYear = (isset($_GET['year']) && (int)$_GET['year']);
			$year = ($bHasYear ? (int)$_GET['year'] : 0);
			?>
			<div class="filter_block border_block">
				<ul>
					<li class="prop <?=($bHasYear ? '' : 'active');?>">
						<?if($bHasYear):?>
							<a class="btn-inline black" href="<?=$arResult['FOLDER'];?>"><?=GetMessage('ALL');?></a>
						<?else:?>
							<span class="btn-inline black"><?=GetMessage('ALL');?></span>
						<?endif;?>
					</li>
					<?if(!$bGetYear):?>
						<?foreach($arYears as $value):?>
							<li class="prop">
								<a href="<?=$arParams["SEF_FOLDER"]?><?=$value?>/"><?=$value?></a>
							</li>
						<?endforeach;?>
					<?else:?>
						<?foreach( $arYears as $value ):?>
							<?$bSelected = ($bHasYear && $value == $year);?>
							<li class="prop <?=($bSelected ? 'active' : '');?>">
								<?if($bSelected):?>
									<span class="btn-inline black"><?=$value;?></span>
								<?else:?>
									<a class="btn-inline black" href="<?=$APPLICATION->GetCurPageParam('year='.$value, array('year'));?>"><?=$value;?></a>
								<?endif;?>
							</li>
						<?endforeach;?>
					<?endif;?>
				</ul>
				<div class="cls"></div>
			</div>
			<?
			if($bHasYear && $bGetYear){
				$GLOBALS[$arParams["FILTER_NAME"]][] = array(
					">=DATE_ACTIVE_FROM" => ConvertDateTime("01.01.".$year, "DD.MM.YYYY"),
					"<DATE_ACTIVE_FROM" => ConvertDateTime("01.01.".($year+1), "DD.MM.YYYY"),
				);
			}
		}
	}
?>

<?if(!$itemsCnt && !$arSubSections):?>
	<div class="alert alert-warning"><?=GetMessage("SECTION_EMPTY")?></div>
<? else: ?>
	<?// sections?>
	<?@include_once('page_blocks/sections_1.php');?>

	<?// section elements?>
	<?if(strlen($arParams["FILTER_NAME"])):?>
		<?$arTmpFilter = $GLOBALS[$arParams["FILTER_NAME"]];?>
		<?$GLOBALS[$arParams["FILTER_NAME"]] = array_merge((array)$GLOBALS[$arParams["FILTER_NAME"]], $arItemFilter);?>
	<?else:?>
		<?$arParams["FILTER_NAME"] = "arrFilterServ";?>
		<?$GLOBALS[$arParams["FILTER_NAME"]] = $arItemFilter;?>
	<?endif;?>
	<? @include_once('page_blocks/list_elements_1.php'); ?>
	
	<?if(strlen($arParams["FILTER_NAME"])):?>
		<?$GLOBALS[$arParams["FILTER_NAME"]] = $arTmpFilter;?>
	<?endif;?>
<? endif; ?>
<?if ($arParams["SHOW_FAQ_BLOCK"]=="Y"):?></div><?endif;?>