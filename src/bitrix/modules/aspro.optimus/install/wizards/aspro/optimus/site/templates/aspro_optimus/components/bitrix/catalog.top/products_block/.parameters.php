<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
	"FILTER_NAME" => Array(
		"NAME" => GetMessage("FILTER_NAME"),
		"TYPE" => "STRING",
		"DEFAULT" => "arrTopFilter",
	),
	"SHOW_MEASURE" => Array(
		"NAME" => GetMessage("SHOW_MEASURE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	),
	"SHOW_MEASURE_WITH_RATIO" => Array(
		"NAME" => GetMessage("SHOW_MEASURE_WITH_RATIO"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	),
	"SHOW_DISCOUNT_PERCENT" => Array(
		"NAME" => GetMessage("SHOW_DISCOUNT_PERCENT"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"SHOW_OLD_PRICE" => Array(
		"NAME" => GetMessage("SHOW_OLD_PRICE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"DISPLAY_WISH_BUTTONS" => Array(
		"NAME" => GetMessage("DISPLAY_WISH_BUTTONS"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
);
?>
