<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

if($arResult["VARIABLES"]["SECTION_ID"] || $arResult["VARIABLES"]["SECTION_CODE"]) {
	//CURRENT_SECTION_FIELDS//
	$arFilter = array();

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

	$isCacheManager = defined("BX_COMP_MANAGED_CACHE") && is_object($GLOBALS["CACHE_MANAGER"]);

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
				<img src="<?=$arCurSection['UF_BANNER']['SRC']?>" alt="<?=$arCurSection['TITLE']?>" />
			</a>
		</div>
		<?$APPLICATION->AddViewContent("UF_BANNER", ob_get_contents());
		ob_end_clean();
	}?>

	<div class="gallery-container">
		<?//PREVIEW//
		if(!empty($arCurSection["UF_PREVIEW"])) {
			if(!$_REQUEST["PAGEN_1"] || empty($_REQUEST["PAGEN_1"]) || $_REQUEST["PAGEN_1"] <= 1) {?>
				<div class="preview">
					<?=$arCurSection["UF_PREVIEW"];?>
				</div>
			<?}
		}
		
		//SECTION_LIST//?>
		<?$APPLICATION->IncludeComponent("bitrix:catalog.section.list", "",
			Array(
				"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
				"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
				"COUNT_ELEMENTS" => "N",
				"TOP_DEPTH" => "1",
				"SECTION_FIELDS" => array(),
				"SECTION_USER_FIELDS" => array(
					0 => "UF_SHORT_DESC"
				),
				"VIEW_MODE" => "",
				"SHOW_PARENT_NAME" => "",
				"SECTION_URL" => "",
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"ADD_SECTIONS_CHAIN" => "N"
			),
			$component
		);?>

		<?//ELEMENTS//?>	
		<?$APPLICATION->IncludeComponent("bitrix:news.list", "gallery",
			Array(
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
				"AJAX_OPTION_ADDITIONAL" => $arParams["AJAX_OPTION_ADDITIONAL"]
			),
			$component
		);?>
		
		<?//DESCRIPTION//
		if(!empty($arCurSection["DESCRIPTION"])) {
			if(!$_REQUEST["PAGEN_1"] || empty($_REQUEST["PAGEN_1"]) || $_REQUEST["PAGEN_1"] <= 1) {?>
				<div class="description">
					<?=$arCurSection["DESCRIPTION"];?>
				</div>
			<?}
		}?>
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
		$APPLICATION->AddHeadString("<meta property='og:title' content='".$arCurSection["TITLE"]."' />", true);
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
	//404//
	\Bitrix\Iblock\Component\Tools::process404(
		trim($arParams["MESSAGE_404"]) ?: GetMessage("SECT_NOT_FOUND")
		,true
		,$arParams["SET_STATUS_404"] === "Y"
		,$arParams["SHOW_404"] === "Y"
		,$arParams["FILE_404"]
	);
}