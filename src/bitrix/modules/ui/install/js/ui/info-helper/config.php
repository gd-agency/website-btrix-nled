<?php

use Bitrix\Main\Loader;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

return [
	"css" => [
		"/bitrix/js/ui/info-helper/css/info-helper.css"
	],
	"js" => [
		"/bitrix/js/ui/info-helper/info-helper.js",
	],
	"rel" => [
		"sidepanel",
		"loader",
	],
	'settings' => [
		'licenseType' => Loader::includeModule('bitrix24') ? strtoupper(\CBitrix24::getLicenseType()) : null,
	],
];