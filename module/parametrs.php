<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

IncludeModuleLangFile(__FILE__);

$moduleClass = "CEnext";

$seoIblockList = array("" => GetMessage("SMART_FILTER_DEFAULT_VALUE"));
$catalogIblockList = array("" => GetMessage("PROPS_GROUPS_DEFAULT_VALUE"));
if(Bitrix\Main\Loader::includeModule("iblock")) {	
	$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("TYPE" => "seo", "ACTIVE" => "Y"));
	while($arIBlock = $rsIBlock->Fetch()) {
		$seoIblockList[$arIBlock["ID"]] = "[".$arIBlock["ID"]."] ".$arIBlock["NAME"];
	}
	unset($arIBlock, $rsIBlock);

	$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("TYPE" => "catalog", "ACTIVE" => "Y"));
	while($arIBlock = $rsIBlock->Fetch()) {
		$catalogIblockList[$arIBlock["ID"]] = "[".$arIBlock["ID"]."] ".$arIBlock["NAME"];
	}
	unset($arIBlock, $rsIBlock);
}

$deliveries = array();
if(Bitrix\Main\Loader::includeModule("sale")) {
	$deliveryList = Bitrix\Sale\Delivery\Services\Table::GetList(array(
		"filter" => array(
			"ACTIVE" => "Y",
			"!ID" => Bitrix\Sale\Delivery\Services\EmptyDeliveryService::getEmptyDeliveryServiceId()
		)
	));
	while($deliveryObj = $deliveryList->fetch()) {
		$deliveryItems[$deliveryObj["ID"]] = array(
			"ID" =>	$deliveryObj["ID"],
			"PARENT_ID" => $deliveryObj["PARENT_ID"],
			"NAME" => $deliveryObj["NAME"]
		);
	}
	unset($deliveryObj, $deliveryList);

	if(!empty($deliveryItems)) {
		foreach($deliveryItems as $deliveryObj) {
			$deliveries[$deliveryObj["ID"]] = !empty($deliveryObj["PARENT_ID"]) ? $deliveryItems[$deliveryObj["PARENT_ID"]]["NAME"]." (".$deliveryObj["NAME"].")" : $deliveryObj["NAME"];
		}
		unset($deliveryObj);
	}
	unset($deliveryItems);
}

$agreements = Bitrix\Main\UserConsent\Agreement::getActiveList();
if(empty($agreements))
	$agreements = array();

//initialize module parametrs list and default values
$moduleClass::$arParametrsList = array(
	"MAIN" => array(
		"TITLE" => GetMessage("MAIN_OPTIONS"),
		"OPTIONS" => array(			
			"SHOW_SETTINGS_PANEL" => array(
				"TITLE" => GetMessage("SHOW_SETTINGS_PANEL"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"COLOR_SCHEME" => array(
				"TITLE" => GetMessage("COLOR_SCHEME"), 
				"TYPE" => "selectbox", 
				"LIST" => array(
					"BLUE" => array("COLOR" => "#33a9d9", "TITLE" => GetMessage("COLOR_SCHEME_BLUE")),
					"STRONG_BLUE" => array("COLOR" => "#286bc6", "TITLE" => GetMessage("COLOR_SCHEME_STRONG_BLUE")),
					"DARK_BLUE" => array("COLOR" => "#3847a2", "TITLE" => GetMessage("COLOR_SCHEME_DARK_BLUE")),
					"VIOLET" => array("COLOR" => "#6639b6", "TITLE" => GetMessage("COLOR_SCHEME_VIOLET")),
					"PINK" => array("COLOR" => "#d3115a", "TITLE" => GetMessage("COLOR_SCHEME_PINK")),
					"RED" => array("COLOR" => "#d93324", "TITLE" => GetMessage("COLOR_SCHEME_RED")),
					"ORANGE" => array("COLOR" => "#ff6634", "TITLE" => GetMessage("COLOR_SCHEME_ORANGE")),
					"YELLOW" => array("COLOR" => "#faa510", "TITLE" => GetMessage("COLOR_SCHEME_YELLOW")),
					"STRONG_YELLOW" => array("COLOR" => "#9dc21a", "TITLE" => GetMessage("COLOR_SCHEME_STRONG_YELLOW")),
					"GREEN" => array("COLOR" => "#349933", "TITLE" => GetMessage("COLOR_SCHEME_GREEN")),
					"CYAN" => array("COLOR" => "#1cc1a4", "TITLE" => GetMessage("COLOR_SCHEME_CYAN")),
					"GRAY" => array("COLOR" => "#55686e", "TITLE" => GetMessage("COLOR_SCHEME_GRAY")),
					"CUSTOM" => array("COLOR" => "", "TITLE" => GetMessage("COLOR_SCHEME_CUSTOM")),
				),
				"DEFAULT" => "VIOLET",
				"IN_SETTINGS_PANEL" => "Y"
			),
			"COLOR_SCHEME_CUSTOM" => array(
				"TITLE" => GetMessage("COLOR_SCHEME_CUSTOM"), 
				"TYPE" => "text", 
				"DEFAULT" => "#d3115a",
				"IN_SETTINGS_PANEL" => "Y"
			),
			"PAGE_BACKGROUND_COLOR" => array( //PRO_WS//
				"TITLE" => GetMessage("PAGE_BACKGROUND_COLOR"),
				"HINT" => GetMessage("PAGE_BACKGROUND_COLOR_HINT"),
				"TYPE" => "text",
				"DEFAULT" => "",
				"IN_SETTINGS_PANEL" => "N"
			),
			"PAGE_BACKGROUND_IMAGE" => array( //PRO_WS//
				"TITLE" => GetMessage("PAGE_BACKGROUND_IMAGE"),
				"HINT" => GetMessage("PAGE_BACKGROUND_IMAGE_HINT"),
				"TYPE" => "file",
				"DEFAULT" => "",
				"IN_SETTINGS_PANEL" => "N"
			),			
			"HOME_PAGE" => array(
				"TITLE" => GetMessage("HOME_PAGE"),
				"TYPE" => "multiselectbox",
				"LIST" => array(					
					"SLIDER" => GetMessage("HOME_PAGE_SLIDER"),
					"BANNERS" => GetMessage("HOME_PAGE_BANNERS"),
					"PROMOTIONS" => GetMessage("HOME_PAGE_PROMOTIONS"),
					"ADVANTAGES" => GetMessage("HOME_PAGE_ADVANTAGES"),
					"TABS" => GetMessage("HOME_PAGE_TABS"),
					"SECTIONS" => GetMessage("HOME_PAGE_SECTIONS"),
					"BRANDS" => GetMessage("HOME_PAGE_BRANDS"),
					"SERVICES" => GetMessage("HOME_PAGE_SERVICES"),
					"CONTENT" => GetMessage("HOME_PAGE_CONTENT"),
					"GALLERY" => GetMessage("HOME_PAGE_GALLERY"),
					"NEWS" => GetMessage("HOME_PAGE_NEWS"),
					"ARTICLES" => GetMessage("HOME_PAGE_ARTICLES"),
					"LOCATION" => GetMessage("HOME_PAGE_LOCATION")
				),
				"DEFAULT" => array("SLIDER", "BANNERS", "PROMOTIONS", "ADVANTAGES", "TABS", "SECTIONS", "BRANDS", "CONTENT"),
				"IN_SETTINGS_PANEL" => "Y"
			),
			"SITE_BLOCKS" => array(
				"TITLE" => GetMessage("SITE_BLOCKS"),
				"TYPE" => "multiselectbox",
				"LIST" => array(					
					"TOP_MENU" => GetMessage("SITE_BLOCKS_TOP_MENU"),
					"BIG_DATA" => GetMessage("SITE_BLOCKS_BIG_DATA"),
					"FEEDBACK" => GetMessage("SITE_BLOCKS_FEEDBACK"),
					"BOTTOM_MENU" => GetMessage("SITE_BLOCKS_BOTTOM_MENU")
				),
				"DEFAULT" => array("TOP_MENU", "FEEDBACK", "BOTTOM_MENU"),
				"IN_SETTINGS_PANEL" => "Y"
			),
			"DISABLE_BASKET" => array(
				"TITLE" => GetMessage("DISABLE_BASKET"),
				"TYPE" => "checkbox",
				"DEFAULT" => "N",
				"IN_SETTINGS_PANEL" => "N"
			),
			"DISABLE_DELAY" => array(
				"TITLE" => GetMessage("DISABLE_DELAY"),
				"TYPE" => "checkbox",
				"DEFAULT" => "N",
				"IN_SETTINGS_PANEL" => "N"
			),
			"QUICK_ORDER" => array(
				"TITLE" => GetMessage("QUICK_ORDER"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"DISABLE_ORDER" => array(
				"TITLE" => GetMessage("DISABLE_ORDER"),
				"TYPE" => "checkbox",
				"DEFAULT" => "N",
				"IN_SETTINGS_PANEL" => "N"
			),
			"MIN_ORDER_SUM" => array(
				"TITLE" => GetMessage("MIN_ORDER_SUM"),				
				"TYPE" => "number",
				"DEFAULT" => "",
				"IN_SETTINGS_PANEL" => "N"
			),
			"BUY_INFO_MESSAGE" => array(
				"TITLE" => GetMessage("BUY_INFO_MESSAGE"),				
				"TYPE" => "textarea",				
				"HEIGHT" => "84",				
				"DEFAULT" => "",
				"IN_SETTINGS_PANEL" => "N"				
			),
			"BUY_INFO_MESSAGE_TYPE" => array(
				"TYPE" => "hidden",				
				"DEFAULT" => "text",
				"IN_SETTINGS_PANEL" => "N"				
			),
			"ACCUMULATIVE_DISCOUNT" => array(
				"TITLE" => GetMessage("ACCUMULATIVE_DISCOUNT"),
				"HINT" => GetMessage("ACCUMULATIVE_DISCOUNT_HINT"),
				"TYPE" => "checkbox",
				"DEFAULT" => "N",
				"IN_SETTINGS_PANEL" => "N"
			),
			"MAP_SERVICE" => array(
				"TITLE" => GetMessage("MAP_SERVICE"),
				"TYPE" => "selectbox",
				"LIST" => array(
					"YANDEX" => GetMessage("MAP_SERVICE_YANDEX"),
					"GOOGLE" => GetMessage("MAP_SERVICE_GOOGLE")
				),
				"DEFAULT" => "YANDEX",
				"IN_SETTINGS_PANEL" => "N"
			),
			"MAIN_SEARCH" => array(
				"TITLE" => GetMessage("MAIN_SEARCH"),
				"TYPE" => "selectbox",
				"LIST" => array(
					"BITRIX" => GetMessage("MAIN_SEARCH_BITRIX"),
					"YANDEX" => GetMessage("MAIN_SEARCH_YANDEX")
				),
				"DEFAULT" => "BITRIX",
				"IN_SETTINGS_PANEL" => "N"
			),
			"MAIN_SEARCH_YANDEX_API_KEY" => array(
				"TITLE" => GetMessage("MAIN_SEARCH_YANDEX_API_KEY"),
				"TYPE" => "text", 
				"DEFAULT" => "",
				"IN_SETTINGS_PANEL" => "N"
			),
			"MAIN_SEARCH_YANDEX_SEARCH_ID" => array(
				"TITLE" => GetMessage("MAIN_SEARCH_YANDEX_SEARCH_ID"),
				"TYPE" => "text", 
				"DEFAULT" => "",
				"IN_SETTINGS_PANEL" => "N"
			),
			"MAIN_SEARCH_YANDEX_SHOW_SECTIONS" => array(
				"TITLE" => GetMessage("MAIN_SEARCH_YANDEX_SHOW_SECTIONS"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"MAIN_SEARCH_YANDEX_SHOW_CHECKBOX_AVAILABLE" => array(
				"TITLE" => GetMessage("MAIN_SEARCH_YANDEX_SHOW_CHECKBOX_AVAILABLE"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"BLOCK_SHARE" => array(
				"TITLE" => GetMessage("BLOCK_SHARE"),
				"TYPE" => "selectbox",
				"LIST" => array(
					"NONE" => GetMessage("BLOCK_SHARE_NONE"),
					"YANDEX" => GetMessage("BLOCK_SHARE_YANDEX")
				),
				"DEFAULT" => "YANDEX",
				"IN_SETTINGS_PANEL" => "N"
			),
			//PRO_WS//
			"ALLOW_SELECTION" => array(
				"TITLE" => GetMessage("ALLOW_SELECTION"),
				"TYPE" => "checkbox",
				"DEFAULT" => "N",
				"IN_SETTINGS_PANEL" => "N"
			),
			"LEGACY_COOKIES_AGREEMENT" => array(
				"TITLE" => GetMessage("LEGACY_COOKIES_AGREEMENT"),
				"TYPE" => "checkbox",
				"DEFAULT" => "N",
				"IN_SETTINGS_PANEL" => "N"
			),
			"SITE_WIDTH" => array(
				"TITLE" => GetMessage("SITE_WIDTH"),
				"TYPE" => "selectbox",
				"LIST" => array(
					"FULL" => GetMessage("SITE_WIDTH_FULL"),
					"LIMITED" => GetMessage("SITE_WIDTH_LIMITED")
				),
				"DEFAULT" => "LIMITED",
				"IN_SETTINGS_PANEL" => "N"
			)
			//PRO_WS//
		)
	),
	"SITE_OPTIMIZATION" => array(
		"TITLE" => GetMessage("SITE_OPTIMIZATION_OPTIONS"),
		"OPTIONS" => array(
			"DELETE_SPACES" => array(
				"TITLE" => GetMessage("DELETE_SPACES"),
				"HINT" => GetMessage("DELETE_SPACES_HINT"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"IMG_LAZYLOAD" => array(
				"TITLE" => GetMessage("IMG_LAZYLOAD"),
				"HINT" => GetMessage("IMG_LAZYLOAD_HINT"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"IMG_WEBP" => array(
				"TITLE" => GetMessage("IMG_WEBP"),
				"HINT" => GetMessage("IMG_WEBP_HINT"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"JS_LAZYLOAD" => array(
				"TITLE" => GetMessage("JS_LAZYLOAD"),
				"HINT" => GetMessage("JS_LAZYLOAD_HINT"),
				"TYPE" => "checkbox",
				"DEFAULT" => "N",
				"IN_SETTINGS_PANEL" => "N"
			),
			"MOVE_CSS_TO_BODY" => array(
				"TITLE" => GetMessage("MOVE_CSS_TO_BODY"),
				"HINT" => GetMessage("MOVE_CSS_TO_BODY_HINT"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			)
		)
	),
	"ICON_FONTS" => array(
		"TITLE" => GetMessage("ICON_FONTS_OPTIONS"),
		"OPTIONS" => array(
			"ICON_FONTS_UI_NEXT" => array(
				"TITLE" => GetMessage("ICON_FONTS_UI_NEXT"),
				"TYPE" => "selectbox",
				"LIST" => array(
					"MIN" => GetMessage("ICON_FONTS_MIN"),
					"MAX" => GetMessage("ICON_FONTS_MAX")
				),
				"DEFAULT" => "MIN",
				"IN_SETTINGS_PANEL" => "N"
			),
			"ICON_FONTS_FONT_AWESOME" => array(
				"TITLE" => GetMessage("ICON_FONTS_FONT_AWESOME"),
				"TYPE" => "selectbox",
				"LIST" => array(
					"DISABLE" => GetMessage("ICON_FONTS_DISABLE"),
					"MIN" => GetMessage("ICON_FONTS_MIN"),
					"MAX" => GetMessage("ICON_FONTS_MAX")
				),
				"DEFAULT" => "MIN",
				"IN_SETTINGS_PANEL" => "N"
			),
			"ICON_FONTS_ELASTO_FONT_NEXT" => array(
				"TITLE" => GetMessage("ICON_FONTS_ELASTO_FONT_NEXT"),
				"TYPE" => "multiselectbox",
				"LIST" => array(					
					"01-POWER-TOOLS-EFN" => GetMessage("ICON_FONTS_POWER_TOOLS_EFN"),
					"02-AUTO-EFN" => GetMessage("ICON_FONTS_AUTO_EFN"),
					"03-GARDEN-TOOLS-EFN" => GetMessage("ICON_FONTS_GARDEN_TOOLS_EFN"),
					"04-INTERIOR-EFN" => GetMessage("ICON_FONTS_INTERIOR_EFN"),
					"05-ELECTRICITY-EFN" => GetMessage("ICON_FONTS_ELECTRICITY_EFN"),
					"06-HAND-TOOLS-EFN" => GetMessage("ICON_FONTS_HAND_TOOLS_EFN"),
					"07-BUILDING-MATERIALS-EFN" => GetMessage("ICON_FONTS_BUILDING_MATERIALS_EFN"),
					"08-SANITARY-WARE-EFN" => GetMessage("ICON_FONTS_SANITARY_WARE_EFN"),
					"09-HEATING-COOLING-EFN" => GetMessage("ICON_FONTS_HEATING_COOLING_EFN"),
					"10-SECURITY-FIRE-ALARM-EFN" => GetMessage("ICON_FONTS_SECURITY_FIRE_ALARM_EFN"),
					"11-MOTO-EFN" => GetMessage("ICON_FONTS_MOTO_EFN"),
					"12-STEEL-METAL-EFN" => GetMessage("ICON_FONTS_STEEL_METAL_EFN"),
					"13-FURNITURE-EFN" => GetMessage("ICON_FONTS_FURNITURE_EFN"),
					"14-SPORT-HOBBY-EFN" => GetMessage("ICON_FONTS_SPORT_HOBBY_EFN"),
					"15-GARDEN-EFN" => GetMessage("ICON_FONTS_GARDEN_EFN"),
					"16-PC-HARDWARE-EFN" => GetMessage("ICON_FONTS_PC_HARDWARE_EFN"),
					"17-APPLIANCES-ELECTRONICS-EFN" => GetMessage("ICON_FONTS_APPLIANCES_ELECTRONICS_EFN"),
					"18-BABY-SHOP-EFN" => GetMessage("ICON_FONTS_BABY_SHOP_EFN"),
					"19-FISHING-EFN" => GetMessage("ICON_FONTS_FISHING_EFN"),
					"20-CAMPING-EFN" => GetMessage("ICON_FONTS_CAMPING_EFN"),
					"21-LIGHT-EFN" => GetMessage("ICON_FONTS_LIGHT_EFN"),
					"22-PET-SHOP-EFN" => GetMessage("ICON_FONTS_PET_SHOP_EFN"),
					"23-FAST-FOOD-EFN" => GetMessage("ICON_FONTS_FAST_FOOD_EFN"),
					"24-FOOD-EFN" => GetMessage("ICON_FONTS_FOOD_EFN"),
					"25-VENTILATION-EFN" => GetMessage("ICON_FONTS_VENTILATION_EFN"),
					"26-SPECIAL-EQUIPMENT-EFN" => GetMessage("ICON_FONTS_SPECIAL_EQUIPMENT_EFN"),
					"27-HYGIENE-COSMETICS-EFN" => GetMessage("ICON_FONTS_HYGIENE_COSMETICS_EFN"),
					"28-INDUSTRIAL-EQUIPMENT-EFN" => GetMessage("ICON_FONTS_INDUSTRIAL_EQUIPMENT_EFN"),
					"29-KITCHEN-EQUIPMENT-EFN" => GetMessage("ICON_FONTS_KITCHEN_EQUIPMENT_EFN"),
					"30-HONEY-EFN" => GetMessage("ICON_FONTS_HONEY_EFN"),
                    "31-STATIONERY-EFN" => GetMessage("ICON_FONTS_STATIONERY_EFN"),
                    "32-CLOTHING-EFN" => GetMessage("ICON_FONTS_CLOTHING_EFN"),
                    "33-TACTICAL-GEAR-EFN" => GetMessage("ICON_FONTS_TACTICAL_GEAR_EFN"),
                    "34-HOUSEHOLD-EFN" => GetMessage("ICON_FONTS_HOUSEHOLD_EFN")
				),
				"DEFAULT" => array("01-POWER-TOOLS-EFN", "03-GARDEN-TOOLS-EFN", "05-ELECTRICITY-EFN", "06-HAND-TOOLS-EFN", "07-BUILDING-MATERIALS-EFN", "08-SANITARY-WARE-EFN", "09-HEATING-COOLING-EFN", "10-SECURITY-FIRE-ALARM-EFN", "15-GARDEN-EFN"),
				"IN_SETTINGS_PANEL" => "N"
			),
			"ICON_FONTS_ELASTO_FONT" => array(
				"TITLE" => GetMessage("ICON_FONTS_ELASTO_FONT"),
				"TYPE" => "checkbox",
				"DEFAULT" => "N",
				"IN_SETTINGS_PANEL" => "N"
			),
			"ICON_FONTS_CUSTOM" => array(
				"TITLE" => GetMessage("ICON_FONTS_CUSTOM"),
				"HINT" => GetMessage("ICON_FONTS_CUSTOM_HINT"),
				"TYPE" => "text", 
				"DEFAULT" => "",
				"IN_SETTINGS_PANEL" => "N"
			)
		)
	),
	"TOP_PANEL" => array(
		"TITLE" => GetMessage("TOP_PANEL_OPTIONS"),
		"OPTIONS" => array(
			"TOP_PANEL_SEARCH_BUTTON" => array(
				"TITLE" => GetMessage("TOP_PANEL_SEARCH_BUTTON"),
				"TYPE" => "checkbox",
				"DEFAULT" => "N",
				"IN_SETTINGS_PANEL" => "N"
			),
			"TOP_PANEL_GEO_LOCATION" => array(
				"TITLE" => GetMessage("TOP_PANEL_GEO_LOCATION"),
				"HINT" => GetMessage("TOP_PANEL_GEO_LOCATION_HINT"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"TOP_PANEL_CONTACTS" => array(
				"TITLE" => GetMessage("TOP_PANEL_CONTACTS"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"TOP_PANEL_BASKET_VIEW" => array(
				"TITLE" => GetMessage("TOP_PANEL_BASKET_VIEW"),
				"TYPE" => "selectbox",
				"LIST" => array(
					"HREF" => GetMessage("TOP_PANEL_BASKET_VIEW_HREF"),
					"RIGHT" => GetMessage("TOP_PANEL_BASKET_VIEW_RIGHT")
				),
				"DEFAULT" => "RIGHT",
				"IN_SETTINGS_PANEL" => "N"
			),
			//PRO_WS//
			"TOP_PANEL_MESSAGE" => array(
				"TITLE" => GetMessage("TOP_PANEL_MESSAGE"),				
				"TYPE" => "textarea",				
				"HEIGHT" => "84",				
				"DEFAULT" => "",
				"IN_SETTINGS_PANEL" => "N"				
			),
			"TOP_PANEL_MESSAGE_TYPE" => array(
				"TYPE" => "hidden",				
				"DEFAULT" => "text",
				"IN_SETTINGS_PANEL" => "N"				
			)
			//PRO_WS//
		)
	),
	"MENU" => array(
		"TITLE" => GetMessage("MENU_OPTIONS"),
		"OPTIONS" => array(
			"CATALOG_MENU" => array(
				"TITLE" => GetMessage("CATALOG_MENU"),
				"TYPE" => "selectbox",
				"LIST" => array(
					"INTERFACE-2-0-1" => GetMessage("CATALOG_MENU_INTERFACE_2_0_1"),
					"INTERFACE-2-0-2" => GetMessage("CATALOG_MENU_INTERFACE_2_0_2"),
					"INTERFACE-2-0-3" => GetMessage("CATALOG_MENU_INTERFACE_2_0_3"),
					"OPTION-1" => GetMessage("CATALOG_MENU_OPTION_1"),
					"OPTION-2" => GetMessage("CATALOG_MENU_OPTION_2"),
					"OPTION-3" => GetMessage("CATALOG_MENU_OPTION_3"),
					"OPTION-4" => GetMessage("CATALOG_MENU_OPTION_4"),
					"OPTION-5" => GetMessage("CATALOG_MENU_OPTION_5"),
					"OPTION-6" => GetMessage("CATALOG_MENU_OPTION_6")					
				),
				"DEFAULT" => "INTERFACE-2-0-1",
				"IN_SETTINGS_PANEL" => "Y"
			),
			"CATALOG_MENU_INTERFACE_2_0_SUBMENU" => array(
				"TITLE" => GetMessage("CATALOG_MENU_INTERFACE_2_0_SUBMENU"),
				"TYPE" => "selectbox",
				"LIST" => array(
					"TWO_LEVELS_LARGE_CARDS" => GetMessage("CATALOG_MENU_INTERFACE_2_0_SUBMENU_TWO_LEVELS_LARGE_CARDS"),
					"TWO_LEVELS_SMALL_CARDS" => GetMessage("CATALOG_MENU_INTERFACE_2_0_SUBMENU_TWO_LEVELS_SMALL_CARDS"),
					"THREE_LEVELS" => GetMessage("CATALOG_MENU_INTERFACE_2_0_SUBMENU_THREE_LEVELS")
				),
				"DEFAULT" => "THREE_LEVELS",
				"IN_SETTINGS_PANEL" => "N"
			),
			"CATALOG_MENU_INTERFACE_2_0_SUBMENU_MOBILE" => array(
				"TITLE" => GetMessage("CATALOG_MENU_INTERFACE_2_0_SUBMENU_MOBILE"),
				"TYPE" => "selectbox",
				"LIST" => array(
					"ONE_LEVEL" => GetMessage("CATALOG_MENU_INTERFACE_2_0_SUBMENU_MOBILE_ONE_LEVEL"),
					"TWO_LEVELS" => GetMessage("CATALOG_MENU_INTERFACE_2_0_SUBMENU_MOBILE_TWO_LEVELS")
				),
				"DEFAULT" => "TWO_LEVELS",
				"IN_SETTINGS_PANEL" => "N"
			),
			"CATALOG_MENU_OPEN" => array(
				"TITLE" => GetMessage("CATALOG_MENU_OPEN"),
				"TYPE" => "selectbox",
				"LIST" => array(
					"FIRST_LEVEL" => GetMessage("CATALOG_MENU_OPEN_FIRST_LEVEL"),
					"ACTIVE_LEVEL" => GetMessage("CATALOG_MENU_OPEN_ACTIVE_LEVEL")
				),
				"DEFAULT" => "ACTIVE_LEVEL",
				"IN_SETTINGS_PANEL" => "N"
			),
			"CATALOG_MENU_NAV" => array(
				"TITLE" => GetMessage("CATALOG_MENU_NAV"),
				"TYPE" => "selectbox",
				"LIST" => array(
					"LAST_ITEM" => GetMessage("CATALOG_MENU_NAV_LAST_ITEM"),
					"ALL_ITEMS" => GetMessage("CATALOG_MENU_NAV_ALL_ITEMS")
				),
				"DEFAULT" => "ALL_ITEMS",
				"IN_SETTINGS_PANEL" => "N"
			)
		)
	),
	"SMART_FILTER" => array(
		"TITLE" => GetMessage("SMART_FILTER_OPTIONS"),
		"OPTIONS" => array(
			"SMART_FILTER_VIEW" => array(
				"TITLE" => GetMessage("SMART_FILTER_VIEW"),
				"TYPE" => "selectbox",
				"LIST" => array(
					"LEFT" => GetMessage("SMART_FILTER_VIEW_LEFT"),
					"RIGHT" => GetMessage("SMART_FILTER_VIEW_RIGHT")
				),
				"DEFAULT" => "LEFT",
				"IN_SETTINGS_PANEL" => "Y"
			),
			"SMART_FILTER_LEFT" => array(
				"TITLE" => GetMessage("SMART_FILTER_LEFT"),
				"TYPE" => "selectbox",
				"LIST" => array(
					"ALWAYS_OPEN" => GetMessage("SMART_FILTER_LEFT_ALWAYS_OPEN"),
					"DEFAULT_OPEN" => GetMessage("SMART_FILTER_LEFT_DEFAULT_OPEN"),
					"DEFAULT_CLOSED" => GetMessage("SMART_FILTER_LEFT_DEFAULT_CLOSED")
				),
				"DEFAULT" => "DEFAULT_OPEN",
				"IN_SETTINGS_PANEL" => "N"
			),
			"SMART_FILTER_LEFT_SAVE_STATUS" => array(
				"TITLE" => GetMessage("SMART_FILTER_LEFT_SAVE_STATUS"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"SMART_FILTER_SEO_ID" => array(
				"TITLE" => GetMessage("SMART_FILTER_SEO_ID"),
				"TYPE" => "selectbox",
				"LIST" => $seoIblockList,
				"DEFAULT" => "",
				"IN_SETTINGS_PANEL" => "N"
			),
			"SMART_FILTER_HIDE_DISABLED_PROPS" => array(
				"TITLE" => GetMessage("SMART_FILTER_HIDE_DISABLED_PROPS"),
				"TYPE" => "checkbox",
				"DEFAULT" => "N",
				"IN_SETTINGS_PANEL" => "N"
			)
		)
	),
	"CATALOG" => array(
		"TITLE" => GetMessage("CATALOG_OPTIONS"),
		"OPTIONS" => array(
			"QUICK_VIEW" => array(
				"TITLE" => GetMessage("QUICK_VIEW"),
				"TYPE" => "selectbox", 
				"LIST" => array(					
					"OFF" => GetMessage("QUICK_VIEW_OFF"),
					"CLASSICAL" => GetMessage("QUICK_VIEW_CLASSICAL"),
					"FULL" => GetMessage("QUICK_VIEW_FULL")
				),
				"DEFAULT" => "FULL",
				"IN_SETTINGS_PANEL" => "Y"
			),			
			"PRODUCTS_VIEW" => array(
				"TITLE" => GetMessage("PRODUCTS_VIEW"),
				"TYPE" => "selectbox", 
				"LIST" => array(					
					"CARD" => GetMessage("PRODUCTS_VIEW_CARD"),
					"LIST" => GetMessage("PRODUCTS_VIEW_LIST"),
					"PRICE" => GetMessage("PRODUCTS_VIEW_PRICE")
				),
				"DEFAULT" => "CARD",
				"IN_SETTINGS_PANEL" => "Y"
			),
			"PRODUCTS_LIST_VIEW_MOBILE" => array(
				"TITLE" => GetMessage("PRODUCTS_LIST_VIEW_MOBILE"),
				"TYPE" => "selectbox", 
				"LIST" => array(					
					"TWO_IN_ROW" => GetMessage("PRODUCTS_LIST_VIEW_MOBILE_TWO_IN_ROW"),
					"ONE_IN_ROW_LIST" => GetMessage("PRODUCTS_LIST_VIEW_MOBILE_ONE_IN_ROW_LIST"),
					"ONE_IN_ROW_FULL_CARD" => GetMessage("PRODUCTS_LIST_VIEW_MOBILE_ONE_IN_ROW_FULL_CARD")
				),
				"DEFAULT" => "TWO_IN_ROW",
				"IN_SETTINGS_PANEL" => "N"
			),
			"COLLECTIONS_DISPLAY" => array(
				"TITLE" => GetMessage("COLLECTIONS_DISPLAY"),
				"TYPE" => "selectbox", 
				"LIST" => array(
					"ITEMS" => GetMessage("COLLECTIONS_DISPLAY_ITEMS"),
					"COLLECTIONS" => GetMessage("COLLECTIONS_DISPLAY_COLLECTIONS")
				),
				"DEFAULT" => "ITEMS",
				"IN_SETTINGS_PANEL" => "N"
			),
			"PRICE_UPDATE_PERIOD" => array(
				"TITLE" => GetMessage("PRICE_UPDATE_PERIOD"),
				"HINT" => GetMessage("PRICE_UPDATE_PERIOD_HINT"),
				"TYPE" => "number",
				"DEFAULT" => "",
				"IN_SETTINGS_PANEL" => "N"
			)
		)
	),
	"CARD_PRODUCT" => array(
		"TITLE" => GetMessage("CARD_PRODUCT_OPTIONS"),
		"OPTIONS" => array(
            "SHORT_CARD_PANEL_MOBILE" => array(
                "TITLE" => GetMessage("SHORT_CARD_PANEL_MOBILE"),
                "TYPE" => "checkbox",
                "DEFAULT" => "Y",
                "IN_SETTINGS_PANEL" => "N"
            ),
			"OFFERS_VIEW" => array(
				"TITLE" => GetMessage("OFFERS_VIEW"),
				"TYPE" => "selectbox", 
				"LIST" => array(					
					"PROPS" => GetMessage("OFFERS_VIEW_PROPS"),
					"LIST" => GetMessage("OFFERS_VIEW_LIST"),
					"DROPDOWN_LIST" => GetMessage("OFFERS_VIEW_DROPDOWN_LIST")
				),
				"DEFAULT" => "PROPS",
				"IN_SETTINGS_PANEL" => "Y"
			),
			"OFFERS_ON_MAP" => array(
				"TITLE" => GetMessage("OFFERS_ON_MAP"),
				"HINT" => GetMessage("OFFERS_ON_MAP_HINT"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"PROPS_GROUPS_IBLOCK_ID" => array(
				"TITLE" => GetMessage("PROPS_GROUPS_IBLOCK_ID"),
				"TYPE" => "selectbox",
				"LIST" => $catalogIblockList,
				"DEFAULT" => "",
				"IN_SETTINGS_PANEL" => "N"
			),
			"PROPS_UNGROUPS_NAME" => array(
				"TITLE" => GetMessage("PROPS_UNGROUPS_NAME"),				
				"TYPE" => "text",
				"DEFAULT" => GetMessage("PROPS_UNGROUPS_NAME_DEF"),
				"IN_SETTINGS_PANEL" => "N"				
			),
			"TAB_PROPERTIES" => array(
				"TITLE" => GetMessage("TAB_PROPERTIES"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"ASK_PRICE" => array(
				"TITLE" => GetMessage("ASK_PRICE"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"UNDER_ORDER" => array(
				"TITLE" => GetMessage("UNDER_ORDER"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"AUTO_DELIVERY_CALCULATION" => array(
				"TITLE" => GetMessage("AUTO_DELIVERY_CALCULATION"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"DEFAULT_LOCATION_ID" => array(
				"TITLE" => GetMessage("DEFAULT_LOCATION_ID"),
				"HINT" => GetMessage("DEFAULT_LOCATION_ID_HINT"),
				"TYPE" => "number",
				"DEFAULT" => "",
				"IN_SETTINGS_PANEL" => "N"
			),
			"IGNORE_DELIVERY" => array(
				"TITLE" => GetMessage("IGNORE_DELIVERY"),
				"HINT" => GetMessage("IGNORE_DELIVERY_HINT"),
				"TYPE" => "multiselectbox",
				"LIST" => $deliveries,
				"DEFAULT" => "",
				"IN_SETTINGS_PANEL" => "N"
			),
			//PRO_WS//
			/*
			"BIGDATA" => array(
				"TITLE" => GetMessage("BIGDATA"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"BIGDATA_TITLE" => array(
				"TITLE" => GetMessage("BIGDATA_TITLE"),				
				"TYPE" => "text", 
				"DEFAULT" => GetMessage("BIGDATA_TITLE_DEF"),
				"IN_SETTINGS_PANEL" => "N"
			),
			"BIGDATA_RCM_TYPE" => array(
				"TITLE" => GetMessage("BIGDATA_RCM_TYPE"),
				"TYPE" => "selectbox", 
				"LIST" => array(
					"PERSONAL" => GetMessage("BIGDATA_RCM_TYPE_PERSONAL"),
					"BESTSELL" => GetMessage("BIGDATA_RCM_TYPE_BESTSELL"),
					"SIMILAR_SELL" => GetMessage("BIGDATA_RCM_TYPE_SIMILAR_SELL"),
					"SIMILAR_VIEW" => GetMessage("BIGDATA_RCM_TYPE_SIMILAR_VIEW"),
					"SIMILAR" => GetMessage("BIGDATA_RCM_TYPE_SIMILAR"),
					"ANY_SIMILAR" => GetMessage("BIGDATA_RCM_TYPE_ANY_SIMILAR"),
					"ANY_PERSONAL" => GetMessage("BIGDATA_RCM_TYPE_ANY_PERSONAL"),
					"ANY" => GetMessage("BIGDATA_RCM_TYPE_ANY")
				),
				"DEFAULT" => "ANY_SIMILAR",
				"IN_SETTINGS_PANEL" => "N"
			),
			*/
			"RELATED_PRODUCTS" => array(
				"TITLE" => GetMessage("RELATED_PRODUCTS"),
				"TYPE" => "checkbox",
				"DEFAULT" => "N",
				"IN_SETTINGS_PANEL" => "N"
			)
		)
	),
	"SLIDER" => array(
		"TITLE" => GetMessage("SLIDER_OPTIONS"),
		"OPTIONS" => array(
			"SMART_SPEED" => array(
				"TITLE" => GetMessage("SMART_SPEED"),
				"TYPE" => "number",
				"DEFAULT" => "1000",
				"IN_SETTINGS_PANEL" => "N"
			),
			"AUTOPLAY_TIMEOUT" => array(
				"TITLE" => GetMessage("AUTOPLAY_TIMEOUT"),
				"TYPE" => "number",
				"DEFAULT" => "5000",
				"IN_SETTINGS_PANEL" => "N"
			),
			"ANIMATE_OUT" => array(
				"TITLE" => GetMessage("ANIMATE_OUT"),
				"TYPE" => "selectbox", 
				"LIST" => array(
					"none" => GetMessage("ANIMATE_NONE"),
					"bounce" => GetMessage("ANIMATE_BOUNCE"),
					"flash" => GetMessage("ANIMATE_FLASH"),
					"pulse" => GetMessage("ANIMATE_PULSE"),
					"rubberBand" => GetMessage("ANIMATE_RUBBER_BAND"),
					"shake" => GetMessage("ANIMATE_SHAKE"),
					"swing" => GetMessage("ANIMATE_SWING"),
					"tada" => GetMessage("ANIMATE_TADA"),
					"wobble" => GetMessage("ANIMATE_WOBBLE"),
					"jello" => GetMessage("ANIMATE_JELLO"),
					"bounceOut" => GetMessage("ANIMATE_BOUNCE_OUT"),
					"bounceOutDown" => GetMessage("ANIMATE_BOUNCE_OUT_DOWN"),
					"bounceOutLeft" => GetMessage("ANIMATE_BOUNCE_OUT_LEFT"),
					"bounceOutRight" => GetMessage("ANIMATE_BOUNCE_OUT_RIGHT"),
					"bounceOutUp" => GetMessage("ANIMATE_BOUNCE_OUT_UP"),
					"fadeOut" => GetMessage("ANIMATE_FADE_OUT"),
					"fadeOutDown" => GetMessage("ANIMATE_FADE_OUT_DOWN"),
					"fadeOutDownBig" => GetMessage("ANIMATE_FADE_OUT_DOWN_BIG"),
					"fadeOutLeft" => GetMessage("ANIMATE_FADE_OUT_LEFT"),
					"fadeOutLeftBig" => GetMessage("ANIMATE_FADE_OUT_LEFT_BIG"),
					"fadeOutRight" => GetMessage("ANIMATE_FADE_OUT_RIGHT"),
					"fadeOutRightBig" => GetMessage("ANIMATE_FADE_OUT_RIGHT_BIG"),
					"fadeOutUp" => GetMessage("ANIMATE_FADE_OUT_UP"),
					"fadeOutUpBig" => GetMessage("ANIMATE_FADE_OUT_UP_BIG"),
					"flip" => GetMessage("ANIMATE_FLIP"),
					"flipOutX" => GetMessage("ANIMATE_FLIP_OUT_X"),
					"flipOutY" => GetMessage("ANIMATE_FLIP_OUT_Y"),
					"lightSpeedOut" => GetMessage("ANIMATE_LIGHT_SPEED_OUT"),
					"rotateOut" => GetMessage("ANIMATE_ROTATE_OUT"),
					"rotateOutDownLeft" => GetMessage("ANIMATE_ROTATE_OUT_DOWN_LEFT"),
					"rotateOutDownRight" => GetMessage("ANIMATE_ROTATE_OUT_DOWN_RIGHT"),
					"rotateOutUpLeft" => GetMessage("ANIMATE_ROTATE_OUT_UP_LEFT"),
					"rotateOutUpRight" => GetMessage("ANIMATE_ROTATE_OUT_UP_RIGHT"),
					"slideOutUp" => GetMessage("ANIMATE_SLIDE_OUT_UP"),
					"slideOutDown" => GetMessage("ANIMATE_SLIDE_OUT_DOWN"),
					"slideOutLeft" => GetMessage("ANIMATE_SLIDE_OUT_LEFT"),
					"slideOutRight" => GetMessage("ANIMATE_SLIDE_OUT_RIGHT"),
					"zoomOut" => GetMessage("ANIMATE_ZOOM_OUT"),
					"zoomOutDown" => GetMessage("ANIMATE_ZOOM_OUT_DOWN"),
					"zoomOutLeft" => GetMessage("ANIMATE_ZOOM_OUT_LEFT"),
					"zoomOutRight" => GetMessage("ANIMATE_ZOOM_OUT_RIGHT"),
					"zoomOutUp" => GetMessage("ANIMATE_ZOOM_OUT_UP"),					
					"hinge" => GetMessage("ANIMATE_HINGE"),
					"rollOut" => GetMessage("ANIMATE_ROLL_OUT")
				),
				"DEFAULT" => "fadeOut",
				"IN_SETTINGS_PANEL" => "N"
			),
			"ANIMATE_IN" => array(
				"TITLE" => GetMessage("ANIMATE_IN"),
				"TYPE" => "selectbox", 
				"LIST" => array(
					"none" => GetMessage("ANIMATE_NONE"),
					"bounce" => GetMessage("ANIMATE_BOUNCE"),
					"flash" => GetMessage("ANIMATE_FLASH"),
					"pulse" => GetMessage("ANIMATE_PULSE"),
					"rubberBand" => GetMessage("ANIMATE_RUBBER_BAND"),
					"shake" => GetMessage("ANIMATE_SHAKE"),
					"swing" => GetMessage("ANIMATE_SWING"),
					"tada" => GetMessage("ANIMATE_TADA"),
					"wobble" => GetMessage("ANIMATE_WOBBLE"),
					"jello" => GetMessage("ANIMATE_JELLO"),
					"bounceIn" => GetMessage("ANIMATE_BOUNCE_IN"),
					"bounceInDown" => GetMessage("ANIMATE_BOUNCE_IN_DOWN"),
					"bounceInLeft" => GetMessage("ANIMATE_BOUNCE_IN_LEFT"),
					"bounceInRight" => GetMessage("ANIMATE_BOUNCE_IN_RIGHT"),
					"bounceInUp" => GetMessage("ANIMATE_BOUNCE_IN_UP"),
					"fadeIn" => GetMessage("ANIMATE_FADE_IN"),
					"fadeInDown" => GetMessage("ANIMATE_FADE_IN_DOWN"),
					"fadeInDownBig" => GetMessage("ANIMATE_FADE_IN_DOWN_BIG"),
					"fadeInLeft" => GetMessage("ANIMATE_FADE_IN_LEFT"),
					"fadeInLeftBig" => GetMessage("ANIMATE_FADE_IN_LEFT_BIG"),
					"fadeInRight" => GetMessage("ANIMATE_FADE_IN_RIGHT"),
					"fadeInRightBig" => GetMessage("ANIMATE_FADE_IN_RIGHT_BIG"),
					"fadeInUp" => GetMessage("ANIMATE_FADE_IN_UP"),
					"fadeInUpBig" => GetMessage("ANIMATE_FADE_IN_UP_BIG"),
					"flip" => GetMessage("ANIMATE_FLIP"),
					"flipInX" => GetMessage("ANIMATE_FLIP_IN_X"),
					"flipInY" => GetMessage("ANIMATE_FLIP_IN_Y"),
					"lightSpeedIn" => GetMessage("ANIMATE_LIGHT_SPEED_IN"),
					"rotateIn" => GetMessage("ANIMATE_ROTATE_IN"),
					"rotateInDownLeft" => GetMessage("ANIMATE_ROTATE_IN_DOWN_LEFT"),
					"rotateInDownRight" => GetMessage("ANIMATE_ROTATE_IN_DOWN_RIGHT"),
					"rotateInUpLeft" => GetMessage("ANIMATE_ROTATE_IN_UP_LEFT"),
					"rotateInUpRight" => GetMessage("ANIMATE_ROTATE_IN_UP_RIGHT"),
					"slideInUp" => GetMessage("ANIMATE_SLIDE_IN_UP"),
					"slideInDown" => GetMessage("ANIMATE_SLIDE_IN_DOWN"),
					"slideInLeft" => GetMessage("ANIMATE_SLIDE_IN_LEFT"),
					"slideInRight" => GetMessage("ANIMATE_SLIDE_IN_RIGHT"),
					"zoomIn" => GetMessage("ANIMATE_ZOOM_IN"),
					"zoomInDown" => GetMessage("ANIMATE_ZOOM_IN_DOWN"),
					"zoomInLeft" => GetMessage("ANIMATE_ZOOM_IN_LEFT"),
					"zoomInRight" => GetMessage("ANIMATE_ZOOM_IN_RIGHT"),
					"zoomInUp" => GetMessage("ANIMATE_ZOOM_IN_UP"),					
					"hinge" => GetMessage("ANIMATE_HINGE"),
					"rollIn" => GetMessage("ANIMATE_ROLL_IN")
				),
				"DEFAULT" => "fadeIn",
				"IN_SETTINGS_PANEL" => "N"
			)
		)
	),
	"FORMS" => array(
		"TITLE" => GetMessage("FORMS_OPTIONS"),
		"OPTIONS" => array(			
			"FORMS_USE_CAPTCHA" => array(
				"TITLE" => GetMessage("FORMS_USE_CAPTCHA"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"FORMS_USER_CONSENT" => array(
				"TITLE" => GetMessage("FORMS_USER_CONSENT"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"FORMS_USER_CONSENT_ID" => array(
				"TITLE" => GetMessage("FORMS_USER_CONSENT_ID"),
				"TYPE" => "selectbox",
				"LIST" => $agreements,
				"DEFAULT" => "1",
				"IN_SETTINGS_PANEL" => "N"
			),
			"FORMS_USER_CONSENT_IS_CHECKED" => array(
				"TITLE" => GetMessage("FORMS_USER_CONSENT_IS_CHECKED"),
				"TYPE" => "checkbox",
				"DEFAULT" => "N",
				"IN_SETTINGS_PANEL" => "N"
			),
			"FORMS_USER_CONSENT_IS_LOADED" => array(
				"TITLE" => GetMessage("FORMS_USER_CONSENT_IS_LOADED"),
				"TYPE" => "checkbox",
				"DEFAULT" => "N",
				"IN_SETTINGS_PANEL" => "N"
			),
			"FORMS_DEFAULT_COUNTRY" => array(
				"TITLE" => GetMessage("FORMS_DEFAULT_COUNTRY"),
				"HINT" => GetMessage("FORMS_DEFAULT_COUNTRY_HINT"),
				"TYPE" => "selectbox",
				"LIST" => array(
					"AU" => GetMessage("FORMS_DEFAULT_COUNTRY_AU"),
					"AT" => GetMessage("FORMS_DEFAULT_COUNTRY_AT"),
					"AZ" => GetMessage("FORMS_DEFAULT_COUNTRY_AZ"),
					"AX" => GetMessage("FORMS_DEFAULT_COUNTRY_AX"),
					"AL" => GetMessage("FORMS_DEFAULT_COUNTRY_AL"),
					"DZ" => GetMessage("FORMS_DEFAULT_COUNTRY_DZ"),
					"AS" => GetMessage("FORMS_DEFAULT_COUNTRY_AS"),
					"AI" => GetMessage("FORMS_DEFAULT_COUNTRY_AI"),
					"AO" => GetMessage("FORMS_DEFAULT_COUNTRY_AO"),
					"AD" => GetMessage("FORMS_DEFAULT_COUNTRY_AD"),
					"AG" => GetMessage("FORMS_DEFAULT_COUNTRY_AG"),
					"AR" => GetMessage("FORMS_DEFAULT_COUNTRY_AR"),
					"AM" => GetMessage("FORMS_DEFAULT_COUNTRY_AM"),
					"AW" => GetMessage("FORMS_DEFAULT_COUNTRY_AW"),
					"AF" => GetMessage("FORMS_DEFAULT_COUNTRY_AF"),
					"BS" => GetMessage("FORMS_DEFAULT_COUNTRY_BS"),
					"BD" => GetMessage("FORMS_DEFAULT_COUNTRY_BD"),
					"BB" => GetMessage("FORMS_DEFAULT_COUNTRY_BB"),					
					"BH" => GetMessage("FORMS_DEFAULT_COUNTRY_BH"),
					"BY" => GetMessage("FORMS_DEFAULT_COUNTRY_BY"),
					"BZ" => GetMessage("FORMS_DEFAULT_COUNTRY_BZ"),					
					"BE" => GetMessage("FORMS_DEFAULT_COUNTRY_BE"),					
					"BJ" => GetMessage("FORMS_DEFAULT_COUNTRY_BJ"),
					"BM" => GetMessage("FORMS_DEFAULT_COUNTRY_BM"),
					"BG" => GetMessage("FORMS_DEFAULT_COUNTRY_BG"),
					"BO" => GetMessage("FORMS_DEFAULT_COUNTRY_BO"),
					"BA" => GetMessage("FORMS_DEFAULT_COUNTRY_BA"),
					"BW" => GetMessage("FORMS_DEFAULT_COUNTRY_BW"),
					"BR" => GetMessage("FORMS_DEFAULT_COUNTRY_BR"),
					"IO" => GetMessage("FORMS_DEFAULT_COUNTRY_IO"),
					"BN" => GetMessage("FORMS_DEFAULT_COUNTRY_BN"),
					"BF" => GetMessage("FORMS_DEFAULT_COUNTRY_BF"),
					"BI" => GetMessage("FORMS_DEFAULT_COUNTRY_BI"),
					"BT" => GetMessage("FORMS_DEFAULT_COUNTRY_BT"),
					"VU" => GetMessage("FORMS_DEFAULT_COUNTRY_VU"),
					"VA" => GetMessage("FORMS_DEFAULT_COUNTRY_VA"),
					"GB" => GetMessage("FORMS_DEFAULT_COUNTRY_GB"),
					"HU" => GetMessage("FORMS_DEFAULT_COUNTRY_HU"),
					"VE" => GetMessage("FORMS_DEFAULT_COUNTRY_VE"),
					"VG" => GetMessage("FORMS_DEFAULT_COUNTRY_VG"),
					"VI" => GetMessage("FORMS_DEFAULT_COUNTRY_VI"),
					"TL" => GetMessage("FORMS_DEFAULT_COUNTRY_TL"),
					"VN" => GetMessage("FORMS_DEFAULT_COUNTRY_VN"),
					"GA" => GetMessage("FORMS_DEFAULT_COUNTRY_GA"),
					"HT" => GetMessage("FORMS_DEFAULT_COUNTRY_HT"),
					"GY" => GetMessage("FORMS_DEFAULT_COUNTRY_GY"),
					"GM" => GetMessage("FORMS_DEFAULT_COUNTRY_GM"),
					"GH" => GetMessage("FORMS_DEFAULT_COUNTRY_GH"),
					"GP" => GetMessage("FORMS_DEFAULT_COUNTRY_GP"),
					"GT" => GetMessage("FORMS_DEFAULT_COUNTRY_GT"),
					"GN" => GetMessage("FORMS_DEFAULT_COUNTRY_GN"),
					"GW" => GetMessage("FORMS_DEFAULT_COUNTRY_GW"),
					"DE" => GetMessage("FORMS_DEFAULT_COUNTRY_DE"),
					"GG" => GetMessage("FORMS_DEFAULT_COUNTRY_GG"),
					"GI" => GetMessage("FORMS_DEFAULT_COUNTRY_GI"),
					"HN" => GetMessage("FORMS_DEFAULT_COUNTRY_HN"),
					"HK" => GetMessage("FORMS_DEFAULT_COUNTRY_HK"),
					"GD" => GetMessage("FORMS_DEFAULT_COUNTRY_GD"),
					"GL" => GetMessage("FORMS_DEFAULT_COUNTRY_GL"),
					"GR" => GetMessage("FORMS_DEFAULT_COUNTRY_GR"),
					"GE" => GetMessage("FORMS_DEFAULT_COUNTRY_GE"),
					"GU" => GetMessage("FORMS_DEFAULT_COUNTRY_GU"),
					"DK" => GetMessage("FORMS_DEFAULT_COUNTRY_DK"),
					"JE" => GetMessage("FORMS_DEFAULT_COUNTRY_JE"),
					"DJ" => GetMessage("FORMS_DEFAULT_COUNTRY_DJ"),
					"DM" => GetMessage("FORMS_DEFAULT_COUNTRY_DM"),
					"DO" => GetMessage("FORMS_DEFAULT_COUNTRY_DO"),
					"EG" => GetMessage("FORMS_DEFAULT_COUNTRY_EG"),
					"ZM" => GetMessage("FORMS_DEFAULT_COUNTRY_ZM"),
					"EH" => GetMessage("FORMS_DEFAULT_COUNTRY_EH"),
					"ZW" => GetMessage("FORMS_DEFAULT_COUNTRY_ZW"),
					"IL" => GetMessage("FORMS_DEFAULT_COUNTRY_IL"),
					"IN" => GetMessage("FORMS_DEFAULT_COUNTRY_IN"),
					"ID" => GetMessage("FORMS_DEFAULT_COUNTRY_ID"),
					"JO" => GetMessage("FORMS_DEFAULT_COUNTRY_JO"),
					"IQ" => GetMessage("FORMS_DEFAULT_COUNTRY_IQ"),
					"IR" => GetMessage("FORMS_DEFAULT_COUNTRY_IR"),
					"IE" => GetMessage("FORMS_DEFAULT_COUNTRY_IE"),
					"IS" => GetMessage("FORMS_DEFAULT_COUNTRY_IS"),
					"ES" => GetMessage("FORMS_DEFAULT_COUNTRY_ES"),
					"IT" => GetMessage("FORMS_DEFAULT_COUNTRY_IT"),
					"YE" => GetMessage("FORMS_DEFAULT_COUNTRY_YE"),
					"CV" => GetMessage("FORMS_DEFAULT_COUNTRY_CV"),
					"KZ" => GetMessage("FORMS_DEFAULT_COUNTRY_KZ"),
					"KY" => GetMessage("FORMS_DEFAULT_COUNTRY_KY"),
					"KH" => GetMessage("FORMS_DEFAULT_COUNTRY_KH"),
					"CM" => GetMessage("FORMS_DEFAULT_COUNTRY_CM"),
					"CA" => GetMessage("FORMS_DEFAULT_COUNTRY_CA"),
					"BQ" => GetMessage("FORMS_DEFAULT_COUNTRY_BQ"),
					"QA" => GetMessage("FORMS_DEFAULT_COUNTRY_QA"),
					"KE" => GetMessage("FORMS_DEFAULT_COUNTRY_KE"),
					"CY" => GetMessage("FORMS_DEFAULT_COUNTRY_CY"),
					"KG" => GetMessage("FORMS_DEFAULT_COUNTRY_KG"),
					"KI" => GetMessage("FORMS_DEFAULT_COUNTRY_KI"),
					"CN" => GetMessage("FORMS_DEFAULT_COUNTRY_CN"),
					"CC" => GetMessage("FORMS_DEFAULT_COUNTRY_CC"),
					"CO" => GetMessage("FORMS_DEFAULT_COUNTRY_CO"),
					"KM" => GetMessage("FORMS_DEFAULT_COUNTRY_KM"),
					"CD" => GetMessage("FORMS_DEFAULT_COUNTRY_CD"),
					"CG" => GetMessage("FORMS_DEFAULT_COUNTRY_CG"),
					"XK" => GetMessage("FORMS_DEFAULT_COUNTRY_XK"),
					"CR" => GetMessage("FORMS_DEFAULT_COUNTRY_CR"),
					"CI" => GetMessage("FORMS_DEFAULT_COUNTRY_CI"),
					"CU" => GetMessage("FORMS_DEFAULT_COUNTRY_CU"),
					"KW" => GetMessage("FORMS_DEFAULT_COUNTRY_KW"),
					"CW" => GetMessage("FORMS_DEFAULT_COUNTRY_CW"),
					"LA" => GetMessage("FORMS_DEFAULT_COUNTRY_LA"),
					"LV" => GetMessage("FORMS_DEFAULT_COUNTRY_LV"),
					"LS" => GetMessage("FORMS_DEFAULT_COUNTRY_LS"),
					"LR" => GetMessage("FORMS_DEFAULT_COUNTRY_LR"),
					"LB" => GetMessage("FORMS_DEFAULT_COUNTRY_LB"),
					"LY" => GetMessage("FORMS_DEFAULT_COUNTRY_LY"),
					"LT" => GetMessage("FORMS_DEFAULT_COUNTRY_LT"),
					"LI" => GetMessage("FORMS_DEFAULT_COUNTRY_LI"),
					"LU" => GetMessage("FORMS_DEFAULT_COUNTRY_LU"),
					"MU" => GetMessage("FORMS_DEFAULT_COUNTRY_MU"),
					"MR" => GetMessage("FORMS_DEFAULT_COUNTRY_MR"),
					"MG" => GetMessage("FORMS_DEFAULT_COUNTRY_MG"),
					"YT" => GetMessage("FORMS_DEFAULT_COUNTRY_YT"),
					"MO" => GetMessage("FORMS_DEFAULT_COUNTRY_MO"),
					"MK" => GetMessage("FORMS_DEFAULT_COUNTRY_MK"),
					"MW" => GetMessage("FORMS_DEFAULT_COUNTRY_MW"),
					"MY" => GetMessage("FORMS_DEFAULT_COUNTRY_MY"),
					"ML" => GetMessage("FORMS_DEFAULT_COUNTRY_ML"),
					"MV" => GetMessage("FORMS_DEFAULT_COUNTRY_MV"),
					"MT" => GetMessage("FORMS_DEFAULT_COUNTRY_MT"),
					"MA" => GetMessage("FORMS_DEFAULT_COUNTRY_MA"),
					"MQ" => GetMessage("FORMS_DEFAULT_COUNTRY_MQ"),
					"MH" => GetMessage("FORMS_DEFAULT_COUNTRY_MH"),
					"MX" => GetMessage("FORMS_DEFAULT_COUNTRY_MX"),
					"FM" => GetMessage("FORMS_DEFAULT_COUNTRY_FM"),
					"MZ" => GetMessage("FORMS_DEFAULT_COUNTRY_MZ"),
					"MD" => GetMessage("FORMS_DEFAULT_COUNTRY_MD"),
					"MC" => GetMessage("FORMS_DEFAULT_COUNTRY_MC"),
					"MN" => GetMessage("FORMS_DEFAULT_COUNTRY_MN"),
					"MS" => GetMessage("FORMS_DEFAULT_COUNTRY_MS"),
					"MM" => GetMessage("FORMS_DEFAULT_COUNTRY_MM"),
					"NA" => GetMessage("FORMS_DEFAULT_COUNTRY_NA"),
					"NR" => GetMessage("FORMS_DEFAULT_COUNTRY_NR"),
					"NP" => GetMessage("FORMS_DEFAULT_COUNTRY_NP"),
					"NE" => GetMessage("FORMS_DEFAULT_COUNTRY_NE"),
					"NG" => GetMessage("FORMS_DEFAULT_COUNTRY_NG"),
					"NL" => GetMessage("FORMS_DEFAULT_COUNTRY_NL"),
					"NI" => GetMessage("FORMS_DEFAULT_COUNTRY_NI"),
					"NU" => GetMessage("FORMS_DEFAULT_COUNTRY_NU"),
					"NZ" => GetMessage("FORMS_DEFAULT_COUNTRY_NZ"),
					"NC" => GetMessage("FORMS_DEFAULT_COUNTRY_NC"),
					"NO" => GetMessage("FORMS_DEFAULT_COUNTRY_NO"),
					"AE" => GetMessage("FORMS_DEFAULT_COUNTRY_AE"),
					"OM" => GetMessage("FORMS_DEFAULT_COUNTRY_OM"),
					"IM" => GetMessage("FORMS_DEFAULT_COUNTRY_IM"),
					"NF" => GetMessage("FORMS_DEFAULT_COUNTRY_NF"),
					"CX" => GetMessage("FORMS_DEFAULT_COUNTRY_CX"),
					"SH" => GetMessage("FORMS_DEFAULT_COUNTRY_SH"),
					"CK" => GetMessage("FORMS_DEFAULT_COUNTRY_CK"),
					"TC" => GetMessage("FORMS_DEFAULT_COUNTRY_TC"),
					"PK" => GetMessage("FORMS_DEFAULT_COUNTRY_PK"),
					"PW" => GetMessage("FORMS_DEFAULT_COUNTRY_PW"),
					"PS" => GetMessage("FORMS_DEFAULT_COUNTRY_PS"),
					"PA" => GetMessage("FORMS_DEFAULT_COUNTRY_PA"),
					"PG" => GetMessage("FORMS_DEFAULT_COUNTRY_PG"),
					"PY" => GetMessage("FORMS_DEFAULT_COUNTRY_PY"),
					"PE" => GetMessage("FORMS_DEFAULT_COUNTRY_PE"),
					"PL" => GetMessage("FORMS_DEFAULT_COUNTRY_PL"),
					"PT" => GetMessage("FORMS_DEFAULT_COUNTRY_PT"),
					"PR" => GetMessage("FORMS_DEFAULT_COUNTRY_PR"),
					"RE" => GetMessage("FORMS_DEFAULT_COUNTRY_RE"),
					"RU" => GetMessage("FORMS_DEFAULT_COUNTRY_RU"),
					"RW" => GetMessage("FORMS_DEFAULT_COUNTRY_RW"),
					"RO" => GetMessage("FORMS_DEFAULT_COUNTRY_RO"),
					"SV" => GetMessage("FORMS_DEFAULT_COUNTRY_SV"),
					"WS" => GetMessage("FORMS_DEFAULT_COUNTRY_WS"),
					"SM" => GetMessage("FORMS_DEFAULT_COUNTRY_SM"),
					"ST" => GetMessage("FORMS_DEFAULT_COUNTRY_ST"),
					"SA" => GetMessage("FORMS_DEFAULT_COUNTRY_SA"),
					"SZ" => GetMessage("FORMS_DEFAULT_COUNTRY_SZ"),
					"KP" => GetMessage("FORMS_DEFAULT_COUNTRY_KP"),
					"MP" => GetMessage("FORMS_DEFAULT_COUNTRY_MP"),
					"SC" => GetMessage("FORMS_DEFAULT_COUNTRY_SC"),
					"BL" => GetMessage("FORMS_DEFAULT_COUNTRY_BL"),
					"MF" => GetMessage("FORMS_DEFAULT_COUNTRY_MF"),
					"PM" => GetMessage("FORMS_DEFAULT_COUNTRY_PM"),
					"SN" => GetMessage("FORMS_DEFAULT_COUNTRY_SN"),
					"VC" => GetMessage("FORMS_DEFAULT_COUNTRY_VC"),
					"KN" => GetMessage("FORMS_DEFAULT_COUNTRY_KN"),
					"LC" => GetMessage("FORMS_DEFAULT_COUNTRY_LC"),
					"RS" => GetMessage("FORMS_DEFAULT_COUNTRY_RS"),
					"SG" => GetMessage("FORMS_DEFAULT_COUNTRY_SG"),
					"SX" => GetMessage("FORMS_DEFAULT_COUNTRY_SX"),
					"SY" => GetMessage("FORMS_DEFAULT_COUNTRY_SY"),
					"SK" => GetMessage("FORMS_DEFAULT_COUNTRY_SK"),
					"SI" => GetMessage("FORMS_DEFAULT_COUNTRY_SI"),
					"SB" => GetMessage("FORMS_DEFAULT_COUNTRY_SB"),
					"SO" => GetMessage("FORMS_DEFAULT_COUNTRY_SO"),
					"SD" => GetMessage("FORMS_DEFAULT_COUNTRY_SD"),
					"SR" => GetMessage("FORMS_DEFAULT_COUNTRY_SR"),
					"US" => GetMessage("FORMS_DEFAULT_COUNTRY_US"),
					"SL" => GetMessage("FORMS_DEFAULT_COUNTRY_SL"),
					"TJ" => GetMessage("FORMS_DEFAULT_COUNTRY_TJ"),
					"TH" => GetMessage("FORMS_DEFAULT_COUNTRY_TH"),
					"TW" => GetMessage("FORMS_DEFAULT_COUNTRY_TW"),
					"TZ" => GetMessage("FORMS_DEFAULT_COUNTRY_TZ"),
					"TG" => GetMessage("FORMS_DEFAULT_COUNTRY_TG"),
					"TK" => GetMessage("FORMS_DEFAULT_COUNTRY_TK"),
					"TO" => GetMessage("FORMS_DEFAULT_COUNTRY_TO"),
					"TT" => GetMessage("FORMS_DEFAULT_COUNTRY_TT"),
					"TV" => GetMessage("FORMS_DEFAULT_COUNTRY_TV"),
					"TN" => GetMessage("FORMS_DEFAULT_COUNTRY_TN"),
					"TM" => GetMessage("FORMS_DEFAULT_COUNTRY_TM"),
					"TR" => GetMessage("FORMS_DEFAULT_COUNTRY_TR"),
					"UG" => GetMessage("FORMS_DEFAULT_COUNTRY_UG"),
					"UZ" => GetMessage("FORMS_DEFAULT_COUNTRY_UZ"),
					"UA" => GetMessage("FORMS_DEFAULT_COUNTRY_UA"),
					"WF" => GetMessage("FORMS_DEFAULT_COUNTRY_WF"),
					"UY" => GetMessage("FORMS_DEFAULT_COUNTRY_UY"),
					"FO" => GetMessage("FORMS_DEFAULT_COUNTRY_FO"),
					"FJ" => GetMessage("FORMS_DEFAULT_COUNTRY_FJ"),
					"PH" => GetMessage("FORMS_DEFAULT_COUNTRY_PH"),
					"FI" => GetMessage("FORMS_DEFAULT_COUNTRY_FI"),
					"FK" => GetMessage("FORMS_DEFAULT_COUNTRY_FK"),
					"FR" => GetMessage("FORMS_DEFAULT_COUNTRY_FR"),
					"GF" => GetMessage("FORMS_DEFAULT_COUNTRY_GF"),
					"PF" => GetMessage("FORMS_DEFAULT_COUNTRY_PF"),
					"HR" => GetMessage("FORMS_DEFAULT_COUNTRY_HR"),
					"CF" => GetMessage("FORMS_DEFAULT_COUNTRY_CF"),
					"TD" => GetMessage("FORMS_DEFAULT_COUNTRY_TD"),
					"ME" => GetMessage("FORMS_DEFAULT_COUNTRY_ME"),
					"CZ" => GetMessage("FORMS_DEFAULT_COUNTRY_CZ"),
					"CL" => GetMessage("FORMS_DEFAULT_COUNTRY_CL"),
					"CH" => GetMessage("FORMS_DEFAULT_COUNTRY_CH"),
					"SE" => GetMessage("FORMS_DEFAULT_COUNTRY_SE"),
					"SJ" => GetMessage("FORMS_DEFAULT_COUNTRY_SJ"),
					"LK" => GetMessage("FORMS_DEFAULT_COUNTRY_LK"),
					"EC" => GetMessage("FORMS_DEFAULT_COUNTRY_EC"),					
					"GQ" => GetMessage("FORMS_DEFAULT_COUNTRY_GQ"),
					"ER" => GetMessage("FORMS_DEFAULT_COUNTRY_ER"),
					"EE" => GetMessage("FORMS_DEFAULT_COUNTRY_EE"),
					"ET" => GetMessage("FORMS_DEFAULT_COUNTRY_ET"),
					"ZA" => GetMessage("FORMS_DEFAULT_COUNTRY_ZA"),
					"KR" => GetMessage("FORMS_DEFAULT_COUNTRY_KR"),
					"SS" => GetMessage("FORMS_DEFAULT_COUNTRY_SS"),
					"JM" => GetMessage("FORMS_DEFAULT_COUNTRY_JM"),
					"JP" => GetMessage("FORMS_DEFAULT_COUNTRY_JP")
				),
				"DEFAULT" => "RU",
				"IN_SETTINGS_PANEL" => "N"
			),
			"FORMS_PHONE_MASK" => array(
				"TITLE" => GetMessage("FORMS_PHONE_MASK"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			)
		)
	),
	"REVIEWS" => array(
		"TITLE" => GetMessage("REVIEWS_OPTIONS"),
		"OPTIONS" => array(
			"REVIEWS_PREMODERATION" => array(
				"TITLE" => GetMessage("REVIEWS_PREMODERATION"),
				"TYPE" => "checkbox",
				"DEFAULT" => "N",
				"IN_SETTINGS_PANEL" => "N"
			),
			"REVIEWS_ADD_AUTHORIZED" => array(
				"TITLE" => GetMessage("REVIEWS_ADD_AUTHORIZED"),
				"TYPE" => "checkbox",
				"DEFAULT" => "N",
				"IN_SETTINGS_PANEL" => "N"
			),
		)
	),
	"PARTNERS" => array(
		"TITLE" => GetMessage("PARTNERS_OPTIONS"),
		"OPTIONS" => array(
			"PARTNERS_INFO_MESSAGE" => array(
				"TITLE" => GetMessage("PARTNERS_INFO_MESSAGE"),				
				"TYPE" => "textarea",				
				"HEIGHT" => "84",				
				"DEFAULT" => GetMessage("PARTNERS_INFO_MESSAGE_DEF"),
				"IN_SETTINGS_PANEL" => "N"				
			),
			"PARTNERS_INFO_MESSAGE_TYPE" => array(
				"TYPE" => "hidden",				
				"DEFAULT" => "text",
				"IN_SETTINGS_PANEL" => "N"				
			)
		)
	),
	"SITE_CLOSING" => array(
		"TITLE" => GetMessage("SITE_CLOSING_OPTIONS"),
		"OPTIONS" => array(			
			"SITE_CLOSED_TITLE" => array(
				"TITLE" => GetMessage("SITE_CLOSED_TITLE"),				
				"TYPE" => "text",
				"DEFAULT" => GetMessage("SITE_CLOSED_TITLE_DEF"),
				"IN_SETTINGS_PANEL" => "N"				
			),
			"SITE_CLOSED_DESCRIPTION" => array(
				"TITLE" => GetMessage("SITE_CLOSED_DESCRIPTION"),				
				"TYPE" => "textarea",				
				"HEIGHT" => "84",
				"DEFAULT" => GetMessage("SITE_CLOSED_DESCRIPTION_DEF"),
				"IN_SETTINGS_PANEL" => "N"				
			),
			"SITE_CLOSED_DESCRIPTION_TYPE" => array(
				"TYPE" => "hidden",				
				"DEFAULT" => "html",
				"IN_SETTINGS_PANEL" => "N"				
			),
			"SITE_OPENING_TITLE" => array(
				"TITLE" => GetMessage("SITE_OPENING_TITLE"),				
				"TYPE" => "text",
				"DEFAULT" => GetMessage("SITE_OPENING_TITLE_DEF"),
				"IN_SETTINGS_PANEL" => "N"				
			),
			"DATE_OPENING_SITE" => array(
				"TITLE" => GetMessage("DATE_OPENING_SITE"), 
				"TYPE" => "date", 
				"DEFAULT" => ConvertTimeStamp(time() + CTimeZone::GetOffset(), "FULL"),
				"IN_SETTINGS_PANEL" => "N"
			)
		)
	),
	"SITE_DEFAULT_PICTURES" => array(
		"TITLE" => GetMessage("SITE_DEFAULT_PICTURES"),
		"OPTIONS" => array(
			"SHARE_DEFAULT_PICTURE" => array(
				"TITLE" => GetMessage("SHARE_DEFAULT_PICTURE"),
				"TYPE" => "file",
				"DEFAULT" => COption::GetOptionString("enext", "SHARE_DEFAULT_PICTURE"),				
				"IN_SETTINGS_PANEL" => "N"
			),
			"APPLE_TOUCH_ICON_180_180" => array(
				"TITLE" => GetMessage("APPLE_TOUCH_ICON_180_180"),
				"HINT" => GetMessage("APPLE_TOUCH_ICON_180_180_HINT"),
				"TYPE" => "file",
				"DEFAULT" => COption::GetOptionString("enext", "APPLE_TOUCH_ICON_180_180"),				
				"IN_SETTINGS_PANEL" => "N"
			)
		)
	),
	"COUNTERS_SCRIPTS" => array(
		"TITLE" => GetMessage("COUNTERS_SCRIPTS_OPTIONS"),
		"OPTIONS" => array(
			"COUNTERS_SCRIPTS_HEAD" => array(
				"TITLE" => GetMessage("COUNTERS_SCRIPTS_HEAD"),
				"HINT" => GetMessage("COUNTERS_SCRIPTS_HEAD_HINT"),
				"TYPE" => "textarea",				
				"HEIGHT" => "168",
				"DEFAULT" => "",
				"IN_SETTINGS_PANEL" => "N"
			),
			"COUNTERS_SCRIPTS_HEAD_TYPE" => array(
				"TYPE" => "hidden",				
				"DEFAULT" => "text",
				"IN_SETTINGS_PANEL" => "N"				
			),
			"COUNTERS_SCRIPTS_BODY_START" => array(
				"TITLE" => GetMessage("COUNTERS_SCRIPTS_BODY_START"),
				"HINT" => GetMessage("COUNTERS_SCRIPTS_BODY_START_HINT"),
				"TYPE" => "textarea",				
				"HEIGHT" => "168",
				"DEFAULT" => "",
				"IN_SETTINGS_PANEL" => "N"
			),
			"COUNTERS_SCRIPTS_BODY_START_TYPE" => array(
				"TYPE" => "hidden",				
				"DEFAULT" => "text",
				"IN_SETTINGS_PANEL" => "N"				
			),
			"COUNTERS_SCRIPTS_BODY_END" => array(
				"TITLE" => GetMessage("COUNTERS_SCRIPTS_BODY_END"),
				"HINT" => GetMessage("COUNTERS_SCRIPTS_BODY_END_HINT"),
				"TYPE" => "textarea",				
				"HEIGHT" => "168",
				"DEFAULT" => "",
				"IN_SETTINGS_PANEL" => "N"
			),
			"COUNTERS_SCRIPTS_BODY_END_TYPE" => array(
				"TYPE" => "hidden",				
				"DEFAULT" => "text",
				"IN_SETTINGS_PANEL" => "N"				
			)
		)
	)
);?>