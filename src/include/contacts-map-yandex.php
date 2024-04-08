<?$APPLICATION->IncludeComponent(
	"bitrix:map.yandex.view", 
	"map", 
	array(
		"INIT_MAP_TYPE" => "ROADMAP",
		"MAP_DATA" => "a:4:{s:10:\"yandex_lat\";d:55.75567215872764;s:10:\"yandex_lon\";d:37.60761724722134;s:12:\"yandex_scale\";i:18;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:4:\"TEXT\";s:0:\"\";s:3:\"LON\";d:37.60764956474327;s:3:\"LAT\";d:55.75567424904235;}}}",
		"MAP_WIDTH" => "100%",
		"MAP_HEIGHT" => "400",
		"CONTROLS" => array(
		),
		"OPTIONS" => array(
			0 => "ENABLE_DBLCLICK_ZOOM",
			1 => "ENABLE_DRAGGING",
		),
		"MAP_ID" => "",
		"ZOOM_BLOCK" => array(
			"POSITION" => "right center",
		),
		"COMPONENT_TEMPLATE" => "map"
	),
	false
);?>