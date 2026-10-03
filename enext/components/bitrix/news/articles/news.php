<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

$arParams["COUNT_ELEMENTS"] = $arParams["SECTION_COUNT_ELEMENTS"] != "N";

//SECTIONS//
$arFilter = array(
	"ACTIVE" => "Y",
	"DEPTH_LEVEL" => 1,
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],
	"CNT_ACTIVE" => "Y",
	"ELEMENT_SUBSECTIONS" => "N"
);

$isCacheManager = defined("BX_COMP_MANAGED_CACHE") && is_object($GLOBALS["CACHE_MANAGER"]);

$obCache = new CPHPCache();
if($obCache->InitCache($arParams["CACHE_TIME"], serialize($arFilter), "/iblock/news")) {
	$arSections = $obCache->GetVars();
} elseif(Bitrix\Main\Loader::includeModule("iblock") && $obCache->StartDataCache()) {
	$arSections = array();
	$rsSections = CIBlockSection::GetList(array("left_margin" => "asc"), $arFilter, $arParams["COUNT_ELEMENTS"], array("ID", "CODE", "NAME", "IBLOCK_ID", "SECTION_PAGE_URL"));

	if($isCacheManager) {
		$GLOBALS["CACHE_MANAGER"]->StartTagCache("/iblock/news");
		$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);
	}

	while($arSection = $rsSections->GetNext()) {
		$ipropValues = new Bitrix\Iblock\InheritedProperty\SectionValues($arSection["IBLOCK_ID"], $arSection["ID"]);
		$arSection["IPROPERTY_VALUES"] = $ipropValues->getValues();

		$arSections[$arSection["ID"]] = $arSection;
	}

	if($isCacheManager)
		$GLOBALS["CACHE_MANAGER"]->EndTagCache();
	
	$obCache->EndDataCache($arSections);
} else {
	$arSections = array();
}
unset($arFilter);

//GLOBAL_FILTER//
if(!empty($arSections)) {
	$GLOBALS[$arParams["FILTER_NAME"]] = array(
		array(
			"LOGIC" => "OR",
			array("SECTION_ID" => array_keys($arSections)),
			array("SECTION_ID" => false)
		)
	);
} else {
	$GLOBALS[$arParams["FILTER_NAME"]]["SECTION_ID"] = false;
}

//ELEMENT_CNT//
if($arParams["COUNT_ELEMENTS"]) {
	$arFilter = array(
		"ACTIVE" => "Y",
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"INCLUDE_SUBSECTIONS" => "N"
	);
	if(!empty($arSections))
		$arFilter = array_merge($arFilter, $GLOBALS[$arParams["FILTER_NAME"]]);
	else
		$arFilter["SECTION_ID"] = false;
	
	$obCache = new CPHPCache();
	if($obCache->InitCache($arParams["CACHE_TIME"], serialize($arFilter), "/iblock/news")) {
		$arElementCnt = $obCache->GetVars();
	} elseif(Bitrix\Main\Loader::includeModule("iblock") && $obCache->StartDataCache()) {
		$arElementCnt = 0;

		if($isCacheManager) {
			$GLOBALS["CACHE_MANAGER"]->StartTagCache("/iblock/news");
			$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);
		}

		$arElementCnt = CIBlockElement::GetList(array(), $arFilter, array(), false);

		if($isCacheManager)
			$GLOBALS["CACHE_MANAGER"]->EndTagCache();
		
		$obCache->EndDataCache($arElementCnt);
	} else {
		$arElementCnt = 0;
	}
	unset($arFilter);
}?>

<div class="articles-container">
	<?//PREVIEW//
	if(!$_REQUEST["PAGEN_1"] || empty($_REQUEST["PAGEN_1"]) || $_REQUEST["PAGEN_1"] <= 1) {?>
		<?$APPLICATION->IncludeComponent("bitrix:main.include", "preview",
			array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => SITE_DIR."include/articles_preview.php"
			),		
			$component
		);?>
	<?}
	
	//SECTION_LIST//?>
	<div class="articles-links">
		<a class="articles-link active" href="<?=$arResult['FOLDER'].$arResult['URL_TEMPLATES']['news']?>" title="<?=Loc::getMessage('SECT_ALL')?>"><?=Loc::getMessage("SECT_ALL").($arParams["COUNT_ELEMENTS"] && $arElementCnt > 0 ? "<span>".$arElementCnt."</span>" : "")?></a>	
		<?if(!empty($arSections)) {
			foreach($arSections as $arSection) {
				$sectionTitle = $arSection["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"] != "" ? $arSection["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"] : $arSection["NAME"];?>
				
				<a class="articles-link" href="<?=$arSection['SECTION_PAGE_URL']?>" title="<?=$sectionTitle?>"><?=$arSection["NAME"].($arParams["COUNT_ELEMENTS"] && $arSection["ELEMENT_CNT"] > 0 ? "<span>".$arSection["ELEMENT_CNT"]."</span>" : "")?></a>
			<?}
			unset($arSection);
		}?>
	</div>
	
	<?//ELEMENTS//?>
	<?$APPLICATION->IncludeComponent("bitrix:news.list", "articles",
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
			"INCLUDE_SUBSECTIONS" => "N",
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
		),
		$component
	);?>
	
	<?//DESCRIPTION//
	if(!$_REQUEST["PAGEN_1"] || empty($_REQUEST["PAGEN_1"]) || $_REQUEST["PAGEN_1"] <= 1) {?>
		<?$APPLICATION->IncludeComponent("bitrix:main.include", "description",
			array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => SITE_DIR."include/articles_description.php"
			),
			$component
		);?>
	<?}?>
</div>

<?//TITLE//
if(!empty($_REQUEST["PAGEN_1"]) && $_REQUEST["PAGEN_1"] > 1) {
	$APPLICATION->SetPageProperty("title", $arParams["PAGER_TITLE"]." | ".Loc::getMessage("SECT_TITLE")." ".$_REQUEST["PAGEN_1"]);
	$APPLICATION->SetPageProperty("keywords", "");
	$APPLICATION->SetPageProperty("description", "");
}