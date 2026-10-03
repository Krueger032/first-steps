<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

$isCacheManager = defined("BX_COMP_MANAGED_CACHE") && is_object($GLOBALS["CACHE_MANAGER"]);

if(intval($arParams["CONTACTS_IBLOCK_ID"] > 0)) {
	$arFilter = array(
		"IBLOCK_ID" => $arParams["CONTACTS_IBLOCK_ID"],
		"ACTIVE" => "Y"
	);
	
	$obCache = new CPHPCache();
	if($obCache->InitCache($arParams["CACHE_TIME"], serialize($arFilter), "/iblock/news")) {
		$arContacts = $obCache->GetVars();
	} elseif(Bitrix\Main\Loader::includeModule("iblock") && $obCache->StartDataCache()) {		
		$arContacts = array();
		$rsElements = CIBlockElement::GetList(array("SORT" => "ASC", "ACTIVE_FROM" => "DESC"), $arFilter, false, array("nTopCount" => 1), array("ID", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE"));

		if($isCacheManager)
			$GLOBALS["CACHE_MANAGER"]->StartTagCache("/iblock/news");

		if($obElement = $rsElements->GetNextElement()) {
			if($isCacheManager)
				$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$arParams["CONTACTS_IBLOCK_ID"]);
			
			$arElement = $obElement->GetFields();
			$arProps = $obElement->GetProperties();

			$arContacts["NAME"] = $arElement["NAME"];		
			if($arElement["PREVIEW_PICTURE"] > 0)
				$arContacts["PREVIEW_PICTURE"] = CFile::GetFileArray($arElement["PREVIEW_PICTURE"]);
			
			$arContacts["ADDRESS"] = $arProps["ADDRESS"]["VALUE"];
			$arContacts["PHONE"] = $arProps["PHONE"]["VALUE"];
			$arContacts["EMAIL"] = $arProps["EMAIL"]["VALUE"];
		}

		if($isCacheManager)
			$GLOBALS["CACHE_MANAGER"]->EndTagCache();
		
		$obCache->EndDataCache($arContacts);
	} else {
		$arContacts = array();
	}
	unset($arFilter);
}

if(intval($arParams["IBLOCK_ID"]) > 0) {
	$arFilter = array(
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ACTIVE" => "Y"
	);

	$obCache = new CPHPCache();
	if($obCache->InitCache($arParams["CACHE_TIME"], serialize($arFilter), "/iblock/news")) {
		$arRating = $obCache->GetVars();
	} elseif(Bitrix\Main\Loader::includeModule("iblock") && $obCache->StartDataCache()) {		
		$arRating = array();
		$ratingSum = $reviewsCount = 0;
		$rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, array("ID", "IBLOCK_ID"));

		if($isCacheManager) {
			$GLOBALS["CACHE_MANAGER"]->StartTagCache("/iblock/news");
			$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);
		}

		while($obElement = $rsElements->GetNextElement()) {
			$arElement = $obElement->GetFields();
			$arProps = $obElement->GetProperties();
			
			$ratingSum += $arProps["RATING"]["VALUE_XML_ID"];
			
			$reviewsCount++;
		}

		$arRating["VALUE"] = $reviewsCount > 0 ? sprintf("%.1f", round($ratingSum / $reviewsCount, 1)) : 0;
		$arRating["REVIEWS_COUNT"] = $reviewsCount;

		if($isCacheManager)
			$GLOBALS["CACHE_MANAGER"]->EndTagCache();
		
		$obCache->EndDataCache($arRating);
	} else {
		$arRating = array();
	}
	unset($arFilter);
}?>

<div class="reviews-container" itemscope itemtype="http://schema.org/Organization">
	<?//PREVIEW//
	if(!$_REQUEST["PAGEN_1"] || empty($_REQUEST["PAGEN_1"]) || $_REQUEST["PAGEN_1"] <= 1) {?>
		<?$APPLICATION->IncludeComponent("bitrix:main.include", "preview",
			array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => SITE_DIR."include/reviews_preview.php"
			),		
			$component
		);?>
	<?}
	
	//ELEMENTS//?>
	<?$APPLICATION->IncludeComponent("bitrix:news.list", "reviews",
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
			"AJAX_OPTION_ADDITIONAL" => $arParams["AJAX_OPTION_ADDITIONAL"]
		),
		$component
	);?>
	
	<?//DESCRIPTION//
	if(!$_REQUEST["PAGEN_1"] || empty($_REQUEST["PAGEN_1"]) || $_REQUEST["PAGEN_1"] <= 1) {?>
		<?$APPLICATION->IncludeComponent("bitrix:main.include", "description",
			array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => SITE_DIR."include/reviews_description.php"
			),
			$component
		);?>
	<?}
	
	//META//
	if(!empty($arContacts["NAME"])) {?>
		<meta itemprop="name" content="<?=$arContacts['NAME']?>" />
	<?}?>
	<link itemprop="url" href="<?=(CMain::IsHTTPS() ? 'https' : 'http').'://'.SITE_SERVER_NAME.$arResult['FOLDER']?>" />
	<?if(is_array($arContacts["PREVIEW_PICTURE"])) {?>
		<link itemprop="logo" href="<?=$arContacts['PREVIEW_PICTURE']['SRC']?>" />
	<?}
	if(!empty($arContacts["ADDRESS"])) {?>
		<meta itemprop="address" content="<?=$arContacts['ADDRESS']?>" />
	<?}
	if(!empty($arContacts["PHONE"])) {
		foreach($arContacts["PHONE"] as $arPhone) {?>
			<meta itemprop="telephone" content="<?=$arPhone?>" />
		<?}
		unset($arPhone);
	}
	if(!empty($arContacts["EMAIL"])) {
		foreach($arContacts["EMAIL"] as $arEmail) {?>
			<meta itemprop="email" content="<?=$arEmail?>" />
		<?}
		unset($arEmail);
	}
	if(isset($arRating["REVIEWS_COUNT"]) && $arRating["REVIEWS_COUNT"] > 0) {?>
		<span itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
			<meta itemprop="ratingValue" content="<?=$arRating['VALUE']?>" />
			<meta itemprop="reviewCount" content="<?=$arRating['REVIEWS_COUNT']?>" />
		</span>
	<?}?>
</div>

<?//TITLE//
if(!empty($_REQUEST["PAGEN_1"]) && $_REQUEST["PAGEN_1"] > 1) {
	$APPLICATION->SetPageProperty("title", $arParams["PAGER_TITLE"]." | ".GetMessage("SECT_TITLE")." ".$_REQUEST["PAGEN_1"]);
	$APPLICATION->SetPageProperty("keywords", "");
	$APPLICATION->SetPageProperty("description", "");
}

//META_PROPERTY//
if(!empty($arContacts["NAME"]))
	$APPLICATION->AddHeadString("<meta property='og:title' content='".$arContacts["NAME"]."' />", true);

$ogScheme = CMain::IsHTTPS() ? "https" : "http";
$APPLICATION->AddHeadString("<meta property='og:url' content='".$ogScheme."://".SITE_SERVER_NAME.$arResult["FOLDER"]."' />", true);
if(is_array($arContacts["PREVIEW_PICTURE"])) {
	$APPLICATION->AddHeadString("<meta property='og:image' content='".$ogScheme."://".SITE_SERVER_NAME.$arContacts["PREVIEW_PICTURE"]["SRC"]."' />", true);
	$APPLICATION->AddHeadString("<meta property='og:image:width' content='".$arContacts["PREVIEW_PICTURE"]["WIDTH"]."' />", true);
	$APPLICATION->AddHeadString("<meta property='og:image:height' content='".$arContacts["PREVIEW_PICTURE"]["HEIGHT"]."' />", true);
	$APPLICATION->AddHeadString("<link rel='image_src' href='".$ogScheme."://".SITE_SERVER_NAME.$arContacts["PREVIEW_PICTURE"]["SRC"]."' />", true);
}