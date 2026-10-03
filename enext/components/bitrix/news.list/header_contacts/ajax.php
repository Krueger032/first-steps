<?define("STOP_STATISTICS", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
if($request->isAjaxRequest()) {
	$action = $request->get("action");
	if($action == "workingHoursToday") {
		$timezone = $request->get("timezone");
		if(!empty($timezone))
			$currentDateTime = strtotime(gmdate("Y-m-d H:i", strtotime($timezone." hours")));
		else
			$currentDateTime = time() + CTimeZone::GetOffset();	
		
		$workingHours = $request->get("workingHours");
		$siteCharset = $request->get("siteCharset") ?: SITE_CHARSET;
		if(!empty($workingHours) && $siteCharset != "utf-8")
			$workingHours = Bitrix\Main\Text\Encoding::convertEncoding($workingHours, "utf-8", $siteCharset);
		
		if(!empty($currentDateTime) && !empty($workingHours)) {
			$currentDay = strtoupper(date("D", $currentDateTime));
			$arCurDay = $workingHours[$currentDay];
			if(!empty($arCurDay)) {			
				$arWorkingHoursToday[$currentDay] = array(
					"WORK_START" => strtotime($arCurDay["WORK_START"]) ? $arCurDay["WORK_START"] : "",
					"WORK_END" => strtotime($arCurDay["WORK_END"]) ? $arCurDay["WORK_END"] : "",
					"BREAK_START" => strtotime($arCurDay["BREAK_START"]) ? $arCurDay["BREAK_START"] : "",
					"BREAK_END" => strtotime($arCurDay["BREAK_END"]) ? $arCurDay["BREAK_END"] : ""
				);
				
				$currentDate = date("Y-m-d", $currentDateTime);
					
				$workStart = strtotime($arCurDay["WORK_START"]);
				$workStartDateTime = strtotime($currentDate." ".$arCurDay["WORK_START"]);
				$workEnd = strtotime($arCurDay["WORK_END"]);
					
				$breakStart = strtotime($arCurDay["BREAK_START"]);
				$breakStartDateTime = strtotime($currentDate." ".$arCurDay["BREAK_START"]);
				$breakEnd = strtotime($arCurDay["BREAK_END"]);

				if($workStart && $workEnd) {
					if($workStart < $workEnd) {				
						$workEndDateTime = strtotime($currentDate." ".$arCurDay["WORK_END"]);
						$prevDayWorkEndDateTime = strtotime($currentDate." ".$arCurDay["WORK_END"]." -1 days");

						$breakEndDateTime = strtotime($currentDate." ".$arCurDay["BREAK_END"]);
						$prevDayBreakEndDateTime = strtotime($currentDate." ".$arCurDay["BREAK_END"]." -1 days");
					} elseif($workStart > $workEnd) {				
						$workEndDateTime = strtotime($currentDate." ".$arCurDay["WORK_END"]." +1 days");
						$prevDayWorkEndDateTime = strtotime($currentDate." ".$arCurDay["WORK_END"]);

						$breakEndDateTime = strtotime($currentDate." ".$arCurDay["BREAK_END"]." +1 days");
						$prevDayBreakEndDateTime = strtotime($currentDate." ".$arCurDay["BREAK_END"]);
					} else {
						$arWorkingHoursToday[$currentDay]["STATUS"] = "OPEN";
					}
				} else {
					$arWorkingHoursToday[$currentDay]["STATUS"] = "CLOSED";
				}

				if(!$arWorkingHoursToday[$currentDay]["STATUS"]) {
					if($workStartDateTime && $workEndDateTime) {
						if($currentDateTime >= $workStartDateTime && $currentDateTime < $workEndDateTime) {
							$arWorkingHoursToday[$currentDay]["STATUS"] = "OPEN";					
							if($breakStartDateTime && $breakEndDateTime)
								if($currentDateTime >= $breakStartDateTime && $currentDateTime < $breakEndDateTime)
									$arWorkingHoursToday[$currentDay]["STATUS"] = "CLOSED";					
						} elseif($currentDateTime < $workStartDateTime && $currentDateTime < $prevDayWorkEndDateTime) {
							$arWorkingHoursToday[$currentDay]["STATUS"] = "OPEN";
							if($breakStartDateTime && $breakEndDateTime)
								if($currentDateTime < $breakStartDateTime && $currentDateTime < $prevDayBreakEndDateTime)
									$arWorkingHoursToday[$currentDay]["STATUS"] = "CLOSED";
						} else {
							$arWorkingHoursToday[$currentDay]["STATUS"] = "CLOSED";
						}
					}
				}
			}
			unset($arCurDay, $currentDay);
		}
		unset($currentDateTime);

		echo Bitrix\Main\Web\Json::encode(array(
			"today" => !empty($arWorkingHoursToday) ? $arWorkingHoursToday : false
		));
	} elseif($action == "mapObjectsCallback") {
		$siteCharset = $request->get("siteCharset") ?: SITE_CHARSET;

		$signer = new Bitrix\Main\Security\Sign\Signer;
		$parameters = unserialize(base64_decode($signer->unsign($request->get("parameters"), "news.list")));

		$arItem = $request->get("item");
		
		$arSettings = CEnext::GetFrontParametrsValues(SITE_ID);

		if($parameters["SHOW_MAP"] != "N") {
			$mapData = array();

			if(!empty($arItem["MAP"])) {
				$arTmp = explode(",", $arItem["MAP"]);
				$mapData["PLACEMARKS"][] = array(
					"LON" => $arTmp[1],
					"LAT" => $arTmp[0],
					"TEXT" => "<div class='object-item-marker'>".(!empty($arItem["PREVIEW_PICTURE"]) ? "<div class='object-item-marker-image'><img src='".$arItem["PREVIEW_PICTURE"]."' /></div>" : "")."<div class='object-item-marker-caption'><div class='object-item-marker-title'>".iconv("UTF-8", $siteCharset, $arItem["NAME"])."</div>".(!empty($arItem["ADDRESS"]) ? "<div class='object-item-marker-address'><i class='icon-map-marker'></i><span>".iconv("UTF-8", $siteCharset, $arItem["ADDRESS"])."</span></div>" : "")."</div></div>"
				);
				unset($arTmp);
			}

			//OBJECTS//
			if(intval($parameters["OBJECTS_IBLOCK_ID"]) > 0) {
				$rsElements = CIBlockElement::GetList(array(), array("ACTIVE" => "Y", "IBLOCK_ID" => $parameters["OBJECTS_IBLOCK_ID"], "!PROPERTY_SHOW_IN_CONTACTS" => false), false, false, array("ID", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PAGE_URL"));	
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
				unset($arElement, $obElement, $rsElements);

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
				}
			
				if(!empty($arObjects)) {
					foreach($arObjects as $arObject) {
						if(!empty($arObject["PARENT_ID"])) {
							foreach($arObject["PARENT_ID"] as $arId) {
								$mapData["PLACEMARKS"][] = array(
									"LON" => $arObject["LON"],
									"LAT" => $arObject["LAT"],
									"TEXT" => "<div class='object-item-marker'>".(!empty($arObject["PREVIEW_PICTURE_SRC"]) ? "<div class='object-item-marker-image'><img src='".$arObject["PREVIEW_PICTURE_SRC"]."' /></div>" : "")."<div class='object-item-marker-caption'><div class='object-item-marker-title'>".$arObject["NAME"]."</div>".(!empty($arObject["ADDRESS"]) ? "<div class='object-item-marker-address'><i class='icon-map-marker'></i><span>".$arObject["ADDRESS"]."</span></div>" : "")."<a target='_blank' class='object-item-marker-link' href='".$arObjects[$arId]["DETAIL_PAGE_URL"]."'>".Bitrix\Main\Localization\Loc::getMessage("HEADER_CONTACTS_AJAX_PLACEMARK_MORE")."</a></div></div>"
								);
							}
							unset($arId);
						} else {
							$mapData["PLACEMARKS"][] = array(
								"LON" => $arObject["LON"],
								"LAT" => $arObject["LAT"],
								"TEXT" => "<div class='object-item-marker'>".(!empty($arObject["PREVIEW_PICTURE_SRC"]) ? "<div class='object-item-marker-image'><img src='".$arObject["PREVIEW_PICTURE_SRC"]."' /></div>" : "")."<div class='object-item-marker-caption'><div class='object-item-marker-title'>".$arObject["NAME"]."</div>".(!empty($arObject["ADDRESS"]) ? "<div class='object-item-marker-address'><i class='icon-map-marker'></i><span>".$arObject["ADDRESS"]."</span></div>" : "")."<a target='_blank' class='object-item-marker-link' href='".$arObject["DETAIL_PAGE_URL"]."'>".Bitrix\Main\Localization\Loc::getMessage("HEADER_CONTACTS_AJAX_PLACEMARK_MORE")."</a></div></div>"
							);
						}
					}
					unset($arObject);
				}
			}?>
		
			<div class="slide-panel-contacts__map">
				<?if($arSettings["MAP_SERVICE"] != "YANDEX") {
					if(count($mapData["PLACEMARKS"]) == 1) {
						$mapData["google_lat"] = $mapData["PLACEMARKS"][0]["LAT"];
						$mapData["google_lon"] = $mapData["PLACEMARKS"][0]["LON"];
						$mapData["google_scale"] = "13";
					}
					$APPLICATION->IncludeComponent("bitrix:map.google.view", "",
						array(
							"API_KEY" => Bitrix\Main\Config\Option::get("fileman", "google_map_api_key"),
							"CONTROLS" => array(
								0 => "SMALL_ZOOM_CONTROL",
							),
							"INIT_MAP_TYPE" => "ROADMAP",
							"MAP_DATA" => serialize($mapData),
							"MAP_HEIGHT" => "100%",
							"MAP_ID" => "slidePanelContacts",
							"MAP_WIDTH" => "100%",
							"OPTIONS" => array(
								0 => "ENABLE_DBLCLICK_ZOOM",
								1 => "ENABLE_DRAGGING",
								2 => "ENABLE_KEYBOARD",
							),
							"COMPONENT_TEMPLATE" => ".default"
						),
						false
					);
				} else {
					if(count($mapData["PLACEMARKS"]) == 1) {
						$mapData["yandex_lat"] = $mapData["PLACEMARKS"][0]["LAT"];
						$mapData["yandex_lon"] = $mapData["PLACEMARKS"][0]["LON"];
						$mapData["yandex_scale"] = "14";
					}

					$APPLICATION->IncludeComponent("altop:map.yandex.view.enext", "",
						array(
							"API_KEY" => Bitrix\Main\Config\Option::get("fileman", "yandex_map_api_key"),
							"CONTROLS" => array(
								0 => "zoomControl",
							),
							"INIT_MAP_TYPE" => "map",
							"MAP_DATA" => serialize($mapData),
							"MAP_HEIGHT" => "100%",
							"MAP_ID" => "slidePanelContacts",
							"MAP_WIDTH" => "100%",
							"OPTIONS" => array(
								0 => "drag",
								1 => "dblClickZoom",
								2 => "multiTouch",
								3 => "rightMouseButtonMagnifier",
							)
						),
						false
					);
				}?>
			</div>
		<?}
		
		if($parameters["SHOW_OBJECTS"] != "N") {
			$GLOBALS["arSlidePanelContactsObjectsFilter"] = array(
				"!PROPERTY_SHOW_IN_CONTACTS" => false,
				"PROPERTY_HIDE_IN_OBJECTS_LIST" => false
			);?>
			<?$APPLICATION->IncludeComponent("bitrix:news.list", "objects",
				array(
					"IBLOCK_TYPE" => $parameters["OBJECTS_IBLOCK_TYPE"],
					"IBLOCK_ID" => $parameters["OBJECTS_IBLOCK_ID"],
					"NEWS_COUNT" => $parameters["OBJECTS_NEWS_COUNT"],
					"SORT_BY1" => $parameters["OBJECTS_SORT_BY1"],
					"SORT_ORDER1" => $parameters["OBJECTS_SORT_ORDER1"],
					"SORT_BY2" => $parameters["OBJECTS_SORT_BY2"],
					"SORT_ORDER2" => $parameters["OBJECTS_SORT_ORDER2"],
					"FILTER_NAME" => "arSlidePanelContactsObjectsFilter",
					"FIELD_CODE" => array(),
					"PROPERTY_CODE" => $parameters["OBJECTS_PROPERTY_CODE"],
					"CHECK_DATES" => "Y",
					"DETAIL_URL" => "",
					"AJAX_MODE" => "",
					"AJAX_OPTION_SHADOW" => "",
					"AJAX_OPTION_JUMP" => "",
					"AJAX_OPTION_STYLE" => "",
					"AJAX_OPTION_HISTORY" => "",
					"CACHE_TYPE" => $parameters["CACHE_TYPE"],
					"CACHE_TIME" => $parameters["CACHE_TIME"],
					"CACHE_FILTER" => $parameters["CACHE_FILTER"],
					"CACHE_GROUPS" => $parameters["CACHE_GROUPS"],
					"PREVIEW_TRUNCATE_LEN" => "",
					"ACTIVE_DATE_FORMAT" => "",
					"DISPLAY_PANEL" => "",
					"SET_TITLE" => "N",
					"SET_BROWSER_TITLE" => "N",
					"SET_META_KEYWORDS" => "N",
					"SET_META_DESCRIPTION" => "N",
					"SET_STATUS_404" => "N",
					"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
					"ADD_SECTIONS_CHAIN" => "",
					"HIDE_LINK_WHEN_NO_DETAIL" => "",
					"PARENT_SECTION" => "",
					"PARENT_SECTION_CODE" => "",
					"DISPLAY_NAME" => "",
					"DISPLAY_DATE" => "",
					"DISPLAY_TOP_PAGER" => "N",
					"DISPLAY_BOTTOM_PAGER" => "Y",
					"PAGER_SHOW_ALWAYS" => "",
					"PAGER_TEMPLATE" => "arrows",
					"PAGER_DESC_NUMBERING" => "",
					"PAGER_DESC_NUMBERING_CACHE_TIME" => "",
					"PAGER_SHOW_ALL" => "",
					"AJAX_OPTION_ADDITIONAL" => "",
					"ITEMS_TITLE" => $parameters["OBJECTS_TITLE"],				
					"USE_REVIEW" => $parameters["OBJECTS_USE_REVIEW"],
					"REVIEWS_IBLOCK_ID" => $parameters["OBJECTS_REVIEWS_IBLOCK_ID"],
					"SLIDE_PANEL_MODE" => "Y",
				),
				false
			);
		}
			
		if($parameters["SHOW_OBJECTS"] == "N") {
			$APPLICATION->IncludeComponent("bitrix:main.include", "",
				array(
					"AREA_FILE_SHOW" => "file",
					"PATH" => SITE_DIR."include/slide_panel_callback.php"
				)
			);
		}

		$content = ob_get_contents();
		ob_end_clean();

		if(Bitrix\Main\Loader::includeModule("iblock")) {
			Bitrix\Iblock\Component\Base::sendJsonAnswer(array(
				"content" => $content
			));
		}
	}
}