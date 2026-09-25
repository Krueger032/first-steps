<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

global $arSettings;?>

<div class="objects-cols">
	<div class="objects-col objects-col-main">
		<div class="objects-container">
			<?//SECTION_LIST//?>
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
			
			//ELEMENTS//?>			
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
	<aside class="objects-map-aside" aria-label="<?=htmlspecialcharsbx(GetMessage('OBJECTS_MAP_ARIA'))?>">
		<div class="objects-map-anchor">
		<?//MAP//
		$arFilter = array(
			"ACTIVE" => "Y",
			"IBLOCK_ID" => $arParams["IBLOCK_ID"]
		);

		$isCacheManager = defined("BX_COMP_MANAGED_CACHE") && is_object($GLOBALS["CACHE_MANAGER"]);

		$obCache = new CPHPCache();
		if($obCache->InitCache($arParams["CACHE_TIME"], serialize($arFilter), "/iblock/news")) {
			$map = $obCache->GetVars();
		} elseif(Bitrix\Main\Loader::includeModule("iblock") && $obCache->StartDataCache()) {
			$map = array();
			$rsElements = CIBlockElement::GetList(array($arParams["SORT_BY1"] => $arParams["SORT_ORDER1"], $arParams["SORT_BY2"] => $arParams["SORT_ORDER2"]), $arFilter, false, false, array("ID", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PAGE_URL"));

			if($isCacheManager) {
				$GLOBALS["CACHE_MANAGER"]->StartTagCache("/iblock/news");
				$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);
			}

			while($obElement = $rsElements->GetNextElement()) {
				$arElement = $obElement->GetFields();
				
				//PREVIEW_PICTURE//
				if($arElement["PREVIEW_PICTURE"] > 0)
					$arElement["PREVIEW_PICTURE"] = CFile::GetFileArray($arElement["PREVIEW_PICTURE"]);
				
				//PROPERTIES//
				$arElement["PROPERTIES"] = $obElement->GetProperties();						
				if(!empty($arElement["PROPERTIES"]["MAP"]["VALUE"])) {
					$arTmp = explode(",", $arElement["PROPERTIES"]["MAP"]["VALUE"]);
					$map["PLACEMARKS"][] = array(
						"OBJECT_ID" => $arElement["ID"],	
						"LON" => $arTmp[1],
						"LAT" => $arTmp[0],
						"TEXT" => "<div class='object-item-marker'>".(is_array($arElement["PREVIEW_PICTURE"]) ? "<div class='object-item-marker-image'><img src='".$arElement["PREVIEW_PICTURE"]["SRC"]."' /></div>" : "")."<div class='object-item-marker-caption'><div class='object-item-marker-title'>".$arElement["NAME"]."</div>".(!empty($arElement["PROPERTIES"]["ADDRESS"]["VALUE"]) ? "<div class='object-item-marker-address'><i class='icon-map-marker'></i><span>".$arElement["PROPERTIES"]["ADDRESS"]["VALUE"]."</span></div>" : "")."<a target='_blank' class='object-item-marker-link' href='".$arElement["DETAIL_PAGE_URL"]."'>".GetMessage("OBJECT_MORE")."</a></div></div>"
					);
					unset($arTmp);
				}
			}

			if($isCacheManager)
				$GLOBALS["CACHE_MANAGER"]->EndTagCache();
			
			$obCache->EndDataCache($map);
		} else {
			$map = array();
		}

		if (!isset($map["PLACEMARKS"]) || !is_array($map["PLACEMARKS"])) {
			$map["PLACEMARKS"] = array();
		}

		if (count($map["PLACEMARKS"]) === 0) {
			$map["PLACEMARKS"][] = array(
				"OBJECT_ID" => 0,
				"LAT" => "55.61052",
				"LON" => "37.48751",
				"TEXT" => "<div class='object-item-marker'><div class='object-item-marker-caption'><div class='object-item-marker-title'>".htmlspecialcharsbx(GetMessage("OBJECTS_MAP_OFFICE_TITLE"))."</div><div class='object-item-marker-address'><i class='icon-map-marker'></i><span>".htmlspecialcharsbx(GetMessage("OBJECTS_MAP_OFFICE_ADDRESS"))."</span></div></div></div>"
			);
		}

		$objectsMapDefaultLat = 55.61052;
		$objectsMapDefaultLon = 37.48751;
		if(count($map["PLACEMARKS"]) === 1) {
			$map["yandex_lat"] = $map["PLACEMARKS"][0]["LAT"];
			$map["yandex_lon"] = $map["PLACEMARKS"][0]["LON"];
			$map["yandex_scale"] = "14";
			$map["google_lat"] = $map["PLACEMARKS"][0]["LAT"];
			$map["google_lon"] = $map["PLACEMARKS"][0]["LON"];
			$map["google_scale"] = "13";
		} else {
			$map["yandex_lat"] = $objectsMapDefaultLat;
			$map["yandex_lon"] = $objectsMapDefaultLon;
			$map["yandex_scale"] = "16";
			$map["google_lat"] = $objectsMapDefaultLat;
			$map["google_lon"] = $objectsMapDefaultLon;
			$map["google_scale"] = "16";
		}
		
		if($arSettings["MAP_SERVICE"]["VALUE"] != "YANDEX") {
			?>
			<?$APPLICATION->IncludeComponent("bitrix:map.google.view", "objects",
				array(
					"API_KEY" => Bitrix\Main\Config\Option::get("fileman", "google_map_api_key"),
					"CONTROLS" => array(
						0 => "SMALL_ZOOM_CONTROL",
					),
					"INIT_MAP_TYPE" => "ROADMAP",
					"MAP_DATA" => serialize($map),
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
		<?} else {?>
			<?$APPLICATION->IncludeComponent("altop:map.yandex.view.enext", "objects",
				array(
					"API_KEY" => Bitrix\Main\Config\Option::get("fileman", "yandex_map_api_key"),
					"CONTROLS" => array(
						0 => "fullscreenControl",
						1 => "zoomControl",
					),
					"INIT_MAP_TYPE" => "map",
					"MAP_DATA" => serialize($map),
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
	</aside>
</div>

<?//TITLE//
if(!empty($_REQUEST["PAGEN_1"]) && $_REQUEST["PAGEN_1"] > 1) {
	$APPLICATION->SetPageProperty("title", $arParams["PAGER_TITLE"]." | ".GetMessage("SECT_TITLE")." ".$_REQUEST["PAGEN_1"]);
	$APPLICATION->SetPageProperty("keywords", "");
	$APPLICATION->SetPageProperty("description", "");
}