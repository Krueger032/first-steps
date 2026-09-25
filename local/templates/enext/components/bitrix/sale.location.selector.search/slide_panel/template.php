<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc,
	Bitrix\Sale\Location;

Loc::loadMessages(__FILE__);

if($arParams["UI_FILTER"]) {
	$arParams["USE_POPUP"] = true;
}

if(!empty($arResult["ERRORS"]["FATAL"])) {
	foreach($arResult["ERRORS"]["FATAL"] as $error)
		ShowError($error);
} else {
	CJSCore::Init();
	$this->addExternalJS("/bitrix/js/sale/core_ui_widget.js");
	$this->addExternalJS("/bitrix/js/sale/core_ui_etc.js");
	$this->addExternalJS("/bitrix/js/sale/core_ui_autocomplete.js");?>
	
	<div id="slide-panel-sls-<?=$arResult['RANDOM_TAG']?>" class="slide-panel-bx-sls">
		<?if(!isset($arParams["SHOW_TITLE"]) || $arParams["SHOW_TITLE"] != "N") {?>
			<div class="bx-ui-sls-input-title"><?=Loc::getMessage("SALE_SLS_SLIDE_PANEL_INPUT_TITLE")?></div>
		<?}?>
		<div class="dropdown-block bx-ui-sls-input-block">
			<div class="dropdown-icon"><i class="icon-search"></i></div>
			<input type="text" autocomplete="off" name="<?=$arParams['INPUT_NAME']?>" value="<?=$arResult['VALUE']?>" class="dropdown-field" placeholder="<?=Loc::getMessage('SALE_SLS_SLIDE_PANEL_INPUT_SOME')?> ..." />
			<div class="bx-ui-sls-loader"></div>
			<div class="bx-ui-sls-clear" title="<?=Loc::getMessage('SALE_SLS_SLIDE_PANEL_CLEAR_SELECTION')?>"><i class="icon-close"></i></div>
			<div class="bx-ui-sls-pane"></div>
		</div>		
		<script type="text/html" data-template-id="bx-ui-sls-error">
			<div class="bx-ui-sls-error">{{message}}</div>
		</script>
		<script type="text/html" data-template-id="bx-ui-sls-dropdown-item">
			<div class="dropdown-item bx-ui-sls-variant">
				<span class="dropdown-item-text">{{display_wrapped}}</span>
			</div>
		</script>
		<div class="bx-ui-sls-error-message">
			<?if(!$arParams["SUPPRESS_ERRORS"]) {
				if(!empty($arResult["ERRORS"]["NONFATAL"])) {
					foreach($arResult["ERRORS"]["NONFATAL"] as $error)
						ShowError($error);
				}
			}?>
		</div>
		<?if(is_array($arResult["DEFAULT_LOCATIONS"]) && !empty($arResult["DEFAULT_LOCATIONS"])) {?>
			<div class="bx-ui-sls-quick-locations quick-locations">
				<?foreach($arResult["DEFAULT_LOCATIONS"] as $lid => $loc) {?>
					<a href="javascript:void(0)" data-id="<?=intval($loc['ID'])?>" class="quick-location-tag"><?=htmlspecialcharsbx($loc["NAME"])?></a>
				<?}?>
			</div>
		<?}
		if(isset($arParams["SHOW_SELECT_LOCATION_BUTTON"]) && $arParams["SHOW_SELECT_LOCATION_BUTTON"] == "Y") {?>
			<div class="bx-ui-sls-btn">
				<button type="button" class="btn btn-buy" data-entity="selectLocation"><span><?=Loc::getMessage("SALE_SLS_SLIDE_PANEL_SELECT_LOCATION")?></span></button>
			</div>
		<?}?>
	</div>
	<script type="text/javascript">
		if(!window.BX && top.BX)
			window.BX = top.BX;

		<?if(strlen($arParams["JS_CONTROL_DEFERRED_INIT"])) {?>
			if(typeof window.BX.locationsDeferred == 'undefined')
				window.BX.locationsDeferred = {};
			window.BX.locationsDeferred['<?=$arParams["JS_CONTROL_DEFERRED_INIT"]?>'] = function() {
		<?}
		if(strlen($arParams["JS_CONTROL_GLOBAL_ID"])) {?>
			if(typeof window.BX.locationSelectors == 'undefined')
				window.BX.locationSelectors = {};
			window.BX.locationSelectors['<?=$arParams["JS_CONTROL_GLOBAL_ID"]?>'] =
		<?}?>
		
		new BX.Sale.component.location.selector.searchSlidePanel(<?=CUtil::PhpToJSObject(array(
			//common
			"scope" => "slide-panel-sls-".$arResult["RANDOM_TAG"],
			"source" => $this->__component->getPath()."/get.php",
			"query" => array(
				"FILTER" => array(
					"EXCLUDE_ID" => intval($arParams["EXCLUDE_SUBTREE"]),
					"SITE_ID" => $arParams["FILTER_BY_SITE"] && !empty($arParams["FILTER_SITE_ID"]) ? $arParams["FILTER_SITE_ID"] : ""
				),
				"BEHAVIOUR" => array(
					"SEARCH_BY_PRIMARY" => $arParams["SEARCH_BY_PRIMARY"] ? "1" : "0",
					"LANGUAGE_ID" => LANGUAGE_ID
				),
			),
			"selectedItem" => !empty($arResult["LOCATION"]) ? $arResult["LOCATION"]["VALUE"] : false,
			"knownItems" => $arResult["KNOWN_ITEMS"],
			"provideLinkBy" => $arParams["PROVIDE_LINK_BY"],
			"messages" => array(
				"nothingFound" => Loc::getMessage("SALE_SLS_SLIDE_PANEL_NOTHING_FOUND"),
				"error" => Loc::getMessage("SALE_SLS_SLIDE_PANEL_ERROR_OCCURED"),
			),
			//"js logic"-related part
			"callback" => $arParams["JS_CALLBACK"],
			"useSpawn" => $arParams["USE_JS_SPAWN"] == "Y",
			"usePopup" => ($arParams["USE_POPUP"] ? true : false),
			"initializeByGlobalEvent" => $arParams["INITIALIZE_BY_GLOBAL_EVENT"],
			"globalEventScope" => $arParams["GLOBAL_EVENT_SCOPE"],
			//specific
			"pathNames" => $arResult["PATH_NAMES"], // deprecated
			"types" => $arResult["TYPES"]
		), false, false, true)?>);

		<?if(strlen($arParams["JS_CONTROL_DEFERRED_INIT"])) {?>
			};
		<?}?>
	</script>
<?}?>