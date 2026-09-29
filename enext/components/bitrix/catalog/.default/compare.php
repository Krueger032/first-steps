<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);?>
		
		</div>
	</div>
</div>
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12">

<?$APPLICATION->IncludeComponent("altop:catalog.compare.result", "",
	array(
		"NAME" => $arParams["COMPARE_NAME"],
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"FIELD_CODE" => $arParams["COMPARE_FIELD_CODE"],
		"PROPERTY_CODE" => $arParams["COMPARE_PROPERTY_CODE"],
		"OFFERS_FIELD_CODE" => $arParams["COMPARE_OFFERS_FIELD_CODE"],
		"OFFERS_PROPERTY_CODE" => $arParams["COMPARE_OFFERS_PROPERTY_CODE"],
		"ELEMENT_SORT_FIELD" => $arParams["COMPARE_ELEMENT_SORT_FIELD"],
		"ELEMENT_SORT_ORDER" => $arParams["COMPARE_ELEMENT_SORT_ORDER"],
		"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
		"BASKET_URL" => $arParams["BASKET_URL"],
		"ACTION_VARIABLE" => (!empty($arParams["ACTION_VARIABLE"]) ? $arParams["ACTION_VARIABLE"] : "action")."_ccr",
		"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
		"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
		"PRICE_CODE" => $arParams["~PRICE_CODE"],
		"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
		"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
		"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
		"CURRENCY_ID" => $arParams["CURRENCY_ID"],
		"USE_REVIEW" => $arParams["USE_REVIEW"],
		"REVIEWS_IBLOCK_TYPE" => $arParams["REVIEWS_IBLOCK_TYPE"],
		"REVIEWS_IBLOCK_ID" => $arParams["REVIEWS_IBLOCK_ID"]
	),
	$component,
	array("HIDE_ICONS" => "Y")
);?>

<?//BREADCRUMBS//
$APPLICATION->AddChainItem(Loc::getMessage("CATALOG_CATALOG"), $arResult["FOLDER"]);

//TITLE//
$APPLICATION->SetTitle(Loc::getMessage("CATALOG_COMPARE"));