<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode( true ); ?>
<? if($arResult['SECTIONS']): ?>
	<div class="articles-list lists_block sections <?=($arParams["IS_VERTICAL"]=="Y" ? "vertical row" : "")?> <?=($arParams["SHOW_FAQ_BLOCK"]=="Y" ? "faq" : "")?> ">
		<? foreach($arResult['SECTIONS'] as $arSection): ?>
			<?
				// edit/add/delete buttons for edit mode
				$arSectionButtons = CIBlock::GetPanelButtons($arSection['IBLOCK_ID'], 0, $arSection['ID'], array('SESSID' => false, 'CATALOG' => true));
				$this->AddEditAction($arSection['ID'], $arSectionButtons['edit']['edit_section']['ACTION_URL'], CIBlock::GetArrayByID($arSection['IBLOCK_ID'], 'SECTION_EDIT'));
				$this->AddDeleteAction($arSection['ID'], $arSectionButtons['edit']['delete_section']['ACTION_URL'], CIBlock::GetArrayByID($arSection['IBLOCK_ID'], 'SECTION_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			?>
			<?
				// preview picture
				if($bShowSectionImage = in_array('PREVIEW_PICTURE', $arParams['FIELD_CODE'])){
					$bImage = strlen($arSection['~PICTURE']);
					$arSectionImage = ($bImage ? CFile::ResizeImageGet($arSection['~PICTURE'], array('width' => 254, 'height' => 254), BX_RESIZE_IMAGE_PROPORTIONAL, true) : array());
					$imageSectionSrc = ($bImage ? $arSectionImage['src'] : SITE_TEMPLATE_PATH.'/images/no_photo_medium.png');
				}
			?>
			<div class="item item_block" id="<?=$this->GetEditAreaId($arSection['ID']);?>">
				<div class="wrapper_inner_block">
					<div class="left-data">
						<a href="<?=$arSection["SECTION_PAGE_URL"]?>" class="thumb">
							<img src="<?= $imageSectionSrc; ?>" 
								 alt="<?= $arSection['PICTURE']['ALT'] ?: $arSection['NAME'];?>" title="<?= $arSection['PICTURE']['TITLE'] ?: $arSection['NAME']; ?>" 
								 height="90" />
						</a>
					</div>
					<div class="right-data">
						<? if(in_array('NAME', $arParams['FIELD_CODE'])): ?>
							<div class="item-title">
								<a href="<?= $arSection["SECTION_PAGE_URL"]; ?>">
									<span><?= $arSection["NAME"]; ?></span>
								</a>
							</div>
						<? endif; ?>

						<? if(isset($arSection['DESCRIPTION']) && strlen($arSection['DESCRIPTION'])): ?>
							<div class="preview-text"><?= $arSection['DESCRIPTION']; ?></div>
						<? endif; ?>

						<?// section child?>
						<? if($arSection['CHILD']): ?>
							<div class="text childs" style="display: none">
								<ul class="text-childs-list">
									<? foreach($arSection['CHILD'] as $arSubItem): ?>
										<li>
											<a class="colored" href="<?=($arSubItem['SECTION_PAGE_URL'] ?: $arSubItem['DETAIL_PAGE_URL'] ); ?>">
												<?=$arSubItem['NAME']?>
											</a>
										</li>
									<? endforeach; ?>
								</ul>
							</div>
							<button 
								type="button" 
								class="button_opener colored" 
								data-open_text="<?=GetMessage('CLOSE_TEXT');?>" 
								data-close_text="<?=GetMessage('OPEN_TEXT');?>"
							>
								<?= COptimus::showIconSvg([
									'CLASS' => "arrow",
									'PATH' =>  SITE_TEMPLATE_PATH.'/images/svg/arrow_down_accordion.svg'
								]); ?>
								<span class="opener font_upper"><?=GetMessage('OPEN_TEXT');?></span>
							</button>
						<? endif; ?>
					</div>
				</div>
			</div>
		<? endforeach; ?>
	</div>
<?endif;?>