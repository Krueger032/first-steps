<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

$this->addExternalCss(SITE_TEMPLATE_PATH."/components/bitrix/catalog.product.subscribe/.default/style.min.css");

$templateLibrary = array("popup", "ajax", "fx");
$currencyList = "";
if(!empty($arResult["CURRENCIES"])) {
	$templateLibrary[] = "currency";
	$currencyList = CUtil::PhpToJSObject($arResult["CURRENCIES"], false, true, true);
}

$templateData = array(
	"TEMPLATE_LIBRARY" => $templateLibrary,
	"CURRENCIES" => $currencyList
);
unset($currencyList, $templateLibrary);

$elementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$elementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$elementDeleteParams = array("CONFIRM" => GetMessage("CT_SPGS_TPL_ELEMENT_DELETE_CONFIRM"));

$arParams["MESS_BTN_BUY"] = $arParams["MESS_BTN_BUY"] ?: Loc::getMessage("CT_SPGS_TPL_MESS_BTN_BUY");
$arParams["GIFTS_MESS_BTN_BUY"] = $arParams["GIFTS_MESS_BTN_BUY"] ?: Loc::getMessage("CT_SPGS_TPL_GIFTS_MESS_BTN_BUY");
$arParams["MESS_BTN_DETAIL"] = $arParams["MESS_BTN_DETAIL"] ?: Loc::getMessage("CT_SPGS_TPL_MESS_BTN_DETAIL");
$arParams["MESS_BTN_DELAY"] = $arParams["MESS_BTN_DELAY"] ?: Loc::getMessage("CT_SPGS_TPL_MESS_BTN_DELAY");
$arParams["MESS_BTN_COMPARE"] = $arParams["MESS_BTN_COMPARE"] ?: Loc::getMessage("CT_SPGS_TPL_MESS_BTN_COMPARE");
$arParams["MESS_BTN_SUBSCRIBE"] = $arParams["MESS_BTN_SUBSCRIBE"] ?: Loc::getMessage("CT_SPGS_TPL_MESS_BTN_SUBSCRIBE");
$arParams["MESS_BTN_ADD_TO_BASKET"] = $arParams["MESS_BTN_ADD_TO_BASKET"] ?: Loc::getMessage("CT_SPGS_TPL_MESS_BTN_ADD_TO_BASKET");
$arParams["GIFTS_MESS_BTN_ADD_TO_BASKET"] = $arParams["GIFTS_MESS_BTN_ADD_TO_BASKET"] ?: Loc::getMessage("CT_SPGS_TPL_GIFTS_MESS_BTN_ADD_TO_BASKET");
$arParams["MESS_NOT_AVAILABLE"] = $arParams["MESS_NOT_AVAILABLE"] ?: Loc::getMessage("CT_SPGS_TPL_MESS_PRODUCT_NOT_AVAILABLE");
$arParams["MESS_SHOW_MAX_QUANTITY"] = $arParams["MESS_SHOW_MAX_QUANTITY"] ?: Loc::getMessage("CT_SPGS_CATALOG_SHOW_MAX_QUANTITY");
$arParams["MESS_RELATIVE_QUANTITY_MANY"] = $arParams["MESS_RELATIVE_QUANTITY_MANY"] ?: Loc::getMessage("CT_SPGS_CATALOG_RELATIVE_QUANTITY_MANY");
$arParams["MESS_RELATIVE_QUANTITY_FEW"] = $arParams["MESS_RELATIVE_QUANTITY_FEW"] ?: Loc::getMessage("CT_SPGS_CATALOG_RELATIVE_QUANTITY_FEW");

$arParams["TEXT_LABEL_GIFT"] = $arParams["TEXT_LABEL_GIFT"] ?: Loc::getMessage("CT_SPGS_TPL_TEXT_LABEL_GIFT");

$generalParams = array(
	"~ADD_URL_TEMPLATE" => $arResult["~ADD_URL_TEMPLATE"],
	"~BUY_URL_TEMPLATE" => $arResult["~BUY_URL_TEMPLATE"],
	"~COMPARE_URL_TEMPLATE" => $arResult["~COMPARE_URL_TEMPLATE"],
	"~COMPARE_DELETE_URL_TEMPLATE" => $arResult["~COMPARE_DELETE_URL_TEMPLATE"]
);

$obName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $this->GetEditAreaId($this->randString()));
$containerName = "sale-products-gift-section-container";?>

<div class="row sale-products-gift card <?=strtolower($arParams['PRODUCTS_LIST_VIEW_MOBILE'])?>" data-entity="<?=$containerName?>">
	<?if(!empty($arResult["ITEMS"]) && !empty($arResult["ITEM_ROWS"])) {
		$areaIds = array();
		foreach($arResult["ITEMS"] as &$item) {
			$uniqueId = $item["ID"]."_".md5($this->randString().$component->getAction());
			$areaIds[$item["ID"]] = $this->GetEditAreaId($uniqueId);
			$this->AddEditAction($uniqueId, $item["EDIT_LINK"], $elementEdit);
			$this->AddDeleteAction($uniqueId, $item["DELETE_LINK"], $elementDelete, $elementDeleteParams);
		}
		unset($item);?>
		<!-- items-container -->
		<?foreach($arResult["ITEM_ROWS"] as $rowData) {
			$rowItems = array_splice($arResult["ITEMS"], 0, $rowData["COUNT"]);
			foreach($rowItems as $item) {?>
				<div class="<?=($arParams['PRODUCTS_LIST_VIEW_MOBILE'] == 'TWO_IN_ROW' ? 'col-xs-6' : 'col-xs-12').' '.($rowData['VARIANT'] == 2 ? 'col-md-4' : 'col-md-3')?>" data-entity="item-col">
					<?$APPLICATION->IncludeComponent("bitrix:catalog.item", "gift",
						array(
							"RESULT" => array(
								"ITEM" => $item,
								"AREA_ID" => $areaIds[$item["ID"]],
								"TYPE" => "CARD"
							),
							"PARAMS" => $arParams + $generalParams + array("SKU_PROPS" => $arResult["SKU_PROPS"][$item["IBLOCK_ID"]])
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);?>
				</div>
			<?}
			unset($item);
		}
		unset($rowItems, $rowData, $generalParams);?>
		<!-- items-container -->
	<?} else {
		//load css for bigData/deferred load
		$APPLICATION->IncludeComponent("bitrix:catalog.item", "",
			array(),
			$component,
			array("HIDE_ICONS" => "Y")
		);
	}?>
</div>

<?$signer = new \Bitrix\Main\Security\Sign\Signer;
$signedTemplate = $signer->sign($templateName, "sale.products.gift.section");
$signedParams = $signer->sign(base64_encode(serialize($arResult["ORIGINAL_PARAMETERS"])), "sale.products.gift.section");?>

<script type="text/javascript">
	BX.message({
		SPG_BASKET_URL: '<?=$arParams["BASKET_URL"]?>',
		SPG_ADD_BASKET_MESSAGE: '<?=($arParams["ADD_TO_BASKET_ACTION"] == "BUY" ? $arParams["GIFTS_MESS_BTN_BUY"] : $arParams["GIFTS_MESS_BTN_ADD_TO_BASKET"])?>',
		SPG_ADD_BASKET_OK_MESSAGE: '<?=GetMessageJS("CT_SPGS_TPL_MESS_BTN_ADD_TO_BASKET_OK")?>',
		SPG_BTN_MESSAGE_DETAIL_ITEM: '<?=GetMessageJS("CT_SPGS_TPL_MESS_BTN_DETAIL_ITEM")?>'
	});	
	var <?=$obName?> = new JCSaleProductsGiftSectionComponent({
		siteId: '<?=CUtil::JSEscape($component->getSiteId())?>',
		componentPath: '<?=CUtil::JSEscape($componentPath)?>',
		deferredLoad: true,
		initiallyShowHeader: '<?=!empty($arResult["ITEM_ROWS"])?>',				
		template: '<?=CUtil::JSEscape($signedTemplate)?>',
		parameters: '<?=CUtil::JSEscape($signedParams)?>',
		container: '<?=$containerName?>'
	});
</script>