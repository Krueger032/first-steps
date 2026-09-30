<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

global $USER;
if(!$USER->IsAuthorized() && $arParams["GUEST_MODE"] !== "Y")
	return;

if(!Loader::includeModule("iblock") || !Loader::includeModule("catalog"))
	return;

$sumPrice = 0;
foreach($arResult["BASKET"] as $key => $arBasketItem) {
	$sumPrice += $arBasketItem["BASE_PRICE"] * $arBasketItem["QUANTITY"];	
	
	$productIblockId = CIBlockElement::GetById($arBasketItem["PRODUCT_ID"])->GetNext();
	
	$obProductArticle = CIBlockElement::GetProperty($productIblockId["IBLOCK_ID"], $arBasketItem["PRODUCT_ID"], array(), array("CODE" => "ARTNUMBER"));
	if($arProductArticle = $obProductArticle->Fetch())
		$arResult["BASKET"][$key]["ARTNUMBER"] = $arProductArticle["VALUE"];
	unset($arProductArticle, $obProductArticle, $productIblockId);
}
unset($key, $arBasketItem);

$arResult["PRICE_NO_DISCOUNT_FORMATED"] = SaleFormatCurrency($sumPrice, $arResult["CURRENCY"]);
$arResult["DISCOUNT_PRICE"] = $sumPrice - $arResult["PRODUCT_SUM"];
$arResult["DISCOUNT_PRICE_FORMATED"] = SaleFormatCurrency($arResult["DISCOUNT_PRICE"], $arResult["CURRENCY"]);

unset($sumPrice);