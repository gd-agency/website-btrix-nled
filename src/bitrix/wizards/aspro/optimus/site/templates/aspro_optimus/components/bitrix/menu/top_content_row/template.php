<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? 
	use \Bitrix\Main\Config\Option;
	$this->setFrameMode( true ); 
?>
<?if( !empty( $arResult ) ){?>
	<ul class="menu topest">
		<?foreach( $arResult as $key => $arItem ){?>
			<?
				// Secure logout
				if( strpos($arItem['LINK'], 'logout') && Option::get('main', 'secure_logout') === 'Y' ){
					$arItem['LINK'] .= "&sessid=".bitrix_sessid();
				}
			?>
			<li <?if( $arItem["SELECTED"] ):?> class="current"<?endif?> >
				<a href="<?=$arItem["LINK"]?>"><span><?=$arItem["TEXT"]?></span></a>
			</li>
		<?}?>
		<li class="more hidden">
			<span>...</span>
			<ul class="dropdown"></ul>
		</li>
	</ul>
<?}?>