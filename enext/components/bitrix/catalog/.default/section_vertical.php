<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader,
	Bitrix\Main\ModuleManager,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\Application;

$request = Application::getInstance()->getContext()->getRequest();

$filterSeoId = intval($arSettings["SMART_FILTER_SEO_ID"]["VALUE"]);

$isFilterLeft = $arSettings["SMART_FILTER_VIEW"]["VALUE"] == "LEFT";

$filterLeft = $arSettings["SMART_FILTER_LEFT"]["VALUE"];
if($filterLeft != "ALWAYS_OPEN") {
	$filterLeftCookie = $request->getCookie("ENEXT_SMART_FILTER_LEFT");
	if($arSettings["SMART_FILTER_LEFT_SAVE_STATUS"]["VALUE"] == "Y" && !empty($filterLeftCookie))
		$filterLeft = $filterLeftCookie;
	unset($filterLeftCookie);
}

if($isFilter) {
	//SECTION_FILTER//?>
	<?$APPLICATION->IncludeComponent("bitrix:catalog.smart.filter", "",
		array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"SECTION_ID" => $arCurSection["ID"],
			"FILTER_NAME" => $arParams["FILTER_NAME"],
			"PRICE_CODE" => $arParams["PRICE_CODE"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			"SAVE_IN_SESSION" => "N",				
			"XML_EXPORT" => "Y",
			"SECTION_TITLE" => "NAME",
			"SECTION_DESCRIPTION" => "DESCRIPTION",
			"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],				
			"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
			"CURRENCY_ID" => $arParams["CURRENCY_ID"],
			"SEF_MODE" => $arParams["SEF_MODE"],
			"SEF_RULE" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["smart_filter"],
			"SMART_FILTER_PATH" => $arResult["VARIABLES"]["SMART_FILTER_PATH"],
			"PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
			"INSTANT_RELOAD" => $arParams["INSTANT_RELOAD"],
		),
		$component,
		array("HIDE_ICONS" => "Y")
	);?>
<?}

//COLLECTIONS_IDS//
if($arParams["SHOW_COLLECTIONS"] != "N") {
	$arCollectFilter = array("ACTIVE" => "Y", "IBLOCK_ID" => $arParams["IBLOCK_ID"], "SECTION_ID" => $arCurSection["ID"], "INCLUDE_SUBSECTIONS" => "Y", "SECTION_GLOBAL_ACTIVE" => "Y", "!PROPERTY_COLLECTION" => false);

	if(!empty($GLOBALS[$arParams["FILTER_NAME"]]))
		$arCollectFilter = array_merge($arCollectFilter, $GLOBALS[$arParams["FILTER_NAME"]]);

	$obCache = new CPHPCache();
	if($obCache->InitCache($arParams["CACHE_TIME"], serialize($arCollectFilter), "/iblock/catalog")) {
		$collectionsIds = $obCache->GetVars();
	} elseif(Bitrix\Main\Loader::includeModule("iblock") && $obCache->StartDataCache()) {
		$collectionsIds = array();		
		$rsElements = CIBlockElement::GetList(array(), $arCollectFilter, false, false, array("ID", "IBLOCK_ID", "PROPERTY_COLLECTION"));	
		
		if($isCacheManager) {
			$GLOBALS["CACHE_MANAGER"]->StartTagCache("/iblock/catalog");
			$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);
		}

		while($arElement = $rsElements->GetNext()) {
			if(!isset($collectionsIds) || !in_array($arElement["PROPERTY_COLLECTION_VALUE"], $collectionsIds))
				$collectionsIds[] = $arElement["PROPERTY_COLLECTION_VALUE"];
		}
		
		if($isCacheManager)
			$GLOBALS["CACHE_MANAGER"]->EndTagCache();
		
		$obCache->EndDataCache($collectionsIds);
	} else {
		$collectionsIds = array();
	}
	unset($arCollectFilter);
}

//SECTION_LINKS//
if(!empty($arCurSection["PATH"]) && $filterSeoId > 0) {
	$arSectLinksFilter = array("ACTIVE" => "Y", "IBLOCK_ID" => $filterSeoId, "PROPERTY_SECTION" => array_keys($arCurSection["PATH"]));
	
	$obCache = new CPHPCache();
	if($obCache->InitCache($arParams["CACHE_TIME"], serialize($arSectLinksFilter), "/iblock/catalog")) {
		$sectionLinks = $obCache->GetVars();
	} elseif(Bitrix\Main\Loader::includeModule("iblock") && $obCache->StartDataCache()) {		
		$sectionLinks = array();		
		$rsElements = CIBlockElement::GetList(array("SORT" => "ASC", "NAME" => "ASC"), $arSectLinksFilter, false, false, array("ID", "CODE", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL", "PROPERTY_DEFAULT_URL", "PROPERTY_SECTION", "PROPERTY_SHOW_IN_SUBSECTIONS", "PROPERTY_SHOW_IN_SELECTED_SECTION"));	
		
		if($isCacheManager) {
			$GLOBALS["CACHE_MANAGER"]->StartTagCache("/iblock/catalog");
			$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$filterSeoId);
		}
		
		while($arElement = $rsElements->GetNext()) {
			if($arElement["PROPERTY_SECTION_VALUE"] == $arCurSection["ID"] || !empty($arElement["PROPERTY_SHOW_IN_SUBSECTIONS_VALUE"]))
				$sectionLinks[] = $arElement;
		}
		
		if($isCacheManager)
			$GLOBALS["CACHE_MANAGER"]->EndTagCache();				
		
		$obCache->EndDataCache($sectionLinks);
	} else {
		$sectionLinks = array();
	}
	unset($arSectLinksFilter);
}

//PAGE_PROPERTY//
if($isFilter && $isFilterLeft)
	$APPLICATION->SetPageProperty("smartFilterView", " smart-filter-view-left".($filterLeft != "DEFAULT_CLOSED" && $filterLeft != "CLOSED" ? " smart-filter-view-left-active" : ""));?>

<div class="catalog-section-container">
	<?//SECTION_LINKS//
	if(!empty($sectionLinks)) {
		foreach($sectionLinks as $key => $quickLink) {
			if(!empty($quickLink["PROPERTY_SHOW_IN_SELECTED_SECTION_VALUE"]) && $seoMeta["SMART_FILTER_LINK"])
				unset($sectionLinks[$key]);
		}
		unset($key, $quickLink);
		if(!empty($sectionLinks)) {?>
			<div class="catalog-section-links">
				<?foreach($sectionLinks as $quickLink) {
					$url = !empty($quickLink["CODE"]) ? $quickLink["DETAIL_PAGE_URL"] : $quickLink["PROPERTY_DEFAULT_URL_VALUE"];?>
					<a class="catalog-section-link<?=($APPLICATION->GetCurPage() == $url ? ' active' : '')?>" href="<?=$url?>"><?=$quickLink["NAME"]?></a>
				<?}
				unset($quickLink);?>
			</div>
		<?}
	}
	unset($sectionLinks);

	//SECTION_FILTER_LINKS//
	$APPLICATION->ShowViewContent("CATALOG_SECTION_FILTER_LINKS");
	
	//SECTION_LIST//			
	$GLOBALS["arCatalogSectListFilter"] = array("UF_HIDDEN" => false);
	$APPLICATION->IncludeComponent("bitrix:catalog.section.list", "catalog",
		array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"FILTER_NAME" => "arCatalogSectListFilter",
			"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
			"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			"CACHE_FILTER" => $arParams["CACHE_FILTER"],
			"COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
			"COUNT_ELEMENTS_FILTER" => $arParams["HIDE_NOT_AVAILABLE"] == "Y" ? "CNT_AVAILABLE" : "CNT_ACTIVE",
			"TOP_DEPTH" => "1",
			"SECTION_FIELDS" => array(),
			"SECTION_USER_FIELDS" => array(
				0 => "UF_ICON"
			),
			"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
			"HIDE_SECTION_NAME" => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"]) ? $arParams["SECTIONS_HIDE_SECTION_NAME"] : "N"),
			"ADD_SECTIONS_CHAIN" => "N"
		),
		$component,
		array("HIDE_ICONS" => "Y")
	);
	
	//SECTION_PREVIEW//
	if(!$_REQUEST["PAGEN_1"] || $_REQUEST["PAGEN_1"] <= 1) {
		if($seoMeta["SMART_FILTER_LINK"]) {
			if(!empty($seoMeta["PREVIEW"])) {?>
				<div class="catalog-section-prev"><?=$seoMeta["PREVIEW"]?></div>
			<?}
		} else {
			if(!empty($seoMeta["PREVIEW"]) || !empty($arCurSection["UF_PREVIEW"])) {?>
				<div class="catalog-section-prev"><?=(!empty($seoMeta["PREVIEW"]) ? $seoMeta["PREVIEW"] : $arCurSection["UF_PREVIEW"]);?></div>
			<?}
		}
	}
	
	//SECTION_VIEW//
	if(!empty($collectionsIds)) {
		$arAvailableView = array("items", "collections");
		
		$catalogViewField = $APPLICATION->get_cookie("ELEMENT_VIEW") ? $APPLICATION->get_cookie("ELEMENT_VIEW") : strtolower($arSettings["COLLECTIONS_DISPLAY"]["VALUE"]);
		$getView = $request->get("view");
		
		if(!empty($getView) && in_array($getView, $arAvailableView)) {
			$APPLICATION->set_cookie("ELEMENT_VIEW", $getView, false, "/", SITE_SERVER_NAME);
			$catalogView = $getView;
		} elseif(!empty($catalogViewField) && in_array($catalogViewField, $arAvailableView)) {
			$catalogView = $catalogViewField;
		}
	}

	//SECTION_SORT//
	if($isSort && ((!empty($collectionsIds) && $catalogView != "collections") || (!isset($collectionsIds) || empty($collectionsIds)))) {
		$arAvailableSort = array(
			"default" => array(					
				"FIELD" => !empty($arParams["ELEMENT_SORT_FIELD"]) ? $arParams["ELEMENT_SORT_FIELD"] : "SORT",
				"ORDER" => !empty($arParams["ELEMENT_SORT_ORDER"]) ? $arParams["ELEMENT_SORT_ORDER"] : "ASC",
				"VALUE" => Loc::getMessage("CATALOG_SORT_DEFAULT")
			),
			"cheap" => array(					
				"FIELD" => "PROPERTY_MINIMUM_PRICE_1",
				"ORDER" => "ASC",
				"VALUE" => Loc::getMessage("CATALOG_SORT_CHEAP")
			),
			"expensive" => array(
				"FIELD" => "PROPERTY_MAXIMUM_PRICE_1",
				"ORDER" => "DESC",
				"VALUE" => Loc::getMessage("CATALOG_SORT_EXPENSIVE")
			)
		);

		$catalogSortField = $APPLICATION->get_cookie("ELEMENT_SORT") ? $APPLICATION->get_cookie("ELEMENT_SORT") : "default";
		$getSort = $request->get("sort");

		if(!empty($getSort) && !empty($arAvailableSort[$getSort])) {
			$APPLICATION->set_cookie("ELEMENT_SORT", $getSort, false, "/", SITE_SERVER_NAME);
			$arParams["ELEMENT_SORT_FIELD"] = $arAvailableSort[$getSort]["FIELD"];
			$arParams["ELEMENT_SORT_ORDER"] = $arAvailableSort[$getSort]["ORDER"];
			$arAvailableSort[$getSort]["CHECKED"] = "Y";
		} elseif(!empty($catalogSortField) && !empty($arAvailableSort[$catalogSortField])) {
			$arParams["ELEMENT_SORT_FIELD"] = $arAvailableSort[$catalogSortField]["FIELD"];
			$arParams["ELEMENT_SORT_ORDER"] = $arAvailableSort[$catalogSortField]["ORDER"];
			$arAvailableSort[$catalogSortField]["CHECKED"] = "Y";
		}
	}
	
	if(!empty($collectionsIds) || $isFilter || !empty($arAvailableSort)) {
		ob_start();?>
		<div class="catalog-section-panel-wrapper">
			<div class="container-ws">
				<div class="row">
					<div class="col-xs-12">
						<div class="catalog-section-panel">
							<div class="catalog-section-panel-block<?=(!empty($collectionsIds) || $isFilter ? "" : (!empty($arAvailableSort) ? " catalog-section-panel-block-reverse" : ""))?>">
								<?if(!empty($collectionsIds) || $isFilter) {?>
									<div class="catalog-section-filter-toggle">
										<?if($isFilter) {
											$filterPropsCount = 0;
											if(!empty($GLOBALS[$arParams["FILTER_NAME"]])) {
												foreach($GLOBALS[$arParams["FILTER_NAME"]] as $key => $val) {
													if($key == "FACET_OPTIONS")
														continue;
													else
														$filterPropsCount++;
												}
												unset($key, $val);
											}?>
											<div class="catalog-section-filter-container<?=($isFilterLeft ? ' is-filter-left' : '')?>">
												<div class="catalog-section-filter" data-entity="showFilter" data-id="<?=$arCurSection['ID']?>">
													<div class="catalog-section-filter-block"><i class="icon-sliders"></i><span><?=GetMessage("CATALOG_FILTER")?></span><?=($filterPropsCount > 0 ? "<span class=\"catalog-section-filter-count\">".$filterPropsCount."</span>" : "")?></div>
												</div>
												<?if($isFilterLeft && $filterLeft != "ALWAYS_OPEN") {?>
													<div class="catalog-section-filter-toggle hidden-xs hidden-sm hidden-md">
														<input type="checkbox" id="catalog-section-filter-toggle" name="filter-toggle"<?=($filterLeft == "DEFAULT_OPEN" || $filterLeft == "OPEN" ? " checked='checked'" : "")?> />
														<label for="catalog-section-filter-toggle"></label>
													</div>
												<?}?>
											</div>
										<?}
										if(!empty($collectionsIds)) {?>
											<div class="catalog-section-toggle hidden-xs hidden-sm">
												<input type="checkbox" id="catalog-section-toggle" name="toggle"<?=($catalogView == "collections" ? " checked='checked'" : "")?> />
												<span><?=GetMessage("CATALOG_COLLECTIONS")?></span>
												<label for="catalog-section-toggle"></label>
												<span><?=GetMessage("CATALOG_ELEMENTS")?></span>
											</div>
										<?}?>
									</div>
								<?}
								if(!empty($arAvailableSort)) {?>
									<div class="catalog-section-sort-container">
										<div class="catalog-section-sort" data-role="catalogSectionSort">
											<div class="catalog-section-sort-block">
												<div class="catalog-section-sort-text">
													<?=Loc::getMessage("CATALOG_SORT")?>
													<span>
														<?foreach($arAvailableSort as $value) {
															if($value["CHECKED"]) {
																echo $value["VALUE"];
																break;
															}
														}
														unset($value);?>
													</span>
												</div>
												<div class="catalog-section-sort-arrow"><i class="icon-arrow-down"></i></div>
											</div>
											<div class="catalog-section-sort-popup" data-role="dropdownContent" style="display: none;">
												<ul>
													<?foreach($arAvailableSort as $val => $ar) {?>
														<li<?=($ar["CHECKED"] ? " class='active'" : "")?>>
															<a rel="nofollow" href="<?=$APPLICATION->GetCurPageParam('sort='.$val, array('sort'))?>"><?=$ar["VALUE"]?></a>
														</li>
													<?}?>
												</ul>
											</div>
										</div>							
									</div>
								<?}?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?$APPLICATION->AddViewContent("CATALOG_SECTION_PANEL", ob_get_contents());
		ob_end_clean();
	}
	
	if(!empty($collectionsIds) && $catalogView == "collections") {
		//COLLECTIONS//?>
		<div class="catalog-section-collections">
			<?$GLOBALS["arCatalogCollectFilter"] = array("ID" => $collectionsIds);?>
			<?$APPLICATION->IncludeComponent("bitrix:news.list", "collections",
				array(
					"IBLOCK_TYPE" => $arParams["COLLECTIONS_IBLOCK_TYPE"],
					"IBLOCK_ID" => $arParams["COLLECTIONS_IBLOCK_ID"],
					"NEWS_COUNT" => $arParams["COLLECTIONS_NEWS_COUNT"],
					"SORT_BY1" => $arParams["COLLECTIONS_SORT_BY1"],
					"SORT_ORDER1" => $arParams["COLLECTIONS_SORT_ORDER1"],
					"SORT_BY2" => $arParams["COLLECTIONS_SORT_BY2"],
					"SORT_ORDER2" => $arParams["COLLECTIONS_SORT_ORDER2"],
					"FILTER_NAME" => "arCatalogCollectFilter",
					"FIELD_CODE" => array(),
					"PROPERTY_CODE" => $arParams["COLLECTIONS_PROPERTY_CODE"],
					"CHECK_DATES" => "Y",
					"DETAIL_URL" => "",
					"AJAX_MODE" => "",
					"AJAX_OPTION_SHADOW" => "",
					"AJAX_OPTION_JUMP" => "",
					"AJAX_OPTION_STYLE" => "",
					"AJAX_OPTION_HISTORY" => "",
					"CACHE_TYPE" => $arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"],
					"CACHE_FILTER" => $arParams["CACHE_FILTER"],
					"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
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
					"LOAD_ON_SCROLL" => $arParams["LOAD_ON_SCROLL"],
					"AJAX_OPTION_ADDITIONAL" => "",
					"SHOW_MIN_PRICE" => $arParams["COLLECTIONS_SHOW_MIN_PRICE"],
					"CATALOG_IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
					"CATALOG_IBLOCK_ID" => $arParams["IBLOCK_ID"],
					"CATALOG_PRICE_CODE" => $arParams["PRICE_CODE"],		
					"CATALOG_PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
					"CATALOG_CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
					"CATALOG_CURRENCY_ID" => $arParams["CURRENCY_ID"]
				),
				$component,
				array("HIDE_ICONS" => "Y")
			);?>
		</div>

		<?if(!$_REQUEST["PAGEN_1"] || $_REQUEST["PAGEN_1"] <= 1) {
			if($seoMeta["SMART_FILTER_LINK"]) {
				//SECTION_DESCRIPTION//
				if(!empty($seoMeta["DESCRIPTION"])) {?>
					<div class="catalog-section-desc"><?=$seoMeta["DESCRIPTION"]?></div>
				<?}

				//SECTION_FAQ//
				if(intval($seoMeta["FAQ"]) > 0) {
					$GLOBALS["arCatalogFaqFilter"] = array("ACTIVE" => "Y", "SECTION_ID" => $seoMeta["FAQ"], "INCLUDE_SUBSECTIONS" => "N", "SECTION_GLOBAL_ACTIVE" => "Y");?>
					<?$APPLICATION->IncludeComponent("bitrix:news.list", "faq",
						array(
							"IBLOCK_TYPE" => $arParams["FAQ_IBLOCK_TYPE"],
							"IBLOCK_ID" => $arParams["FAQ_IBLOCK_ID"],
							"NEWS_COUNT" => $arParams["FAQ_NEWS_COUNT"],
							"SORT_BY1" => $arParams["FAQ_SORT_BY1"],
							"SORT_ORDER1" => $arParams["FAQ_SORT_ORDER1"],
							"SORT_BY2" => $arParams["FAQ_SORT_BY2"],
							"SORT_ORDER2" => $arParams["FAQ_SORT_ORDER2"],
							"FILTER_NAME" => "arCatalogFaqFilter",
							"FIELD_CODE" => array(),
							"PROPERTY_CODE" => array(),
							"CHECK_DATES" => "Y",
							"DETAIL_URL" => "",
							"AJAX_MODE" => "",
							"AJAX_OPTION_SHADOW" => "",
							"AJAX_OPTION_JUMP" => "",
							"AJAX_OPTION_STYLE" => "",
							"AJAX_OPTION_HISTORY" => "",
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"CACHE_FILTER" => $arParams["CACHE_FILTER"],
							"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
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
							"DISPLAY_TOP_PAGER" => "",
							"DISPLAY_BOTTOM_PAGER" => "",
							"PAGER_SHOW_ALWAYS" => "",
							"PAGER_TEMPLATE" => "",
							"PAGER_DESC_NUMBERING" => "",
							"PAGER_DESC_NUMBERING_CACHE_TIME" => "",
							"PAGER_SHOW_ALL" => "",
							"AJAX_OPTION_ADDITIONAL" => ""
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);?>
				<?}
			} else {
				//SECTION_DESCRIPTION//
				if(!empty($seoMeta["DESCRIPTION"]) || !empty($arCurSection["DESCRIPTION"])) {?>
					<div class="catalog-section-desc"><?=(!empty($seoMeta["DESCRIPTION"]) ? $seoMeta["DESCRIPTION"] : $arCurSection["DESCRIPTION"]);?></div>
				<?}

				//SECTION_FAQ//
				if(intval($arCurSection["UF_FAQ"] > 0)) {
					$GLOBALS["arCatalogFaqFilter"] = array("ACTIVE" => "Y", "SECTION_ID" => $arCurSection["UF_FAQ"], "INCLUDE_SUBSECTIONS" => "N", "SECTION_GLOBAL_ACTIVE" => "Y");?>
					<?$APPLICATION->IncludeComponent("bitrix:news.list", "faq",
						array(
							"IBLOCK_TYPE" => $arParams["FAQ_IBLOCK_TYPE"],
							"IBLOCK_ID" => $arParams["FAQ_IBLOCK_ID"],
							"NEWS_COUNT" => $arParams["FAQ_NEWS_COUNT"],
							"SORT_BY1" => $arParams["FAQ_SORT_BY1"],
							"SORT_ORDER1" => $arParams["FAQ_SORT_ORDER1"],
							"SORT_BY2" => $arParams["FAQ_SORT_BY2"],
							"SORT_ORDER2" => $arParams["FAQ_SORT_ORDER2"],
							"FILTER_NAME" => "arCatalogFaqFilter",
							"FIELD_CODE" => array(),
							"PROPERTY_CODE" => array(),
							"CHECK_DATES" => "Y",
							"DETAIL_URL" => "",
							"AJAX_MODE" => "",
							"AJAX_OPTION_SHADOW" => "",
							"AJAX_OPTION_JUMP" => "",
							"AJAX_OPTION_STYLE" => "",
							"AJAX_OPTION_HISTORY" => "",
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"CACHE_FILTER" => $arParams["CACHE_FILTER"],
							"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
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
							"DISPLAY_TOP_PAGER" => "",
							"DISPLAY_BOTTOM_PAGER" => "",
							"PAGER_SHOW_ALWAYS" => "",
							"PAGER_TEMPLATE" => "",
							"PAGER_DESC_NUMBERING" => "",
							"PAGER_DESC_NUMBERING_CACHE_TIME" => "",
							"PAGER_SHOW_ALL" => "",
							"AJAX_OPTION_ADDITIONAL" => ""
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);?>
				<?}
			}
		}
		
		//SECTION_TITLE//
		if($arParams["SET_TITLE"] && !empty($arCurSection["TITLE"]))
			$APPLICATION->SetTitle($arCurSection["TITLE"]);
		
		//SECTION_BROWSER_TITLE//
		if($arParams["SET_BROWSER_TITLE"] != "N" && !empty($arCurSection["IPROPERTY_VALUES"]["SECTION_META_TITLE"]))
			$APPLICATION->SetPageProperty("title", $arCurSection["IPROPERTY_VALUES"]["SECTION_META_TITLE"]);
		
		//SECTION_META_KEYWORDS//
		if($arParams["SET_META_KEYWORDS"] != "N" && !empty($arCurSection["IPROPERTY_VALUES"]["SECTION_META_KEYWORDS"]))
			$APPLICATION->SetPageProperty("keywords", $arCurSection["IPROPERTY_VALUES"]["SECTION_META_KEYWORDS"]);
		
		//SECTION_META_DESCRIPTION//
		if($arParams["SET_META_DESCRIPTION"] != "N" && !empty($arCurSection["IPROPERTY_VALUES"]["SECTION_META_DESCRIPTION"]))
			$APPLICATION->SetPageProperty("description", $arCurSection["IPROPERTY_VALUES"]["SECTION_META_DESCRIPTION"]);
	} else {
		//SECTION//
		if(isset($arParams["USE_COMMON_SETTINGS_BASKET_POPUP"]) && $arParams["USE_COMMON_SETTINGS_BASKET_POPUP"] == "Y") {
			$basketAction = isset($arParams["COMMON_ADD_TO_BASKET_ACTION"]) ? $arParams["COMMON_ADD_TO_BASKET_ACTION"] : "";
		} else {
			$basketAction = isset($arParams["SECTION_ADD_TO_BASKET_ACTION"]) ? $arParams["SECTION_ADD_TO_BASKET_ACTION"] : "";
		}
		$intSectionID = $APPLICATION->IncludeComponent("bitrix:catalog.section", "",
			array(
				"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
				"ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
				"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
				"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
				"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],						
				"META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
				"META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
				"BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
				"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
				"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],					
				"BASKET_URL" => $arParams["BASKET_URL"],
				"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
				"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
				"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
				"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
				"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
				"FILTER_NAME" => $arParams["FILTER_NAME"],
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_FILTER" => $arParams["CACHE_FILTER"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"SET_TITLE" => $arParams["SET_TITLE"],
				"MESSAGE_404" => $arParams["~MESSAGE_404"],
				"SET_STATUS_404" => $arParams["SET_STATUS_404"],
				"SHOW_404" => $arParams["SHOW_404"],
				"FILE_404" => $arParams["FILE_404"],						
				"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
				"PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
				"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
				"PRICE_CODE" => $arParams["PRICE_CODE"],
				"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
				"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

				"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
				"USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
				"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ""),
				"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ""),
				"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],

				"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
				"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
				"PAGER_TITLE" => $arParams["PAGER_TITLE"],
				"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
				"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
				"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
				"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
				"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
				"PAGER_BASE_LINK_ENABLE" => $arParams["PAGER_BASE_LINK_ENABLE"],
				"PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
				"PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
				"LAZY_LOAD" => $arParams["LAZY_LOAD"],
				"MESS_BTN_LAZY_LOAD" => $arParams["~MESS_BTN_LAZY_LOAD"],
				"LOAD_ON_SCROLL" => $arParams["LOAD_ON_SCROLL"],

				"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
				"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
				"OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
				"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
				"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
				"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
				"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
				"OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],

				"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
				"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
				"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
				"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],					
				"USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
				"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
				"CURRENCY_ID" => $arParams["CURRENCY_ID"],
				"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
				"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
				
				"PRODUCT_DISPLAY_MODE" => $arParams["PRODUCT_DISPLAY_MODE"],						
				"PRODUCT_ROW_VARIANTS" => $arParams["LIST_PRODUCT_ROW_VARIANTS"],				
				
				"OFFER_TREE_PROPS" => $arParams["OFFER_TREE_PROPS"],
				"PRODUCT_SUBSCRIPTION" => $arParams["PRODUCT_SUBSCRIPTION"],
				"SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"],						
				"SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"],
				"SHOW_MAX_QUANTITY" => $arParams["SHOW_MAX_QUANTITY"],
				"MESS_SHOW_MAX_QUANTITY" => (isset($arParams["~MESS_SHOW_MAX_QUANTITY"]) ? $arParams["~MESS_SHOW_MAX_QUANTITY"] : ""),
				"RELATIVE_QUANTITY_FACTOR" => (isset($arParams["RELATIVE_QUANTITY_FACTOR"]) ? $arParams["RELATIVE_QUANTITY_FACTOR"] : ""),
				"MESS_RELATIVE_QUANTITY_MANY" => (isset($arParams["~MESS_RELATIVE_QUANTITY_MANY"]) ? $arParams["~MESS_RELATIVE_QUANTITY_MANY"] : ""),
				"MESS_RELATIVE_QUANTITY_FEW" => (isset($arParams["~MESS_RELATIVE_QUANTITY_FEW"]) ? $arParams["~MESS_RELATIVE_QUANTITY_FEW"] : ""),
				"MESS_BTN_BUY" => (isset($arParams["~MESS_BTN_BUY"]) ? $arParams["~MESS_BTN_BUY"] : ""),
				"MESS_BTN_ADD_TO_BASKET" => (isset($arParams["~MESS_BTN_ADD_TO_BASKET"]) ? $arParams["~MESS_BTN_ADD_TO_BASKET"] : ""),
				"MESS_BTN_SUBSCRIBE" => (isset($arParams["~MESS_BTN_SUBSCRIBE"]) ? $arParams["~MESS_BTN_SUBSCRIBE"] : ""),
				"MESS_BTN_DETAIL" => (isset($arParams["~MESS_BTN_DETAIL"]) ? $arParams["~MESS_BTN_DETAIL"] : ""),
				"MESS_NOT_AVAILABLE" => (isset($arParams["~MESS_NOT_AVAILABLE"]) ? $arParams["~MESS_NOT_AVAILABLE"] : ""),
				"MESS_BTN_COMPARE" => (isset($arParams["~MESS_BTN_COMPARE"]) ? $arParams["~MESS_BTN_COMPARE"] : ""),
					
				"USE_ENHANCED_ECOMMERCE" => (isset($arParams["USE_ENHANCED_ECOMMERCE"]) ? $arParams["USE_ENHANCED_ECOMMERCE"] : ""),
				"DATA_LAYER_NAME" => (isset($arParams["DATA_LAYER_NAME"]) ? $arParams["DATA_LAYER_NAME"] : ""),
				"BRAND_PROPERTY" => (isset($arParams["BRAND_PROPERTY"]) ? $arParams["BRAND_PROPERTY"] : ""),
				
				"ADD_SECTIONS_CHAIN" => "N",
				"ADD_TO_BASKET_ACTION" => $basketAction,
				"COMPARE_PATH" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["compare"],
				"COMPARE_NAME" => $arParams["COMPARE_NAME"],
				"COMPATIBLE_MODE" => (isset($arParams["COMPATIBLE_MODE"]) ? $arParams["COMPATIBLE_MODE"] : ""),
				"DISABLE_INIT_JS_IN_COMPONENT" => (isset($arParams["DISABLE_INIT_JS_IN_COMPONENT"]) ? $arParams["DISABLE_INIT_JS_IN_COMPONENT"] : ""),

				"SEF_RULE" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["smart_filter"],
				"SMART_FILTER_PATH" => $arResult["VARIABLES"]["SMART_FILTER_PATH"],
				"INSTANT_RELOAD" => $arParams["INSTANT_RELOAD"],
				
				"DETAIL_ADD_PICT_PROP" => $arParams["ADD_PICT_PROP"],				
				"DETAIL_OFFER_ADD_PICT_PROP" => $arParams["OFFER_ADD_PICT_PROP"],
				"DETAIL_USE_RATIO_IN_RANGES" => $arParams["USE_RATIO_IN_RANGES"],
				"DETAIL_PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],				
				"DETAIL_OFFERS_FIELD_CODE" => $arParams["DETAIL_OFFERS_FIELD_CODE"],
				"DETAIL_OFFERS_PROPERTY_CODE" => $arParams["DETAIL_OFFERS_PROPERTY_CODE"],
				"DETAIL_MAIN_BLOCK_PROPERTY_CODE" => $arParams["DETAIL_MAIN_BLOCK_PROPERTY_CODE"],
				"DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE" => $arParams["DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE"],	
				"DETAIL_IMAGE_RESOLUTION" => $arParams["DETAIL_IMAGE_RESOLUTION"],				
				"DETAIL_ADD_DETAIL_TO_SLIDER" => $arParams["DETAIL_ADD_DETAIL_TO_SLIDER"],
				"DETAIL_DETAIL_PICTURE_MODE" => $arParams["DETAIL_DETAIL_PICTURE_MODE"],
				"DETAIL_SHOW_SLIDER" => $arParams["DETAIL_SHOW_SLIDER"],
				"DETAIL_SLIDER_INTERVAL" => $arParams["DETAIL_SLIDER_INTERVAL"],
				"DETAIL_SLIDER_PROGRESS" => $arParams["DETAIL_SLIDER_PROGRESS"],

				"USE_GIFTS_DETAIL" => $arParams["USE_GIFTS_DETAIL"],
				"GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => $arParams["GIFTS_DETAIL_PAGE_ELEMENT_COUNT"],
				"GIFTS_DETAIL_HIDE_BLOCK_TITLE" => $arParams["GIFTS_DETAIL_HIDE_BLOCK_TITLE"],
				"GIFTS_DETAIL_BLOCK_TITLE" => $arParams["GIFTS_DETAIL_BLOCK_TITLE"],
				"GIFTS_DETAIL_TEXT_LABEL_GIFT" => $arParams["GIFTS_DETAIL_TEXT_LABEL_GIFT"],
				"GIFTS_MESS_BTN_BUY" => $arParams["~GIFTS_MESS_BTN_BUY"],

				"USE_STORE" => $arParams["USE_STORE"],
				"STORE_PATH" => $arParams["STORE_PATH"],
				"STORES" => $arParams["STORES"],
				"USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
				"USER_FIELDS" => $arParams["USER_FIELDS"],
				"FIELDS" => $arParams["FIELDS"],
				"MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
				"SHOW_EMPTY_STORE" => $arParams["SHOW_EMPTY_STORE"],
				"SHOW_GENERAL_STORE_INFORMATION" => $arParams["SHOW_GENERAL_STORE_INFORMATION"],
				"MAIN_TITLE" => $arParams["~MAIN_TITLE"],

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
				"MESS_REVIEWS_TAB" => $arParams["MESS_REVIEWS_TAB"],

				"SET_ITEMS_COUNT" => $arParams["SET_ITEMS_COUNT"],

				"OBJECTS_USE_REVIEW" => $arParams["OBJECTS_USE_REVIEW"],
				"OBJECTS_REVIEWS_IBLOCK_ID" => $arParams["OBJECTS_REVIEWS_IBLOCK_ID"],
				"CONTACTS_IBLOCK_ID" => $arParams["CONTACTS_IBLOCK_ID"],
				"CONTACTS_USE_REVIEW" => $arParams["CONTACTS_USE_REVIEW"],
				"CONTACTS_REVIEWS_IBLOCK_ID" => $arParams["CONTACTS_REVIEWS_IBLOCK_ID"],
				"CONTACTS_REVIEWS_PAGE_LINK" => $arParams["CONTACTS_REVIEWS_PAGE_LINK"],

				"QUICK_VIEW_PREV_NEXT" => $arSettings["QUICK_VIEW"]["VALUE"] != "OFF" ? "Y" : "N",
				"PRODUCTS_VIEW" => !empty($arCurSection["UF_PRODUCTS_VIEW"]) ? $arCurSection["UF_PRODUCTS_VIEW"] : $arSettings["PRODUCTS_VIEW"]["VALUE"]
			),
			$component
		);?>
		<?$GLOBALS["CATALOG_CURRENT_SECTION_ID"] = $intSectionID;
		
		if(!$_REQUEST["PAGEN_1"] || $_REQUEST["PAGEN_1"] <= 1) {
			if($seoMeta["SMART_FILTER_LINK"]) {
				//SECTION_DESCRIPTION//
				if(!empty($seoMeta["DESCRIPTION"])) {?>
					<div class="catalog-section-desc"><?=$seoMeta["DESCRIPTION"]?></div>
				<?}
				
				//SECTION_FAQ//
				if(intval($seoMeta["FAQ"]) > 0) {
					$GLOBALS["arCatalogFaqFilter"] = array("ACTIVE" => "Y", "SECTION_ID" => $seoMeta["FAQ"], "INCLUDE_SUBSECTIONS" => "N", "SECTION_GLOBAL_ACTIVE" => "Y");?>
					<?$APPLICATION->IncludeComponent("bitrix:news.list", "faq",
						array(
							"IBLOCK_TYPE" => $arParams["FAQ_IBLOCK_TYPE"],
							"IBLOCK_ID" => $arParams["FAQ_IBLOCK_ID"],
							"NEWS_COUNT" => $arParams["FAQ_NEWS_COUNT"],
							"SORT_BY1" => $arParams["FAQ_SORT_BY1"],
							"SORT_ORDER1" => $arParams["FAQ_SORT_ORDER1"],
							"SORT_BY2" => $arParams["FAQ_SORT_BY2"],
							"SORT_ORDER2" => $arParams["FAQ_SORT_ORDER2"],
							"FILTER_NAME" => "arCatalogFaqFilter",
							"FIELD_CODE" => array(),
							"PROPERTY_CODE" => array(),
							"CHECK_DATES" => "Y",
							"DETAIL_URL" => "",
							"AJAX_MODE" => "",
							"AJAX_OPTION_SHADOW" => "",
							"AJAX_OPTION_JUMP" => "",
							"AJAX_OPTION_STYLE" => "",
							"AJAX_OPTION_HISTORY" => "",
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"CACHE_FILTER" => $arParams["CACHE_FILTER"],
							"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
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
							"DISPLAY_TOP_PAGER" => "",
							"DISPLAY_BOTTOM_PAGER" => "",
							"PAGER_SHOW_ALWAYS" => "",
							"PAGER_TEMPLATE" => "",
							"PAGER_DESC_NUMBERING" => "",
							"PAGER_DESC_NUMBERING_CACHE_TIME" => "",
							"PAGER_SHOW_ALL" => "",
							"AJAX_OPTION_ADDITIONAL" => ""
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);?>
				<?}
			} else {
				//SECTION_DESCRIPTION//
				if(!empty($seoMeta["DESCRIPTION"]) || !empty($arCurSection["DESCRIPTION"])) {?>
					<div class="catalog-section-desc"><?=(!empty($seoMeta["DESCRIPTION"]) ? $seoMeta["DESCRIPTION"] : $arCurSection["DESCRIPTION"]);?></div>
				<?}

				//SECTION_FAQ//
				if(intval($arCurSection["UF_FAQ"] > 0)) {
					$GLOBALS["arCatalogFaqFilter"] = array("ACTIVE" => "Y", "SECTION_ID" => $arCurSection["UF_FAQ"], "INCLUDE_SUBSECTIONS" => "N", "SECTION_GLOBAL_ACTIVE" => "Y");?>
					<?$APPLICATION->IncludeComponent("bitrix:news.list", "faq",
						array(
							"IBLOCK_TYPE" => $arParams["FAQ_IBLOCK_TYPE"],
							"IBLOCK_ID" => $arParams["FAQ_IBLOCK_ID"],
							"NEWS_COUNT" => $arParams["FAQ_NEWS_COUNT"],
							"SORT_BY1" => $arParams["FAQ_SORT_BY1"],
							"SORT_ORDER1" => $arParams["FAQ_SORT_ORDER1"],
							"SORT_BY2" => $arParams["FAQ_SORT_BY2"],
							"SORT_ORDER2" => $arParams["FAQ_SORT_ORDER2"],
							"FILTER_NAME" => "arCatalogFaqFilter",
							"FIELD_CODE" => array(),
							"PROPERTY_CODE" => array(),
							"CHECK_DATES" => "Y",
							"DETAIL_URL" => "",
							"AJAX_MODE" => "",
							"AJAX_OPTION_SHADOW" => "",
							"AJAX_OPTION_JUMP" => "",
							"AJAX_OPTION_STYLE" => "",
							"AJAX_OPTION_HISTORY" => "",
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"CACHE_FILTER" => $arParams["CACHE_FILTER"],
							"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
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
							"DISPLAY_TOP_PAGER" => "",
							"DISPLAY_BOTTOM_PAGER" => "",
							"PAGER_SHOW_ALWAYS" => "",
							"PAGER_TEMPLATE" => "",
							"PAGER_DESC_NUMBERING" => "",
							"PAGER_DESC_NUMBERING_CACHE_TIME" => "",
							"PAGER_SHOW_ALL" => "",
							"AJAX_OPTION_ADDITIONAL" => ""
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);?>
				<?}
			}
		}
	}?>
</div>

<?$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($this->randString()));?>

<script type="text/javascript">
	BX.message({
		SECTION_LINKS_ALL: "<?=GetMessageJS('CATALOG_SECTION_LINKS_ALL')?>",
		SECTION_LINKS_SHOW_ALL: "<?=GetMessageJS('CATALOG_SECTION_LINKS_SHOW_ALL')?>",
		SECTION_LINKS_HIDE: "<?=GetMessageJS('CATALOG_SECTION_LINKS_HIDE')?>"
	});
	var <?=$obName?> = new JCCatalogComponent();
</script>

<?//GIFTS//
if($arParams["USE_GIFTS_SECTION"] == "Y" && ModuleManager::isModuleInstalled("sale")) {?>
	<div class="catalog-section-gifts" data-entity="parent-container" style="display: none;">
		<?if($arParams["GIFTS_SECTION_LIST_HIDE_BLOCK_TITLE"] !== "Y") {?>
			<div class="h2" data-entity="header" data-showed="false" style="display: none; opacity: 0;"><?=($arParams["GIFTS_SECTION_LIST_BLOCK_TITLE"] ?: Loc::getMessage("CATALOG_GIFTS"))?></div>
		<?}
		CBitrixComponent::includeComponentClass("bitrix:sale.products.gift.section");?>
		<?$APPLICATION->IncludeComponent("bitrix:sale.products.gift.section", ".default",
			array(
				"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
				"ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
				"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
				"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
				"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],										
				"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
				"BASKET_URL" => $arParams["BASKET_URL"],
				"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
				"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
				"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
				"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
				"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],				
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_FILTER" => $arParams["CACHE_FILTER"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],									
				"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
				"PAGE_ELEMENT_COUNT" => $arParams["GIFTS_SECTION_LIST_PAGE_ELEMENT_COUNT"],
				"DEFERRED_PAGE_ELEMENT_COUNT" => 0,				
				"PRICE_CODE" => $arParams["PRICE_CODE"],
				"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
				"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

				"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
				"USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
				"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ""),
				"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ""),
				"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
					
				"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
				"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
				"OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
				"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
				"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
				"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
				"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
				"OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],

				"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
				"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
				"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
				"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],					
				"USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
				"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
				"CURRENCY_ID" => $arParams["CURRENCY_ID"],
				"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
				"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],

				"TEXT_LABEL_GIFT" => $arParams["GIFTS_SECTION_LIST_TEXT_LABEL_GIFT"],
				
				"PRODUCT_DISPLAY_MODE" => $arParams["PRODUCT_DISPLAY_MODE"],						
				"PRODUCT_ROW_VARIANTS" => Bitrix\Main\Web\Json::encode(SaleProductsGiftSectionComponent::predictRowVariants(4, $arParams["GIFTS_SECTION_LIST_PAGE_ELEMENT_COUNT"])),
				"DEFERRED_PRODUCT_ROW_VARIANTS" => "",
				
				"OFFER_TREE_PROPS" => $arParams["OFFER_TREE_PROPS"],
				"PRODUCT_SUBSCRIPTION" => $arParams["PRODUCT_SUBSCRIPTION"],
				"SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"],						
				"SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"],
				"SHOW_MAX_QUANTITY" => $arParams["SHOW_MAX_QUANTITY"],
				"MESS_SHOW_MAX_QUANTITY" => (isset($arParams["~MESS_SHOW_MAX_QUANTITY"]) ? $arParams["~MESS_SHOW_MAX_QUANTITY"] : ""),
				"RELATIVE_QUANTITY_FACTOR" => (isset($arParams["RELATIVE_QUANTITY_FACTOR"]) ? $arParams["RELATIVE_QUANTITY_FACTOR"] : ""),
				"MESS_RELATIVE_QUANTITY_MANY" => (isset($arParams["~MESS_RELATIVE_QUANTITY_MANY"]) ? $arParams["~MESS_RELATIVE_QUANTITY_MANY"] : ""),
				"MESS_RELATIVE_QUANTITY_FEW" => (isset($arParams["~MESS_RELATIVE_QUANTITY_FEW"]) ? $arParams["~MESS_RELATIVE_QUANTITY_FEW"] : ""),				
				"MESS_BTN_BUY" => (isset($arParams["~MESS_BTN_BUY"]) ? $arParams["~MESS_BTN_BUY"] : ""),
				"MESS_BTN_ADD_TO_BASKET" => (isset($arParams["~MESS_BTN_ADD_TO_BASKET"]) ? $arParams["~MESS_BTN_ADD_TO_BASKET"] : ""),
				"GIFTS_MESS_BTN_BUY" => (isset($arParams["~GIFTS_MESS_BTN_BUY"]) ? $arParams["~GIFTS_MESS_BTN_BUY"] : ""),
				"GIFTS_MESS_BTN_ADD_TO_BASKET" => (isset($arParams["~GIFTS_MESS_BTN_BUY"]) ? $arParams["~GIFTS_MESS_BTN_BUY"] : ""),
				"MESS_BTN_SUBSCRIBE" => (isset($arParams["~MESS_BTN_SUBSCRIBE"]) ? $arParams["~MESS_BTN_SUBSCRIBE"] : ""),
				"MESS_BTN_DETAIL" => (isset($arParams["~MESS_BTN_DETAIL"]) ? $arParams["~MESS_BTN_DETAIL"] : ""),
				"MESS_NOT_AVAILABLE" => (isset($arParams["~MESS_NOT_AVAILABLE"]) ? $arParams["~MESS_NOT_AVAILABLE"] : ""),
				"MESS_BTN_COMPARE" => (isset($arParams["~MESS_BTN_COMPARE"]) ? $arParams["~MESS_BTN_COMPARE"] : ""),
					
				"USE_ENHANCED_ECOMMERCE" => (isset($arParams["USE_ENHANCED_ECOMMERCE"]) ? $arParams["USE_ENHANCED_ECOMMERCE"] : ""),
				"DATA_LAYER_NAME" => (isset($arParams["DATA_LAYER_NAME"]) ? $arParams["DATA_LAYER_NAME"] : ""),
				"BRAND_PROPERTY" => (isset($arParams["BRAND_PROPERTY"]) ? $arParams["BRAND_PROPERTY"] : ""),
				
				"ADD_TO_BASKET_ACTION" => $basketAction,
				"COMPARE_PATH" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["compare"],
				"COMPARE_NAME" => $arParams["COMPARE_NAME"],
				
				"DETAIL_ADD_PICT_PROP" => $arParams["ADD_PICT_PROP"],				
				"DETAIL_OFFER_ADD_PICT_PROP" => $arParams["OFFER_ADD_PICT_PROP"],
				"DETAIL_USE_RATIO_IN_RANGES" => $arParams["USE_RATIO_IN_RANGES"],
				"DETAIL_PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],				
				"DETAIL_OFFERS_FIELD_CODE" => $arParams["DETAIL_OFFERS_FIELD_CODE"],
				"DETAIL_OFFERS_PROPERTY_CODE" => $arParams["DETAIL_OFFERS_PROPERTY_CODE"],
				"DETAIL_MAIN_BLOCK_PROPERTY_CODE" => $arParams["DETAIL_MAIN_BLOCK_PROPERTY_CODE"],
				"DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE" => $arParams["DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE"],	
				"DETAIL_IMAGE_RESOLUTION" => $arParams["DETAIL_IMAGE_RESOLUTION"],				
				"DETAIL_ADD_DETAIL_TO_SLIDER" => $arParams["DETAIL_ADD_DETAIL_TO_SLIDER"],
				"DETAIL_DETAIL_PICTURE_MODE" => $arParams["DETAIL_DETAIL_PICTURE_MODE"],
				"DETAIL_SHOW_SLIDER" => $arParams["DETAIL_SHOW_SLIDER"],
				"DETAIL_SLIDER_INTERVAL" => $arParams["DETAIL_SLIDER_INTERVAL"],
				"DETAIL_SLIDER_PROGRESS" => $arParams["DETAIL_SLIDER_PROGRESS"],

				"USE_GIFTS_DETAIL" => $arParams["USE_GIFTS_DETAIL"],
				"GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => $arParams["GIFTS_DETAIL_PAGE_ELEMENT_COUNT"],
				"GIFTS_DETAIL_HIDE_BLOCK_TITLE" => $arParams["GIFTS_DETAIL_HIDE_BLOCK_TITLE"],
				"GIFTS_DETAIL_BLOCK_TITLE" => $arParams["GIFTS_DETAIL_BLOCK_TITLE"],
				"GIFTS_DETAIL_TEXT_LABEL_GIFT" => $arParams["GIFTS_DETAIL_TEXT_LABEL_GIFT"],
				"GIFTS_MESS_BTN_BUY" => $arParams["~GIFTS_MESS_BTN_BUY"],

				"USE_STORE" => $arParams["USE_STORE"],
				"STORE_PATH" => $arParams["STORE_PATH"],
				"STORES" => $arParams["STORES"],
				"USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
				"USER_FIELDS" => $arParams["USER_FIELDS"],
				"FIELDS" => $arParams["FIELDS"],
				"MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
				"SHOW_EMPTY_STORE" => $arParams["SHOW_EMPTY_STORE"],
				"SHOW_GENERAL_STORE_INFORMATION" => $arParams["SHOW_GENERAL_STORE_INFORMATION"],
				"MAIN_TITLE" => $arParams["~MAIN_TITLE"],

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
				"MESS_REVIEWS_TAB" => $arParams["MESS_REVIEWS_TAB"],

				"SET_ITEMS_COUNT" => $arParams["SET_ITEMS_COUNT"],

				"OBJECTS_USE_REVIEW" => $arParams["OBJECTS_USE_REVIEW"],
				"OBJECTS_REVIEWS_IBLOCK_ID" => $arParams["OBJECTS_REVIEWS_IBLOCK_ID"],
				"CONTACTS_IBLOCK_ID" => $arParams["CONTACTS_IBLOCK_ID"],
				"CONTACTS_USE_REVIEW" => $arParams["CONTACTS_USE_REVIEW"],
				"CONTACTS_REVIEWS_IBLOCK_ID" => $arParams["CONTACTS_REVIEWS_IBLOCK_ID"],
				"CONTACTS_REVIEWS_PAGE_LINK" => $arParams["CONTACTS_REVIEWS_PAGE_LINK"]
			),
			$component,
			array("HIDE_ICONS" => "Y")
		);?>
		<?unset($basketAction);?>
	</div>
<?}?>