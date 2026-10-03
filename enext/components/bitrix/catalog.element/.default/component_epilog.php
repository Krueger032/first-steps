<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

//CURRENCIES//
if(!empty($templateData["TEMPLATE_LIBRARY"])) {
	$loadCurrency = false;
	if(!empty($templateData["CURRENCIES"])) {
		$loadCurrency = Bitrix\Main\Loader::includeModule("currency");
	}
	CJSCore::Init($templateData["TEMPLATE_LIBRARY"]);
	if($loadCurrency) {?>
		<script type="text/javascript">
			BX.Currency.setCurrencies(<?=$templateData["CURRENCIES"]?>);
		</script>
	<?}
}

//SECTION_PATH//
$addSectionsChain = $arParams["ADD_SECTIONS_CHAIN_EPILOG"] == "Y";
$addElementChain = $arParams["ADD_ELEMENT_CHAIN_EPILOG"] == "Y";
if($addSectionsChain) {
	foreach($arResult["SECTION"]["PATH"] as $key => $path) {
		unset($arResult["SECTION"]["PATH"][$key]);
		$arResult["SECTION"]["PATH"][$path["ID"]] = $path;
		$ipropValues = new Bitrix\Iblock\InheritedProperty\SectionValues($arResult["IBLOCK_ID"], $path["ID"]);
		$arResult["SECTION"]["PATH"][$path["ID"]]["IPROPERTY_VALUES"] = $ipropValues->getValues();
	}
	unset($ipropValues, $key, $path);
	
	if(is_array($arResult["SECTION"]["PATH"])){ //PRO_WS//
		$arFilter = array(
			"IBLOCK_ID" => $arResult["IBLOCK_ID"],
			"ACTIVE" => "Y",
			"GLOBAL_ACTIVE" => "Y",
			"ID" => array_keys($arResult["SECTION"]["PATH"])
		);
		
		$arSelect = array("ID", "IBLOCK_ID", "UF_BREADCRUMB_TITLE");
		
		$isCacheManager = defined("BX_COMP_MANAGED_CACHE") && is_object($GLOBALS["CACHE_MANAGER"]);

		$obCache = new CPHPCache();
		if($obCache->InitCache($arParams["CACHE_TIME"], serialize($arFilter), "/iblock/catalog")) {
			$arCurElement = $obCache->GetVars();
		} elseif(Bitrix\Main\Loader::includeModule("iblock") && $obCache->StartDataCache()) {
			$arCurElement = array();		
			$rsSections = CIBlockSection::GetList(array("DEPTH_LEVEL" => "DESC"), $arFilter, false, $arSelect);

			if($isCacheManager) {
				$GLOBALS["CACHE_MANAGER"]->StartTagCache("/iblock/catalog");
				$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$arResult["IBLOCK_ID"]);
			}
			
			while($arSection = $rsSections->GetNext()) {
				$arCurElement["SECTION_PATH"][$arSection["ID"]]["BREADCRUMB_TITLE"] = $arSection["UF_BREADCRUMB_TITLE"];
			}

			if($isCacheManager)
				$GLOBALS["CACHE_MANAGER"]->EndTagCache();
			
			$obCache->EndDataCache($arCurElement);
		} else {
			$arCurElement = array();
		}
	}
}

//BREADCRUMBS//
if($addSectionsChain) {
	foreach($arResult["SECTION"]["PATH"] as $path) {	
		if(!empty($arCurElement["SECTION_PATH"][$path["ID"]]["BREADCRUMB_TITLE"]))
			$APPLICATION->AddChainItem($arCurElement["SECTION_PATH"][$path["ID"]]["BREADCRUMB_TITLE"], $path["~SECTION_PAGE_URL"]);
		elseif(!empty($path["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"]))
			$APPLICATION->AddChainItem($path["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"], $path["~SECTION_PAGE_URL"]);
		else
			$APPLICATION->AddChainItem($path["NAME"], $path["~SECTION_PAGE_URL"]);
	}
}
unset($addSectionsChain);

if($addElementChain) {
	if(!empty($arResult["BREADCRUMB_TITLE"]))
		$APPLICATION->AddChainItem($arResult["BREADCRUMB_TITLE"], $arResult["~DETAIL_PAGE_URL"]);
	elseif(!empty($arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]))
		$APPLICATION->AddChainItem($arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"], $arResult["~DETAIL_PAGE_URL"]);
	else
		$APPLICATION->AddChainItem($arResult["NAME"], $arResult["~DETAIL_PAGE_URL"]);
}
unset($addElementChain);

//OPEN_GRAPH//
$APPLICATION->AddHeadString("<meta property='og:type' content='product' />", true);

$ogTitle = !empty($arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]) ? $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] : $arResult["NAME"];
$APPLICATION->AddHeadString("<meta property='og:title' content='".$ogTitle."' />", true);
unset($ogTitle);

if(!empty($arResult["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"]))
	$APPLICATION->AddHeadString("<meta property='og:description' content='".$arResult["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"]."' />", true);

$ogScheme = CMain::IsHTTPS() ? "https" : "http";
$ogPhoto = $arResult["DETAIL_PICTURE_EPILOG"];
$APPLICATION->AddHeadString("<meta property='og:url' content='".$ogScheme."://".SITE_SERVER_NAME.$APPLICATION->GetCurPage()."' />", true);
if(!empty($ogPhoto)) {
	$APPLICATION->AddHeadString("<meta property='og:image' content='".$ogScheme."://".SITE_SERVER_NAME.$ogPhoto["SRC"]."' />", true);
	$APPLICATION->AddHeadString("<meta property='og:image:width' content='".$ogPhoto["WIDTH"]."' />", true);
	$APPLICATION->AddHeadString("<meta property='og:image:height' content='".$ogPhoto["HEIGHT"]."' />", true);
	$APPLICATION->AddHeadString("<link rel='image_src' href='".$ogScheme."://".SITE_SERVER_NAME.$ogPhoto["SRC"]."' />", true);
}
unset($ogPhoto, $ogScheme);