<?
AddEventHandler('main', 'OnBuildGlobalMenu', 'OnBuildGlobalMenuHandlerOptimus');
function OnBuildGlobalMenuHandlerOptimus(&$arGlobalMenu, &$arModuleMenu){
	if(!defined('OPTIMUS_MENU_INCLUDED')){
		define('OPTIMUS_MENU_INCLUDED', true);

		IncludeModuleLangFile(__FILE__);
		$moduleID = 'aspro.optimus';

		$GLOBALS['APPLICATION']->SetAdditionalCss("/bitrix/css/".$moduleID."/menu.css");

		if($GLOBALS['APPLICATION']->GetGroupRight($moduleID) >= 'R'){
			$arMenu = array(
				'menu_id' => 'global_menu_aspro_optimus',
				'text' => GetMessage('OPTIMUS_GLOBAL_MENU_TEXT'),
				'title' => GetMessage('OPTIMUS_GLOBAL_MENU_TITLE'),
				'sort' => 1000,
				'items_id' => 'global_menu_aspro_optimus_items',
				'icon' => 'imi_optimus',
				'items' => array(
					array(
						'text' => GetMessage('OPTIMUS_MENU_CONTROL_CENTER_TEXT'),
						'title' => GetMessage('OPTIMUS_MENU_CONTROL_CENTER_TITLE'),
						'sort' => 10,
						'url' => '/bitrix/admin/'.$moduleID.'_mc.php',
						'icon' => 'imi_control_center',
						'page_icon' => 'pi_control_center',
						'items_id' => 'control_center',
					),
					array(
						'text' => GetMessage('OPTIMUS_MENU_TYPOGRAPHY_TEXT'),
						'title' => GetMessage('OPTIMUS_MENU_TYPOGRAPHY_TITLE'),
						'sort' => 20,
						'url' => '/bitrix/admin/'.$moduleID.'_options.php?mid=main',
						'icon' => 'imi_typography',
						'page_icon' => 'pi_typography',
						'items_id' => 'main',
					),
					array(
						'text' => GetMessage('OPTIMUS_MENU_CRM_TEXT'),
						'title' => GetMessage('OPTIMUS_MENU_CRM_TITLE'),
						'sort' => 30,
						'icon' => 'imi_marketing',
						'page_icon' => 'pi_typography',
						'items_id' => 'ncrm',
						"items" => array(
							array(
								'text' => GetMessage('OPTIMUS_MENU_AMO_CRM_TEXT'),
								'title' => GetMessage('OPTIMUS_MENU_AMO_CRM_TITLE'),
								'sort' => 10,
								'url' => '/bitrix/admin/'.$moduleID.'_crm_amo.php?mid=main',
								'icon' => '',
								'page_icon' => 'pi_typography',
								'items_id' => 'crm_amo',
							),
							array(
								'text' => GetMessage('OPTIMUS_MENU_FLOWLU_CRM_TEXT'),
								'title' => GetMessage('OPTIMUS_MENU_FLOWLU_CRM_TITLE'),
								'sort' => 20,
								'url' => '/bitrix/admin/'.$moduleID.'_crm_flowlu.php?mid=main',
								'icon' => '',
								'page_icon' => 'pi_typography',
								'items_id' => 'crm_flowlu',
							),
						)
					),
					array(
						'text' => GetMessage('OPTIMUS_MENU_DEVELOP_TEXT'),
						'title' => GetMessage('OPTIMUS_MENU_DEVELOP_TITLE'),
						'sort' => 20,
						'url' => '/bitrix/admin/'.$moduleID.'_develop.php?mid=main',
						'icon' => 'util_menu_icon',
						'page_icon' => 'pi_typography',
						'items_id' => 'develop',
					),
				),
			);

			if(!isset($arGlobalMenu['global_menu_aspro'])){
				$arGlobalMenu['global_menu_aspro'] = array(
					'menu_id' => 'global_menu_aspro',
					'text' => GetMessage('OPTIMUS_GLOBAL_ASPRO_MENU_TEXT'),
					'title' => GetMessage('OPTIMUS_GLOBAL_ASPRO_MENU_TITLE'),
					'sort' => 1000,
					'items_id' => 'global_menu_aspro_items',
				);
			}

			$arGlobalMenu['global_menu_aspro']['items']['aspro.optimus'] = $arMenu;
		}
	}
}
?>