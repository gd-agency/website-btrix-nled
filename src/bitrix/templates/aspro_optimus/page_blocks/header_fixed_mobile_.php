<div class="wrapper_inner">
    <table class="middle-h-row">
        <tr class="page-header__fixed--mobile__columns">
            <td class="left-col">
                <div class="menu-logo-wrap">
                    <div class="menu-block mobile">
                        <div class="title" >
                            <?= \COptimus::showIconSvg([
                                'CLASS_ICON' => "svg-burger", 
                                'PATH' => SITE_TEMPLATE_PATH."/images/svg/burger_black.svg"
                            ]); ?>
                        </div>
                        <div class="navs table-menu js-nav">
                            <? if(COptimus::nlo('menu-mobile-fixed')): ?>
                                <!-- noindex -->
                                <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
                                    array(
                                        "COMPONENT_TEMPLATE" => ".default",
                                        "PATH" => SITE_DIR."include/menu/fixed/menu.top_fixed_field_mobile.php",
                                        "AREA_FILE_SHOW" => "file",
                                        "AREA_FILE_SUFFIX" => "",
                                        "AREA_FILE_RECURSIVE" => "Y",
                                        "EDIT_TEMPLATE" => "include_area.php"
                                    ),
                                    false, array("HIDE_ICONS" => "Y")
                                );?>
                                <!-- /noindex -->
                            <? endif; ?>
                            <? COptimus::nlo('menu-mobile-fixed'); ?>
                        </div>
                    </div>
                    <div class="logo_wrapp">
                        <div class="logo nofill_<?=strtolower(\Bitrix\Main\Config\Option::get('aspro.optimus', 'NO_LOGO_BG', 'N'));?>">
                            <? COptimus::ShowLogo(); ?>
                        </div>
                    </div>
                </div>
			</td>
			<td class="right-col">
                <div class="basket_wrapp">
                    <div class="wrapp_all_icons page-header__fixed__icons">
                        <?= COptimus::ShowBasketWithCompareLink('', 'big', false, false, true); ?>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>