<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? 
	use \Bitrix\Main\Config\Option;
	$this->setFrameMode( true );
 ?>
<?if($arResult){?>
	<div class="submenu_top rows_block">
		<?if (is_array($arResult) && !empty($arResult)):?>
			<?foreach( $arResult as $arItem ){?>
				<?
					// Secure logout
					if( strpos($arItem['LINK'], 'logout') && Option::get('main', 'secure_logout') === 'Y' ){
						$arItem['LINK'] .= "&sessid=".bitrix_sessid();
					}
				?>
				<div class="item_block col-3">
					<div class="menu_item"><a href="<?=$arItem["LINK"]?>" class="dark_link"><?=$arItem["TEXT"]?></a></div>
				</div>
			<?}?>
		<?endif;?>
	</div>
<?}?>