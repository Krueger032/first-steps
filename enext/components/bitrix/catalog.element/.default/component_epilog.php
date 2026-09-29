<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$GLOBALS['IS_DETAIL_PAGE'] = true;

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
	
	$arFilter = array(
		"IBLOCK_ID" => $arResult["IBLOCK_ID"],
		"ACTIVE" => "Y",
		"GLOBAL_ACTIVE" => "Y",
		"ID" => is_array($arResult["SECTION"]["PATH"]) ? array_keys($arResult["SECTION"]["PATH"]) : []
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
		$APPLICATION->AddChainItem($arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"], $arResult["DETAIL_PAGE_URL"]);
	else
		$APPLICATION->AddChainItem($arResult["NAME"], $arResult["~DETAIL_PAGE_URL"]);
}
unset($addElementChain);

//OPEN_GRAPH//
$APPLICATION->AddHeadString("<meta property='og:type' content='product' />", true);

$ogTitle = !empty($arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]) ? $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] : $arResult["NAME"];
$APPLICATION->AddHeadString("<meta property='og:type' content='website'/>");$APPLICATION->AddHeadString("<meta property='og:title' content='".$ogTitle."' />", true);
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

$APPLICATION->AddHeadString('<link href="https://'.SITE_SERVER_NAME.$arResult['DETAIL_PAGE_URL'].'" rel="canonical" />',true);

$currentUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
if ($currentUrl !== $arResult['DETAIL_PAGE_URL']) {
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: {$arResult['DETAIL_PAGE_URL']}");
	exit();
}

// PRODUCT_SCHEMA_JSON_LD
$spSchemaItem = $arResult;
if (!empty($arResult["OFFERS"]) && is_array($arResult["OFFERS"])) {
    $spSchemaOfferIndex = isset($arResult["OFFERS_SELECTED"]) ? (int)$arResult["OFFERS_SELECTED"] : 0;
    $spSchemaItem = isset($arResult["OFFERS"][$spSchemaOfferIndex])
        ? $arResult["OFFERS"][$spSchemaOfferIndex]
        : reset($arResult["OFFERS"]);
}

$spSchemaDbProperties = array();
if (Bitrix\Main\Loader::includeModule("iblock") && !empty($arResult["IBLOCK_ID"]) && !empty($arResult["ID"])) {
    $spSchemaPropertyResult = CIBlockElement::GetProperty(
        (int)$arResult["IBLOCK_ID"],
        (int)$arResult["ID"],
        array("sort" => "asc", "id" => "asc"),
        array()
    );
    while ($spSchemaProperty = $spSchemaPropertyResult->Fetch()) {
        if ($spSchemaProperty["CODE"] !== "" && $spSchemaProperty["VALUE"] !== "" && $spSchemaProperty["VALUE"] !== null) {
            $spSchemaDbProperties[$spSchemaProperty["CODE"]][] = $spSchemaProperty;
        }
    }
}

$spSchemaAbsoluteUrl = static function ($url) {
    $url = trim((string)$url);
    if ($url === "") return "";
    if (preg_match("~^https://~i", $url)) return $url;
    if (strpos($url, "//") === 0) return "https:" . $url;
    return "https://santehpodbor.ru/" . ltrim($url, "/");
};

$spSchemaUrl = "https://santehpodbor.ru" . $APPLICATION->GetCurPage(false);
$spSchemaDescriptionSource = !empty($arResult["DETAIL_TEXT"])
    ? $arResult["DETAIL_TEXT"]
    : (!empty($arResult["PREVIEW_TEXT"]) ? $arResult["PREVIEW_TEXT"] : $arResult["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"]);
$spSchemaDescription = html_entity_decode(strip_tags((string)$spSchemaDescriptionSource), ENT_QUOTES | ENT_HTML5, "UTF-8");
$spSchemaDescription = trim(preg_replace("/\\s+/u", " ", $spSchemaDescription));
if ($spSchemaDescription === "") {
    $spSchemaDescription = $arResult["NAME"] . " в интернет-магазине Santehpodbor";
}

$spSchemaImages = array();
$spSchemaImageSources = array(
    isset($spSchemaItem["DETAIL_PICTURE"]["SRC"]) ? $spSchemaItem["DETAIL_PICTURE"]["SRC"] : "",
    isset($spSchemaItem["PREVIEW_PICTURE"]["SRC"]) ? $spSchemaItem["PREVIEW_PICTURE"]["SRC"] : "",
    isset($arResult["DETAIL_PICTURE_EPILOG"]["SRC"]) ? $arResult["DETAIL_PICTURE_EPILOG"]["SRC"] : "",
    isset($arResult["DETAIL_PICTURE"]["SRC"]) ? $arResult["DETAIL_PICTURE"]["SRC"] : "",
    isset($arResult["PREVIEW_PICTURE"]["SRC"]) ? $arResult["PREVIEW_PICTURE"]["SRC"] : ""
);
foreach (array($spSchemaItem, $arResult) as $spSchemaPhotoOwner) {
    if (!empty($spSchemaPhotoOwner["MORE_PHOTO"]) && is_array($spSchemaPhotoOwner["MORE_PHOTO"])) {
        foreach ($spSchemaPhotoOwner["MORE_PHOTO"] as $spSchemaPhoto) {
            if (!empty($spSchemaPhoto["SRC"])) $spSchemaImageSources[] = $spSchemaPhoto["SRC"];
        }
    }
}
foreach ($spSchemaImageSources as $spSchemaImageSource) {
    $spSchemaImageUrl = $spSchemaAbsoluteUrl($spSchemaImageSource);
    if ($spSchemaImageUrl !== "") $spSchemaImages[$spSchemaImageUrl] = $spSchemaImageUrl;
}
$spSchemaImages = array_values($spSchemaImages);

$spSchemaBrand = "";
if (!empty($arResult["PROPERTIES"]["BRAND"]["FULL_VALUE"]["NAME"])) {
    $spSchemaBrand = trim((string)$arResult["PROPERTIES"]["BRAND"]["FULL_VALUE"]["NAME"]);
} elseif (!empty($arResult["PROPERTIES"]["BRAND"]["DISPLAY_VALUE"]) && !is_array($arResult["PROPERTIES"]["BRAND"]["DISPLAY_VALUE"])) {
    $spSchemaBrand = trim(strip_tags((string)$arResult["PROPERTIES"]["BRAND"]["DISPLAY_VALUE"]));
} elseif (!empty($arResult["PROPERTIES"]["BRAND"]["VALUE"]) && !is_array($arResult["PROPERTIES"]["BRAND"]["VALUE"])) {
    $spSchemaBrand = trim((string)$arResult["PROPERTIES"]["BRAND"]["VALUE"]);
}

if ($spSchemaBrand === "" && !empty($spSchemaDbProperties["BRAND"][0]["VALUE"])) {
    $spSchemaBrandProperty = $spSchemaDbProperties["BRAND"][0];
    if ($spSchemaBrandProperty["PROPERTY_TYPE"] === "E" && (int)$spSchemaBrandProperty["VALUE"] > 0) {
        $spSchemaBrandElement = CIBlockElement::GetByID((int)$spSchemaBrandProperty["VALUE"])->Fetch();
        if (!empty($spSchemaBrandElement["NAME"])) $spSchemaBrand = trim((string)$spSchemaBrandElement["NAME"]);
    } else {
        $spSchemaBrand = trim((string)$spSchemaBrandProperty["VALUE"]);
    }
}

$spSchemaSku = "";
if (!empty($spSchemaItem["PROPERTIES"]["ARTNUMBER"]["VALUE"])) {
    $spSchemaSku = trim((string)$spSchemaItem["PROPERTIES"]["ARTNUMBER"]["VALUE"]);
} elseif (!empty($arResult["PROPERTIES"]["ARTNUMBER"]["VALUE"])) {
    $spSchemaSku = trim((string)$arResult["PROPERTIES"]["ARTNUMBER"]["VALUE"]);
} elseif (!empty($spSchemaDbProperties["ARTNUMBER"][0]["VALUE"])) {
    $spSchemaSku = trim((string)$spSchemaDbProperties["ARTNUMBER"][0]["VALUE"]);
}
if ($spSchemaSku === "") $spSchemaSku = (string)$arResult["ID"];

$spSchemaPrice = array();
if (!empty($spSchemaItem["ITEM_PRICES"]) && is_array($spSchemaItem["ITEM_PRICES"])) {
    $spSchemaPriceIndex = isset($spSchemaItem["ITEM_PRICE_SELECTED"]) ? (int)$spSchemaItem["ITEM_PRICE_SELECTED"] : 0;
    $spSchemaPrice = isset($spSchemaItem["ITEM_PRICES"][$spSchemaPriceIndex])
        ? $spSchemaItem["ITEM_PRICES"][$spSchemaPriceIndex]
        : reset($spSchemaItem["ITEM_PRICES"]);
}
$spSchemaPriceValue = isset($spSchemaPrice["RATIO_PRICE"])
    ? (float)$spSchemaPrice["RATIO_PRICE"]
    : (isset($spSchemaPrice["PRICE"]) ? (float)$spSchemaPrice["PRICE"] : 0);

$spSchemaCatalogProduct = array();
$spSchemaOptimalPrice = array();
$spSchemaProductId = !empty($spSchemaItem["ID"]) ? (int)$spSchemaItem["ID"] : (int)$arResult["ID"];
if (Bitrix\Main\Loader::includeModule("catalog") && $spSchemaProductId > 0) {
    $spSchemaCatalogProduct = CCatalogProduct::GetByID($spSchemaProductId);
    global $USER;
    $spSchemaUserGroups = is_object($USER) ? $USER->GetUserGroupArray() : array(2);
    $spSchemaOptimalPrice = CCatalogProduct::GetOptimalPrice($spSchemaProductId, 1, $spSchemaUserGroups, "N");
    if ($spSchemaPriceValue <= 0 && !empty($spSchemaOptimalPrice)) {
        if (isset($spSchemaOptimalPrice["DISCOUNT_PRICE"])) {
            $spSchemaPriceValue = (float)$spSchemaOptimalPrice["DISCOUNT_PRICE"];
        } elseif (isset($spSchemaOptimalPrice["RESULT_PRICE"]["DISCOUNT_PRICE"])) {
            $spSchemaPriceValue = (float)$spSchemaOptimalPrice["RESULT_PRICE"]["DISCOUNT_PRICE"];
        } elseif (isset($spSchemaOptimalPrice["PRICE"]["PRICE"])) {
            $spSchemaPriceValue = (float)$spSchemaOptimalPrice["PRICE"]["PRICE"];
        }
        if (empty($spSchemaPrice["CURRENCY"])) {
            if (!empty($spSchemaOptimalPrice["PRICE"]["CURRENCY"])) {
                $spSchemaPrice["CURRENCY"] = $spSchemaOptimalPrice["PRICE"]["CURRENCY"];
            } elseif (!empty($spSchemaOptimalPrice["RESULT_PRICE"]["CURRENCY"])) {
                $spSchemaPrice["CURRENCY"] = $spSchemaOptimalPrice["RESULT_PRICE"]["CURRENCY"];
            }
        }
    }
}

$spSchemaCanBuy = !empty($spSchemaItem["CAN_BUY"]) && ($spSchemaItem["CAN_BUY"] === true || $spSchemaItem["CAN_BUY"] === "Y");
if (!$spSchemaCanBuy && isset($spSchemaItem["CATALOG_QUANTITY_TRACE"]) && $spSchemaItem["CATALOG_QUANTITY_TRACE"] === "N") $spSchemaCanBuy = true;
if (!$spSchemaCanBuy && isset($spSchemaItem["CATALOG_CAN_BUY_ZERO"]) && $spSchemaItem["CATALOG_CAN_BUY_ZERO"] === "Y") $spSchemaCanBuy = true;
if (!$spSchemaCanBuy && isset($spSchemaItem["CATALOG_QUANTITY"]) && (float)$spSchemaItem["CATALOG_QUANTITY"] > 0) $spSchemaCanBuy = true;
if (!empty($spSchemaCatalogProduct)) {
    $spSchemaCanBuy =
        (isset($spSchemaCatalogProduct["QUANTITY_TRACE"]) && $spSchemaCatalogProduct["QUANTITY_TRACE"] === "N")
        || (isset($spSchemaCatalogProduct["CAN_BUY_ZERO"]) && $spSchemaCatalogProduct["CAN_BUY_ZERO"] === "Y")
        || (isset($spSchemaCatalogProduct["QUANTITY"]) && (float)$spSchemaCatalogProduct["QUANTITY"] > 0);
}

if ($spSchemaPriceValue <= 0) $spSchemaCanBuy = false;

$spSchemaOffer = array(
    "@type" => "Offer",
    "url" => $spSchemaUrl,
    "availability" => $spSchemaCanBuy ? "https://schema.org/InStock" : "https://schema.org/OutOfStock",
    "itemCondition" => "https://schema.org/NewCondition",
    "seller" => array("@id" => "https://santehpodbor.ru/#store")
);
if ($spSchemaPriceValue > 0) {
    $spSchemaOffer["price"] = number_format($spSchemaPriceValue, 2, ".", "");
    $spSchemaOffer["priceCurrency"] = !empty($spSchemaPrice["CURRENCY"]) ? $spSchemaPrice["CURRENCY"] : "RUB";
}

$spProductSchema = array(
    "@context" => "https://schema.org",
    "@type" => "Product",
    "@id" => $spSchemaUrl . "#product",
    "url" => $spSchemaUrl,
    "name" => trim((string)$arResult["NAME"]),
    "description" => $spSchemaDescription,
    "sku" => $spSchemaSku,
    "mpn" => $spSchemaSku,
    "offers" => $spSchemaOffer
);
if (!empty($spSchemaImages)) $spProductSchema["image"] = $spSchemaImages;
if ($spSchemaBrand !== "") $spProductSchema["brand"] = array("@type" => "Brand", "name" => $spSchemaBrand);
if (!empty($arResult["REVIEWS_COUNT"]) && (float)$arResult["RATING_VALUE"] > 0) {
    $spProductSchema["aggregateRating"] = array(
        "@type" => "AggregateRating",
        "ratingValue" => (float)$arResult["RATING_VALUE"],
        "reviewCount" => (int)$arResult["REVIEWS_COUNT"]
    );
}

$spProductSchemaJson = json_encode(
    $spProductSchema,
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE
);
if ($spProductSchemaJson !== false) {
    $APPLICATION->AddHeadString('<script type="application/ld+json" data-schema="product">' . $spProductSchemaJson . '</script>', true);
}
unset(
    $spSchemaItem, $spSchemaOfferIndex, $spSchemaDbProperties, $spSchemaPropertyResult,
    $spSchemaProperty, $spSchemaBrandProperty, $spSchemaBrandElement, $spSchemaAbsoluteUrl, $spSchemaUrl,
    $spSchemaDescriptionSource, $spSchemaDescription, $spSchemaImages,
    $spSchemaImageSources, $spSchemaPhotoOwner, $spSchemaPhoto, $spSchemaImageSource,
    $spSchemaImageUrl, $spSchemaBrand, $spSchemaSku, $spSchemaPrice,
    $spSchemaPriceIndex, $spSchemaPriceValue, $spSchemaCatalogProduct, $spSchemaOptimalPrice,
    $spSchemaProductId, $spSchemaUserGroups, $spSchemaCanBuy, $spSchemaOffer,
    $spProductSchema, $spProductSchemaJson
);
