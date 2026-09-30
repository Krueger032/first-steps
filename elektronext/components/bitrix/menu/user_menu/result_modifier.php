<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader,
	Bitrix\Sale,
	Bitrix\Catalog;

if(empty($arResult))
	return;

global $arSettings, $USER;

foreach($arResult as &$arItem) {
	//ORDERS_COUNT//
	if($arItem["PARAMS"]["CODE"] == "ORDERS" && Loader::includeModule("sale")) {
		$arItem["COUNT"] = 0;
		$arFilter = array(
			"select" => array("ID", "STATUS_ID"),
			"filter" => array(
				"USER_ID" => $USER->GetID(),
				"LID" => SITE_ID,
				"CANCELED" => "N"
			)
		);
		$dbOrders = Sale\Order::getList($arFilter);
		while($arOrder = $dbOrders->fetch()) {
			if($arOrder["STATUS_ID"] != "F")
				$arItem["COUNT"]++;
		}
		unset($arOrder, $dbOrders, $arFilter);
	//BASKET_ITEMS_COUNT//
	} elseif($arItem["PARAMS"]["CODE"] == "BASKET" && $arSettings["DISABLE_BASKET"]["VALUE"] != "Y" && Loader::includeModule("sale")) {
		$arItem["COUNT"] = (int)Sale\BasketComponentHelper::getFUserBasketQuantity(Sale\Fuser::getId(true), SITE_ID);
	//SUBSCRIBE_ITEMS_COUNT//
	} elseif($arItem["PARAMS"]["CODE"] == "SUBSCRIBE" && Loader::includeModule("catalog")) {
		$arFilter = array(
			"USER_ID" => $USER->GetID(),
			"=SITE_ID" => SITE_ID,
			array(
				"LOGIC" => "OR",
				array("=DATE_TO" => false),
				array(">DATE_TO" => date($DB->dateFormatToPHP(\CLang::getDateFormat("FULL")), time()))
			)
		);
		$countQuery = Catalog\SubscribeTable::getList(
			array(		
				"filter" => $arFilter,
				"select" => array(new Bitrix\Main\Entity\ExpressionField("CNT", "COUNT(1)"))
			)
		);
		$totalCount = $countQuery->fetch();
		$arItem["COUNT"] = (int)$totalCount["CNT"];
		unset($totalCount, $countQuery, $arFilter);
	}
}
unset($arItem);