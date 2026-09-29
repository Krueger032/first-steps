<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader,
	Bitrix\Main\Localization\Loc;

$generalParams = array(
	"PATH_TO_LIST" => $arResult["PATH_TO_PROFILE"],
	"PATH_TO_DETAIL" => $arResult["PATH_TO_PROFILE_DETAIL"],
	"SET_TITLE" => $arParams["SET_TITLE"],
	"USE_AJAX_LOCATIONS" => $arParams["USE_AJAX_LOCATIONS_PROFILE"],
	"COMPATIBLE_LOCATION_MODE" => $arParams["COMPATIBLE_LOCATION_MODE_PROFILE"],		
	"ID" => $arResult["VARIABLES"]["ID"]
);

if(Loader::includeModule("sale")) {
	$rsPropsGroup = CSaleOrderPropsGroup::GetList(array("SORT" => "ASC"), array(), false, false, array());
	while($arPropGroup = $rsPropsGroup->Fetch()) {
		$generalParams["ICON_PROPS_GROUP_".$arPropGroup["ID"]] = $arParams["ICON_PROPS_GROUP_".$arPropGroup["ID"]];
	}
	unset($arPropGroup, $rsPropsGroup);
}?>

<?$APPLICATION->IncludeComponent("bitrix:sale.personal.profile.detail", "",
	$generalParams,
	$component
);?>

<?//BREADCRUMBS//
$APPLICATION->AddChainItem(Loc::getMessage("SPS_CHAIN_PROFILE_LIST"), $arResult["PATH_TO_PROFILE"]);
$APPLICATION->AddChainItem(Loc::getMessage("SPS_TITLE_PROFILE", array("#ID#" => $arResult["VARIABLES"]["ID"])));