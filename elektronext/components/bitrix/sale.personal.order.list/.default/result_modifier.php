<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

global $USER;
if(!$USER->IsAuthorized())
	return;

$arResult["COUNT_ORDERS"] = array(
	"order" => 0,
	"history" => 0,
	"cancel" => 0
);

if(Loader::includeModule("sale")) {
	$rsOrders = CSaleOrder::GetList(array(), array("USER_ID" => $USER->GetID()));
	while($arOrder = $rsOrders->Fetch()) {
		if($arOrder["CANCELED"] == "Y")
			$arResult["COUNT_ORDERS"]["cancel"]++;
		elseif(in_array($arOrder["STATUS_ID"], $arParams["HISTORIC_STATUSES"]))
			$arResult["COUNT_ORDERS"]["history"]++;
		else
			$arResult["COUNT_ORDERS"]["order"]++;
	}
	unset($arOrder, $rsOrders);
}

if(count($arResult["ORDERS"]) < 1)
	return;

if(Loader::includeModule("iblock") && Loader::includeModule("catalog")) {
	foreach($arResult["ORDERS"] as $key => $arOrder) {
		$arBasketItem = reset($arOrder["BASKET_ITEMS"]);
		
		$rsElement = CIBlockElement::GetList(array(), array("ID" => $arBasketItem["PRODUCT_ID"]), false, false, array("ID", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE"));
		if($arElement = $rsElement->Fetch()) {
			if($arElement["PREVIEW_PICTURE"] > 0) {
				$arResult["ORDERS"][$key]["PREVIEW_PICTURE"] = CFile::GetFileArray($arElement["PREVIEW_PICTURE"]);
			} else {
				$arResult["ORDERS"][$key]["PREVIEW_PICTURE"] = array(
					"SRC" => SITE_TEMPLATE_PATH."/images/no_photo.png",
					"WIDTH" => 222,
					"HEIGHT" => 222
				);

				$mxResult = CCatalogSku::GetProductInfo($arElement["ID"]);
				if(is_array($mxResult)) {
					$rsItem = CIBlockElement::GetList(array(), array("ID" => $mxResult["ID"]), false, false, array("ID", "IBLOCK_ID", "PREVIEW_PICTURE"));
					if($arItem = $rsItem->Fetch()) {
						if($arItem["PREVIEW_PICTURE"] > 0)
							$arResult["ORDERS"][$key]["PREVIEW_PICTURE"] = CFile::GetFileArray($arItem["PREVIEW_PICTURE"]);
					}
					unset($arItem, $rsItem);
				}
				unset($mxResult);
			}
			$arResult["ORDERS"][$key]["PREVIEW_PICTURE"]["ALT"] = $arElement["NAME"];
		}
		unset($arElement, $rsElement, $arBasketItem);
	}
	unset($arOrder, $key);
}