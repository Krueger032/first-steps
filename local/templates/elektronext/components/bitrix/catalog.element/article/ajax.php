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
	$action = $request->getPost("action");
	if($action == "ADD_TO_DELAY" || $action == "DELETE_FROM_DELAY") {
		$productId = intval($request->getPost("id"));
		$qnt = doubleval($request->getPost("quantity")) ?: 1;
		
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
	} elseif($action == "checkComparedDelayedBuyedAdded") {
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
		if($checkDelayed || $checkBuyedAdded) {
			if($checkDelayed) {
				$delayed = false;			
				$delayedIds = array();
			}

			if($checkBuyedAdded) {
				$buyedAdded = false;
				$buyedAddedIds = array();
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
					array("ID", "PRODUCT_ID", "DELAY", "TYPE", "SET_PARENT_ID")
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
								}					
								if($checkDelayed && $arItem["DELAY"] == "Y")
									$delayedIds[] = $arOffer["ID"];
								elseif($checkBuyedAdded && $arItem["DELAY"] == "N")
									$buyedAddedIds[] = $arOffer["ID"];
							}
						}
						unset($key, $arOffer);
					} elseif($productId == $arItem["PRODUCT_ID"]) {
						if($checkDelayed && $arItem["DELAY"] == "Y")
							$delayed = true;
						elseif($checkBuyedAdded && $arItem["DELAY"] == "N")
							$buyedAdded = true;
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
		}
		
		echo Bitrix\Main\Web\Json::encode($result);
	} elseif($action == "objectWorkingHoursToday") {
		$timezone = $request->get("timezone");
		if(!empty($timezone))
			$currentDateTime = strtotime(gmdate("Y-m-d H:i", strtotime($timezone." hours")));
		else
			$currentDateTime = time() + CTimeZone::GetOffset();	
		
		$workingHours = $request->get("workingHours");
		$siteCharset = $request->get("siteCharset") ?: SITE_CHARSET;
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

		echo Bitrix\Main\Web\Json::encode(array(
			"today" => !empty($arWorkingHoursToday) ? $arWorkingHoursToday : false
		));
	} elseif($action == "partnerSiteRedirect") {
		$productId = intval($request->getPost("productId"));
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