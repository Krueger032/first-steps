<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\ModuleManager,
	Bitrix\Iblock;

$this->setFrameMode(true);

global $arSettings;

$APPLICATION->SetPageProperty("wideScreenMode", "-ws");

$arParams["USE_FILTER"] = (isset($arParams["USE_FILTER"]) && $arParams["USE_FILTER"] == "Y" ? "Y" : "N");
$isFilter = ($arParams["USE_FILTER"] == "Y");
$isSort = true;

global $seoMeta;

//CUR_SECTION//
$arFilter = array(
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],
	"ACTIVE" => "Y",
	"GLOBAL_ACTIVE" => "Y",
	"ELEMENT_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
	"CNT_ACTIVE" => "Y"
);
if(0 < intval($arResult["VARIABLES"]["SECTION_ID"]))
	$arFilter["ID"] = $arResult["VARIABLES"]["SECTION_ID"];
elseif("" != $arResult["VARIABLES"]["SECTION_CODE"])
	$arFilter["=CODE"] = $arResult["VARIABLES"]["SECTION_CODE"];

$isCacheManager = defined("BX_COMP_MANAGED_CACHE") && is_object($GLOBALS["CACHE_MANAGER"]);

$obCache = new CPHPCache();
if($obCache->InitCache($arParams["CACHE_TIME"], serialize($arFilter), "/iblock/catalog")) {
	$arCurSection = $obCache->GetVars();
} elseif(Loader::includeModule("iblock") && $obCache->StartDataCache()) {
	$arCurSection = array();	
	$dbRes = CIBlockSection::GetList(array(), $arFilter, true, array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME", "PICTURE", "DESCRIPTION", "DEPTH_LEVEL", "UF_PRODUCTS_VIEW", "UF_BANNER", "UF_BANNER_URL", "UF_PREVIEW", "UF_FAQ"));
	
	if($isCacheManager)
		$GLOBALS["CACHE_MANAGER"]->StartTagCache("/iblock/catalog");
	
	if($arCurSection = $dbRes->Fetch()) {
		if($isCacheManager)
			$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);

		//SECTION_FILTER_SORT//
		$arCurSection["IS_FILTER"] = $isFilter;
		$arCurSection["IS_SORT"] = $isSort;
		if($arCurSection["ELEMENT_CNT"] <= 0) {
			$arCurSection["IS_FILTER"] = false;
			$arCurSection["IS_SORT"] = false;
		}
		
		//SECTION_PICTURE//
		if($arCurSection["PICTURE"] > 0)
			$arCurSection["PICTURE"] = CFile::GetFileArray($arCurSection["PICTURE"]);

		//SECTION_PRODUCTS_VIEW//
		if($arCurSection["UF_PRODUCTS_VIEW"] > 0) {
			$rsUserField = CUserFieldEnum::GetList(array(), array("ID" => $arCurSection["UF_PRODUCTS_VIEW"]));
			if($arUserField = $rsUserField->GetNext()) {
				$arCurSection["UF_PRODUCTS_VIEW"] = $arUserField["XML_ID"];						
			}
			unset($arUserField, $rsUserField);
		}

		//SECTION_PATH//
		$rsPath = CIBlockSection::GetNavChain($arCurSection["IBLOCK_ID"], $arCurSection["ID"], array("ID", "IBLOCK_ID", "NAME", "SECTION_PAGE_URL"));
		while($arPath = $rsPath->GetNext()) {
			$arCurSection["PATH"][$arPath["ID"]] = $arPath;
		}
		unset($arPath, $rsPath);
		
		//SECTION_PRODUCTS_VIEW//
		if(intval($arCurSection["UF_PRODUCTS_VIEW"]) <= 0 && !empty($arCurSection["PATH"])) {
			$rsSections = CIBlockSection::GetList(array("DEPTH_LEVEL" => "DESC"), array("IBLOCK_ID" => $arCurSection["IBLOCK_ID"], "ACTIVE" => "Y", "GLOBAL_ACTIVE" => "Y", "ID" => array_keys($arCurSection["PATH"])), false, array("ID", "IBLOCK_ID", "NAME", "UF_PRODUCTS_VIEW"));
			while($arSection = $rsSections->GetNext()) {
				if(intval($arCurSection["UF_PRODUCTS_VIEW"]) <= 0 && $arSection["UF_PRODUCTS_VIEW"] > 0) {
					$rsUserField = CUserFieldEnum::GetList(array(), array("ID" => $arSection["UF_PRODUCTS_VIEW"]));
					if($arUserField = $rsUserField->GetNext()) {
						$arCurSection["UF_PRODUCTS_VIEW"] = $arUserField["XML_ID"];						
					}
					unset($arUserField, $rsUserField);
					break;
				}
			}
			unset($arSection, $rsSections);
		}
		
		//SECTION_BANNER//
		if($arCurSection["UF_BANNER"] > 0)
			$arCurSection["UF_BANNER"] = CFile::GetFileArray($arCurSection["UF_BANNER"]);
		
		//SECTION_TITLE//				
		//SECTION_META_DESCRIPTION//
		$ipropValues = new Iblock\InheritedProperty\SectionValues($arCurSection["IBLOCK_ID"], $arCurSection["ID"]);
		$arCurSection["IPROPERTY_VALUES"] = $ipropValues->getValues();
		$arCurSection["TITLE"] = !empty($arCurSection["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"])
			? $arCurSection["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"]
			: $arCurSection["NAME"];
		$arCurSection["META_DESCRIPTION"] = !empty($arCurSection["IPROPERTY_VALUES"]["SECTION_META_DESCRIPTION"])
			? $arCurSection["IPROPERTY_VALUES"]["SECTION_META_DESCRIPTION"]
			: "";
		unset($ipropValues);

		//SECTION_BREADCRUMBS//
		if($arParams["ADD_SECTIONS_CHAIN"] == "Y" && !empty($arCurSection["PATH"])) {
			foreach($arCurSection["PATH"] as &$arPath) {
				$ipropValues = new Iblock\InheritedProperty\SectionValues($arCurSection["IBLOCK_ID"], $arPath["ID"]);
				$arPath["IPROPERTY_VALUES"] = $ipropValues->getValues();
			}
			unset($arPath);
			
			$rsSections = CIBlockSection::GetList(array(), array("IBLOCK_ID" => $arCurSection["IBLOCK_ID"], "ID" => array_keys($arCurSection["PATH"])), false, array("ID", "IBLOCK_ID", "UF_BREADCRUMB_TITLE"));
			while($arSection = $rsSections->GetNext()) {
				$arCurSection["PATH"][$arSection["ID"]]["BREADCRUMB_TITLE"] = $arSection["UF_BREADCRUMB_TITLE"];
			}
			unset($arSection, $rsSections);
		}
	}
	
	if($isCacheManager)
		$GLOBALS["CACHE_MANAGER"]->EndTagCache();
	
	$obCache->EndDataCache($arCurSection);
} else {
	$arCurSection = array();
}

//SECTION_FILTER//
if(!$arCurSection["IS_FILTER"])
	$isFilter = false;

//SECTION_SORT//
if(!$arCurSection["IS_SORT"])
	$isSort = false;

//SECTION_BANNER//
if(is_array($arCurSection["UF_BANNER"])) {
	ob_start();?>
	<div class="catalog-section-pic">
		<a href="<?=!empty($arCurSection['UF_BANNER_URL']) ? $arCurSection['UF_BANNER_URL'] : 'javascript:void(0)'?>">
			<img src="<?=$arCurSection['UF_BANNER']['SRC']?>" width="<?=$arCurSection['UF_BANNER']['WIDTH']?>" height="<?=$arCurSection['UF_BANNER']['HEIGHT']?>" alt="" title="" />
		</a>
	</div>	
	<?$APPLICATION->AddViewContent("UF_BANNER", ob_get_contents());
	ob_end_clean();
}

include($_SERVER["DOCUMENT_ROOT"]."/".$this->GetFolder()."/section_vertical.php");

//SECTION_BREADCRUMBS//
if(!empty($arCurSection["PATH"])) {
	foreach($arCurSection["PATH"] as $path) {
		if(!empty($path["BREADCRUMB_TITLE"]))
			$APPLICATION->AddChainItem($path["BREADCRUMB_TITLE"], $path["~SECTION_PAGE_URL"]);
		elseif(!empty($path["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"]))
			$APPLICATION->AddChainItem($path["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"], $path["~SECTION_PAGE_URL"]);
		else
			$APPLICATION->AddChainItem($path["NAME"], $path["~SECTION_PAGE_URL"]);
	}
}

//SECTION_TITLE//
//SECTION_META_TITLE//
//SECTION_META_KEYWORDS//
//SECTION_META_DESCRIPTION//
if(!empty($seoMeta["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]))
	$APPLICATION->SetTitle($seoMeta["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]);
	
if(!empty($seoMeta["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"]))
	$APPLICATION->SetPageProperty("title", $seoMeta["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"]);

if(!empty($seoMeta["IPROPERTY_VALUES"]["ELEMENT_META_KEYWORDS"]))
	$APPLICATION->SetPageProperty("keywords", $seoMeta["IPROPERTY_VALUES"]["ELEMENT_META_KEYWORDS"]);
	
if(!empty($seoMeta["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"]))
	$APPLICATION->SetPageProperty("description", $seoMeta["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"]);

if(!empty($_REQUEST["PAGEN_1"]) && $_REQUEST["PAGEN_1"] > 1) {
	if(!empty($seoMeta["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"])) {
		$APPLICATION->SetPageProperty("title", $seoMeta["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"]." | ".Loc::getMessage("CATALOG_PAGE")." ".$_REQUEST["PAGEN_1"]);
	} elseif(!empty($arCurSection["IPROPERTY_VALUES"]["SECTION_META_TITLE"])) {
		$APPLICATION->SetPageProperty("title", $arCurSection["IPROPERTY_VALUES"]["SECTION_META_TITLE"]." | ".Loc::getMessage("CATALOG_PAGE")." ".$_REQUEST["PAGEN_1"]);
	} else {
		$APPLICATION->SetPageProperty("title", $arCurSection["TITLE"]." | ".Loc::getMessage("CATALOG_PAGE")." ".$_REQUEST["PAGEN_1"]);
	}	
	$APPLICATION->SetPageProperty("keywords", "");
	if(!empty($seoMeta["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"])) {
		$APPLICATION->SetPageProperty("description", $seoMeta["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"]." | ".Loc::getMessage("CATALOG_PAGE")." ".$_REQUEST["PAGEN_1"]);
	} elseif(!empty($arCurSection["META_DESCRIPTION"])) {
		$APPLICATION->SetPageProperty("description", $arCurSection["META_DESCRIPTION"]." | ".Loc::getMessage("CATALOG_PAGE")." ".$_REQUEST["PAGEN_1"]);
	} else {
		$APPLICATION->SetPageProperty("description", "");
	}
}

//SECTION_OPEN_GRAPH//
$APPLICATION->AddHeadString("<meta property='og:title' content='".(!empty($seoMeta["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]) ? $seoMeta["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] : $arCurSection["TITLE"])."' />", true);
if(!empty($seoMeta["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"]) || !empty($arCurSection["META_DESCRIPTION"]))
	$APPLICATION->AddHeadString("<meta property='og:description' content='".(!empty($seoMeta["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"]) ? $seoMeta["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"] : $arCurSection["META_DESCRIPTION"])."' />", true);

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