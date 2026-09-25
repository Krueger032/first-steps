<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$arTemplateParameters = array(
	"SHOW_ACCOUNT_PAGE" => array(
		"HIDDEN" => "Y"
	),
	"SHOW_ORDER_PAGE" => array(
		"HIDDEN" => "Y"
	),
	"SHOW_PRIVATE_PAGE" => array(
		"HIDDEN" => "Y"
	),
	"SHOW_PROFILE_PAGE" => array(
		"HIDDEN" => "Y"
	),
	"SHOW_SUBSCRIBE_PAGE" => array(
		"HIDDEN" => "Y"
	),	
	"SHOW_CONTACT_PAGE" => array(
		"HIDDEN" => "Y"
	),
	"SHOW_BASKET_PAGE" => array(
		"HIDDEN" => "Y"
	),
	"PATH_TO_CONTACT" => array(
		"HIDDEN" => "Y"
	),
	"CUSTOM_PAGES" => array(
		"HIDDEN" => "Y"
	),
	"SEF_MODE" => array(
		"index" => array(
			"NAME" => Loc::getMessage("SPS_MAIN_PERSONAL"),
			"DEFAULT" => "index.php",
			"VARIABLES" => array()
		),
		"orders" => array(
			"NAME" => Loc::getMessage("SPS_GROUP_ORDER"),
			"DEFAULT" => "orders/",
			"VARIABLES" => array("ID")
		),
		"account" => array(
			"NAME" => Loc::getMessage("SPS_GROUP_ACCOUNT"),
			"DEFAULT" => "",
			"VARIABLES" => array("ID"),
			"HIDDEN" => "Y"
		),		
		"subscribe" => array(
			"NAME" => Loc::getMessage("SPS_GROUP_SUBSCRIBE"),
			"DEFAULT" => "subscribe/",
			"VARIABLES" => array("ID")
		),
		"profile" => array(
			"NAME" => Loc::getMessage("SPS_GROUP_PROFILE_LIST"),
			"DEFAULT" => "private/profiles/",
			"VARIABLES" => array("ID")
		),
		"profile_detail" => array(
			"NAME" => Loc::getMessage("SPS_GROUP_PROFILE"),
			"DEFAULT" => "private/profiles/#ID#/",
			"VARIABLES" => array("ID")
		),
		"private" => array(
			"NAME" => Loc::getMessage("SPS_GROUP_PRIVATE"),
			"DEFAULT" => "private/",
			"VARIABLES" => array("ID")
		),
		"order_detail" => array(
			"NAME" => Loc::getMessage("SPS_DETAIL_DESC"),
			"DEFAULT" => "orders/#ID#/",
			"VARIABLES" => array("ID")
		),			
		"order_cancel" => array(
			"NAME" => Loc::getMessage("SPS_CANCEL_ORDER_DESC"),
			"DEFAULT" => "cancel/#ID#/",
			"VARIABLES" => array("ID")
		)
	)
);

if($arCurrentValues["SHOW_ACCOUNT_PAGE"] !== "N" && CBXFeatures::IsFeatureEnabled("SaleAccounts")) {
	$arTemplateParameters["SHOW_ACCOUNT_COMPONENT"] = array(
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["SHOW_ACCOUNT_PAY_COMPONENT"] = array(
		"HIDDEN" => "Y"
	);
	if($arCurrentValues["SHOW_ACCOUNT_PAY_COMPONENT"] !== "N") {
		$arTemplateParameters["ACCOUNT_PAYMENT_SELL_CURRENCY"] = array(
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["ACCOUNT_PAYMENT_PERSON_TYPE"] = array(
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["ACCOUNT_PAYMENT_ELIMINATED_PAY_SYSTEMS"] = array(
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["ACCOUNT_PAYMENT_SELL_SHOW_FIXED_VALUES"] = array(
			"HIDDEN" => "Y"
		);
		if($arCurrentValues["SELL_SHOW_FIXED_VALUES"] != "N") {
			$arTemplateParameters["ACCOUNT_PAYMENT_SELL_TOTAL"] = array(
				"HIDDEN" => "Y"
			);
		}
		$arTemplateParameters["ACCOUNT_PAYMENT_SELL_USER_INPUT"] = array(
			"HIDDEN" => "Y"
		);
	}
}

if(CModule::IncludeModule("sale")) {
	$dbPropsGroup = CSaleOrderPropsGroup::GetList(array("SORT" => "ASC"), array(), false, false, array());
	while($arPropGroup = $dbPropsGroup->Fetch()) {
		$arPersonType = CSalePersonType::GetByID($arPropGroup["PERSON_TYPE_ID"]);
		$arTemplateParameters["ICON_PROPS_GROUP_".$arPropGroup["ID"]] = array(
			"NAME" => Loc::getMessage("SPS_ICON_PROPS_GROUP").$arPropGroup["NAME"]." (".$arPersonType["NAME"]." ".$arPersonType["LID"].")",
			"TYPE" => "STRING",
			"DEFAULT" => ""
		);
	}
	unset($arPropGroup, $dbPropsGroup, $arPersonType);
}?>