<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if($arParams["ORDER_DISALLOW_CANCEL"] == "Y")
	LocalRedirect($arResult["PATH_TO_ORDERS"]);?>

<?$APPLICATION->IncludeComponent("bitrix:sale.personal.order.cancel", "",
	array(
		"PATH_TO_LIST" => $arResult["PATH_TO_ORDERS"],
		"PATH_TO_DETAIL" => $arResult["PATH_TO_ORDER_DETAIL"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"ID" => $arResult["VARIABLES"]["ID"],
	),
	$component
);?>

<?//BREADCRUMBS//
$APPLICATION->AddChainItem(Loc::getMessage("SPS_CHAIN_ORDERS"), $arResult["PATH_TO_ORDERS"]);
$APPLICATION->AddChainItem(Loc::getMessage("SPS_CHAIN_ORDER_DETAIL", array("#ID#" => $arResult["VARIABLES"]["ID"])));