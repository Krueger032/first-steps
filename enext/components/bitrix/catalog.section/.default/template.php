<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

$this->addExternalCss(SITE_TEMPLATE_PATH."/components/bitrix/catalog.product.subscribe/.default/style.css");

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

if(!empty($arResult["NAV_RESULT"])) {
	$navParams =  array(
		"NavPageCount" => $arResult["NAV_RESULT"]->NavPageCount,
		"NavPageNomer" => $arResult["NAV_RESULT"]->NavPageNomer,
		"NavNum" => $arResult["NAV_RESULT"]->NavNum
	);
} else {
	$navParams = array(
		"NavPageCount" => 1,
		"NavPageNomer" => 1,
		"NavNum" => $this->randString()
	);
}

$showBottomPager = false;
$showLazyLoad = false;
if($arParams["PAGE_ELEMENT_COUNT"] > 0 && $navParams["NavPageCount"] > 1) {
	$showBottomPager = $arParams["DISPLAY_BOTTOM_PAGER"];
	$showLazyLoad = $arParams["LAZY_LOAD"] === "Y" && $navParams["NavPageNomer"] != $navParams["NavPageCount"];
}

$elementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$elementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$elementDeleteParams = array("CONFIRM" => GetMessage("CT_BCS_TPL_ELEMENT_DELETE_CONFIRM"));

$arParams["MESS_BTN_BUY"] = $arParams["MESS_BTN_BUY"] ?: Loc::getMessage("CT_BCS_TPL_MESS_BTN_BUY");
$arParams["MESS_BTN_DETAIL"] = $arParams["MESS_BTN_DETAIL"] ?: Loc::getMessage("CT_BCS_TPL_MESS_BTN_DETAIL");
$arParams["MESS_BTN_DELAY"] = $arParams["MESS_BTN_DELAY"] ?: Loc::getMessage("CT_BCS_TPL_MESS_BTN_DELAY");
$arParams["MESS_BTN_COMPARE"] = $arParams["MESS_BTN_COMPARE"] ?: Loc::getMessage("CT_BCS_TPL_MESS_BTN_COMPARE");
$arParams["MESS_BTN_SUBSCRIBE"] = $arParams["MESS_BTN_SUBSCRIBE"] ?: Loc::getMessage("CT_BCS_TPL_MESS_BTN_SUBSCRIBE");
$arParams["MESS_BTN_ADD_TO_BASKET"] = $arParams["MESS_BTN_ADD_TO_BASKET"] ?: Loc::getMessage("CT_BCS_TPL_MESS_BTN_ADD_TO_BASKET");
$arParams["MESS_NOT_AVAILABLE"] = $arParams["MESS_NOT_AVAILABLE"] ?: Loc::getMessage("CT_BCS_TPL_MESS_PRODUCT_NOT_AVAILABLE");
$arParams["MESS_SHOW_MAX_QUANTITY"] = $arParams["MESS_SHOW_MAX_QUANTITY"] ?: Loc::getMessage("CT_BCS_CATALOG_SHOW_MAX_QUANTITY");
$arParams["MESS_RELATIVE_QUANTITY_MANY"] = $arParams["MESS_RELATIVE_QUANTITY_MANY"] ?: Loc::getMessage("CT_BCS_CATALOG_RELATIVE_QUANTITY_MANY");
$arParams["MESS_RELATIVE_QUANTITY_FEW"] = $arParams["MESS_RELATIVE_QUANTITY_FEW"] ?: Loc::getMessage("CT_BCS_CATALOG_RELATIVE_QUANTITY_FEW");

$arParams["MESS_BTN_LAZY_LOAD"] = $arParams["MESS_BTN_LAZY_LOAD"] ?: Loc::getMessage("CT_BCS_TPL_MESS_BTN_LAZY_LOAD");

if(!empty($GLOBALS[$arParams["FILTER_NAME"]]))
	$arParams["GLOBAL_FILTER"] = $GLOBALS[$arParams["FILTER_NAME"]];

$generalParams = array(
	"~ADD_URL_TEMPLATE" => $arResult["~ADD_URL_TEMPLATE"],
	"~BUY_URL_TEMPLATE" => $arResult["~BUY_URL_TEMPLATE"],
	"~COMPARE_URL_TEMPLATE" => $arResult["~COMPARE_URL_TEMPLATE"],
	"~COMPARE_DELETE_URL_TEMPLATE" => $arResult["~COMPARE_DELETE_URL_TEMPLATE"]
);

$prefix = isset($arParams["POPUP_MODE"]) && $arParams["POPUP_MODE"] == "Y" ? "popup-" : "";

$obName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $this->GetEditAreaId($navParams["NavNum"]));
$containerName = $prefix."container-".$navParams["NavNum"];?>

<div class="row catalog-section <?=strtolower($arParams['PRODUCTS_VIEW']).($arParams['PRODUCTS_VIEW'] == 'CARD' ? ' '.strtolower($arParams['PRODUCTS_LIST_VIEW_MOBILE']) : '')?>" data-entity="<?=$containerName?>">
	<?if(!empty($arResult["ITEMS"]) && !empty($arResult["ITEM_ROWS"])) {
		$areaIds = array();
		foreach($arResult["ITEMS"] as $item) {
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
				<div class="<?=($arParams['PRODUCTS_VIEW'] != 'CARD' ? 'col-xs-6 col-md-12' : (($arParams['PRODUCTS_LIST_VIEW_MOBILE'] == 'TWO_IN_ROW' ? 'col-xs-6' : 'col-xs-12').' '.($rowData['VARIANT'] == 2 ? 'col-md-4' : 'col-md-3')))?>" data-entity="item-col">
					<?$APPLICATION->IncludeComponent("bitrix:catalog.item", "",
						array(
							"RESULT" => array(
								"ITEM" => $item,
								"AREA_ID" => $areaIds[$item["ID"]],
								"TYPE" => $arParams["PRODUCTS_VIEW"]
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

<?if($showLazyLoad) {?>
	<div class="catalog-section-more" data-entity="<?=$prefix?>catalog-show-more-container">
		<button type="button" class="btn btn-more" data-use="<?=$prefix?>show-more-<?=$navParams['NavNum']?>"><?=$arParams["MESS_BTN_LAZY_LOAD"]?></button>
	</div>
<?}

if($showBottomPager) {?>
	<div class="catalog-section-pagination" data-pagination-num="<?=$navParams['NavNum']?>">
		<!-- pagination-container -->
		<?=$arResult["NAV_STRING"]?>
		<!-- pagination-container -->
	</div>
<?}

$signer = new \Bitrix\Main\Security\Sign\Signer;
$signedTemplate = $signer->sign($templateName, "catalog.section");
$signedParams = $signer->sign(base64_encode(serialize($arResult["ORIGINAL_PARAMETERS"])), "catalog.section");?>

<script type="text/javascript">
	BX.message({
		SQ_M_MESSAGE: '<?=GetMessageJS("CT_BCS_CATALOG_MEASURE_SQ_M")?>',
		ECONOMY_INFO_MESSAGE: '<?=GetMessageJS("CT_BCS_CATALOG_ECONOMY_INFO2")?>',		
		BASKET_URL: '<?=$arParams["BASKET_URL"]?>',
		ADD_BASKET_MESSAGE: '<?=($arParams["ADD_TO_BASKET_ACTION"] == "BUY" ? $arParams["MESS_BTN_BUY"] : $arParams["MESS_BTN_ADD_TO_BASKET"])?>',
		ADD_BASKET_OK_MESSAGE: '<?=GetMessageJS("CT_BCS_TPL_MESS_BTN_ADD_TO_BASKET_OK")?>',
		DELAY_MESSAGE: '<?=$arParams["MESS_BTN_DELAY"]?>',
		DELAY_OK_MESSAGE: '<?=GetMessageJS("CT_BCS_TPL_MESS_BTN_DELAY_OK")?>',
		RELATIVE_QUANTITY_MANY: '<?=CUtil::JSEscape($arParams["MESS_RELATIVE_QUANTITY_MANY"])?>',
		RELATIVE_QUANTITY_FEW: '<?=CUtil::JSEscape($arParams["MESS_RELATIVE_QUANTITY_FEW"])?>',
		COMPARE_MESSAGE: '<?=$arParams["MESS_BTN_COMPARE"]?>',
		COMPARE_OK_MESSAGE: '<?=GetMessageJS("CT_BCS_TPL_MESS_BTN_COMPARE_OK")?>',
		BTN_MESSAGE_LAZY_LOAD: '<?=$arParams["MESS_BTN_LAZY_LOAD"]?>',
		BTN_MESSAGE_LAZY_LOAD_WAITER: '<?=GetMessageJS("CT_BCS_TPL_MESS_BTN_LAZY_LOAD_WAITER")?>',
		BTN_MESSAGE_DETAIL_ITEM: '<?=GetMessageJS("CT_BCS_TPL_MESS_BTN_DETAIL_ITEM")?>',
		SITE_ID: '<?=SITE_ID?>'
	});
	var <?=$obName?> = new JCCatalogSectionComponent({
		siteId: '<?=CUtil::JSEscape(SITE_ID)?>',
		componentPath: '<?=CUtil::JSEscape($componentPath)?>',		
		navParams: <?=CUtil::PhpToJSObject($navParams)?>,
		deferredLoad: false, //enable it for deferred load
		initiallyShowHeader: '<?=!empty($arResult["ITEM_ROWS"])?>',
		bigData: <?=CUtil::PhpToJSObject($arResult["BIG_DATA"])?>,
		lazyLoad: !!'<?=$showLazyLoad?>',
		loadOnScroll: !!'<?=($arParams["LOAD_ON_SCROLL"] === "Y")?>',
		prefix: '<?=$prefix?>',
		template: '<?=CUtil::JSEscape($signedTemplate)?>',
		ajaxId: '<?=CUtil::JSEscape($arParams["AJAX_ID"])?>',
		parameters: '<?=CUtil::JSEscape($signedParams)?>',
		container: '<?=$containerName?>'
	});
</script>
<!-- component-end -->