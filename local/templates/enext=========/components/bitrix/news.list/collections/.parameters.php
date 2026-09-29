<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$boolCatalog = CModule::IncludeModule("catalog");

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["CATALOG_IBLOCK_TYPE"], "ACTIVE" => "Y"));
while($arr = $rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arPrice = array();
if($boolCatalog) {	
	$rsPrice = CCatalogGroup::GetList($v1 = "sort", $v2 = "asc");
	while($arr = $rsPrice->Fetch())
		$arPrice[$arr["NAME"]] = "[".$arr["NAME"]."] ".$arr["NAME_LANG"];
}

$arTemplateParameters = array(
	"SHOW_MIN_PRICE" => array(
		"NAME" => GetMessage("CP_BNL_SHOW_MIN_PRICE"),
		"TYPE" => "CHECKBOX",
		"REFRESH" => "Y",
		"DEFAULT" => "Y",
	)
);

if(isset($arCurrentValues["SHOW_MIN_PRICE"]) && $arCurrentValues["SHOW_MIN_PRICE"] == "Y") {
	$arTemplateParameters["CATALOG_IBLOCK_TYPE"] = array(		
		"NAME" => GetMessage("CP_BNL_CATALOG_IBLOCK_TYPE"),
		"TYPE" => "LIST",		
		"REFRESH" => "Y",
		"VALUES" => $arIBlockType,
	);
	$arTemplateParameters["CATALOG_IBLOCK_ID"] = array(		
		"NAME" => GetMessage("CP_BNL_CATALOG_IBLOCK_ID"),
		"TYPE" => "LIST",
		"REFRESH" => "Y",		
		"VALUES" => $arIBlock,
		"ADDITIONAL_VALUES" => "Y",
	);
	$arTemplateParameters["CATALOG_PRICE_CODE"] = array(		
		"NAME" => GetMessage("CP_BNL_CATALOG_PRICE_CODE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arPrice,
	);		
	$arTemplateParameters["CATALOG_PRICE_VAT_INCLUDE"] = array(		
		"NAME" => GetMessage("CP_BNL_CATALOG_PRICE_VAT_INCLUDE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	);

	if($boolCatalog) {
		if(CModule::IncludeModule("currency")) {
			$arTemplateParameters["CATALOG_CONVERT_CURRENCY"] = array(				
				"NAME" => GetMessage("CP_BNL_CATALOG_CONVERT_CURRENCY"),
				"TYPE" => "CHECKBOX",				
				"REFRESH" => "Y",
				"DEFAULT" => "N",
			);

			if(isset($arCurrentValues["CATALOG_CONVERT_CURRENCY"]) && "Y" == $arCurrentValues["CATALOG_CONVERT_CURRENCY"]) {
				$arCurrencyList = array();
				$rsCurrencies = CCurrency::GetList(($by = "SORT"), ($order = "ASC"));
				while($arCurrency = $rsCurrencies->Fetch()) {
					$arCurrencyList[$arCurrency["CURRENCY"]] = $arCurrency["CURRENCY"];
				}
				$arTemplateParameters["CATALOG_CURRENCY_ID"] = array(					
					"NAME" => GetMessage("CP_BNL_CATALOG_CURRENCY_ID"),
					"TYPE" => "LIST",
					"VALUES" => $arCurrencyList,
					"DEFAULT" => CCurrency::GetBaseCurrency(),
					"ADDITIONAL_VALUES" => "Y",
				);
			}
		}
	}
}