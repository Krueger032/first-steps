<?if(!defined("ENEXT_MODULE_ID")) define("ENEXT_MODULE_ID", "altop.enext");
 
IncludeModuleLangFile(__FILE__);

//initialize module parametrs list and default values
include_once __DIR__."/../../parametrs.php";

class CEnext{
	const MODULE_ID = ENEXT_MODULE_ID;
	const PARTNER_NAME = "altop"; 
	const SOLUTION_NAME	= "enext";
	
	static $arParametrsList = array();
	
	public static function checkModuleRight($reqRight = "R", $bShowError = false) {
		global $APPLICATION;
		
		if($APPLICATION->GetGroupRight(self::MODULE_ID) < $reqRight) {
			if($bShowError){
				$APPLICATION->AuthForm(GetMessage("ENEXT_ACCESS_DENIED"));
			}
			return false;
		}
		
		return true;
	}
	
	public static function GetBackParametrsValues($SITE_ID, $bStatic = true){
		if($bStatic){
			static $arValues;
		}
		if($bStatic && $arValues === NULL || !$bStatic){
			$arDefaultValues = $arValues = array();
			if(self::$arParametrsList && is_array(self::$arParametrsList)){
				foreach(self::$arParametrsList as $blockCode => $arBlock){
					if($arBlock["OPTIONS"] && is_array($arBlock["OPTIONS"])){
						foreach($arBlock["OPTIONS"] as $optionCode => $arOption){
							$arDefaultValues[$optionCode] = $arOption["DEFAULT"];
						}
					}
				}
			}
			$arValues = unserialize(COption::GetOptionString(self::MODULE_ID, "OPTIONS", serialize(array()), $SITE_ID));		
			if($arValues && is_array($arValues)){
				foreach($arValues as $optionCode => $arOption){
					if(!isset($arDefaultValues[$optionCode])){
						unset($arValues[$optionCode]);
					}
				}
			}
			if($arDefaultValues && is_array($arDefaultValues)){
				foreach($arDefaultValues as $optionCode => $arOption){
					if(!isset($arValues[$optionCode])){
						$arValues[$optionCode] = $arOption;
					}
				}
			}
		}
		return $arValues;
	}

	public static function GetFrontParametrsValues($SITE_ID){
		if(!strlen($SITE_ID))
			$SITE_ID = SITE_ID;
		$arBackParametrs = self::GetBackParametrsValues($SITE_ID);		
		$arValues = (array)$arBackParametrs;
		return $arValues;
	}

	public static function CheckColor($strColor, $code) {
		if(strlen($strColor) > 0) {
			$strColor = str_replace("#", "", $strColor);
			if(strlen($strColor) < 6) {
				if(strlen($strColor) <> 3) {
					for($i = 0, $l = 6 - strlen($strColor); $i < $l; ++$i) {
						$strColor = $strColor."0";
					}					
				}
			} elseif(strlen($strColor) > 6) {
				$strColor = substr($strColor, 0, -(strlen($strColor) - 6));							
			}
			$strColor = "#".$strColor;
		} else {
			if($code == "COLOR_SCHEME_CUSTOM")
				$strColor = self::$arParametrsList["MAIN"]["OPTIONS"]["COLOR_SCHEME"]["LIST"][self::$arParametrsList["MAIN"]["OPTIONS"]["COLOR_SCHEME"]["DEFAULT"]]["COLOR"];
		}		
		return $strColor;
	}

	public static function UpdateParametrsValues($SITE_ID) {
		if(!strlen($SITE_ID))
			$SITE_ID = SITE_ID;
		$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();		
		$theme = $request->getPost("THEME");
		if(self::$arParametrsList && is_array(self::$arParametrsList)) {
			foreach(self::$arParametrsList as $blockCode => $arBlock) {
				if($arBlock["OPTIONS"] && is_array($arBlock["OPTIONS"])) {
					foreach($arBlock["OPTIONS"] as $optionCode => $arOption) {
						if($arOption["IN_SETTINGS_PANEL"] == "Y" && $theme == "default") {
							$newVal = $arOption["DEFAULT"];
						} else {
							$post = $request->getPost($optionCode);
							if($arOption["IN_SETTINGS_PANEL"] != "Y")
								$post = unserialize(base64_decode(strtr($post, "-_,", "+/=")));							
							$newVal = $post;
							if($arOption["TYPE"] == "multiselectbox") {
								if(!is_array($newVal))
									$newVal = array();
							}
						}
						$arTab["OPTIONS"][$optionCode] = $newVal;
					}
				}
			}
		}
		Bitrix\Main\Config\Option::set(self::MODULE_ID, "OPTIONS", serialize((array)$arTab["OPTIONS"]), $SITE_ID);
		
		if(CHTMLPagesCache::isOn()) {
			$staticHtmlCache = Bitrix\Main\Data\StaticHtmlCache::getInstance();
			$staticHtmlCache->deleteAll();
		}
		
		BXClearCache(true, "/".SITE_ID."/bitrix/catalog.element/");
		BXClearCache(true, "/".SITE_ID."/bitrix/catalog.section/");
		BXClearCache(true, "/".SITE_ID."/bitrix/menu/");
		BXClearCache(true, "/".SITE_ID."/bitrix/news.detail/");
		BXClearCache(true, "/".SITE_ID."/bitrix/news.list/");
		BXClearCache(true, "/".SITE_ID."/bitrix/sale.products.gift/");
		BXClearCache(true, "/".SITE_ID."/bitrix/sale.products.gift.basket/");
		BXClearCache(true, "/".SITE_ID."/bitrix/sale.products.gift.section/");
		BXClearCache(true, "/js/".SITE_ID."/enext/");
	}	

	public static function GenerateColorScheme($SITE_ID) {
		if(!strlen($SITE_ID))
			$SITE_ID = SITE_ID;
		$arBackParametrs = self::GetBackParametrsValues($SITE_ID);		
		$colorScheme = $arBackParametrs["COLOR_SCHEME"];
		$arColorSchemes = self::$arParametrsList["MAIN"]["OPTIONS"]["COLOR_SCHEME"]["LIST"];		
		if(!class_exists("lessc"))
			include_once "lessc.inc.php";		
		$less = new lessc;
		try {
			if($colorScheme == "CUSTOM")
				$less->setVariables(array("bcolor" => $arBackParametrs["COLOR_SCHEME_CUSTOM"]));
			elseif($arColorSchemes && is_array($arColorSchemes))
				$less->setVariables(array("bcolor" => $arColorSchemes[$colorScheme]["COLOR"]));
			$less->setFormatter("compressed");
			if(defined("SITE_TEMPLATE_PATH")) {
				$schemeDirPath = $_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH."/schemes/".$colorScheme.($colorScheme == "CUSTOM" ? "_".$SITE_ID : "")."/";
				if(!is_dir($schemeDirPath))
					mkdir($schemeDirPath, 0755, true);
				$inputFile = __DIR__."/../../css/colors.less";
				$outputFile = $schemeDirPath."colors.min.css";
				
				$cache = $less->cachedCompile($inputFile);
				if(md5(file_get_contents($outputFile)) != md5($cache["compiled"]))
					file_put_contents($outputFile, $cache['compiled']);				
			}
		} catch(exception $e) {
			echo "Fatal error: ".$e->getMessage();
			die();
		}
	}
	
	public static function LoadCountdown() {
		$arBackParametrs = self::GetBackParametrsValues(SITE_ID);
		$openingDate = $arBackParametrs["DATE_OPENING_SITE"];
		$showCountdown = $openingDate && time() + CTimeZone::GetOffset() < MakeTimeStamp($openingDate) ? true : false;		
		if($showCountdown) {
			$arOpeningDate = ParseDateTime($openingDate, FORMAT_DATETIME);
			$GLOBALS["APPLICATION"]->AddHeadScript(SITE_TEMPLATE_PATH."/js/countdown/jquery.plugin.min.js");
			$GLOBALS["APPLICATION"]->AddHeadScript(SITE_TEMPLATE_PATH."/js/countdown/jquery.countdown.min.js");
			$GLOBALS["APPLICATION"]->AddHeadString("
				<script type='text/javascript'>
					$(function() {
						$.countdown.regionalOptions['ru'] = {
							labels: ['".GetMessage("COUNTDOWN_REGIONAL_LABELS_YEAR")."', '".GetMessage("COUNTDOWN_REGIONAL_LABELS_MONTH")."', '".GetMessage("COUNTDOWN_REGIONAL_LABELS_WEEK")."', '".GetMessage("COUNTDOWN_REGIONAL_LABELS_DAY")."', '".GetMessage("COUNTDOWN_REGIONAL_LABELS_HOUR")."', '".GetMessage("COUNTDOWN_REGIONAL_LABELS_MIN")."', '".GetMessage("COUNTDOWN_REGIONAL_LABELS_SEC")."'],
							labels1: ['".GetMessage("COUNTDOWN_REGIONAL_LABELS1_YEAR")."', '".GetMessage("COUNTDOWN_REGIONAL_LABELS1_MONTH")."', '".GetMessage("COUNTDOWN_REGIONAL_LABELS1_WEEK")."', '".GetMessage("COUNTDOWN_REGIONAL_LABELS1_DAY")."', '".GetMessage("COUNTDOWN_REGIONAL_LABELS1_HOUR")."', '".GetMessage("COUNTDOWN_REGIONAL_LABELS1_MIN")."', '".GetMessage("COUNTDOWN_REGIONAL_LABELS1_SEC")."'],
							labels2: ['".GetMessage("COUNTDOWN_REGIONAL_LABELS2_YEAR")."', '".GetMessage("COUNTDOWN_REGIONAL_LABELS2_MONTH")."', '".GetMessage("COUNTDOWN_REGIONAL_LABELS2_WEEK")."', '".GetMessage("COUNTDOWN_REGIONAL_LABELS2_DAY")."', '".GetMessage("COUNTDOWN_REGIONAL_LABELS2_HOUR")."', '".GetMessage("COUNTDOWN_REGIONAL_LABELS2_MIN")."', '".GetMessage("COUNTDOWN_REGIONAL_LABELS2_SEC")."'],
							compactLabels: ['".GetMessage("COUNTDOWN_REGIONAL_COMPACT_LABELS_YEAR")."', '".GetMessage("COUNTDOWN_REGIONAL_COMPACT_LABELS_MONTH")."', '".GetMessage("COUNTDOWN_REGIONAL_COMPACT_LABELS_WEEK")."', '".GetMessage("COUNTDOWN_REGIONAL_COMPACT_LABELS_DAY")."'],
							compactLabels1: ['".GetMessage("COUNTDOWN_REGIONAL_COMPACT_LABELS1_YEAR")."', '".GetMessage("COUNTDOWN_REGIONAL_COMPACT_LABELS_MONTH")."', '".GetMessage("COUNTDOWN_REGIONAL_COMPACT_LABELS_WEEK")."', '".GetMessage("COUNTDOWN_REGIONAL_COMPACT_LABELS_DAY")."'],
							whichLabels: function(amount) {
								var units = amount % 10;
								var tens = Math.floor((amount % 100) / 10);
								return (amount == 1 ? 1 : (units >= 2 && units <= 4 && tens != 1 ? 2 : (units == 1 && tens != 1 ? 1 : 0)));
							},
							digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
							timeSeparator: ':',
							isRTL: false
						};
						$.countdown.setDefaults($.countdown.regionalOptions['ru']);
					});
				</script>
			", true);		
			$GLOBALS["APPLICATION"]->AddHeadString("
				<script type='text/javascript'>
					$(function() {
						if($('.site-opening-timer').length) {
							$('.site-opening-timer').countdown({
								until: new Date(".$arOpeningDate["YYYY"].", ".$arOpeningDate["MM"]." - 1, ".$arOpeningDate["DD"].($arOpeningDate["HH"] ? ", ".$arOpeningDate["HH"] : "").($arOpeningDate["MI"] ? ", ".$arOpeningDate["MI"] : "").")								
							});
						}
					});
				</script>
			", true);
		}		
		return $showCountdown;
	}

	public static function CheckCaptchaCode($userCode, $sid, $bUpperCode = true) {
		global $DB;		
		
		if(strlen($userCode) <= 0 || strlen($sid) <= 0)
			return false;		
		
		if($bUpperCode)
			$userCode = strtoupper($userCode);		
		
		$res = $DB->Query("SELECT CODE FROM b_captcha WHERE ID = '".$DB->ForSQL($sid,32)."' ");
		if(!$ar = $res->Fetch())
			return false;		
		
		if($ar["CODE"] != $userCode)
			return false;		
		
		return true;
	}

	public static function DeleteCaptcha($sid) {
		global $DB;
		
		if(!$DB->Query("DELETE FROM b_captcha WHERE ID='".$DB->ForSQL($sid, 32)."' "))
			return false;

		return true;
	}
	
	public static function showPanel() {
		global $APPLICATION, $USER;
		if($USER->IsAdmin() && COption::GetOptionString("main", "wizard_solution", "", SITE_ID) == self::SOLUTION_NAME) {
			$APPLICATION->SetAdditionalCSS("/bitrix/wizards/".self::PARTNER_NAME."/".self::SOLUTION_NAME."/css/panel.css"); 
			
			$arMenu = array(
				array(
					"ACTION" => "jsUtils.Redirect([], \"".CUtil::JSEscape("/bitrix/admin/wizard_install.php?lang=".LANGUAGE_ID."&wizardSiteID=".SITE_ID."&wizardName=".self::PARTNER_NAME.":".self::SOLUTION_NAME."&".bitrix_sessid_get())."\")",
					"ICON" => "bx-popup-item-wizard-icon",
					"TITLE" => GetMessage("STOM_BUTTON_TITLE_W1"),
					"TEXT" => GetMessage("STOM_BUTTON_NAME_W1"),
				),
			);

			$APPLICATION->AddPanelButton(
				array(
					"HREF" => "/bitrix/admin/wizard_install.php?lang=".LANGUAGE_ID."&wizardName=".self::PARTNER_NAME.":".self::SOLUTION_NAME."&wizardSiteID=".SITE_ID."&".bitrix_sessid_get(),
					"ID" => self::SOLUTION_NAME."_wizard",
					"ICON" => "bx-panel-site-wizard-icon",
					"MAIN_SORT" => 2500,
					"TYPE" => "BIG",
					"SORT" => 10,	
					"ALT" => GetMessage("SCOM_BUTTON_DESCRIPTION"),
					"TEXT" => GetMessage("SCOM_BUTTON_NAME"),
					"MENU" => $arMenu,
				)
			);
		}
	}	
	
	public static function correctInstall(){
		if(CModule::IncludeModule("main")) {
			if(COption::GetOptionString(self::MODULE_ID, "WIZARD_DEMO_INSTALLED") == "Y") {
				require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/wizard.php");
				@set_time_limit(0);
				if(!CWizardUtil::DeleteWizard(self::PARTNER_NAME.":".self::SOLUTION_NAME)) {
					if(!DeleteDirFilesEx($_SERVER["DOCUMENT_ROOT"]."/bitrix/wizards/".self::PARTNER_NAME."/".self::SOLUTION_NAME."/")) {
						self::removeDirectory($_SERVER["DOCUMENT_ROOT"]."/bitrix/wizards/".self::PARTNER_NAME."/".self::SOLUTION_NAME."/");
					}
				}
				
				UnRegisterModuleDependences("main", "OnBeforeProlog", self::MODULE_ID, __CLASS__, "correctInstall"); 
				COption::SetOptionString(self::MODULE_ID, "WIZARD_DEMO_INSTALLED", "N");
			}
		}  
	}
	
	public static function UpdateMailEvent(&$arFields) {
		if($arFields["IBLOCK_ID"]) {			
			$arIBlock = CIBlock::GetList(array("SORT" => "ASC"), array("ID" => $arFields["IBLOCK_ID"]))->Fetch();						
			$eventName = "ALTOP_FORM_".$arIBlock["CODE"];
			$arEvent = CEventType::GetByID($eventName, LANGUAGE_ID)->Fetch();
			if($arEvent) {
				if(strpos($arEvent["DESCRIPTION"], "#".$arFields["CODE"]."# - ".$arFields["NAME"]) == false) {
					$arEvent["DESCRIPTION"] = str_replace("#".$arFields["CODE"]."#", "", $arEvent["DESCRIPTION"]);
					$arEvent["DESCRIPTION"] .= "#".$arFields["CODE"]."# - ".$arFields["NAME"]."\n";
					CEventType::Update(array("ID" => $arEvent["ID"]), $arEvent);
				}
			}
		}
	}

	public static function UpdateMeasureRatio($arFields) {
		if(!isset($arFields["IBLOCK_ID"])) {
			$rsElement = CIBlockElement::GetByID($arFields["ID"]);
			if($arElement = $rsElement->GetNext())
				$arFields["IBLOCK_ID"] = $arElement["IBLOCK_ID"];
		}

		$rsProp = CIBlockElement::GetProperty($arFields["IBLOCK_ID"], $arFields["ID"], array(), array("CODE" => "M2_COUNT"));			
		if($arProp = $rsProp->Fetch()) {
			if(!empty($arProp["VALUE"]) && Bitrix\Main\Loader::includeModule("catalog")) {
				$sqMCount = round(1 / str_replace(",", ".", $arProp["VALUE"]), 4);

				$arCatalog = CCatalogSKU::GetInfoByProductIBlock($arFields["IBLOCK_ID"]);
				if(is_array($arCatalog)) {
					$rsOffers = CIBlockElement::GetList(
						array(),
						array(
							"ACTIVE" => "Y",
							"IBLOCK_ID" => $arCatalog["IBLOCK_ID"],
							"PROPERTY_".$arCatalog["SKU_PROPERTY_ID"] => $arFields["ID"],
						),
						false,
						false,
						array("ID")
					);
					while($arOffer = $rsOffers->Fetch()) {
						$arProductID[] = $arOffer["ID"];
					}

					if(!isset($arProductID))
						$arProductID[] = $arFields["ID"];

					$ratioMeasure = Bitrix\Catalog\ProductTable::getCurrentRatioWithMeasure($arProductID);
					foreach($ratioMeasure as $productId => $arRatioMeasure) {
						if($arRatioMeasure["MEASURE"]["SYMBOL_INTL"] == "m2") {
							$rsRatio = Bitrix\Catalog\MeasureRatioTable::getList(array(
								"select" => array("ID", "RATIO"),
								"filter" => array("=PRODUCT_ID" => $productId, "=IS_DEFAULT" => "Y"),
							));
							if($arRatio = $rsRatio->Fetch()) {
								if($arRatio["RATIO"] != $sqMCount)
									CCatalogMeasureRatio::update($arRatio["ID"], array("RATIO" => $sqMCount));
							} else {
								CCatalogMeasureRatio::add(array("PRODUCT_ID" => $productId, "RATIO" => $sqMCount, "IS_DEFAULT" => "Y"));
							}
							unset($arRatio, $rsRatio);
						}
					}
					unset($productId, $arRatioMeasure);
				}
				unset($arCatalog, $sqMCount);
			}
		}
		unset($arProp, $rsProp);
	}

	public static function DoIBlockAfterSave($arg1, $arg2 = false) {
		$ELEMENT_ID = false;
		$IBLOCK_ID = false;
		$OFFERS_IBLOCK_ID = false;
		$OFFERS_PROPERTY_ID = false;
		
		if(CModule::IncludeModule('currency'))
			$strDefaultCurrency = CCurrency::GetBaseCurrency();

		//Check for catalog event
		if(is_array($arg2) && $arg2["PRODUCT_ID"] > 0) {
			//Get iblock element
			$rsPriceElement = CIBlockElement::GetList(
				array(),
				array(
					"ID" => $arg2["PRODUCT_ID"],
				),
				false,
				false,
				array("ID", "IBLOCK_ID")
			);
			if($arPriceElement = $rsPriceElement->Fetch()) {
				$arCatalog = CCatalog::GetByID($arPriceElement["IBLOCK_ID"]);
				if(is_array($arCatalog)) {
					//Check if it is offers iblock
					if($arCatalog["OFFERS"] == "Y") {
						//Find product element
						$rsElement = CIBlockElement::GetProperty(
							$arPriceElement["IBLOCK_ID"],
							$arPriceElement["ID"],
							"sort",
							"asc",
							array("ID" => $arCatalog["SKU_PROPERTY_ID"])
						);
						$arElement = $rsElement->Fetch();
						if($arElement && $arElement["VALUE"] > 0) {
							$ELEMENT_ID = $arElement["VALUE"];
							$IBLOCK_ID = $arCatalog["PRODUCT_IBLOCK_ID"];
							$OFFERS_IBLOCK_ID = $arCatalog["IBLOCK_ID"];
							$OFFERS_PROPERTY_ID = $arCatalog["SKU_PROPERTY_ID"];
						}
					//or iblock which has offers
					} elseif($arCatalog["OFFERS_IBLOCK_ID"] > 0) {
						$ELEMENT_ID = $arPriceElement["ID"];
						$IBLOCK_ID = $arPriceElement["IBLOCK_ID"];
						$OFFERS_IBLOCK_ID = $arCatalog["OFFERS_IBLOCK_ID"];
						$OFFERS_PROPERTY_ID = $arCatalog["OFFERS_PROPERTY_ID"];
					//or it's regular catalog 
					} else {               
						$ELEMENT_ID = $arPriceElement["ID"];
						$IBLOCK_ID = $arPriceElement["IBLOCK_ID"];
						$OFFERS_IBLOCK_ID = false;
						$OFFERS_PROPERTY_ID = false;
					}
				}
			}
		//Check for iblock event
		} elseif(is_array($arg1) && $arg1["ID"] > 0 && $arg1["IBLOCK_ID"] > 0) {
			//Check if iblock has offers
			$arOffers = CIBlockPriceTools::GetOffersIBlock($arg1["IBLOCK_ID"]);
			if(is_array($arOffers)) {
				$ELEMENT_ID = $arg1["ID"];
				$IBLOCK_ID = $arg1["IBLOCK_ID"];
				$OFFERS_IBLOCK_ID = $arOffers["OFFERS_IBLOCK_ID"];
				$OFFERS_PROPERTY_ID = $arOffers["OFFERS_PROPERTY_ID"];
			}
		}

		if($ELEMENT_ID) {
			//Compose elements filter
			if($OFFERS_IBLOCK_ID) {
				$rsOffers = CIBlockElement::GetList(
					array(),
					array(
						"ACTIVE" => "Y",
						"IBLOCK_ID" => $OFFERS_IBLOCK_ID,
						"PROPERTY_".$OFFERS_PROPERTY_ID => $ELEMENT_ID,
					),
					false,
					false,
					array("ID")
				);
				while($arOffer = $rsOffers->Fetch()) {
					$arProductID[] = $arOffer["ID"];
				}

				if(!is_array($arProductID)) {
					$arProductID = array($ELEMENT_ID);
				}
			} else {
				$arProductID = array($ELEMENT_ID);
			}

			$minPrice = array();
			$maxPrice = array();
			//Get prices
			$rsPrices = CPrice::GetList(
				array(),
				array(
					"PRODUCT_ID" => $arProductID,
				)
			);
			while($arPrice = $rsPrices->Fetch()) {
				if(CModule::IncludeModule('currency') && $strDefaultCurrency != $arPrice['CURRENCY']) {
					$arPrice["PRICE"] = CCurrencyRates::ConvertCurrency($arPrice["PRICE"], $arPrice["CURRENCY"], $strDefaultCurrency);
				}

				$PRICE = $arPrice["PRICE"];
				if($PRICE <= 0)
					continue;

				if(!$minPrice[$arPrice['CATALOG_GROUP_ID']] || $minPrice[$arPrice['CATALOG_GROUP_ID']] > $PRICE) {
					$minPrice[$arPrice['CATALOG_GROUP_ID']] = $PRICE;
				}

				if(!$maxPrice[$arPrice['CATALOG_GROUP_ID']] || $maxPrice[$arPrice['CATALOG_GROUP_ID']] < $PRICE) {
					$maxPrice[$arPrice['CATALOG_GROUP_ID']] = $PRICE;
				}
			}

			foreach($minPrice as $priceId => $minPriceItem) {
				$fields['MINIMUM_PRICE_'.$priceId] = $minPriceItem;
				$fields['MAXIMUM_PRICE_'.$priceId] = $maxPrice[$priceId];                
			}
			
			//Save found minimal price into property
			CIBlockElement::SetPropertyValuesEx(
				$ELEMENT_ID,
				$IBLOCK_ID,
				$fields
			);
		}
	}

	public static function ReinitPath() {
		$context = Bitrix\Main\Application::getInstance()->getContext();
		$request = $context->getRequest();
		
		if(strpos($request->getRequestUri(), "/bitrix") === false && Bitrix\Main\Config\Option::get("main", "wizard_solution", "", SITE_ID) == "enext" && SITE_TEMPLATE_ID == "enext" && (!$request->isAjaxRequest() || $request->get("bxajaxid"))) {
			$arBackParametrs = CEnext::GetBackParametrsValues(SITE_ID);
			
			if(intval($arBackParametrs["SMART_FILTER_SEO_ID"]) > 0) {
				$arUrl = CHTTP::ParseURL($request->getRequestUri());
				
				$arFilter = array("ACTIVE" => "Y", "IBLOCK_ID" => $arBackParametrs["SMART_FILTER_SEO_ID"], array("LOGIC" => "OR", array("LOGIC" => "OR", array("PROPERTY_DEFAULT_URL" => $arUrl["path"]), array("PROPERTY_DEFAULT_URL" => $arUrl["path_query"])), array("CODE" => basename($arUrl["path"]))));

				$isCacheManager = defined("BX_COMP_MANAGED_CACHE") && is_object($GLOBALS["CACHE_MANAGER"]);

				$obCache = new CPHPCache();
				if($obCache->InitCache(36000000, serialize($arFilter), "/iblock/catalog")) {
					$arResult = $obCache->GetVars();
				} elseif(Bitrix\Main\Loader::includeModule("iblock") && $obCache->StartDataCache()) {
					$arResult = array();
					$rsElement = CIBlockElement::GetList(array(), $arFilter, false, false, array("ID", "CODE", "IBLOCK_ID", "PREVIEW_TEXT", "DETAIL_TEXT", "DETAIL_PAGE_URL", "PROPERTY_DEFAULT_URL", "PROPERTY_FAQ", "PROPERTY_CANONICAL_URL"));

					if($isCacheManager)
						$GLOBALS["CACHE_MANAGER"]->StartTagCache("/iblock/catalog");

					if($arElement = $rsElement->GetNext()) {
						if($isCacheManager)
							$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$arBackParametrs["SMART_FILTER_SEO_ID"]);
						
						$arResult["DEFAULT_URL"] = $arElement["PROPERTY_DEFAULT_URL_VALUE"];
						
						if(!empty($arElement["CODE"]))
							$arResult["NEW_URL"] = $arElement["DETAIL_PAGE_URL"];

						$arResult["PREVIEW_TEXT"] = $arElement["PREVIEW_TEXT"];
						$arResult["DETAIL_TEXT"] = $arElement["DETAIL_TEXT"];

						$arResult["FAQ"] = $arElement["PROPERTY_FAQ_VALUE"];

						$ipropValues = new Bitrix\Iblock\InheritedProperty\ElementValues($arElement["IBLOCK_ID"], $arElement["ID"]);
						$arResult["IPROPERTY_VALUES"] = $ipropValues->getValues();

						$arResult["CANONICAL_URL"] = $arElement["PROPERTY_CANONICAL_URL_VALUE"];
					}

					if($isCacheManager)
						$GLOBALS["CACHE_MANAGER"]->EndTagCache();
					
					$obCache->EndDataCache($arResult);
				} else {
					$arResult = array();
				}
				unset($arFilter);

				global $seoMeta;
				$seoMeta["IPROPERTY_VALUES"] = $arResult["IPROPERTY_VALUES"];
				$seoMeta["PREVIEW"] = $arResult["PREVIEW_TEXT"];
				$seoMeta["DESCRIPTION"] = $arResult["DETAIL_TEXT"];
				$seoMeta["FAQ"] = $arResult["FAQ"];
				$seoMeta["CANONICAL_URL"] = $arResult["CANONICAL_URL"];
				
				if(!empty($arResult["DEFAULT_URL"]) && !empty($arResult["NEW_URL"]) && $arResult["DEFAULT_URL"] != $arResult["NEW_URL"]) {
					$seoMeta["SMART_FILTER_LINK"] = true;

					if($arUrl["path"] == $arResult["DEFAULT_URL"] || $arUrl["path_query"] == $arResult["DEFAULT_URL"]) {
						LocalRedirect($arResult["NEW_URL"].(!empty($arUrl["query"]) ? "?".$arUrl["query"] : ""), false, "301 Moved Permanently");
					} elseif($arUrl["path"] == $arResult["NEW_URL"]) {
						$server = $context->getServer();
						$server_array = $server->toArray();
						
						$arUrlNew = CHTTP::ParseURL($arResult["DEFAULT_URL"]);
						if(!empty($arUrlNew["query"])) {
							$getList = explode("&", $arUrlNew["query"]);
							foreach($getList as $getItem) {
								$get = explode("=", $getItem);
								$_GET[$get[0]] = $get[1];
							}
							unset($get, $getItem, $getList);
						}
						unset($arUrlNew);
						
						$_SERVER["REQUEST_URI"] = $arResult["DEFAULT_URL"];
						$server_array["REQUEST_URI"] = $_SERVER["REQUEST_URI"];
						$server->set($server_array);

						$context->initialize(new Bitrix\Main\HttpRequest($server, $_GET, array(), array(), $_COOKIE), $context->getResponse(), $server);
						$GLOBALS["APPLICATION"]->sDocPath2 = GetPagePath(false, true);
						$GLOBALS["APPLICATION"]->sDirPath = GetDirPath($GLOBALS["APPLICATION"]->sDocPath2);
						$GLOBALS["APPLICATION"]->SetCurPage($arResult["NEW_URL"]);
					}
				}
			}
		}
	}
	
	public static function GetShareDefPict() {
		$arBackParametrs = self::GetBackParametrsValues(SITE_ID);
		$shareDefPict = $arBackParametrs["SHARE_DEFAULT_PICTURE"];
		if($shareDefPict > 0)
			$GLOBALS["SHARE_DEFAULT_PICTURE"] = CFile::GetFileArray($shareDefPict);
	}

	public static function SetCanonicalUrl() {
        if (
            Bitrix\Main\Config\Option::get("main", "wizard_solution", "", SITE_ID) != self::SOLUTION_NAME ||
            SITE_TEMPLATE_ID != "enext"
        ) {
            return false;
        }

		global $APPLICATION, $seoMeta;
		$scheme = CMain::IsHTTPS() ? 'https' : 'http';
		if(!empty($seoMeta["CANONICAL_URL"])) {
			$APPLICATION->SetPageProperty("canonical", $scheme."://".SITE_SERVER_NAME.$seoMeta["CANONICAL_URL"]);
		} else {
			$arUrl = CHTTP::ParseURL($APPLICATION->GetCurPageParam());
			if(!empty($arUrl["query"]))
				$APPLICATION->SetPageProperty("canonical", $scheme."://".SITE_SERVER_NAME.$arUrl["path"]);
		}
	}
	
	public static function compressCSS($css) {
		$arResult = $css;
		
		$arResult = preg_replace("/\/\*[^*]+\*\//", "", $arResult); //comments
		$arResult = preg_replace("/\/\**\*\//", "", $arResult); //comments
		$arResult = preg_replace("/\s*(:|,|;|{|}|\t)\s*/", "$1", $arResult); //whitespaces
		$arResult = preg_replace("/(\t+|\s{2,})/", " ", $arResult); //tabs and double whitespace
		$arResult = preg_replace("/(\s|:)([\-]{0,1}0px)\s/", " 0 ", $arResult); //zeros
		
		return $arResult;
	}

	public static function compressJS($js) {
		$arResult = $js;
		
		$arResult = preg_replace('@[\n]{2,}@', "\n", $arResult);
		$arResult = preg_replace('@[\t\ ]{2,}@', ' ', $arResult);
		$arResult = preg_replace('@[\t\ ]*([{}=;]+)[\t\ ]*@', '$1', $arResult);
		$arResult = preg_replace('@[\n]+[\t\ ]+@', "\n", $arResult);
		
		return $arResult;
	}

	public static function convertImgToWebp($src) {
		$pathinfo = pathinfo($src);

		if(!in_array($pathinfo["extension"], array("jpg", "jpeg", "png")))
			return false;

		$newFile = $_SERVER["DOCUMENT_ROOT"].$pathinfo["dirname"]."/".$pathinfo["filename"].".webp";
		if(file_exists($newFile))
			return false;
		
		$sourceFile = $_SERVER["DOCUMENT_ROOT"].$src;
		$sourceFileSize = getimagesize($sourceFile);
		
		switch($sourceFileSize["mime"]) { 
			case "image/jpeg": 
				$im = imagecreatefromjpeg($sourceFile);
				break; 
			case "image/png": 
				$pngimg = imagecreatefrompng($sourceFile);
				$w = imagesx($pngimg);
				$h = imagesy($pngimg);
				$im = imagecreatetruecolor($w, $h);
				imageAlphaBlending($im, false);
				imageSaveAlpha($im, true);
				$trans = imagecolorallocatealpha($im, 0, 0, 0, 127);
				imagefilledrectangle($im, 0, 0, $w - 1, $h - 1, $trans);
				imagecopy($im, $pngimg, 0, 0, 0, 0, $w, $h);
				break; 
		}
		
		$imagewebp = imagewebp($im, $newFile, 75);

		imagedestroy($im);

		return $imagewebp ? true : false;
	}
	
	public static function ChangeContent(&$content) {
		$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
		
		if(strpos($request->getRequestUri(), "/bitrix") === false && Bitrix\Main\Config\Option::get("main", "wizard_solution", "", SITE_ID) == self::SOLUTION_NAME && SITE_TEMPLATE_ID == "enext" && !$request->isAjaxRequest()) {
			$arBackParametrs = self::GetBackParametrsValues(SITE_ID);
			
			$isPersonal = CSite::inDir(SITE_DIR."personal/");
			//$isAdmin = $GLOBALS["USER"]->IsAdmin();
			$isAdmin = (is_object($GLOBALS["USER"]) && $GLOBALS["USER"]->IsAdmin()) ? true : false; //PRO_WS//

			$isSiteClosed = COption::GetOptionString("main", "site_stopped") == "Y" && !$GLOBALS["USER"]->CanDoOperation("edit_other_settings") ? true : false;
			
			$b404 = defined("ERROR_404") && ERROR_404 == "Y" ? true : false;

			$isCompositeOn = CHTMLPagesCache::isOn();
			
			//DELETE_SPACES//
			if($arBackParametrs["DELETE_SPACES"] == "Y" && !$isAdmin && !$isPersonal)
				$content = preg_replace("/[\t\ ]{2,100}/", " ", $content);
			
			//IMG_LAZYLOAD//
			//IMG_WEBP//
			$webpSupport = strpos($_SERVER["HTTP_ACCEPT"], "image/webp") !== false || strpos($_SERVER["HTTP_USER_AGENT"], " Chrome/") !== false ? true : false;
			
			$GLOBALS["IMG_LAZYLOAD"] = $arBackParametrs["IMG_LAZYLOAD"] == "Y";
			$GLOBALS["IMG_WEBP"] = $arBackParametrs["IMG_WEBP"] == "Y" && function_exists("imagewebp") && $webpSupport && !CSite::inDir(SITE_DIR."personal/order/make/");
			
			if($GLOBALS["IMG_LAZYLOAD"] || $GLOBALS["IMG_WEBP"]) {
				if($GLOBALS["IMG_WEBP"])
					$content = preg_replace("/<body([^>]*)>/", "<body$1 data-img-webp=\"true\">", $content, 1);
				
				$content = preg_replace_callback("/<img[^>]+src=\"([^\"]+)\"/is", function($matches) {
					if($GLOBALS["IMG_LAZYLOAD"])
						$matches[0] = str_replace(" src=", " data-lazyload-src=", $matches[0]);

					if($GLOBALS["IMG_WEBP"]) {
						if(substr($matches[1], 0, 4) != "http" && substr($matches[1], 0, 2) != "//" && substr($matches[1], 0, 11) != "data:image/") {
							$pathinfo = pathinfo($matches[1]);
							if(in_array($pathinfo["extension"], array("jpg", "jpeg", "png"))) {
								$newFile = $_SERVER["DOCUMENT_ROOT"].$pathinfo["dirname"]."/".$pathinfo["filename"].".webp";
								if(file_exists($newFile)) {
									$newSrc = $pathinfo["dirname"]."/".$pathinfo["filename"].".webp?".filemtime($newFile);
									$matches[0] = str_replace($matches[1], $newSrc, $matches[0]);
								}
								unset($newSrc, $newFile);
							}
							unset($pathinfo);
						}
					}
					
					return $matches[0];					
				}, $content);
			}
			
			//MOVE_CSS_TO_BODY//
			if($arBackParametrs["MOVE_CSS_TO_BODY"] == "Y" && !$isAdmin && !$isPersonal && Bitrix\Main\Config\Option::get("main", "optimize_css_files", "Y") == "Y") {
				$GLOBALS["STYLES_IN"] = "";
				$GLOBALS["STYLES_EXT"] = "";

				$content = preg_replace_callback("/<link([^>]+)stylesheet([^>]+)>/is", function($matches) {
					if(preg_match("/href=\"(.*?\/bitrix\/cache\/css\/".SITE_ID."\/".SITE_TEMPLATE_ID."\/[template|page|default]+_[^\"]+)\"/", $matches[0], $matchesNew)) {
						if(substr($matchesNew[1], 0, 4) != "http" && substr($matchesNew[1], 0, 2) != "//") {
							$GLOBALS["STYLES_IN"] .= $matches[0];
							return "";
						} elseif(ini_get("allow_url_fopen")) {
							$GLOBALS["STYLES_IN"] .= $matches[0];
							return "";
						} else {
							return $matches[0];
						}
					}
					unset($matchesNew);
					
					$GLOBALS["STYLES_EXT"] .= $matches[0];
					return "";
				}, $content);
				
				if(!empty($GLOBALS["STYLES_IN"])) {
					$obCache = new CPHPCache();
					if($obCache->InitCache(36000000, $GLOBALS["STYLES_IN"], "/css/".SITE_ID."/".SITE_TEMPLATE_ID)) {
						$includeCss = $obCache->GetVars();
					} elseif($obCache->StartDataCache()) {
						$includeCss = "";
						if(preg_match_all("/href=\"([^\"]+)\"/", $GLOBALS["STYLES_IN"], $links)) {
							foreach($links[1] as $link) {
								if(substr($link, 0, 4) != "http" && substr($link, 0, 2) != "//") {
									$tmp = explode("?", $link);
									$file = new Bitrix\Main\IO\File($_SERVER["DOCUMENT_ROOT"].$tmp[0]);
									if($file->isExists())
										$includeCss .= self::compressCSS($file->getContents());
									unset($file, $tmp);
								} else {
									if(substr($link, 0, 2) == "//")
										$link = (CMain::IsHTTPS() ? "https" : "http").":".$link;

									$handle = fopen($link, "r");
									if($handle)
										$includeCss .= self::compressCSS(file_get_contents($link));
									unset($handle);
								}
							}
							unset($link);
						}
						unset($links);
						$obCache->EndDataCache($includeCss);
					}

					if(!empty($includeCss))
						$content = preg_replace("/<body([^>]*)>/", "<body$1><style>".$includeCss."</style>", $content, 1);
				}
				unset($GLOBALS["STYLES_IN"]);
				
				if(!empty($GLOBALS["STYLES_EXT"]))
					$content = preg_replace("/<\/body>(?![\s\S]*<\/body>[\s\S]*$)/i", $GLOBALS["STYLES_EXT"]."</body>", $content);
				unset($GLOBALS["STYLES_EXT"]);
			}
			
			//JS_LAZYLOAD//
			$jsLazyLoad = $arBackParametrs["JS_LAZYLOAD"] == "Y" && !$isAdmin && !$isPersonal && !$isSiteClosed && !$b404 && !$isCompositeOn;
			if($jsLazyLoad) {
				$GLOBALS["SCRIPTS_MESS"] = "";
				$GLOBALS["SCRIPTS_CUR"] = "";
				$GLOBALS["SCRIPTS_IN"] = "";
				$GLOBALS["SCRIPTS_EXT"] = "";
				
				$content = preg_replace_callback("/<script(.*?)>(.*?)<\/script>/is", function($matches) {
					if(!empty($matches[2])) {
						$jsAttrs = array("data-skip-moving", "text/html");
						foreach($jsAttrs as $attr) {
							if(strpos($matches[1], $attr) !== false) {
								if(strpos($matches[2], "w.frameCacheVars") === false)
									return $matches[0];
							}
						}
						unset($attr, $jsAttrs);
						
						$jsIncs = array("(!window.BX)window.BX", "(window.BX||top.BX)");
						foreach($jsIncs as $inc) {
							if(strpos($matches[2], $inc) !== false)
								return $matches[0];
						}
						unset($inc, $jsIncs);
						
						$regex = "/BX\.message\(\{.*?\}\)\;/is";
						if(preg_match($regex, $matches[2], $matchesNew)) {						
							$matches[0] = preg_replace($regex, "", $matches[0]);
							$GLOBALS["SCRIPTS_MESS"] .= $matchesNew[0];
						}
						unset($matchesNew, $regex);

						$regex = "/BX\.Currency\.setCurrencies\(\[.*?\]\)\;/is";
						if(preg_match($regex, $matches[2], $matchesNew)) {
							$matches[0] = preg_replace($regex, "", $matches[0]);
							$GLOBALS["SCRIPTS_CUR"] .= $matchesNew[0];
						}
						unset($matchesNew, $regex);
						
						$GLOBALS["SCRIPTS_IN"] .= $matches[0];
					} else {
						if(strpos($matches[1], "data-skip-moving") !== false)
							return $matches[0];
						
						if(preg_match("/src=\"(.*?\/bitrix\/js\/main\/jquery\/jquery[0-9-.]+[|.min]+\.js\?[0-9]+)\"/", $matches[1]))
							return str_replace("src=", "defer src=", $matches[0]);
						
						$GLOBALS["SCRIPTS_EXT"] .= $matches[0];
					}
					return "";
				}, $content);

				if(!empty($GLOBALS["SCRIPTS_MESS"]))
					$GLOBALS["SCRIPTS_MESS"] = "<script type='text/javascript'>".$GLOBALS["SCRIPTS_MESS"]."</script>";

				if(!empty($GLOBALS["SCRIPTS_CUR"]))
					$GLOBALS["SCRIPTS_CUR"] = "<script type='text/javascript'>".implode(";", array_unique(explode(";", $GLOBALS["SCRIPTS_CUR"])))."</script>";
				
				if(!empty($GLOBALS["SCRIPTS_IN"])) {
					$regex = "/<script.*?>\s*?<\/script>/";
					if(preg_match($regex, $GLOBALS["SCRIPTS_IN"]))
						$GLOBALS["SCRIPTS_IN"] = preg_replace($regex, "", $GLOBALS["SCRIPTS_IN"]);
				}
				
				if(!empty($GLOBALS["SCRIPTS_EXT"])) {
					if(preg_match("/src=\"(.*?\/bitrix\/js\/".self::MODULE_ID."\/intlTelInput\/intlTelInput[|.min]+\.js\?[0-9]+)\"/", $GLOBALS["SCRIPTS_EXT"]))
						$GLOBALS["SCRIPTS_EXT"] .= "<script type='text/javascript' src='/bitrix/js/".self::MODULE_ID."/intlTelInput/utils.js'></script>";
				}
				
				$scriptsAll = self::compressJS($GLOBALS["SCRIPTS_MESS"].$GLOBALS["SCRIPTS_EXT"].$GLOBALS["SCRIPTS_CUR"].$GLOBALS["SCRIPTS_IN"]);

				unset($GLOBALS["SCRIPTS_MESS"], $GLOBALS["SCRIPTS_CUR"], $GLOBALS["SCRIPTS_IN"], $GLOBALS["SCRIPTS_EXT"]);
				
				if(!empty($scriptsAll)) {
					$obCache = new CPHPCache();
					if($obCache->InitCache(36000000, $GLOBALS["USER"]->IsAuthorized().urldecode($GLOBALS["APPLICATION"]->GetCurUri()), "/js/".SITE_ID."/".SITE_TEMPLATE_ID)) {
						$res = $obCache->GetVars();
					} elseif($obCache->StartDataCache()) {
						$res = $scriptsAll;
						$obCache->EndDataCache($res);
					}

					$content = preg_replace("/<body([^>]*)>/", "<body$1 data-js-lazyload=\"true\" data-site-id=\"".SITE_ID."\">", $content, 1);
				}
				unset($scriptsAll);
			}
			
			//CREATE_SCRIPT//
			if($GLOBALS["IMG_LAZYLOAD"] || $GLOBALS["IMG_WEBP"] || $jsLazyLoad) {
				$src = "/bitrix/js/".self::MODULE_ID."/script.min.js";
				$content = preg_replace("/<\/body>(?![\s\S]*<\/body>[\s\S]*$)/i", "<script type='text/javascript' defer src='".$src."?".filemtime($_SERVER["DOCUMENT_ROOT"].$src)."'></script></body>", $content);
				unset($src);
			}
			unset($GLOBALS["IMG_LAZYLOAD"], $GLOBALS["IMG_WEBP"], $jsLazyLoad);

			//SET_OPEN_GRAPH//
			$str = "";
			if(!empty($GLOBALS["SHARE_DEFAULT_PICTURE"])) {
				$scheme = CMain::IsHTTPS() ? "https" : "http";
				if(!preg_match("/<meta[^>]+og:type[^>]+>/is", $content))
					$str .= "<meta property=\"og:type\" content=\"website\" />";
				if(!preg_match("/<meta[^>]+og:image[^>]+>/is", $content))
					$str .= "<meta property=\"og:image\" content=\"".$scheme."://".SITE_SERVER_NAME.$GLOBALS["SHARE_DEFAULT_PICTURE"]["SRC"]."\" />";
				if(!preg_match("/<meta[^>]+og:image:width[^>]+>/is", $content))
					$str .= "<meta property=\"og:image:width\" content=\"".$GLOBALS["SHARE_DEFAULT_PICTURE"]["WIDTH"]."\" />";
				if(!preg_match("/<meta[^>]+og:image:height[^>]+>/is", $content))
					$str .= "<meta property=\"og:image:height\" content=\"".$GLOBALS["SHARE_DEFAULT_PICTURE"]["HEIGHT"]."\" />";
				if(!preg_match("/<link[^>]+image_src[^>]+>/is", $content))
					$str .= "<link rel=\"image_src\" href=\"".$scheme."://".SITE_SERVER_NAME.$GLOBALS["SHARE_DEFAULT_PICTURE"]["SRC"]."\" />";
			}
			if(!empty($str))
				$content = preg_replace("/<\/head>/", $str."</head>", $content, 1);
		}
	}
	
	public static function ajax() {
		$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();		
		if($request->isAjaxRequest()) {
			$action = $request->get("action");
			if($action == "getJs" || $action == "getWebp") {
				if($action == "getJs") {
					$url = iconv("UTF-8", SITE_CHARSET, urldecode($request->get("url")));
					if(!$url)
						$url = urldecode($request->get("url"));

					$siteId = !empty($request->get("siteId")) ? $request->get("siteId") : SITE_ID;
					
					$obCache = new CPHPCache();
					if($obCache->InitCache(36000000, $GLOBALS["USER"]->IsAuthorized().$url, "/js/".$siteId."/".SITE_TEMPLATE_ID)) {
						$arResult["JS"] = $obCache->GetVars();
					}
				} elseif($action == "getWebp") {
					$images = $request->get("images");
					if(!empty($images)) {
						foreach($images as $image) {
							$arResult[$image] = self::convertImgToWebp($image);
						}
						unset($image);
					}
					unset($images);
				}

				echo Bitrix\Main\Web\Json::encode(!empty($arResult) ? $arResult : false);
			}
		}
	}
	
	public static function AddPresentToBasket($ID, $arFields) {
		if(Bitrix\Main\Loader::includeModule("iblock")) {
			$rsElement = CIBlockElement::GetList(array(), array("ID" => $arFields["PRODUCT_ID"]), false, false, array("ID", "IBLOCK_ID", "PROPERTY_ARTNUMBER"));
			if($arElement = $rsElement->GetNext()) {
				if(!empty($arElement["PROPERTY_ARTNUMBER_VALUE"])) {
					$arFieldsAdd["PROPS"] = $arFields["PROPS"];
					foreach($arFieldsAdd["PROPS"] as $key => $arProp) {
						if(intval($arProp["SORT"]) > 0)
							$arFieldsAdd["PROPS"][$key]["SORT"]++;
					}
					unset($key, $arProp);

					$arFieldsAdd["PROPS"]["ARTNUMBER"] = array(
						"NAME" => GetMessage("ENEXT_PROPERTY_ARTNUMBER"),
						"CODE" => "ARTNUMBER",
						"VALUE" => $arElement["PROPERTY_ARTNUMBER_VALUE"],
						"SORT" => 1
					);
					CSaleBasket::Update($ID, $arFieldsAdd); 
				}
			}
			unset($arFieldsAdd, $arElement, $rsElement);
		}
	}
	
	public static function getObjectIdsFromProductIds($productIds) {
		$objectIds = array();
		
		if(!empty($productIds) && Bitrix\Main\Loader::includeModule("iblock")) {
			$offersList = array();
			$rsElements = CIBlockElement::GetList(array(), array("ID" => $productIds), false, false, array("ID", "IBLOCK_ID", "CATALOG_TYPE", "PROPERTY_OBJECT"));
			while($arElement = $rsElements->GetNext()) {
				if(!empty($arElement["PROPERTY_OBJECT_VALUE"]))
					$objectIds[] = $arElement["PROPERTY_OBJECT_VALUE"];
				
				if($arElement["CATALOG_TYPE"] == Bitrix\Catalog\ProductTable::TYPE_OFFER)
					$offersList[] = $arElement["ID"];
			}
			unset($arElement, $rsElements);

			if(empty($objectIds) && !empty($offersList)) {
				$itemsParents = CCatalogSku::getProductList($offersList);
				if(!empty($itemsParents)) {
					$offersMap = array();
					foreach($itemsParents as $offerId => $parentData) {
						if(!isset($offersMap[$parentData["ID"]]))
							$offersMap[$parentData["ID"]] = array();
						$offersMap[$parentData["ID"]][$offerId] = $offerId;
					}
					unset($offerId, $parentData);

					if(!empty($offersMap)) {
						$rsElements = CIBlockElement::GetList(array(), array("ID" => array_keys($offersMap)), false, false, array("ID", "IBLOCK_ID", "PROPERTY_OBJECT"));
						while($arElement = $rsElements->GetNext()) {
							if(!empty($arElement["PROPERTY_OBJECT_VALUE"]))
								$objectIds[] = $arElement["PROPERTY_OBJECT_VALUE"];
						}
						unset($arElement, $rsElements);
					}
					unset($offersMap);
				}
				unset($itemsParents);
			}
			unset($offersList);
		}

		return $objectIds;
	}
	
	public static function checkOrderSendEmail($orderId) {
		$order = Bitrix\Sale\Order::load($orderId);	
		$orderBasket = $order->getBasket();		
		if(!$orderBasket->isEmpty()) {
			foreach($orderBasket as $basketItem) {
				$productIds[] = $basketItem->getProductId();
			}
			unset($basketItem);
		}

		$objectIds = array();
		if(!empty($productIds))
			$objectIds = self::getObjectIdsFromProductIds($productIds);
		
		return empty($objectIds);
	}

	public static function CheckOrderNewSendEmail($ID, &$eventName, &$arFields) {
		$needSendEmail = self::checkOrderSendEmail($ID);

		return $needSendEmail;
	}

	public static function CheckOrderStatusSendEmail($ID, &$eventName, &$arFields, $val) {
		$needSendEmail = self::checkOrderSendEmail($ID);

		return $needSendEmail;
	}
}