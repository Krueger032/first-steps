<?define("STOP_STATISTICS", true);
define("NOT_CHECK_PERMISSIONS", true);

$siteId = isset($_REQUEST["siteId"]) && is_string($_REQUEST["siteId"]) ? $_REQUEST["siteId"] : "";
$siteId = substr(preg_replace("/[^a-z0-9_]/i", "", $siteId), 0, 2);
if(!empty($siteId) && is_string($siteId)) {
	define("SITE_ID", $siteId);
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

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
				array("ID", "DELAY", "CAN_BUY")
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
					foreach($offers as $key => $arOffer) {
						if(array_key_exists($arOffer["ID"], $_SESSION[$compareName][$iblockId]["ITEMS"])) {
							if(($offersView == "PROPS" || $offersView == "DROPDOWN_LIST") && $key == $offerNum) {
								$compared = true;
							}
							$comparedIds[] = $arOffer["ID"];
						}
					}
					unset($key, $arOffer);
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
		if($checkDelayed || $checkBuyedAdded) {
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
						foreach($offers as $key => $arOffer) {
							if($arOffer["ID"] == $arItem["PRODUCT_ID"]) {
								if(($offersView == "PROPS" || $offersView == "DROPDOWN_LIST") && $key == $offerNum) {
									if($checkDelayed && $arItem["DELAY"] == "Y")
										$delayed = true;
									elseif($checkBuyedAdded && $arItem["DELAY"] == "N")
										$buyedAdded = true;

									if($checkQuantity && $arItem["DELAY"] == "N")
										$quantity = $arItem["QUANTITY"];
								}					
								if($checkDelayed && $arItem["DELAY"] == "Y")
									$delayedIds[] = $arOffer["ID"];
								elseif($checkBuyedAdded && $arItem["DELAY"] == "N")
									$buyedAddedIds[] = $arOffer["ID"];

								if($checkQuantity && $arItem["DELAY"] == "N")
									$quantityIds[$arOffer["ID"]] = $arItem["QUANTITY"];
							}
						}
						unset($key, $arOffer);
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
		$result = array();

		$productId = intval($request->get("productId"));
		$productIblockId = intval($request->get("productIblockId"));

		$offers = $request->get("offers");
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
			
			foreach($offers as $key => $arOffer) {
				if(array_key_exists($arOffer["ID"], $offersList))
					$parserLink = $offersList[$arOffer["ID"]]["PARSER_LINK"];
				
				if(array_key_exists($arOffer["OBJECT_ID"], $objectsList)) {
					$parserTag = $objectsList[$arOffer["OBJECT_ID"]]["PARSER_TAG"];
					$parserClass = $objectsList[$arOffer["OBJECT_ID"]]["PARSER_CLASS"];
				}

				if(!empty($parserLink) && !empty($parserTag) && !empty($parserClass)) {
					curl_setopt($ch, CURLOPT_URL, $parserLink);
					
					$page = curl_exec($ch);
					
					if(!empty($page)) {
						$parsedPrice = 0;
						if(preg_match("/<span[^>]+itemprop=\"price\"[^>]+content=\"(.*?)\"/is", $page, $matches)) {
							$parsedPrice = (float)$matches[1];
						} elseif(preg_match("/<".$parserTag."[^>]+".$parserClass."[^>]+>(.*?)<\/[^>]+>/is", $page, $matches)) {
							$parsedPrice = (float)str_replace(",", ".", preg_replace("/[^0-9\,\.]/", "", strip_tags($matches[1])));
						}

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
	}
}