<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

global $arSettings;

$isCacheManager = defined("BX_COMP_MANAGED_CACHE") && is_object($GLOBALS["CACHE_MANAGER"]);

if($arResult["VARIABLES"]["SECTION_ID"] || $arResult["VARIABLES"]["SECTION_CODE"]) {
	//CURRENT_SECTION//
	$arFilter = array(
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ACTIVE" => "Y",
		"GLOBAL_ACTIVE" => "Y",
	);

	if(intval($arResult["VARIABLES"]["SECTION_ID"]) > 0) {
		$arFilter["ID"] = $arResult["VARIABLES"]["SECTION_ID"];
	} elseif($arResult["VARIABLES"]["SECTION_CODE"] != "") {
		$arFilter["=CODE"] = $arResult["VARIABLES"]["SECTION_CODE"];
	}
	
	$obCache = new CPHPCache();
	if($obCache->InitCache($arParams["CACHE_TIME"], serialize($arFilter), "/iblock/news")) {
		$arCurSection = $obCache->GetVars();
	} elseif(Bitrix\Main\Loader::includeModule("iblock") && $obCache->StartDataCache()) {
		$arCurSection = array();
		$rsSection = CIBlockSection::GetList(array(), $arFilter, false, array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME", "PICTURE", "DESCRIPTION", "DEPTH_LEVEL", "UF_BANNER", "UF_BANNER_URL", "UF_PREVIEW"));

		if($isCacheManager)
			$GLOBALS["CACHE_MANAGER"]->StartTagCache("/iblock/news");

		if($arCurSection = $rsSection->Fetch()) {
			if($isCacheManager)
				$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);

			//SECTION_PICTURE//
			if($arCurSection["PICTURE"] > 0)
				$arCurSection["PICTURE"] = CFile::GetFileArray($arCurSection["PICTURE"]);
			
			//SECTION_BANNER//
			if($arCurSection["UF_BANNER"] > 0)
				$arCurSection["UF_BANNER"] = CFile::GetFileArray($arCurSection["UF_BANNER"]);

			//SECTION_TITLE//
			//SECTION_META_DESCRIPTION//
			$ipropValues = new Bitrix\Iblock\InheritedProperty\SectionValues($arCurSection["IBLOCK_ID"], $arCurSection["ID"]);
			$iproperty = $ipropValues->getValues();
			$arCurSection["TITLE"] = !empty($iproperty["SECTION_PAGE_TITLE"]) ? $iproperty["SECTION_PAGE_TITLE"] : $arCurSection["NAME"];
			$arCurSection["META_DESCRIPTION"] = !empty($iproperty["SECTION_META_DESCRIPTION"]) ? $iproperty["SECTION_META_DESCRIPTION"] : "";
			unset($iproperty, $ipropValues);

			//SECTION_BREADCRUMBS//
			if($arParams["ADD_SECTIONS_CHAIN"] == "Y") {					
				$arCurSection["PATH"] = array();
				$rsPath = CIBlockSection::GetNavChain($arCurSection["IBLOCK_ID"], $arCurSection["ID"], array("ID", "IBLOCK_ID", "NAME", "SECTION_PAGE_URL"));
				while($path = $rsPath->GetNext()) {								
					$ipropValues = new Bitrix\Iblock\InheritedProperty\SectionValues($arCurSection["IBLOCK_ID"], $path["ID"]);
					$iproperty = $ipropValues->getValues();
					$path["TITLE"] = !empty($iproperty["SECTION_PAGE_TITLE"]) ? $iproperty["SECTION_PAGE_TITLE"] : $path["NAME"];
					$arCurSection["PATH"][$path["ID"]] = $path;
				}
				unset($iproperty, $ipropValues, $path, $rsPath);
				
				if(!empty($arCurSection["PATH"])) {
					$rsSections = CIBlockSection::GetList(array(), array("IBLOCK_ID" => $arCurSection["IBLOCK_ID"], "ID" => array_keys($arCurSection["PATH"])), false, array("ID", "IBLOCK_ID", "UF_BREADCRUMB_TITLE"));
					while($arSection = $rsSections->GetNext()) {
						$arCurSection["PATH"][$arSection["ID"]]["BREADCRUMB_TITLE"] = $arSection["UF_BREADCRUMB_TITLE"];
					}
					unset($arSection, $rsSections);
				}
			}
		}

		if($isCacheManager)
			$GLOBALS["CACHE_MANAGER"]->EndTagCache();
		
		$obCache->EndDataCache($arCurSection);
	} else {
		$arCurSection = array();
	}

	//SECTION_BANNER//
	if(is_array($arCurSection["UF_BANNER"])) {
		ob_start();?>
		<div class="catalog-section-pic">
			<a href="<?=(!empty($arCurSection['UF_BANNER_URL']) ? $arCurSection['UF_BANNER_URL'] : 'javascript:void(0)')?>">
				<img src="<?=$arCurSection['UF_BANNER']['SRC']?>" width="<?=$arCurSection['UF_BANNER']['WIDTH']?>" height="<?=$arCurSection['UF_BANNER']['HEIGHT']?>" alt="<?=$arCurSection['TITLE']?>" />
			</a>
		</div>	
		<?$APPLICATION->AddViewContent("UF_BANNER", ob_get_contents());
		ob_end_clean();
	}?>

	<div class="row objects-cols">
		<div class="col-xs-12 col-md-9 objects-col">
			<div class="objects-container">
				<?//SECTION_LIST//?>
				<?$APPLICATION->IncludeComponent("bitrix:catalog.section.list", "catalog",
					array(
						"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
						"IBLOCK_ID" => $arParams["IBLOCK_ID"],
						"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
						"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
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
						"ADD_SECTIONS_CHAIN" => "N",
						"SECTION_ROW" => "4"
					),
					$component,
					array("HIDE_ICONS" => "Y")
				);?>

				<?//SECTION_PREVIEW//
				if(!empty($arCurSection["UF_PREVIEW"])) {
					if(!$_REQUEST["PAGEN_1"] || empty($_REQUEST["PAGEN_1"]) || $_REQUEST["PAGEN_1"] <= 1) {?>
						<div class="preview">
							<?=$arCurSection["UF_PREVIEW"];?>
						</div>
					<?}
				}
				
				//SECTION_ELEMENTS//?>
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
						"SET_TITLE" => $arParams["SET_TITLE"],
						"SET_BROWSER_TITLE" => $arParams["SET_BROWSER_TITLE"],
						"SET_META_KEYWORDS" => $arParams["SET_META_KEYWORDS"],
						"SET_META_DESCRIPTION" => $arParams["SET_META_DESCRIPTION"],
						"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
						"INCLUDE_IBLOCK_INTO_CHAIN" => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
						"ADD_SECTIONS_CHAIN" => "N",
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

				<?//SECTION_DESCRIPTION//
				if(!empty($arCurSection["DESCRIPTION"])) {
					if(!$_REQUEST["PAGEN_1"] || empty($_REQUEST["PAGEN_1"]) || $_REQUEST["PAGEN_1"] <= 1) {?>
						<div class="description">
							<?=$arCurSection["DESCRIPTION"];?>
						</div>
					<?}
				}?>
			</div>
		</div>
		<div class="col-xs-12 col-md-3 objects-col">		
			<?//MAP//
			$arFilter = array(
				"ACTIVE" => "Y",
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"SECTION_ID" => $arCurSection["ID"],
				"INCLUDE_SUBSECTIONS" => "Y",
				"SECTION_GLOBAL_ACTIVE" => "Y"
			);

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
			
			$placemarks = (isset($map["PLACEMARKS"]) && is_array($map["PLACEMARKS"]) ? $map["PLACEMARKS"] : array());
			
			if($arSettings["MAP_SERVICE"]["VALUE"] != "YANDEX") {
				if(count($placemarks) == 1) {
					$map["google_lat"] = $placemarks[0]["LAT"];
					$map["google_lon"] = $placemarks[0]["LON"];
					$map["google_scale"] = "13";
				}?>
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
			<?} else {
				if(count($placemarks) == 1) {
					$map["yandex_lat"] = $placemarks[0]["LAT"];
					$map["yandex_lon"] = $placemarks[0]["LON"];
					$map["yandex_scale"] = "14";
				}?>
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
	</div>

	<?//SECTION_BREADCRUMBS//
	if(!empty($arCurSection["PATH"])) {
		foreach($arCurSection["PATH"] as $path) {
			if(!empty($path["BREADCRUMB_TITLE"]))
				$APPLICATION->AddChainItem($path["BREADCRUMB_TITLE"], $path["~SECTION_PAGE_URL"]);
			elseif(!empty($path["TITLE"]))
				$APPLICATION->AddChainItem($path["TITLE"], $path["~SECTION_PAGE_URL"]);
		}
		unset($path);
	}

	//SECTION_META_TITLE//
	//SECTION_META_KEYWORDS//
	//SECTION_META_DESCRIPTION//
	if(!empty($arCurSection["TITLE"])) {
		$APPLICATION->SetTitle($arCurSection["TITLE"]);
		if(!empty($_REQUEST["PAGEN_1"]) && $_REQUEST["PAGEN_1"] > 1) {		
			$APPLICATION->SetPageProperty("title", $arCurSection["TITLE"]." | ".Loc::getMessage("SECT_TITLE")." ".$_REQUEST["PAGEN_1"]);
			$APPLICATION->SetPageProperty("keywords", "");
			$APPLICATION->SetPageProperty("description", !empty($arCurSection["META_DESCRIPTION"]) ? $arCurSection["META_DESCRIPTION"]." | ".Loc::getMessage("SECT_TITLE")." ".$_REQUEST["PAGEN_1"] : "");
		}
	}

	//SECTION_OPEN_GRAPH//
	if(!empty($arCurSection["TITLE"]))
		$APPLICATION->AddHeadString("<meta property='og:type' content='website'/>");$APPLICATION->AddHeadString("<meta property='og:title' content='".$arCurSection["TITLE"]."' />", true);
	if(!empty($arCurSection["META_DESCRIPTION"]))
		$APPLICATION->AddHeadString("<meta property='og:description' content='".$arCurSection["META_DESCRIPTION"]."' />", true);

	$ogScheme = CMain::IsHTTPS() ? "https" : "http";
	$APPLICATION->AddHeadString("<meta property='og:url' content='".$ogScheme."://".SITE_SERVER_NAME.$APPLICATION->GetCurPage()."' />", true);
	if(is_array($arCurSection["PICTURE"])) {
		$APPLICATION->AddHeadString("<meta property='og:image' content='".$ogScheme."://".SITE_SERVER_NAME.$arCurSection["PICTURE"]["SRC"]."' />", true);
		$APPLICATION->AddHeadString("<meta property='og:image:width' content='".$arCurSection["PICTURE"]["WIDTH"]."' />", true);
		$APPLICATION->AddHeadString("<meta property='og:image:height' content='".$arCurSection["PICTURE"]["HEIGHT"]."' />", true);
		$APPLICATION->AddHeadString("<link rel='image_src' href='".$ogScheme."://".SITE_SERVER_NAME.$arCurSection["PICTURE"]["SRC"]."' />", true);
	} elseif(is_array($arCurSection["UF_BANNER"])) {
		$APPLICATION->AddHeadString("<meta property='og:image' content='".$ogScheme."://".SITE_SERVER_NAME.$arCurSection["UF_BANNER"]["SRC"]."' />", true);
		$APPLICATION->AddHeadString("<meta property='og:image:width' content='".$arCurSection["UF_BANNER"]["WIDTH"]."' />", true);
		$APPLICATION->AddHeadString("<meta property='og:image:height' content='".$arCurSection["UF_BANNER"]["HEIGHT"]."' />", true);
		$APPLICATION->AddHeadString("<link rel='image_src' href='".$ogScheme."://".SITE_SERVER_NAME.$arCurSection["UF_BANNER"]["SRC"]."' />", true);
	}
} else {
	//ELEMENT//?>
	<?$elementId = $APPLICATION->IncludeComponent("bitrix:news.detail", "object",
		array(
			"DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
			"DISPLAY_NAME" => $arParams["DISPLAY_NAME"],
			"DISPLAY_PICTURE" => $arParams["DISPLAY_PICTURE"],
			"DISPLAY_PREVIEW_TEXT" => $arParams["DISPLAY_PREVIEW_TEXT"],
			"USE_SHARE" => $arParams["USE_SHARE"],
			"SHARE_HIDE" => $arParams["SHARE_HIDE"],
			"SHARE_TEMPLATE" => $arParams["SHARE_TEMPLATE"],
			"SHARE_HANDLERS" => $arParams["SHARE_HANDLERS"],
			"SHARE_SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
			"SHARE_SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
			"AJAX_MODE" => $arParams["AJAX_MODE"],
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
			"ELEMENT_CODE" => $arResult["VARIABLES"]["SECTION_CODE_PATH"],
			"CHECK_DATES" => $arParams["CHECK_DATES"],
			"FIELD_CODE" => $arParams["DETAIL_FIELD_CODE"],
			"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
			"IBLOCK_URL" => $arParams["IBLOCK_URL"],
			"DETAIL_URL" => $arParams["DETAIL_URL"],
			"SET_TITLE" => $arParams["SET_TITLE"],
			"SET_CANONICAL_URL" => $arParams["DETAIL_SET_CANONICAL_URL"],
			"SET_BROWSER_TITLE" => $arParams["SET_BROWSER_TITLE"],
			"BROWSER_TITLE" => $arParams["BROWSER_TITLE"],
			"SET_META_KEYWORDS" => $arParams["SET_META_KEYWORDS"],
			"META_KEYWORDS" => $arParams["META_KEYWORDS"],
			"SET_META_DESCRIPTION" => $arParams["SET_META_DESCRIPTION"],
			"META_DESCRIPTION" => $arParams["META_DESCRIPTION"],
			"SET_STATUS_404" => $arParams["SET_STATUS_404"],
			"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
			"INCLUDE_IBLOCK_INTO_CHAIN" => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
			"ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
			"ADD_ELEMENT_CHAIN" => "N",
			"ACTIVE_DATE_FORMAT" => $arParams["DETAIL_ACTIVE_DATE_FORMAT"],
			"USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
			"GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			"DISPLAY_TOP_PAGER" => $arParams["DETAIL_DISPLAY_TOP_PAGER"],
			"DISPLAY_BOTTOM_PAGER" => $arParams["DETAIL_DISPLAY_BOTTOM_PAGER"],
			"PAGER_TITLE" => $arParams["DETAIL_PAGER_TITLE"],
			"PAGER_TEMPLATE" => $arParams["DETAIL_PAGER_TEMPLATE"],
			"PAGER_SHOW_ALL" => $arParams["DETAIL_PAGER_SHOW_ALL"],
			"PAGER_BASE_LINK_ENABLE" => $arParams["PAGER_BASE_LINK_ENABLE"],
			"SET_STATUS_404" => $arParams["SET_STATUS_404"],
			"SHOW_404" => $arParams["SHOW_404"],
			"MESSAGE_404" => $arParams["MESSAGE_404"],
			"PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
			"PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
			"AJAX_OPTION_JUMP" => $arParams["AJAX_OPTION_JUMP"],
			"AJAX_OPTION_STYLE" => $arParams["AJAX_OPTION_STYLE"],
			"AJAX_OPTION_HISTORY" => $arParams["AJAX_OPTION_HISTORY"],
			"SHOW_PROMOTIONS" => $arParams["SHOW_PROMOTIONS"],
			"PROMOTIONS_IBLOCK_TYPE" => $arParams["PROMOTIONS_IBLOCK_TYPE"],
			"PROMOTIONS_IBLOCK_ID" => $arParams["PROMOTIONS_IBLOCK_ID"],
			"PROMOTIONS_NEWS_COUNT" => $arParams["PROMOTIONS_NEWS_COUNT"],
			"PROMOTIONS_SORT_BY1" => $arParams["PROMOTIONS_SORT_BY1"],
			"PROMOTIONS_SORT_ORDER1" => $arParams["PROMOTIONS_SORT_ORDER1"],
			"PROMOTIONS_SORT_BY2" => $arParams["PROMOTIONS_SORT_BY2"],
			"PROMOTIONS_SORT_ORDER2" => $arParams["PROMOTIONS_SORT_ORDER2"],
			"PROMOTIONS_PROPERTY_CODE" => $arParams["PROMOTIONS_PROPERTY_CODE"],		
			"CATALOG_IBLOCK_TYPE" => $arParams["CATALOG_IBLOCK_TYPE"],
			"CATALOG_IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
			"CATALOG_INCLUDE_SUBSECTIONS" => $arParams["CATALOG_INCLUDE_SUBSECTIONS"],
			"CATALOG_HIDE_NOT_AVAILABLE" => $arParams["CATALOG_HIDE_NOT_AVAILABLE"],
			"CATALOG_HIDE_NOT_AVAILABLE_OFFERS" => $arParams["CATALOG_HIDE_NOT_AVAILABLE_OFFERS"],
			"CATALOG_ELEMENT_SORT_FIELD" => $arParams["CATALOG_ELEMENT_SORT_FIELD"],
			"CATALOG_ELEMENT_SORT_ORDER" => $arParams["CATALOG_ELEMENT_SORT_ORDER"],
			"CATALOG_ELEMENT_SORT_FIELD2" => $arParams["CATALOG_ELEMENT_SORT_FIELD2"],
			"CATALOG_ELEMENT_SORT_ORDER2" => $arParams["CATALOG_ELEMENT_SORT_ORDER2"],
			"CATALOG_OFFERS_SORT_FIELD" => $arParams["CATALOG_OFFERS_SORT_FIELD"],
			"CATALOG_OFFERS_SORT_ORDER" => $arParams["CATALOG_OFFERS_SORT_ORDER"],
			"CATALOG_OFFERS_SORT_FIELD2" => $arParams["CATALOG_OFFERS_SORT_FIELD2"],
			"CATALOG_OFFERS_SORT_ORDER2" => $arParams["CATALOG_OFFERS_SORT_ORDER2"],
			"CATALOG_PROPERTY_CODE" => $arParams["CATALOG_PROPERTY_CODE"],		
			"CATALOG_OFFERS_FIELD_CODE" => $arParams["CATALOG_OFFERS_FIELD_CODE"],
			"CATALOG_OFFERS_PROPERTY_CODE" => $arParams["CATALOG_OFFERS_PROPERTY_CODE"],
			"CATALOG_OFFERS_LIMIT" => $arParams["CATALOG_OFFERS_LIMIT"],
			"CATALOG_PRODUCT_DISPLAY_MODE" => $arParams["CATALOG_PRODUCT_DISPLAY_MODE"],
			"CATALOG_OFFER_TREE_PROPS" => $arParams["CATALOG_OFFER_TREE_PROPS"],
			"CATALOG_SHOW_DISCOUNT_PERCENT" => $arParams["CATALOG_SHOW_DISCOUNT_PERCENT"],		
			"CATALOG_SHOW_OLD_PRICE" => $arParams["CATALOG_SHOW_OLD_PRICE"],
			"CATALOG_SHOW_MAX_QUANTITY" => $arParams["CATALOG_SHOW_MAX_QUANTITY"],
			"CATALOG_MESS_SHOW_MAX_QUANTITY" => $arParams["CATALOG_MESS_SHOW_MAX_QUANTITY"],
			"CATALOG_RELATIVE_QUANTITY_FACTOR" => $arParams["CATALOG_RELATIVE_QUANTITY_FACTOR"],
			"CATALOG_MESS_RELATIVE_QUANTITY_MANY" => $arParams["CATALOG_MESS_RELATIVE_QUANTITY_MANY"],
			"CATALOG_MESS_RELATIVE_QUANTITY_FEW" => $arParams["CATALOG_MESS_RELATIVE_QUANTITY_FEW"],		
			"CATALOG_MESS_BTN_BUY" => $arParams["CATALOG_MESS_BTN_BUY"],
			"CATALOG_MESS_BTN_ADD_TO_BASKET" => $arParams["CATALOG_MESS_BTN_ADD_TO_BASKET"],		
			"CATALOG_MESS_BTN_DETAIL" => $arParams["CATALOG_MESS_BTN_DETAIL"],
			"CATALOG_MESS_NOT_AVAILABLE" => $arParams["CATALOG_MESS_NOT_AVAILABLE"],
			"CATALOG_USE_MAIN_ELEMENT_SECTION" => $arParams["CATALOG_USE_MAIN_ELEMENT_SECTION"],
			"CATALOG_USE_REVIEW" => $arParams["CATALOG_USE_REVIEW"],
			"CATALOG_REVIEWS_IBLOCK_TYPE" => $arParams["CATALOG_REVIEWS_IBLOCK_TYPE"],
			"CATALOG_REVIEWS_IBLOCK_ID" => $arParams["CATALOG_REVIEWS_IBLOCK_ID"],
			"CATALOG_REVIEWS_NEWS_COUNT" => $arParams["CATALOG_REVIEWS_NEWS_COUNT"],
			"CATALOG_REVIEWS_SORT_BY1" => $arParams["CATALOG_REVIEWS_SORT_BY1"],
			"CATALOG_REVIEWS_SORT_ORDER1" => $arParams["CATALOG_REVIEWS_SORT_ORDER1"],
			"CATALOG_REVIEWS_SORT_BY2" => $arParams["CATALOG_REVIEWS_SORT_BY2"],
			"CATALOG_REVIEWS_SORT_ORDER2" => $arParams["CATALOG_REVIEWS_SORT_ORDER2"],
			"CATALOG_REVIEWS_ACTIVE_DATE_FORMAT" => $arParams["CATALOG_REVIEWS_ACTIVE_DATE_FORMAT"],
			"CATALOG_REVIEWS_PROPERTY_CODE" => $arParams["CATALOG_REVIEWS_PROPERTY_CODE"],
			"CATALOG_MESS_REVIEWS_TAB" => $arParams["CATALOG_MESS_REVIEWS_TAB"],
			"CATALOG_PRICE_CODE" => $arParams["CATALOG_PRICE_CODE"],
			"CATALOG_USE_PRICE_COUNT" => $arParams["CATALOG_USE_PRICE_COUNT"],
			"CATALOG_SHOW_PRICE_COUNT" => $arParams["CATALOG_SHOW_PRICE_COUNT"],
			"CATALOG_PRICE_VAT_INCLUDE" => $arParams["CATALOG_PRICE_VAT_INCLUDE"],
			"CATALOG_CONVERT_CURRENCY" => $arParams["CATALOG_CONVERT_CURRENCY"],
			"CATALOG_CURRENCY_ID" => $arParams["CATALOG_CURRENCY_ID"],
			"CATALOG_DETAIL_USE_RATIO_IN_RANGES" => $arParams["CATALOG_DETAIL_USE_RATIO_IN_RANGES"],
			"CATALOG_BASKET_URL" => $arParams["CATALOG_BASKET_URL"],
			"CATALOG_USE_PRODUCT_QUANTITY" => $arParams["CATALOG_USE_PRODUCT_QUANTITY"],
			"CATALOG_ADD_PROPERTIES_TO_BASKET" => $arParams["CATALOG_ADD_PROPERTIES_TO_BASKET"],
			"CATALOG_PARTIAL_PRODUCT_PROPERTIES" => $arParams["CATALOG_PARTIAL_PRODUCT_PROPERTIES"],
			"CATALOG_PRODUCT_PROPERTIES" => $arParams["CATALOG_PRODUCT_PROPERTIES"],
			"CATALOG_OFFERS_CART_PROPERTIES" => $arParams["CATALOG_OFFERS_CART_PROPERTIES"],
			"CATALOG_ADD_TO_BASKET_ACTION" => $arParams["CATALOG_ADD_TO_BASKET_ACTION"],
			"CATALOG_DISPLAY_COMPARE" => $arParams["CATALOG_DISPLAY_COMPARE"],
			"CATALOG_COMPARE_PATH" => $arParams["CATALOG_COMPARE_PATH"],
			"CATALOG_MESS_BTN_COMPARE" => $arParams["CATALOG_MESS_BTN_COMPARE"],
			"CATALOG_COMPARE_NAME" => $arParams["CATALOG_COMPARE_NAME"],
			"CATALOG_DETAIL_ADD_PICT_PROP" => $arParams["CATALOG_DETAIL_ADD_PICT_PROP"],
			"CATALOG_DETAIL_OFFER_ADD_PICT_PROP" => $arParams["CATALOG_DETAIL_OFFER_ADD_PICT_PROP"],
			"CATALOG_DETAIL_PROPERTY_CODE" => $arParams["CATALOG_DETAIL_PROPERTY_CODE"],				
			"CATALOG_DETAIL_OFFERS_FIELD_CODE" => $arParams["CATALOG_DETAIL_OFFERS_FIELD_CODE"],
			"CATALOG_DETAIL_OFFERS_PROPERTY_CODE" => $arParams["CATALOG_DETAIL_OFFERS_PROPERTY_CODE"],
			"CATALOG_DETAIL_MAIN_BLOCK_PROPERTY_CODE" => $arParams["CATALOG_DETAIL_MAIN_BLOCK_PROPERTY_CODE"],
			"CATALOG_DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE" => $arParams["CATALOG_DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE"],	
			"CATALOG_DETAIL_IMAGE_RESOLUTION" => $arParams["CATALOG_DETAIL_IMAGE_RESOLUTION"],				
			"CATALOG_DETAIL_ADD_DETAIL_TO_SLIDER" => $arParams["CATALOG_DETAIL_ADD_DETAIL_TO_SLIDER"],
			"CATALOG_DETAIL_DETAIL_PICTURE_MODE" => $arParams["CATALOG_DETAIL_DETAIL_PICTURE_MODE"],
			"CATALOG_DETAIL_SHOW_SLIDER" => $arParams["CATALOG_DETAIL_SHOW_SLIDER"],
			"CATALOG_DETAIL_SLIDER_INTERVAL" => $arParams["CATALOG_DETAIL_SLIDER_INTERVAL"],
			"CATALOG_DETAIL_SLIDER_PROGRESS" => $arParams["CATALOG_DETAIL_SLIDER_PROGRESS"],
			"CATALOG_USE_GIFTS_DETAIL" => $arParams["CATALOG_USE_GIFTS_DETAIL"],
			"CATALOG_GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => $arParams["CATALOG_GIFTS_DETAIL_PAGE_ELEMENT_COUNT"],
			"CATALOG_GIFTS_DETAIL_HIDE_BLOCK_TITLE" => $arParams["CATALOG_GIFTS_DETAIL_HIDE_BLOCK_TITLE"],
			"CATALOG_GIFTS_DETAIL_BLOCK_TITLE" => $arParams["CATALOG_GIFTS_DETAIL_BLOCK_TITLE"],
			"CATALOG_GIFTS_DETAIL_TEXT_LABEL_GIFT" => $arParams["CATALOG_GIFTS_DETAIL_TEXT_LABEL_GIFT"],
			"CATALOG_GIFTS_MESS_BTN_BUY" => $arParams["CATALOG_GIFTS_MESS_BTN_BUY"],
			"CATALOG_USE_STORE" => $arParams["CATALOG_USE_STORE"],
			"CATALOG_STORE_PATH" => $arParams["CATALOG_STORE_PATH"],
			"CATALOG_STORES" => $arParams["CATALOG_STORES"],
			"CATALOG_USE_MIN_AMOUNT" => $arParams["CATALOG_USE_MIN_AMOUNT"],
			"CATALOG_USER_FIELDS" => $arParams["CATALOG_USER_FIELDS"],
			"CATALOG_FIELDS" => $arParams["CATALOG_FIELDS"],
			"CATALOG_MIN_AMOUNT" => $arParams["CATALOG_MIN_AMOUNT"],
			"CATALOG_SHOW_EMPTY_STORE" => $arParams["CATALOG_SHOW_EMPTY_STORE"],
			"CATALOG_SHOW_GENERAL_STORE_INFORMATION" => $arParams["CATALOG_SHOW_GENERAL_STORE_INFORMATION"],
			"CATALOG_MAIN_TITLE" => $arParams["CATALOG_MAIN_TITLE"],
			"CATALOG_SET_ITEMS_COUNT" => $arParams["CATALOG_SET_ITEMS_COUNT"],
			"CATALOG_REINIT_ADD_BUY_URL_TEMPLATE" => "Y",
			"USE_REVIEW" => $arParams["USE_REVIEW"],
			"REVIEWS_IBLOCK_TYPE" => $arParams["REVIEWS_IBLOCK_TYPE"],
			"REVIEWS_IBLOCK_ID" => $arParams["REVIEWS_IBLOCK_ID"],
			"REVIEWS_NEWS_COUNT" => $arParams["REVIEWS_NEWS_COUNT"],
			"REVIEWS_SORT_BY1" => $arParams["REVIEWS_SORT_BY1"],
			"REVIEWS_SORT_ORDER1" => $arParams["REVIEWS_SORT_ORDER1"],
			"REVIEWS_SORT_BY2" => $arParams["REVIEWS_SORT_BY2"],
			"REVIEWS_SORT_ORDER2" => $arParams["REVIEWS_SORT_ORDER2"],
			"REVIEWS_ACTIVE_DATE_FORMAT" => $arParams["REVIEWS_ACTIVE_DATE_FORMAT"],
			"REVIEWS_PROPERTY_CODE" => $arParams["REVIEWS_PROPERTY_CODE"],
			"OBJECTS_USE_REVIEW" => $arParams["OBJECTS_USE_REVIEW"],
			"OBJECTS_REVIEWS_IBLOCK_ID" => $arParams["OBJECTS_REVIEWS_IBLOCK_ID"],
			"CONTACTS_IBLOCK_ID" => $arParams["CONTACTS_IBLOCK_ID"],
			"CONTACTS_USE_REVIEW" => $arParams["CONTACTS_USE_REVIEW"],
			"CONTACTS_REVIEWS_IBLOCK_ID" => $arParams["CONTACTS_REVIEWS_IBLOCK_ID"]
		),
		$component
	);?>

	<?if($elementId) {
		//CURRENT_ELEMENT//
		$arFilter = array(
			"ID" => $elementId,	
			"ACTIVE" => "Y",
			"IBLOCK_ID" => $arParams["IBLOCK_ID"]
		);

		$obCache = new CPHPCache();
		if($obCache->InitCache($arParams["CACHE_TIME"], serialize($arFilter), "/iblock/news")) {
			$arCurElement = $obCache->GetVars();
		} elseif(Bitrix\Main\Loader::includeModule("iblock") && $obCache->StartDataCache()) {
			$arCurElement = array();
			$rsElement = CIBlockElement::GetList(array(), $arFilter, false, false, array("ID", "IBLOCK_ID", "NAME", "PREVIEW_TEXT", "DETAIL_PICTURE", "DETAIL_PAGE_URL", "PROPERTY_BREADCRUMB_TITLE"));

			if($isCacheManager)
				$GLOBALS["CACHE_MANAGER"]->StartTagCache("/iblock/news");

			if($arCurElement = $rsElement->GetNext()) {
				if($isCacheManager)
					$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);

				//ELEMENT_DETAIL_PICTURE//
				if($arCurElement["DETAIL_PICTURE"] > 0)
					$arCurElement["DETAIL_PICTURE"] = CFile::GetFileArray($arCurElement["DETAIL_PICTURE"]);
				
				//ELEMENT_TITLE//
				//ELEMENT_META_DESCRIPTION//
				$ipropValues = new Bitrix\Iblock\InheritedProperty\ElementValues($arCurElement["IBLOCK_ID"], $arCurElement["ID"]);
				$iproperty = $ipropValues->getValues();				
				$arCurElement["TITLE"] = !empty($iproperty["ELEMENT_PAGE_TITLE"]) ? $iproperty["ELEMENT_PAGE_TITLE"] : $arCurElement["NAME"];
				$arCurElement["META_DESCRIPTION"] = !empty($iproperty["ELEMENT_META_DESCRIPTION"]) ? $iproperty["ELEMENT_META_DESCRIPTION"] : "";
				unset($iproperty, $ipropValues);
			}

			if($isCacheManager)
				$GLOBALS["CACHE_MANAGER"]->EndTagCache();
			
			$obCache->EndDataCache($arCurElement);
		} else {
			$arCurElement = array();
		}
		
		//ELEMENT_BREADCRUMBS//
		if($arParams["ADD_ELEMENT_CHAIN"] == "Y") {
			if(!empty($arCurElement["PROPERTY_BREADCRUMB_TITLE_VALUE"]))
				$APPLICATION->AddChainItem($arCurElement["PROPERTY_BREADCRUMB_TITLE_VALUE"], $arCurElement['~DETAIL_PAGE_URL']);
			elseif(!empty($arCurElement["TITLE"]))
				$APPLICATION->AddChainItem($arCurElement["TITLE"], $arCurElement['~DETAIL_PAGE_URL']);
		}
		
		//ELEMENT_OPEN_GRAPH//
		if(!empty($arCurElement["TITLE"]))
			$APPLICATION->AddHeadString("<meta property='og:type' content='website'/>");$APPLICATION->AddHeadString("<meta property='og:title' content='".$arCurElement["TITLE"]."' />", true);
		if(!empty($arCurElement["META_DESCRIPTION"]))
			$APPLICATION->AddHeadString("<meta property='og:description' content='".$arCurElement["META_DESCRIPTION"]."' />", true);
		
		$ogScheme = CMain::IsHTTPS() ? "https" : "http";
		$APPLICATION->AddHeadString("<meta property='og:url' content='".$ogScheme."://".SITE_SERVER_NAME.$APPLICATION->GetCurPage()."' />", true);
		if(is_array($arCurElement["DETAIL_PICTURE"])) {
			$APPLICATION->AddHeadString("<meta property='og:image' content='".$ogScheme."://".SITE_SERVER_NAME.$arCurElement["DETAIL_PICTURE"]["SRC"]."' />", true);
			$APPLICATION->AddHeadString("<meta property='og:image:width' content='".$arCurElement["DETAIL_PICTURE"]["WIDTH"]."' />", true);
			$APPLICATION->AddHeadString("<meta property='og:image:height' content='".$arCurElement["DETAIL_PICTURE"]["HEIGHT"]."' />", true);
			$APPLICATION->AddHeadString("<link rel='image_src' href='".$ogScheme."://".SITE_SERVER_NAME.$arCurElement["DETAIL_PICTURE"]["SRC"]."' />", true);
		}

		//SET_STATUS//
		CHTTP::SetStatus("200 OK");
	}
}