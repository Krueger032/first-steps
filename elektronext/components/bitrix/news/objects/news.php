<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

global $arSettings;?>

<div class="row objects-cols">
	<div class="col-xs-12 col-md-9 objects-col">
		<div class="objects-container">
			<?//SECTION_LIST//
			$GLOBALS[$arParams["FILTER_NAME"]] = array("PROPERTY" => array("HIDE_IN_OBJECTS_LIST" => false));?>
			<?$APPLICATION->IncludeComponent("bitrix:catalog.section.list", "catalog",
				array(
					"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],
					"CACHE_TYPE" => $arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"],
					"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
					"COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
					"TOP_DEPTH" => "1",
					"SECTION_FIELDS" => array(),
					"SECTION_USER_FIELDS" => array(
						0 => "UF_ICON"
					),
					"FILTER_NAME" => $arParams["FILTER_NAME"],
					"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
					"HIDE_SECTION_NAME" => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"]) ? $arParams["SECTIONS_HIDE_SECTION_NAME"] : "N"),
					"ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : ''),
					"SECTION_ROW" => "4"
				),
				$component,
				array("HIDE_ICONS" => "Y")
			);?>

			<?//PREVIEW//
			if(!$_REQUEST["PAGEN_1"] || empty($_REQUEST["PAGEN_1"]) || $_REQUEST["PAGEN_1"] <= 1) {?>
				<?$APPLICATION->IncludeComponent("bitrix:main.include", "preview",
					array(
						"AREA_FILE_SHOW" => "file",
						"PATH" => SITE_DIR."include/objects_preview.php"
					),		
					$component
				);?>
			<?}
			
			//ELEMENTS//
			$GLOBALS[$arParams["FILTER_NAME"]] = array("PROPERTY_HIDE_IN_OBJECTS_LIST" => false);?>
			<?$APPLICATION->IncludeComponent("bitrix:news.list", "objects",
				array(
					"DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
					"DISPLAY_NAME" => $arParams["DISPLAY_NAME"],
					"DISPLAY_PICTURE" => $arParams["DISPLAY_PICTURE"],
					"DISPLAY_PREVIEW_TEXT" => $arParams["DISPLAY_PREVIEW_TEXT"],
					"AJAX_MODE" => $arParams["AJAX_MODE"],
					"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],
					"NEWS_COUNT" => $arParams["NEWS_COUNT"],
					"SORT_BY1" => $arParams["SORT_BY1"],
					"SORT_ORDER1" => $arParams["SORT_ORDER1"],
					"SORT_BY2" => $arParams["SORT_BY2"],
					"SORT_ORDER2" => $arParams["SORT_ORDER2"],
					"FILTER_NAME" => $arParams["FILTER_NAME"],
					"FIELD_CODE" => $arParams["LIST_FIELD_CODE"],
					"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
					"CHECK_DATES" => $arParams["CHECK_DATES"],
					"DETAIL_URL" => $arParams["DETAIL_URL"],
					"PREVIEW_TRUNCATE_LEN" => $arParams["PREVIEW_TRUNCATE_LEN"],
					"ACTIVE_DATE_FORMAT" => $arParams["LIST_ACTIVE_DATE_FORMAT"],
					"SET_TITLE" => "N",
					"SET_BROWSER_TITLE" => $arParams["SET_BROWSER_TITLE"],
					"SET_META_KEYWORDS" => $arParams["SET_META_KEYWORDS"],
					"SET_META_DESCRIPTION" => $arParams["SET_META_DESCRIPTION"],
					"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
					"INCLUDE_IBLOCK_INTO_CHAIN" => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
					"ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
					"HIDE_LINK_WHEN_NO_DETAIL" => $arParams["HIDE_LINK_WHEN_NO_DETAIL"],
					"PARENT_SECTION" => $arResult["VARIABLES"]["SECTION_ID"],
					"PARENT_SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
					"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
					"CACHE_TYPE" => $arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"],
					"CACHE_FILTER" => $arParams["CACHE_FILTER"],
					"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
					"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
					"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
					"PAGER_TITLE" => $arParams["PAGER_TITLE"],
					"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
					"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
					"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
					"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
					"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
					"PAGER_BASE_LINK_ENABLE" => $arParams["PAGER_BASE_LINK_ENABLE"],
					"SET_STATUS_404" => $arParams["SET_STATUS_404"],
					"SHOW_404" => $arParams["SHOW_404"],
					"MESSAGE_404" => $arParams["MESSAGE_404"],
					"PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
					"PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
					"AJAX_OPTION_JUMP" => $arParams["AJAX_OPTION_JUMP"],
					"AJAX_OPTION_STYLE" => $arParams["AJAX_OPTION_STYLE"],
					"AJAX_OPTION_HISTORY" => $arParams["AJAX_OPTION_HISTORY"],
					"AJAX_OPTION_ADDITIONAL" => $arParams["AJAX_OPTION_ADDITIONAL"],
					"SHOW_PROMOTIONS" => $arParams["SHOW_PROMOTIONS"],
					"PROMOTIONS_IBLOCK_ID" => $arParams["PROMOTIONS_IBLOCK_ID"],
					"CATALOG_IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
					"USE_REVIEW" => $arParams["USE_REVIEW"],
					"REVIEWS_IBLOCK_ID" => $arParams["REVIEWS_IBLOCK_ID"]
				),
				$component
			);?>

			<?//DESCRIPTION//
			if(!$_REQUEST["PAGEN_1"] || empty($_REQUEST["PAGEN_1"]) || $_REQUEST["PAGEN_1"] <= 1) {?>
				<?$APPLICATION->IncludeComponent("bitrix:main.include", "description",
					array(
						"AREA_FILE_SHOW" => "file",
						"PATH" => SITE_DIR."include/objects_description.php"
					),
					$component
				);?>
			<?}?>
		</div>
	</div>
	<div class="col-xs-12 col-md-3 objects-col">
		<?//MAP//
		$arFilter = array(
			"ACTIVE" => "Y",
			"IBLOCK_ID" => $arParams["IBLOCK_ID"]
		);

		$isCacheManager = defined("BX_COMP_MANAGED_CACHE") && is_object($GLOBALS["CACHE_MANAGER"]);

		$obCache = new CPHPCache();
		if($obCache->InitCache($arParams["CACHE_TIME"], serialize($arFilter), "/iblock/news")) {
			$arObjects = $obCache->GetVars();
		} elseif(Bitrix\Main\Loader::includeModule("iblock") && $obCache->StartDataCache()) {
			$arObjects = array();
			$rsElements = CIBlockElement::GetList(array($arParams["SORT_BY1"] => $arParams["SORT_ORDER1"], $arParams["SORT_BY2"] => $arParams["SORT_ORDER2"]), $arFilter, false, false, array("ID", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PAGE_URL"));

			if($isCacheManager) {
				$GLOBALS["CACHE_MANAGER"]->StartTagCache("/iblock/news");
				$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);
			}

			while($obElement = $rsElements->GetNextElement()) {
				$arElement = $obElement->GetFields();

				//OBJECT_PREVIEW_PICTURE//
				if($arElement["PREVIEW_PICTURE"] > 0)
					$arElement["PREVIEW_PICTURE"] = CFile::GetFileArray($arElement["PREVIEW_PICTURE"]);
				
				//OBJECT_PROPERTIES//
				$arElement["PROPERTIES"] = $obElement->GetProperties();
				
				if(!empty($arElement["PROPERTIES"]["MAP"]["VALUE"])) {
					$arTmp = explode(",", $arElement["PROPERTIES"]["MAP"]["VALUE"]);
					$arObjects[$arElement["ID"]] = array(
						"ID" => $arElement["ID"],
						"NAME" => $arElement["NAME"],
						"PREVIEW_PICTURE_SRC" => is_array($arElement["PREVIEW_PICTURE"]) ? $arElement["PREVIEW_PICTURE"]["SRC"] : false,
						"DETAIL_PAGE_URL" => $arElement["DETAIL_PAGE_URL"],					
						"ADDRESS" => $arElement["PROPERTIES"]["ADDRESS"]["VALUE"],
						"LON" => $arTmp[1],
						"LAT" => $arTmp[0],
						"AFFILIATES" => $arElement["PROPERTIES"]["AFFILIATES"]["VALUE"],
						"HIDE_IN_OBJECTS_LIST" => !$arElement["PROPERTIES"]["HIDE_IN_OBJECTS_LIST"]["VALUE"] ? false : true
					);
					unset($arTmp);
				}
			}
			
			if($isCacheManager)
				$GLOBALS["CACHE_MANAGER"]->EndTagCache();
			
			$obCache->EndDataCache($arObjects);
		} else {
			$arObjects = array();
		}

		$mapData = array();

		if(!empty($arObjects)) {
			foreach($arObjects as $arObject) {
				if(!empty($arObject["AFFILIATES"])) {
					if(!is_array($arObject["AFFILIATES"])) {
						if(!!$arObjects[$arObject["AFFILIATES"]]["HIDE_IN_OBJECTS_LIST"])
							$arObjects[$arObject["AFFILIATES"]]["PARENT_ID"][] = $arObject["ID"];
					} else {
						foreach($arObject["AFFILIATES"] as $arAffiliateId) {
							if(!!$arObjects[$arAffiliateId]["HIDE_IN_OBJECTS_LIST"])
								$arObjects[$arAffiliateId]["PARENT_ID"][] = $arObject["ID"];
						}
						unset($arAffiliateId);
					}
				}
			}
			unset($arObject);
			
			//PLACEMARKS//
			foreach($arObjects as $arObject) {
				if(!empty($arObject["PARENT_ID"])) {
					foreach($arObject["PARENT_ID"] as $arId) {
						$mapData["PLACEMARKS"][] = array(
							"OBJECT_ID" => $arObject["ID"],
							"LON" => $arObject["LON"],
							"LAT" => $arObject["LAT"],
							"TEXT" => "<div class='object-item-marker'>".(!empty($arObject["PREVIEW_PICTURE_SRC"]) ? "<div class='object-item-marker-image'><img src='".$arObject["PREVIEW_PICTURE_SRC"]."' /></div>" : "")."<div class='object-item-marker-caption'><div class='object-item-marker-title'>".$arObject["NAME"]."</div>".(!empty($arObject["ADDRESS"]) ? "<div class='object-item-marker-address'><i class='icon-map-marker'></i><span>".$arObject["ADDRESS"]."</span></div>" : "")."<a target='_blank' class='object-item-marker-link' href='".$arObjects[$arId]["DETAIL_PAGE_URL"]."'>".GetMessage("OBJECT_MORE")."</a></div></div>"
						);
					}
					unset($arId);
				} else {
					$mapData["PLACEMARKS"][] = array(
						"OBJECT_ID" => $arObject["ID"],
						"LON" => $arObject["LON"],
						"LAT" => $arObject["LAT"],
						"TEXT" => "<div class='object-item-marker'>".(!empty($arObject["PREVIEW_PICTURE_SRC"]) ? "<div class='object-item-marker-image'><img src='".$arObject["PREVIEW_PICTURE_SRC"]."' /></div>" : "")."<div class='object-item-marker-caption'><div class='object-item-marker-title'>".$arObject["NAME"]."</div>".(!empty($arObject["ADDRESS"]) ? "<div class='object-item-marker-address'><i class='icon-map-marker'></i><span>".$arObject["ADDRESS"]."</span></div>" : "")."<a target='_blank' class='object-item-marker-link' href='".$arObject["DETAIL_PAGE_URL"]."'>".GetMessage("OBJECT_MORE")."</a></div></div>"
					);
				}
			}
			unset($arObject);
		}
		
		if($arSettings["MAP_SERVICE"]["VALUE"] != "YANDEX") {
			if(count($mapData["PLACEMARKS"]) == 1) {
				$mapData["google_lat"] = $mapData["PLACEMARKS"][0]["LAT"];
				$mapData["google_lon"] = $mapData["PLACEMARKS"][0]["LON"];
				$mapData["google_scale"] = "13";
			}?>
			<?$APPLICATION->IncludeComponent("bitrix:map.google.view", "objects",
				array(
					"API_KEY" => Bitrix\Main\Config\Option::get("fileman", "google_map_api_key"),
					"CONTROLS" => array(
						0 => "SMALL_ZOOM_CONTROL",
					),
					"INIT_MAP_TYPE" => "ROADMAP",
					"MAP_DATA" => serialize($mapData),
					"MAP_HEIGHT" => "100%",
					"MAP_ID" => "objects",
					"MAP_WIDTH" => "100%",
					"OPTIONS" => array(
						0 => "ENABLE_DBLCLICK_ZOOM",
						1 => "ENABLE_DRAGGING",
						2 => "ENABLE_KEYBOARD",
					),
					"COMPONENT_TEMPLATE" => "objects"
				),
				$component,
				array("HIDE_ICONS" => "Y")
			);?>
		<?} else {
			if($mapData["PLACEMARKS"] != false && count($mapData["PLACEMARKS"]) == 1) { //PRO_WS//
				$mapData["yandex_lat"] = $mapData["PLACEMARKS"][0]["LAT"];
				$mapData["yandex_lon"] = $mapData["PLACEMARKS"][0]["LON"];
				$mapData["yandex_scale"] = "14";
			}?>
			<?$APPLICATION->IncludeComponent("altop:map.yandex.view.elektronext", "objects",
				array(
					"API_KEY" => Bitrix\Main\Config\Option::get("fileman", "yandex_map_api_key"),
					"CONTROLS" => array(
						0 => "fullscreenControl",
						1 => "zoomControl",
					),
					"INIT_MAP_TYPE" => "map",
					"MAP_DATA" => serialize($mapData),
					"MAP_HEIGHT" => "100%",
					"MAP_ID" => "objects",
					"MAP_WIDTH" => "100%",
					"OPTIONS" => array(
						0 => "drag",
						1 => "dblClickZoom",
						2 => "multiTouch",
						3 => "rightMouseButtonMagnifier",
					)
				),
				$component,
				array("HIDE_ICONS" => "Y")
			);?>
		<?}?>
	</div>
</div>

<?//TITLE//
if(!empty($_REQUEST["PAGEN_1"]) && $_REQUEST["PAGEN_1"] > 1) {
	$APPLICATION->SetPageProperty("title", $arParams["PAGER_TITLE"]." | ".GetMessage("SECT_TITLE")." ".$_REQUEST["PAGEN_1"]);
	$APPLICATION->SetPageProperty("keywords", "");
	$APPLICATION->SetPageProperty("description", "");
}