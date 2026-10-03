<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;?>

<?$APPLICATION->IncludeComponent("bitrix:catalog.product.subscribe.list", "",
	array(
		"SET_TITLE" => "N"
	),
	$component
);?>

<?//BREADCRUMBS//
$APPLICATION->AddChainItem(Loc::getMessage("SPS_CHAIN_SUBSCRIBE"));

//TITLE//
if($arParams["SET_TITLE"] == "Y")
	$APPLICATION->SetTitle(Loc::getMessage("SPS_TITLE_SUBSCRIBE"));