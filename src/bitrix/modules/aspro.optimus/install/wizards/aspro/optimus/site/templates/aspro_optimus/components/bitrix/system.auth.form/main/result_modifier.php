<?php

if( isset($_REQUEST['USER_LOGIN']) && $_REQUEST['USER_LOGIN'] ){
    $arResult['USER_LOGIN'] = htmlspecialchars($_REQUEST['USER_LOGIN']);
}

if( isset( $arParms['BACKURL'] ) && $arParams['BACKURL'] ){
    $arResult['BACKURL'] = $arParams['BACKURL'];
    $arResult["AUTH_FORGOT_PASSWORD_URL"] = $arParams["FORGOT_PASSWORD_URL"]."&backurl=".$arParams["BACKURL"];
	$arResult["AUTH_REGISTER_URL"] = $arParams["REGISTER_URL"]."&backurl=".$arParams["BACKURL"];
}