<?define("STOP_STATISTICS", true);
define("NOT_CHECK_PERMISSIONS", true);

$siteId = isset($_REQUEST["siteId"]) && is_string($_REQUEST["siteId"]) ? $_REQUEST["siteId"] : "";
$siteId = substr(preg_replace("/[^a-z0-9_]/i", "", $siteId), 0, 2);
if(!empty($siteId) && is_string($siteId)) {
	define("SITE_ID", $siteId);
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
if($request->isAjaxRequest()) {
	$action = $request->get("action");
	if($action == "ADD_TO_DELAY" || $action == "DELETE_FROM_DELAY") {
		$productId = intval($request->get("productId"));
		$qnt = doubleval($request->get("quantity")) ?: 1;
		
		if($productId > 0 && Bitrix\Main\Loader::includeModule("sale")) {
			$dbBasketItems = CSaleBasket::GetList(
				array(),
				array(
					"PRODUCT_ID" => $productId,
					"LID" => SITE_ID,
					"DELAY" => $action == "ADD_TO_DELAY" ? "N" : "Y",
					"CAN_BUY" => "Y",
					"FUSER_ID" => Bitrix\Sale\Fuser::getId(true),
					"ORDER_ID" => "NULL"
				),
				false,
				false,
				array("ID")
			);			
			switch($action) {
				case "ADD_TO_DELAY":				
					if($arItem = $dbBasketItems->Fetch()) {
						if(CSaleBasket::Update($arItem["ID"], array("DELAY" => "Y")))
							echo Bitrix\Main\Web\Json::encode(array("STATUS" => "ADDED"));
					} else {
						if(Bitrix\Main\Loader::includeModule("catalog") && Add2BasketByProductID($productId, $qnt, array("LID" => SITE_ID, "DELAY" => "Y"), array()))
							echo Bitrix\Main\Web\Json::encode(array("STATUS" => "ADDED"));
					}
					break;
				case "DELETE_FROM_DELAY":				
					if($arItem = $dbBasketItems->Fetch()) {
						if(CSaleBasket::Delete($arItem["ID"]))
							echo Bitrix\Main\Web\Json::encode(array("STATUS" => "DELETED"));
					}
					break;
			}
			die();
		}
	} elseif($action == "ADD_TO_CART") {
		$productId = intval($request->get("productId"));
		$qnt = doubleval($request->get("quantity")) ?: 1;

		if($productId > 0 && Bitrix\Main\Loader::includeModule("sale")) {
			$dbBasketItems = CSaleBasket::GetList(
				array(),
				array(
					"PRODUCT_ID" => $productId,
					"LID" => SITE_ID,					
					"CAN_BUY" => "Y",
					"FUSER_ID" => Bitrix\Sale\Fuser::getId(true),
					"ORDER_ID" => "NULL"
				),
				false,
				false,
				array("ID", "QUANTITY", "DELAY")
			);
			if($arItem = $dbBasketItems->Fetch()) {
				if($arItem["DELAY"] == "Y") {
					if(CSaleBasket::Update($arItem["ID"], array("DELAY" => "N", "QUANTITY" => $qnt)))
						echo Bitrix\Main\Web\Json::encode(array("STATUS" => "OK"));
				} else {
					if(CSaleBasket::Update($arItem["ID"], array("QUANTITY" => $arItem["QUANTITY"] + $qnt)))
						echo Bitrix\Main\Web\Json::encode(array("STATUS" => "OK"));
				}
			} else {
				echo Bitrix\Main\Web\Json::encode(array("STATUS" => "ADD_TO_CART"));
			}
		}
	} elseif($action == "checkTargetOffer") {		
		$offerNum = false;		
		$offerId = intval($request->get("OFFER_ID"));
		$offerCode = $request->get("OFFER_CODE");

		if($offerId > 0 || !empty($offerCode)) {
			$offerIds = array();
			$offerCodes = array();
			
			$offers = $request->get("offers");
			if(!empty($offers)) {
				foreach($offers as $key => $arOffer) {
					$offerIds[] = $arOffer["ID"];
					$offerCodes[] = $arOffer["CODE"];
				}
			}
			unset($key, $arOffer);
			
			if($offerId > 0 && !empty($offerIds)) {
				$offerNum = array_search($offerId, $offerIds);
			} elseif(!empty($offerCode) && !empty($offerCodes)) {
				$offerNum = array_search($offerCode, $offerCodes);
			}
		}
		
		echo Bitrix\Main\Web\Json::encode(array("offerNum" => $offerNum));
	} elseif($action == "checkComparedDelayedBuyedAddedQuantity") {
		$productId = intval($request->get("productId"));
		$offers = $request->get("offers");
		$offerNum = intval($request->get("offerNum"));
		$offersView = $request->get("offersView");
		
		$result = array();

		$checkCompared = $request->get("checkCompared");
		if($checkCompared) {
			$compareName = $request->get("compareName");
			$iblockId = intval($request->get("iblockId"));

			$compared = false;
			$comparedIds = array();
			
			if(!empty($compareName) && !empty($_SESSION[$compareName][$iblockId])) {
				if(!empty($offers)) {
					foreach($offers as $key => $arOfferId) {
						if(array_key_exists($arOfferId, $_SESSION[$compareName][$iblockId]["ITEMS"])) {
							if(($offersView == "PROPS" || $offersView == "DROPDOWN_LIST") && $key == $offerNum) {
								$compared = true;
							}
							$comparedIds[] = $arOfferId;
						}
					}
					unset($key, $arOfferId);
				} elseif(array_key_exists($productId, $_SESSION[$compareName][$iblockId]["ITEMS"])) {
					$compared = true;
				}
			}

			$result["compared"] = $compared;
			$result["comparedIds"] = $comparedIds;
		}
		
		$checkDelayed = $request->get("checkDelayed");
		$checkBuyedAdded = $request->get("checkBuyedAdded");
		$checkQuantity = $request->get("checkQuantity");
		if($checkDelayed || $checkBuyedAdded || $checkQuantity) {
			if($checkDelayed) {
				$delayed = false;			
				$delayedIds = array();
			}

			if($checkBuyedAdded) {
				$buyedAdded = false;
				$buyedAddedIds = array();
			}

			if($checkQuantity) {
				$quantity = false;
				$quantityIds = array();
			}

			if(Bitrix\Main\Loader::includeModule("sale")) {
				$fuserId = Bitrix\Sale\Fuser::getId(true);
				$dbItems = CSaleBasket::GetList(
					array("NAME" => "ASC", "ID" => "ASC"),
					array(			
						"LID" => SITE_ID,
						"CAN_BUY" => "Y",
						"FUSER_ID" => $fuserId,
						"ORDER_ID" => "NULL"
					),
					false,
					false,
					array("ID", "PRODUCT_ID", "QUANTITY", "DELAY", "TYPE", "SET_PARENT_ID")
				);
				while($arItem = $dbItems->GetNext()) {
					if(CSaleBasketHelper::isSetItem($arItem))
						continue;			
					
					if(!empty($offers)) {
						foreach($offers as $key => $arOfferId) {
							if($arOfferId == $arItem["PRODUCT_ID"]) {
								if(($offersView == "PROPS" || $offersView == "DROPDOWN_LIST") && $key == $offerNum) {
									if($checkDelayed && $arItem["DELAY"] == "Y")
										$delayed = true;
									elseif($checkBuyedAdded && $arItem["DELAY"] == "N")
										$buyedAdded = true;

									if($checkQuantity && $arItem["DELAY"] == "N")
										$quantity = $arItem["QUANTITY"];
								}					
								if($checkDelayed && $arItem["DELAY"] == "Y")
									$delayedIds[] = $arOfferId;
								elseif($checkBuyedAdded && $arItem["DELAY"] == "N")
									$buyedAddedIds[] = $arOfferId;

								if($checkQuantity && $arItem["DELAY"] == "N")
									$quantityIds[$arOfferId] = $arItem["QUANTITY"];
							}
						}
						unset($key, $arOfferId);
					} elseif($productId == $arItem["PRODUCT_ID"]) {
						if($checkDelayed && $arItem["DELAY"] == "Y")
							$delayed = true;
						elseif($checkBuyedAdded && $arItem["DELAY"] == "N")
							$buyedAdded = true;
						
						if($checkQuantity && $arItem["DELAY"] == "N")
							$quantity = $arItem["QUANTITY"];
					}
				}
				unset($arItem, $dbItems, $fuserId);
			}

			if($checkDelayed) {
				$result["delayed"] = $delayed;
				$result["delayedIds"] = $delayedIds;
			}

			if($checkBuyedAdded) {
				$result["buyedAdded"] = $buyedAdded;			
				$result["buyedAddedIds"] = $buyedAddedIds;
			}

			if($checkQuantity) {
				$result["quantity"] = $quantity;
				$result["quantityIds"] = $quantityIds;
			}
		}
		
		echo Bitrix\Main\Web\Json::encode($result);
	} elseif($action == "updateCartProductQuantity") {
		$productId = intval($request->get("productId"));
		$qnt = doubleval($request->get("quantity")) ?: 1;

		if($productId > 0 && Bitrix\Main\Loader::includeModule("sale")) {
			$dbBasketItems = CSaleBasket::GetList(
				array(),
				array(
					"PRODUCT_ID" => $productId,
					"LID" => SITE_ID,
					"DELAY" => "N",
					"CAN_BUY" => "Y",
					"FUSER_ID" => Bitrix\Sale\Fuser::getId(true),
					"ORDER_ID" => "NULL"
				),
				false,
				false,
				array("ID", "QUANTITY")
			);
			if($arItem = $dbBasketItems->Fetch()) {
				if($arItem["QUANTITY"] != $qnt) {
					if(CSaleBasket::Update($arItem["ID"], array("QUANTITY" => $qnt)))
						echo Bitrix\Main\Web\Json::encode(array("STATUS" => "UPDATED"));
				}
			}
		}
	} elseif($action == "objectWorkingHoursToday") {
		$siteCharset = $request->get("siteCharset") ?: SITE_CHARSET;

		$result = array();
		
		$offers = $request->get("offers");
		if(!empty($offers)) {
			$currentDateTime = time() + CTimeZone::GetOffset();
			foreach($offers as $key => $arOffer) {
				$timezone = $arOffer["timezone"];
				if(!empty($timezone))
					$currentDateTime = strtotime(gmdate("Y-m-d H:i", strtotime($timezone." hours")));

				$workingHours = $arOffer["workingHours"];
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
				}

				$result[$key] = array(
					"today" => !empty($arWorkingHoursToday) ? $arWorkingHoursToday : false
				);
			}
			unset($key, $arOffer);
		}

		echo Bitrix\Main\Web\Json::encode($result);
	} elseif($action == "checkObjectsOffersPriceUpdated") {
		$result = array();

		$data = $request->get("data");
		if(!empty($data)) {
			$arSettings = CEnext::GetFrontParametrsValues(SITE_ID);
			$currentDateTime = time() + CTimeZone::GetOffset();

			foreach($data as $key => $timestamp) {
				if(!empty($timestamp)) {
					if(floor(($currentDateTime - strtotime($timestamp)) / 86400) >= intval($arSettings["PRICE_UPDATE_PERIOD"]))
						$result[] = $key;
				}
			}
			unset($key, $timestamp);
		}

		echo Bitrix\Main\Web\Json::encode($result);
	} elseif($action == "updateObjectOfferPrice") {
		//PRO_WS//
		if (!check_bitrix_sessid()) {
			http_response_code(403);
			die('Access denied');
		}
		//PRO_WS//
		
		$result = array();

		global $USER;
		if(!Bitrix\Main\Loader::includeModule("iblock")) {
			http_response_code(403);
			die('Access denied');
		}
		$productIblockId = intval($request->get("productIblockId"));
		$publicPriceUpdate = false;
		if(class_exists("CEnext")) {
			$priceSettings = CEnext::GetFrontParametrsValues(SITE_ID);
			$publicPriceUpdate = (!empty($priceSettings["PRICE_UPDATE_FOR_ALL"]) && $priceSettings["PRICE_UPDATE_FOR_ALL"] == "Y");
		}
		if($productIblockId <= 0 || (!$publicPriceUpdate && !$USER->IsAdmin() && CIBlock::GetPermission($productIblockId) < "W")) {
			http_response_code(403);
			die('Access denied');
		}
		$productId = intval($request->get("productId"));

		$allowedPriceTypes = array();
		if(Bitrix\Main\Loader::includeModule("catalog")) {
			$baseCurrency = Bitrix\Main\Loader::includeModule("currency") ? Bitrix\Currency\CurrencyManager::getBaseCurrency() : "";
			$rsGroups = CCatalogGroup::GetList(array("SORT" => "ASC"), array("CAN_BUY" => "Y"));
			while($arGroup = $rsGroups->Fetch())
				$allowedPriceTypes[(int)$arGroup["ID"]] = $baseCurrency;
			unset($arGroup, $rsGroups, $baseCurrency);
		}
		$offers = array();
		foreach((array)$request->get("offers") as $key => $arOffer) {
			if(!is_array($arOffer))
				continue;
			$priceTypeId = (int)$arOffer["PRICE_TYPE_ID"];
			if(!isset($allowedPriceTypes[$priceTypeId]))
				continue;
			$arOffer["PRICE_TYPE_ID"] = $priceTypeId;
			$arOffer["CURRENCY"] = $allowedPriceTypes[$priceTypeId];
			$offers[$key] = $arOffer;
		}
		unset($key, $arOffer, $priceTypeId, $allowedPriceTypes);
		if(!empty($offers) && $productId > 0 && $productIblockId > 0 && Bitrix\Main\Loader::includeModule("catalog") && Bitrix\Main\Loader::includeModule("iblock")) {
			$skuInfo = CCatalogSKU::GetInfoByProductIBlock($productIblockId);
			$allowedIds = array();
			if(is_array($skuInfo)) {
				$requestedIds = array();
				foreach($offers as $arOffer)
					$requestedIds[] = intval($arOffer["ID"]);
				$requestedIds = array_filter($requestedIds);
				if(!empty($requestedIds)) {
					$rsAllowed = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $skuInfo["IBLOCK_ID"], "ID" => $requestedIds, "PROPERTY_".$skuInfo["SKU_PROPERTY_ID"] => $productId), false, false, array("ID"));
					while($arAllowed = $rsAllowed->Fetch())
						$allowedIds[(int)$arAllowed["ID"]] = true;
					unset($arAllowed, $rsAllowed);
				}
				unset($requestedIds);
			}
			$offersChecked = array();
			foreach($offers as $key => $arOffer) {
				if(!empty($allowedIds[(int)$arOffer["ID"]]))
					$offersChecked[$key] = $arOffer;
			}
			$offers = $offersChecked;
			unset($offersChecked, $allowedIds, $skuInfo);
		}
		if(!empty($offers)) {
			$offersList = $objectsList = array();
			foreach($offers as $arOffer) {
				$offersList[$arOffer["ID"]] = array();
				$objectsList[$arOffer["OBJECT_ID"]] = array();
			}
			unset($arOffer);

			if(Bitrix\Main\Loader::includeModule("iblock")) {
				if(!empty($offersList)) {
					$rsElements = CIBlockElement::GetList(array(), array("ID" => array_keys($offersList)), false, false, array("ID", "IBLOCK_ID", "PROPERTY_PARSER_LINK"));	
					while($arElement = $rsElements->GetNext()) {
						$offersList[$arElement["ID"]]["PARSER_LINK"] = $arElement["PROPERTY_PARSER_LINK_VALUE"];
					}
					unset($arElement, $rsElements);
				}

				if(!empty($objectsList)) {
					$rsElements = CIBlockElement::GetList(array(), array("ID" => array_keys($objectsList)), false, false, array("ID", "IBLOCK_ID", "PROPERTY_PARSER_TAG", "PROPERTY_PARSER_CLASS"));	
					while($arElement = $rsElements->GetNext()) {
						$objectsList[$arElement["ID"]]["PARSER_TAG"] = $arElement["PROPERTY_PARSER_TAG_VALUE"];
						$objectsList[$arElement["ID"]]["PARSER_CLASS"] = $arElement["PROPERTY_PARSER_CLASS_VALUE"];
					}
					unset($arElement, $rsElements);
				}
			}

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
			if(defined("CURLPROTO_HTTP") && defined("CURLPROTO_HTTPS"))
				curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
			
			foreach($offers as $key => $arOffer) {
				$parserLink = "";
				$parserTag = "";
				$parserClass = "";
				if(array_key_exists($arOffer["ID"], $offersList))
					$parserLink = $offersList[$arOffer["ID"]]["PARSER_LINK"];
				
				if(array_key_exists($arOffer["OBJECT_ID"], $objectsList)) {
					$parserTag = $objectsList[$arOffer["OBJECT_ID"]]["PARSER_TAG"];
					$parserClass = $objectsList[$arOffer["OBJECT_ID"]]["PARSER_CLASS"];
				}

				if(!empty($parserLink) && !empty($parserTag) && !empty($parserClass)) {
					$url = parse_url($parserLink);
					$urlScheme = isset($url["scheme"]) ? strtolower($url["scheme"]) : "";
					$urlHost = isset($url["host"]) ? $url["host"] : "";
					if(($urlScheme != "http" && $urlScheme != "https") || $urlHost == "")
						continue;
					$resolvedIp = gethostbyname($urlHost);
					if(!filter_var($resolvedIp, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE))
						continue;

					curl_setopt($ch, CURLOPT_URL, $parserLink);
					
					$page = curl_exec($ch);
					
					if(!empty($page)) {
						$parsedPrice = 0;
						$priceFound = false;
						$parserTag = preg_quote($parserTag, "/");
						$parserClass = preg_quote($parserClass, "/");
						if(preg_match("/<span[^>]+itemprop=\"price\"[^>]+content=\"(.*?)\"/is", $page, $matches)) {
							$parsedPrice = (float)$matches[1];
							$priceFound = true;
						} elseif(preg_match("/<".$parserTag."[^>]+".$parserClass."[^>]+>(.*?)<\/[^>]+>/is", $page, $matches)) {
							$parsedPrice = (float)str_replace(",", ".", preg_replace("/[^0-9\,\.]/", "", strip_tags($matches[1])));
							$priceFound = true;
						}
						if(!$priceFound)
							continue;
						
						$parsedPriceList[] = $parsedPrice;
						
						if(Bitrix\Main\Loader::includeModule("catalog")) {
							$timestamp = ConvertTimeStamp();

							$rsPrice = Bitrix\Catalog\PriceTable::getList(array(
								"filter" => array(
									"PRODUCT_ID" => $arOffer["ID"],
									"CATALOG_GROUP_ID" => $arOffer["PRICE_TYPE_ID"]
								),
								"select" => array("ID", "PRICE")
							));
							//PRO_WS//
							if($arPrice = $rsPrice->fetch()) {
								$resultPrice = Bitrix\Catalog\Model\Price::update($arPrice["ID"], array("PRICE" => $parsedPrice));
							} else {
								$resultPrice = Bitrix\Catalog\Model\Price::add(array(
									"PRODUCT_ID" => $arOffer["ID"],
									"CATALOG_GROUP_ID" => $arOffer["PRICE_TYPE_ID"],
									"PRICE" => $parsedPrice,
									"CURRENCY" => $arOffer["CURRENCY"]
								));
							}
							if($resultPrice->isSuccess()) {
								$result[$key] = array(
									"status" => true,
									"price" => $parsedPrice,
									"printPrice" => CCurrencyLang::CurrencyFormat($parsedPrice, $arOffer["CURRENCY"]),
									"timestampX" => $timestamp
								);
								if(Bitrix\Main\Loader::includeModule("iblock")) {
									$el = new CIBlockElement;
									$el->Update($arOffer["ID"], array("TIMESTAMP_X" => $timestamp));
								}
							} else {
								$result[$key] = array(
									"status" => false,
									"message" => $resultPrice->getErrorMessages()
								);
							}
							/*
							if($arPrice = $rsPrice->fetch()) {
								$resultPrice = Bitrix\Catalog\Model\Price::update($arPrice["ID"], array("PRICE" => $parsedPrice));
								if($resultPrice->isSuccess()) {
									$result[$key] = array(
										"status" => true,
										"price" => $parsedPrice,
										"printPrice" => CCurrencyLang::CurrencyFormat($parsedPrice, $arOffer["CURRENCY"]),
										"timestampX" => $timestamp
									);
									if(Bitrix\Main\Loader::includeModule("iblock")) {
										$el = new CIBlockElement;
										$el->Update($arOffer["ID"], array("TIMESTAMP_X" => $timestamp));
									}
								} else {
									$result[$key] = array(
										"status" => false,
										"message" => $resultPrice->getErrorMessages()
									);
								}
							}
							*/
							//PRO_WS//
							unset($resultPrice, $arPrice, $rsPrice, $timestamp);
						}
					}
				}
			}
			unset($key, $arOffer);
			
			curl_close($ch);
		}
		
		if(!empty($parsedPriceList) && $productId > 0 && $productIblockId > 0 && Bitrix\Main\Loader::includeModule("catalog")) {
			$mxResult = CCatalogSKU::GetInfoByProductIBlock($productIblockId);
			if(is_array($mxResult)) {
				$rsOffers = CIBlockElement::GetList(array(), array("ACTIVE" => "Y", "IBLOCK_ID" => $mxResult["IBLOCK_ID"], "PROPERTY_".$mxResult["SKU_PROPERTY_ID"] => $productId), false, false, array("ID", "IBLOCK_ID"));
				while($arOffer = $rsOffers->GetNext()) {
					$offerIds[] = $arOffer["ID"];
				}
				unset($arOffer, $rsOffers);

				if(!empty($offerIds)) {
					$baseCurrency = Bitrix\Currency\CurrencyManager::getBaseCurrency();

					$minPrice = $maxPrice = array();
					
					$rsPrices = Bitrix\Catalog\PriceTable::getList(array(
						"filter" => array(
							"PRODUCT_ID" => $offerIds
						),
						"select" => array("ID", "CATALOG_GROUP_ID", "PRICE", "CURRENCY")
					));
					while($arPrice = $rsPrices->Fetch()) {
						if(Bitrix\Main\Loader::includeModule("currency") && $baseCurrency != $arPrice["CURRENCY"]) {
							$arPrice["PRICE"] = CCurrencyRates::ConvertCurrency($arPrice["PRICE"], $arPrice["CURRENCY"], $baseCurrency);
						}

						$price = $arPrice["PRICE"];
						if($price <= 0)
							continue;

						if(!$minPrice[$arPrice["CATALOG_GROUP_ID"]] || $minPrice[$arPrice["CATALOG_GROUP_ID"]] > $price) {
							$minPrice[$arPrice["CATALOG_GROUP_ID"]] = $price;
						}

						if(!$maxPrice[$arPrice["CATALOG_GROUP_ID"]] || $maxPrice[$arPrice["CATALOG_GROUP_ID"]] < $price) {
							$maxPrice[$arPrice["CATALOG_GROUP_ID"]] = $price;
						}
					}
					unset($arPrice, $rsPrices);
					
					if(!empty($minPrice)) {
						foreach($minPrice as $priceId => $minPriceItem) {
							$arPropValues["MINIMUM_PRICE_".$priceId] = $minPriceItem;
							$arPropValues["MAXIMUM_PRICE_".$priceId] = $maxPrice[$priceId];
						}
						unset($priceId, $minPriceItem);

						if(!empty($arPropValues)) {
							CIBlockElement::SetPropertyValuesEx($productId, $productIblockId, $arPropValues);
							CIBlock::clearIblockTagCache($productIblockId);
						}
					}
				}
				unset($arPropValues, $minPrice, $maxPrice, $baseCurrency, $offerIds);
			}
			unset($mxResult);
		}

		echo Bitrix\Main\Web\Json::encode($result);
	} elseif($action == "partnerSiteRedirect") {
		$productId = intval($request->get("productId"));
		if($productId > 0 && Bitrix\Main\Loader::includeModule("iblock")) {
			$rsElements = CIBlockElement::GetList(array(), array("ID" => $productId), false, false, array("ID", "IBLOCK_ID"));	
			if($obElement = $rsElements->GetNextElement()) {
				$arProps = $obElement->GetProperties();
				if(!empty($arProps["PARTNERS_URL"]["VALUE"]))
					$partnersUrl = $arProps["PARTNERS_URL"]["VALUE"];
			}
			unset($arProps, $obElement, $rsElements);

			if((!isset($partnersUrl) || empty($partnersUrl)) && Bitrix\Main\Loader::includeModule("catalog")) {
				$mxResult = CCatalogSku::GetProductInfo($productId);
				if(is_array($mxResult)) {
					$rsElements = CIBlockElement::GetList(array(), array("ID" => $mxResult["ID"]), false, false, array("ID", "IBLOCK_ID"));	
					if($obElement = $rsElements->GetNextElement()) {
						$arProps = $obElement->GetProperties();
						if(!empty($arProps["PARTNERS_URL"]["VALUE"]))
							$partnersUrl = $arProps["PARTNERS_URL"]["VALUE"];
					}
					unset($arProps, $obElement, $rsElements);
				}
				unset($mxResult);
			}

			echo Bitrix\Main\Web\Json::encode(array(
				"partnersUrl" => !empty($partnersUrl) ? $partnersUrl : false
			));
		}
	} elseif($action == "setConstructor" && Bitrix\Main\Loader::includeModule("catalog")) {
		$signer = new Bitrix\Main\Security\Sign\Signer;
		$parameters = unserialize(base64_decode($signer->unsign($request->get("parameters"), "catalog.element")));		
		
		$offersIblock = intval($request->get("offersIblock"));
		$offerId = intval($request->get("offerId"));
		$productId = intval($request->get("productId"));

		if($offersIblock > 0 && $offerId > 0) {
			$setItemsCount = false;
			if(Bitrix\Main\Loader::includeModule("iblock") && $productId > 0) {
				$rsElements = CIBlockElement::GetList(array(), array("ID" => $productId), false, false, array("ID", "IBLOCK_ID", "PROPERTY_SET_ITEMS_COUNT"));	
				if($arElement = $rsElements->GetNext()) {
					if(!empty($arElement["PROPERTY_SET_ITEMS_COUNT_VALUE"]))
						$setItemsCount = $arElement["PROPERTY_SET_ITEMS_COUNT_VALUE"];
				}
			}
		
			$APPLICATION->IncludeComponent("altop:catalog.set.constructor.elektronext", ".default",
				array(
					"IBLOCK_TYPE" => $parameters["IBLOCK_TYPE"],
					"IBLOCK_ID" => $offersIblock,
					"ELEMENT_ID" => $offerId,
					"BASKET_URL" => $parameters["BASKET_URL"],
					"PRICE_CODE" => $parameters["PRICE_CODE"],
					"PRICE_VAT_INCLUDE" => $parameters["PRICE_VAT_INCLUDE"],
					"CACHE_TYPE" => $parameters["CACHE_TYPE"],
					"CACHE_TIME" => $parameters["CACHE_TIME"],
					"CACHE_GROUPS" => $parameters["CACHE_GROUPS"],
					"BUNDLE_ITEMS_COUNT" => !empty($setItemsCount) ? $setItemsCount : $parameters["SET_ITEMS_COUNT"],
					"CONVERT_CURRENCY" => $parameters["CONVERT_CURRENCY"],
					"CURRENCY_ID" => $parameters["CURRENCY_ID"],
					"ADD_PROPERTIES_TO_BASKET" => $parameters["ADD_PROPERTIES_TO_BASKET"],
					"PRODUCT_PROPS_VARIABLE" => $parameters["PRODUCT_PROPS_VARIABLE"],
					"PARTIAL_PRODUCT_PROPERTIES" => $parameters["PARTIAL_PRODUCT_PROPERTIES"],
					"PRODUCT_PROPERTIES" => $parameters["PRODUCT_PROPERTIES"],
					"OFFERS_CART_PROPERTIES" => $parameters["OFFERS_CART_PROPERTIES"]
				),
				false
			);

			$content = ob_get_contents();
			ob_end_clean();

			$arSettings = CEnext::GetFrontParametrsValues(SITE_ID);

			$webpSupport = strpos($_SERVER["HTTP_ACCEPT"], "image/webp") !== false || strpos($_SERVER["HTTP_USER_AGENT"], " Chrome/") !== false ? true : false;
			
			$GLOBALS["IMG_LAZYLOAD"] = $arSettings["IMG_LAZYLOAD"] == "Y";
			$GLOBALS["IMG_WEBP"] = $arSettings["IMG_WEBP"] == "Y" && function_exists("imagewebp") && $webpSupport;
			
			if($GLOBALS["IMG_LAZYLOAD"] || $GLOBALS["IMG_WEBP"]) {
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

			if(Bitrix\Main\Loader::includeModule("iblock")) {
				Bitrix\Iblock\Component\Base::sendJsonAnswer(array(
					"content" => $content,
					"imgLazyLoad" => $GLOBALS["IMG_LAZYLOAD"],
					"imgWebp" => $GLOBALS["IMG_WEBP"]
				));
			}
		}
	} elseif($action == "changeMoreProductsSectionLink") {
		$arSettings = CEnext::GetFrontParametrsValues(SITE_ID);
		
		$signer = new Bitrix\Main\Security\Sign\Signer;
		$parameters = unserialize(base64_decode($signer->unsign($request->get("parameters"), "catalog.element")));
		
		$productsIds = unserialize(base64_decode($signer->unsign($request->get("productsIds"), "catalog.element")));
		if(!empty($productsIds))
			$GLOBALS["arMoreProductsFilter"]["ID"] = $productsIds;

		$sectionID = intval($request->get("sectionId"));
		if($sectionID > 0)
			$GLOBALS["arMoreProductsFilter"]["SECTION_ID"] = $sectionID;
		
		$APPLICATION->IncludeComponent("bitrix:catalog.section", ".default", 
			array(
				"COMPONENT_TEMPLATE" => ".default",
				"IBLOCK_TYPE" => $parameters["IBLOCK_TYPE"],
				"IBLOCK_ID" => $parameters["IBLOCK_ID"],
				"SECTION_ID" => "",
				"SECTION_CODE" => "",
				"SECTION_USER_FIELDS" => array(),
				"FILTER_NAME" => "arMoreProductsFilter",
				"INCLUDE_SUBSECTIONS" => $parameters["INCLUDE_SUBSECTIONS"],
				"SHOW_ALL_WO_SECTION" => "Y",
				"CUSTOM_FILTER" => "",
				"HIDE_NOT_AVAILABLE" => $parameters["HIDE_NOT_AVAILABLE"],
				"HIDE_NOT_AVAILABLE_OFFERS" => $parameters["HIDE_NOT_AVAILABLE_OFFERS"],
				"ELEMENT_SORT_FIELD" => $parameters["ELEMENT_SORT_FIELD"],
				"ELEMENT_SORT_ORDER" => $parameters["ELEMENT_SORT_ORDER"],
				"ELEMENT_SORT_FIELD2" => $parameters["ELEMENT_SORT_FIELD2"],
				"ELEMENT_SORT_ORDER2" => $parameters["ELEMENT_SORT_ORDER2"],
				"OFFERS_SORT_FIELD" => $parameters["OFFERS_SORT_FIELD"],
				"OFFERS_SORT_ORDER" => $parameters["OFFERS_SORT_ORDER"],
				"OFFERS_SORT_FIELD2" => $parameters["OFFERS_SORT_FIELD2"],
				"OFFERS_SORT_ORDER2" => $parameters["OFFERS_SORT_ORDER2"],
				"PAGE_ELEMENT_COUNT" => "12",
				"LINE_ELEMENT_COUNT" => "4",
				"PROPERTY_CODE" => $parameters["LIST_PROPERTY_CODE"],
				"OFFERS_FIELD_CODE" => $parameters["LIST_OFFERS_FIELD_CODE"],
				"OFFERS_PROPERTY_CODE" => $parameters["LIST_OFFERS_PROPERTY_CODE"],
				"OFFERS_LIMIT" => $parameters["LIST_OFFERS_LIMIT"],				
				"PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false}]",
				"PRODUCT_DISPLAY_MODE" => $parameters["PRODUCT_DISPLAY_MODE"],
				"OFFER_TREE_PROPS" => $parameters["OFFER_TREE_PROPS"],
				"PRODUCT_SUBSCRIPTION" => $parameters["PRODUCT_SUBSCRIPTION"],
				"SHOW_DISCOUNT_PERCENT" => $parameters["SHOW_DISCOUNT_PERCENT"],
				"SHOW_OLD_PRICE" => $parameters["SHOW_OLD_PRICE"],
				"SHOW_MAX_QUANTITY" => $parameters["SHOW_MAX_QUANTITY"],
				"MESS_SHOW_MAX_QUANTITY" => $parameters["MESS_SHOW_MAX_QUANTITY"],
				"RELATIVE_QUANTITY_FACTOR" => $parameters["RELATIVE_QUANTITY_FACTOR"],
				"MESS_RELATIVE_QUANTITY_MANY" => $parameters["MESS_RELATIVE_QUANTITY_MANY"],
				"MESS_RELATIVE_QUANTITY_FEW" => $parameters["MESS_RELATIVE_QUANTITY_FEW"],
				"MESS_BTN_BUY" => $parameters["MESS_BTN_BUY"],
				"MESS_BTN_ADD_TO_BASKET" => $parameters["MESS_BTN_ADD_TO_BASKET"],
				"MESS_BTN_SUBSCRIBE" => $parameters["MESS_BTN_SUBSCRIBE"],
				"MESS_BTN_DETAIL" => $parameters["MESS_BTN_DETAIL"],
				"MESS_NOT_AVAILABLE" => $parameters["MESS_NOT_AVAILABLE"],
				"RCM_TYPE" => "personal",
				"RCM_PROD_ID" => "",
				"SHOW_FROM_SECTION" => "N",
				"SECTION_URL" => "",
				"DETAIL_URL" => "",
				"SECTION_ID_VARIABLE" => "SECTION_ID",
				"SEF_MODE" => "N",
				"AJAX_MODE" => "N",
				"AJAX_OPTION_JUMP" => "N",
				"AJAX_OPTION_STYLE" => "Y",
				"AJAX_OPTION_HISTORY" => "N",
				"AJAX_OPTION_ADDITIONAL" => "",
				"CACHE_TYPE" => $parameters["CACHE_TYPE"],
				"CACHE_TIME" => $parameters["CACHE_TIME"],
				"CACHE_GROUPS" => $parameters["CACHE_GROUPS"],
				"SET_TITLE" => "N",
				"SET_BROWSER_TITLE" => "N",
				"BROWSER_TITLE" => "-",
				"SET_META_KEYWORDS" => "N",
				"META_KEYWORDS" => "-",
				"SET_META_DESCRIPTION" => "N",
				"META_DESCRIPTION" => "-",
				"SET_LAST_MODIFIED" => "N",
				"USE_MAIN_ELEMENT_SECTION" => $parameters["USE_MAIN_ELEMENT_SECTION"],
				"ADD_SECTIONS_CHAIN" => "N",
				"CACHE_FILTER" => $parameters["CACHE_FILTER"],
				"USE_REVIEW" => $parameters["USE_REVIEW"],
				"REVIEWS_IBLOCK_TYPE" => $parameters["REVIEWS_IBLOCK_TYPE"],
				"REVIEWS_IBLOCK_ID" => $parameters["REVIEWS_IBLOCK_ID"],
				"REVIEWS_NEWS_COUNT" => $parameters["REVIEWS_NEWS_COUNT"],
				"REVIEWS_SORT_BY1" => $parameters["REVIEWS_SORT_BY1"],
				"REVIEWS_SORT_ORDER1" => $parameters["REVIEWS_SORT_ORDER1"],
				"REVIEWS_SORT_BY2" => $parameters["REVIEWS_SORT_BY2"],
				"REVIEWS_SORT_ORDER2" => $parameters["REVIEWS_SORT_ORDER2"],
				"REVIEWS_ACTIVE_DATE_FORMAT" => $parameters["REVIEWS_ACTIVE_DATE_FORMAT"],
				"REVIEWS_PROPERTY_CODE" => $parameters["REVIEWS_PROPERTY_CODE"],
				"MESS_REVIEWS_TAB" => $parameters["MESS_REVIEWS_TAB"],
				"ACTION_VARIABLE" => "action",
				"PRODUCT_ID_VARIABLE" => "id",								
				"PRICE_CODE" => $parameters["PRICE_CODE"],
				"USE_PRICE_COUNT" => $parameters["USE_PRICE_COUNT"],
				"SHOW_PRICE_COUNT" => $parameters["SHOW_PRICE_COUNT"] ? "Y" : "N",
				"PRICE_VAT_INCLUDE" => $parameters["PRICE_VAT_INCLUDE"],
				"CONVERT_CURRENCY" => $parameters["CONVERT_CURRENCY"],
				"CURRENCY_ID" => $parameters["CURRENCY_ID"],
				"BASKET_URL" => $parameters["BASKET_URL"],
				"USE_PRODUCT_QUANTITY" => $parameters["USE_PRODUCT_QUANTITY"],
				"PRODUCT_QUANTITY_VARIABLE" => "quantity",
				"ADD_PROPERTIES_TO_BASKET" => $parameters["ADD_PROPERTIES_TO_BASKET"],
				"PRODUCT_PROPS_VARIABLE" => "prop",
				"PARTIAL_PRODUCT_PROPERTIES" => $parameters["PARTIAL_PRODUCT_PROPERTIES"],
				"PRODUCT_PROPERTIES" => $parameters["PRODUCT_PROPERTIES"],
				"OFFERS_CART_PROPERTIES" => $parameters["OFFERS_CART_PROPERTIES"],
				"ADD_TO_BASKET_ACTION" => in_array("BUY", $parameters["ADD_TO_BASKET_ACTION"]) ? "BUY" : "ADD",
				"DISPLAY_COMPARE" => $parameters["DISPLAY_COMPARE"],
				"COMPARE_PATH" => $parameters["COMPARE_PATH"],
				"MESS_BTN_COMPARE" => $parameters["MESS_BTN_COMPARE"],
				"COMPARE_NAME" => $parameters["COMPARE_NAME"],
				"USE_ENHANCED_ECOMMERCE" => "N",
				"PAGER_TEMPLATE" => "arrows",
				"DISPLAY_TOP_PAGER" => "N",
				"DISPLAY_BOTTOM_PAGER" => "N",
				"PAGER_TITLE" => "",
				"PAGER_SHOW_ALWAYS" => "N",
				"PAGER_DESC_NUMBERING" => "N",
				"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
				"PAGER_SHOW_ALL" => "N",
				"PAGER_BASE_LINK_ENABLE" => "N",
				"LAZY_LOAD" => "Y",
				"LOAD_ON_SCROLL" => "N",
				"SET_STATUS_404" => "N",
				"SHOW_404" => "N",
				"MESSAGE_404" => "",
				"COMPATIBLE_MODE" => "N",
				"DISABLE_INIT_JS_IN_COMPONENT" => "N",
				"DETAIL_ADD_PICT_PROP" => $parameters["ADD_PICT_PROP"],
				"DETAIL_OFFER_ADD_PICT_PROP" => $parameters["OFFER_ADD_PICT_PROP"],
				"DETAIL_USE_RATIO_IN_RANGES" => $arParams["USE_RATIO_IN_RANGES"],
				"DETAIL_PROPERTY_CODE" => $parameters["PROPERTY_CODE"],				
				"DETAIL_OFFERS_FIELD_CODE" => $parameters["OFFERS_FIELD_CODE"],
				"DETAIL_OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
				"DETAIL_MAIN_BLOCK_PROPERTY_CODE" => $parameters["MAIN_BLOCK_PROPERTY_CODE"],
				"DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE" => $parameters["MAIN_BLOCK_OFFERS_PROPERTY_CODE"],	
				"DETAIL_IMAGE_RESOLUTION" => $parameters["IMAGE_RESOLUTION"],				
				"DETAIL_ADD_DETAIL_TO_SLIDER" => $parameters["ADD_DETAIL_TO_SLIDER"],
				"DETAIL_DETAIL_PICTURE_MODE" => $parameters["DETAIL_PICTURE_MODE"],
				"DETAIL_SHOW_SLIDER" => $parameters["SHOW_SLIDER"],
				"DETAIL_SLIDER_INTERVAL" => $parameters["SLIDER_INTERVAL"],
				"DETAIL_SLIDER_PROGRESS" => $parameters["SLIDER_PROGRESS"],
				"USE_GIFTS_DETAIL" => $parameters["USE_GIFTS_DETAIL"],
				"GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => $parameters["GIFTS_DETAIL_PAGE_ELEMENT_COUNT"],
				"GIFTS_DETAIL_HIDE_BLOCK_TITLE" => $parameters["GIFTS_DETAIL_HIDE_BLOCK_TITLE"],
				"GIFTS_DETAIL_BLOCK_TITLE" => $parameters["GIFTS_DETAIL_BLOCK_TITLE"],
				"GIFTS_DETAIL_TEXT_LABEL_GIFT" => $parameters["GIFTS_DETAIL_TEXT_LABEL_GIFT"],
				"GIFTS_MESS_BTN_BUY" => $parameters["~GIFTS_MESS_BTN_BUY"],
				"USE_STORE" => $parameters["USE_STORE"],
				"STORE_PATH" => $parameters["STORE_PATH"],
				"STORES" => $parameters["STORES"],
				"USE_MIN_AMOUNT" => $parameters["USE_MIN_AMOUNT"],
				"USER_FIELDS" => $parameters["USER_FIELDS"],
				"FIELDS" => $parameters["FIELDS"],
				"MIN_AMOUNT" => $parameters["MIN_AMOUNT"],
				"SHOW_EMPTY_STORE" => $parameters["SHOW_EMPTY_STORE"],
				"SHOW_GENERAL_STORE_INFORMATION" => $parameters["SHOW_GENERAL_STORE_INFORMATION"],
				"MAIN_TITLE" => $parameters["~MAIN_TITLE"],
				"SET_ITEMS_COUNT" => $parameters["SET_ITEMS_COUNT"],
				"REINIT_ADD_BUY_URL_TEMPLATE" => "Y",
				"OBJECTS_USE_REVIEW" => $parameters["OBJECTS_USE_REVIEW"],
				"OBJECTS_REVIEWS_IBLOCK_ID" => $parameters["OBJECTS_REVIEWS_IBLOCK_ID"],
				"CONTACTS_IBLOCK_ID" => $parameters["CONTACTS_IBLOCK_ID"],
				"CONTACTS_USE_REVIEW" => $parameters["CONTACTS_USE_REVIEW"],
				"CONTACTS_REVIEWS_IBLOCK_ID" => $parameters["CONTACTS_REVIEWS_IBLOCK_ID"],
				"CONTACTS_REVIEWS_PAGE_LINK" => $parameters["CONTACTS_REVIEWS_PAGE_LINK"],
				"POPUP_MODE" => $parameters["POPUP_MODE"],
				"QUICK_VIEW" => isset($parameters["POPUP_MODE"]) && $parameters["POPUP_MODE"] == "Y" ? "OFF" : $arSettings["QUICK_VIEW"]
			),
			false
		);

		$content = ob_get_contents();
		ob_end_clean();

		$webpSupport = strpos($_SERVER["HTTP_ACCEPT"], "image/webp") !== false || strpos($_SERVER["HTTP_USER_AGENT"], " Chrome/") !== false ? true : false;
		
		$GLOBALS["IMG_LAZYLOAD"] = $arSettings["IMG_LAZYLOAD"] == "Y";
		$GLOBALS["IMG_WEBP"] = $arSettings["IMG_WEBP"] == "Y" && function_exists("imagewebp") && $webpSupport;
		
		if($GLOBALS["IMG_LAZYLOAD"] || $GLOBALS["IMG_WEBP"]) {
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

		if(Bitrix\Main\Loader::includeModule("iblock")) {
			Bitrix\Iblock\Component\Base::sendJsonAnswer(array(
				"content" => $content,
				"imgLazyLoad" => $GLOBALS["IMG_LAZYLOAD"],
				"imgWebp" => $GLOBALS["IMG_WEBP"]
			));
		}
	} elseif($action == "sPanelOffersOnMapRequest") {
		$siteCharset = $request->get("siteCharset") ?: SITE_CHARSET;

		$signer = new Bitrix\Main\Security\Sign\Signer;
		$parameters = unserialize(base64_decode($signer->unsign($request->get("parameters"), "catalog.element")));

		$arSettings = CEnext::GetFrontParametrsValues(SITE_ID);

		$partnersInfoMessage = htmlspecialcharsbx((string)$arSettings["PARTNERS_INFO_MESSAGE"]); //PRO_WS//
		
		$parameters["DISABLE_BASKET"] = false;
		if($arSettings["DISABLE_BASKET"] == "Y")
			$parameters["DISABLE_BASKET"] = true;
		
		$map = array();

		$jsName = $request->get("jsName");
		
		$offers = $request->get("offers");
		if(!empty($offers)) {
			$i = 0;
			foreach($offers as $key => $arOffer) {
				if(!empty($arOffer["OBJECT"]["MAP"])) {
					$arTmp = explode(",", $arOffer["OBJECT"]["MAP"]);
					
					$offerPrice = $arOffer["ITEM_PRICES"][$arOffer["ITEM_PRICE_SELECTED"]];
					$offerMeasureRatio = $arOffer["ITEM_MEASURE_RATIOS"][$arOffer["ITEM_MEASURE_RATIO_SELECTED"]]["RATIO"];
					
					//PRO_WS//
					$objectUrl   = htmlspecialcharsbx((string)$arOffer["OBJECT"]["DETAIL_PAGE_URL"]);
					$objectName = htmlspecialcharsbx((string)iconv("UTF-8", $siteCharset, (string)($arOffer["OBJECT"]["NAME"] ?? '')), ENT_NOQUOTES);
					$ratingValue = htmlspecialcharsbx((string)$arOffer["OBJECT"]["RATING_VALUE"]);
					$reviewsCount = (int)$arOffer["OBJECT"]["REVIEWS_COUNT"];
					$reviewsDeclension = htmlspecialcharsbx((string)iconv("UTF-8", $siteCharset, $arOffer["OBJECT"]["REVIEWS_DECLENSION"] ?? ''));
					//PRO_WS//
					
					$map["PLACEMARKS"][$i] = array(
						"OBJECT_ID" => $arOffer["OBJECT"]["ID"],
						"LON" => $arTmp[1],
						"LAT" => $arTmp[0],
						"PRICE" => $offerPrice["PRICE"],
						"PRINT_PRICE" => $offerPrice["PRICE"] > 0 ? iconv("UTF-8", $siteCharset, $offerPrice["PRINT_PRICE"]) : Bitrix\Main\Localization\Loc::getMessage("CT_BCE_CATALOG_PRICE_NOT_SET"),
						"CAN_BUY" => $arOffer["CAN_BUY"] == "true" ? true : false,
						"JS_NAME" => $jsName,
						//PRO_WS//
						/*
						"TEXT" => "<div class='product-item-detail-scu-item-object-marker' data-entity='sku-item' data-num='".$key."'><div class='product-item-detail-scu-item-object-marker-caption'><a target='_blank' class='product-item-detail-scu-item-object-marker-title' href='".$arOffer["OBJECT"]["DETAIL_PAGE_URL"]."'>".iconv("UTF-8", $siteCharset, $arOffer["OBJECT"]["NAME"])."</a>"
						*/
						"TEXT" => "<div class='product-item-detail-scu-item-object-marker' data-entity='sku-item' data-num='".intval($key)."'><div class='product-item-detail-scu-item-object-marker-caption'><a target='_blank' class='product-item-detail-scu-item-object-marker-title' href='".$objectUrl."'>".$objectName."</a>"
						//PRO_WS//
					);
					
					if(isset($arOffer["OBJECT"]["REVIEWS_COUNT"]) && $arOffer["OBJECT"]["REVIEWS_COUNT"] > 0) {
						//PRO_WS//
						/*
						$map["PLACEMARKS"][$i]["TEXT"] .= "<div class='product-item-detail-scu-item-object-rating'><div class='product-item-detail-scu-item-object-rating-val'".($arOffer["OBJECT"]["RATING_VALUE"] <= 4.4 ? " data-rate='".intval($arOffer["OBJECT"]["RATING_VALUE"])."'" : "").">".$arOffer["OBJECT"]["RATING_VALUE"]."</div><div class='product-item-detail-scu-item-object-rating-reviews-count'>".$arOffer["OBJECT"]["REVIEWS_COUNT"]." ".iconv("UTF-8", $siteCharset, $arOffer["OBJECT"]["REVIEWS_DECLENSION"])."</div></div>";
						*/		
						$map["PLACEMARKS"][$i]["TEXT"] .= "<div class='product-item-detail-scu-item-object-rating'><div class='product-item-detail-scu-item-object-rating-val'".($arOffer["OBJECT"]["RATING_VALUE"] <= 4.4 ? " data-rate='".intval($arOffer["OBJECT"]["RATING_VALUE"])."'" : "").">".$ratingValue."</div><div class='product-item-detail-scu-item-object-rating-reviews-count'>".$reviewsCount." ".$reviewsDeclension."</div></div>";
						//PRO_WS//
					}
					
					if(!empty($arOffer["OBJECT"]["WORKING_HOURS_TODAY"])) {
						foreach($arOffer["OBJECT"]["WORKING_HOURS_TODAY"] as $arWorkingHoursToday) {
							$map["PLACEMARKS"][$i]["TEXT"] .= "<div class='product-item-detail-scu-item-object-marker-hours'>";
							if(isset($arWorkingHoursToday["STATUS"])) {
								$map["PLACEMARKS"][$i]["TEXT"] .= "<span class='product-item-detail-scu-item-object-marker-hours-icon product-item-detail-scu-item-object-marker-hours-icon-".($arWorkingHoursToday["STATUS"] == "OPEN" ? "open" : "closed")."'></span>";
							}
							if(isset($arWorkingHoursToday["WORK_START"]) && isset($arWorkingHoursToday["WORK_END"])) {
								if($arWorkingHoursToday["WORK_START"] != $arWorkingHoursToday["WORK_END"]) {
									//PRO_WS//
									/*
									$map["PLACEMARKS"][$i]["TEXT"] .= $arWorkingHoursToday["WORK_START"]." - ".$arWorkingHoursToday["WORK_END"];
									if(isset($arWorkingHoursToday["BREAK_START"]) && isset($arWorkingHoursToday["BREAK_END"])) {
										if($arWorkingHoursToday["BREAK_START"] != $arWorkingHoursToday["BREAK_END"]) {
											$map["PLACEMARKS"][$i]["TEXT"] .= "<span class='product-item-detail-scu-item-object-marker-hours-break'>".Bitrix\Main\Localization\Loc::getMessage("CT_BCE_CATALOG_OBJECT_BREAK")." ".$arWorkingHoursToday["BREAK_START"]." ".$arWorkingHoursToday["BREAK_END"]."</span>";
										}
									}
									*/
									$map["PLACEMARKS"][$i]["TEXT"] .= htmlspecialcharsbx((string)$arWorkingHoursToday["WORK_START"])." - ".htmlspecialcharsbx((string)$arWorkingHoursToday["WORK_END"]);
									if(isset($arWorkingHoursToday["BREAK_START"]) && isset($arWorkingHoursToday["BREAK_END"])) {
										if($arWorkingHoursToday["BREAK_START"] != $arWorkingHoursToday["BREAK_END"]) {
											$map["PLACEMARKS"][$i]["TEXT"] .= "<span class='product-item-detail-scu-item-object-marker-hours-break'>".Bitrix\Main\Localization\Loc::getMessage("CT_BCE_CATALOG_OBJECT_BREAK")." ".htmlspecialcharsbx((string)$arWorkingHoursToday["BREAK_START"])." ".htmlspecialcharsbx((string)$arWorkingHoursToday["BREAK_END"])."</span>";
										}
									}
									//PRO_WS//
								} else {
									$map["PLACEMARKS"][$i]["TEXT"] .= Bitrix\Main\Localization\Loc::getMessage("CT_BCE_CATALOG_OBJECT_24_HOURS");
								}
							}  else {
								$map["PLACEMARKS"][$i]["TEXT"] .= Bitrix\Main\Localization\Loc::getMessage("CT_BCE_CATALOG_OBJECT_OFF");
							}
							$map["PLACEMARKS"][$i]["TEXT"] .= "</div>";
						}
						unset($arWorkingHoursToday);
					}

					if($offerPrice["PRICE"] > 0) {
						$map["PLACEMARKS"][$i]["TEXT"] .= "<div class='product-item-detail-price'><span class='product-item-detail-scu-item-price-current'>".iconv("UTF-8", $siteCharset, $offerPrice["PRINT_PRICE"])."</span>&nbsp;<span class='product-item-detail-price-measure'>/".iconv("UTF-8", $siteCharset, $arOffer["ITEM_MEASURE"]["TITLE"])."</span></div>";
					} else {
						$map["PLACEMARKS"][$i]["TEXT"] .= "<div class='product-item-detail-price-not-set'>".Bitrix\Main\Localization\Loc::getMessage("CT_BCE_CATALOG_PRICE_NOT_SET")."</div>";
					}

					if($parameters["SHOW_MAX_QUANTITY"] !== "N") {
						$map["PLACEMARKS"][$i]["TEXT"] .= "<div class='product-item-detail-quantity".($arOffer["CAN_BUY"] == "true" ? "" : " product-item-detail-quantity-not-avl")."'><i class='icon-".($arOffer["CAN_BUY"] == "true" ? "ok" : "close")."-b product-item-detail-quantity-icon'></i><span class='product-item-detail-quantity-val'>";

						if($arOffer["CAN_BUY"] == "true") {
							$map["PLACEMARKS"][$i]["TEXT"] .= $parameters["MESS_SHOW_MAX_QUANTITY"]."&nbsp;";
							if($offerMeasureRatio && (float)$arOffer["CATALOG_QUANTITY"] > 0 && $arOffer["CATALOG_QUANTITY_TRACE"] === "Y" && $arOffer["CATALOG_CAN_BUY_ZERO"] === "N") {
								if($parameters["SHOW_MAX_QUANTITY"] === "M") {
									if((float)$arOffer["CATALOG_QUANTITY"] / $offerMeasureRatio >= $parameters["RELATIVE_QUANTITY_FACTOR"]) {
										$map["PLACEMARKS"][$i]["TEXT"] .= $parameters["MESS_RELATIVE_QUANTITY_MANY"];
									} else {
										$map["PLACEMARKS"][$i]["TEXT"] .= $parameters["MESS_RELATIVE_QUANTITY_FEW"];
									}
								} else {
									$map["PLACEMARKS"][$i]["TEXT"] .= $arOffer["CATALOG_QUANTITY"];
								}
							}
						} else {
							$map["PLACEMARKS"][$i]["TEXT"] .= $parameters["MESS_NOT_AVAILABLE"];
						}

						$map["PLACEMARKS"][$i]["TEXT"] .= "</span></div>";
					}
					
					if(!$parameters["DISABLE_BASKET"] && $arOffer["CAN_BUY"] == "true" && $offerPrice["PRICE"] > 0 && $arOffer["PARTNERS_URL"] == "true" && !empty($arSettings["PARTNERS_INFO_MESSAGE"])) {
						//PRO_WS//
						//$map["PLACEMARKS"][$i]["TEXT"] .= "<div class='product-item-detail-info-message'>".$arSettings["PARTNERS_INFO_MESSAGE"]."</div>";
						$map["PLACEMARKS"][$i]["TEXT"] .= "<div class='product-item-detail-info-message'>".$partnersInfoMessage."</div>";
						//PRO_WS//
					}
					
					$map["PLACEMARKS"][$i]["TEXT"] .= "</div><div class='product-item-detail-scu-item-object-marker-buttons'><button type='button' class='btn btn-default' data-entity='object'><i class='icon-phone-call'></i></button>";
					
					if(!$parameters["DISABLE_BASKET"] && $arOffer["CAN_BUY"] == "true" && $offerPrice["PRICE"] > 0) {
						if($arOffer["PARTNERS_URL"] == "false") {
							if($arOffer["OBJECT"]["CALLBACK_FORM"] == "true") {
								if(in_array("ADD", $parameters["ADD_TO_BASKET_ACTION"])) {
									$map["PLACEMARKS"][$i]["TEXT"] .= "<button type='button' class='btn btn-buy' data-entity='add'><i class='icon-cart'></i></button>";
								}
								if(in_array("BUY", $parameters["ADD_TO_BASKET_ACTION"])) {
									$map["PLACEMARKS"][$i]["TEXT"] .= "<button type='button' class='btn btn-buy' data-entity='buy'><i class='icon-cart'></i></button>";
								}
							}								
						} else {
							$map["PLACEMARKS"][$i]["TEXT"] .= "<button type='button' class='btn btn-buy' data-entity='partner-link'><i class='icon-cart'></i><span></span></button>";
						}
					}
					
					$map["PLACEMARKS"][$i]["TEXT"] .= "</div></div>";

					$i++;
				}				
			}
			unset($arReviewsDeclension, $arTmp, $arOffer, $i);
		}

		if(count($map["PLACEMARKS"]) == 1) {
			$map["yandex_lat"] = $map["PLACEMARKS"][0]["LAT"];
			$map["yandex_lon"] = $map["PLACEMARKS"][0]["LON"];
			$map["yandex_scale"] = "14";
		}

		$APPLICATION->IncludeComponent("altop:map.yandex.view.elektronext", "",
			array(
				"API_KEY" => Bitrix\Main\Config\Option::get("fileman", "yandex_map_api_key"),
				"CONTROLS" => array(
					0 => "zoomControl",
				),
				"INIT_MAP_TYPE" => "map",
				"MAP_DATA" => serialize($map),
				"MAP_HEIGHT" => "100%",
				"MAP_ID" => "offers",
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
		
		$content = ob_get_contents();
		ob_end_clean();
		
		if(Bitrix\Main\Loader::includeModule("iblock")) {
			Bitrix\Iblock\Component\Base::sendJsonAnswer(array(
				"content" => $content
			));
		}
	}
}