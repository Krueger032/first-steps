<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

$parentParams = (isset($arResult["ORIGINAL_PARAMETERS"]) && is_array($arResult["ORIGINAL_PARAMETERS"]) ? $arResult["ORIGINAL_PARAMETERS"] : $arParams);

$parentParams["SECTION_ID"] = intval($arParams["EMPTY_PARENT_ID"]);
$parentParams["SECTION_CODE"] = "";
$parentParams["EMPTY_SECTION"] = "N";
$parentParams["FILTER_NAME"] = "arCatalogEmptyParentFilter";
$parentParams["SET_TITLE"] = "N";
$parentParams["SET_BROWSER_TITLE"] = "N";
$parentParams["SET_META_KEYWORDS"] = "N";
$parentParams["SET_META_DESCRIPTION"] = "N";
$parentParams["BROWSER_TITLE"] = "-";
$parentParams["META_KEYWORDS"] = "-";
$parentParams["META_DESCRIPTION"] = "-";
$parentParams["SET_STATUS_404"] = "N";
$parentParams["SHOW_404"] = "N";
$parentParams["ADD_SECTIONS_CHAIN"] = "N";
$parentParams["SEF_RULE"] = "";
$parentParams["SMART_FILTER_PATH"] = "";
$parentParams["DISPLAY_TOP_PAGER"] = "N";
$parentParams["DISPLAY_BOTTOM_PAGER"] = "N";
$parentParams["PAGER_SHOW_ALWAYS"] = "N";
$parentParams["LAZY_LOAD"] = "Y";
$parentParams["POPUP_MODE"] = "Y";
$parentParams["COMPONENT_TEMPLATE"] = ".default";
unset($parentParams["AJAX_ID"]);

if(isset($arParams["~PRODUCT_ROW_VARIANTS"]) && is_string($arParams["~PRODUCT_ROW_VARIANTS"]))
    $parentParams["PRODUCT_ROW_VARIANTS"] = $arParams["~PRODUCT_ROW_VARIANTS"];
elseif(isset($parentParams["PRODUCT_ROW_VARIANTS"]) && is_array($parentParams["PRODUCT_ROW_VARIANTS"]))
    $parentParams["PRODUCT_ROW_VARIANTS"] = \Bitrix\Main\Web\Json::encode($parentParams["PRODUCT_ROW_VARIANTS"]);

$GLOBALS["arCatalogEmptyParentFilter"] = array();

$emptyParentSections = (isset($arParams["EMPTY_PARENT_SECTIONS"]) && is_array($arParams["EMPTY_PARENT_SECTIONS"]) ? $arParams["EMPTY_PARENT_SECTIONS"] : array());

if(!empty($emptyParentSections)) {
    $emptyParentSectionIds = array(0);
    foreach($emptyParentSections as $emptyParentSection)
        $emptyParentSectionIds[] = intval($emptyParentSection["ID"]);
    $signer = new Bitrix\Main\Security\Sign\Signer;
    $signedEmptyParent = $signer->sign(base64_encode(serialize(array(
        "params" => $parentParams,
        "parentId" => intval($arParams["EMPTY_PARENT_ID"]),
        "sections" => $emptyParentSectionIds
    ))), "catalog.empty.section");
    unset($emptyParentSectionIds, $signer);
}
?>
<div class="catalog-empty-parent">
    <?if(!empty($emptyParentSections)) {?>
        <div class="h2"><?=Loc::getMessage("CATALOG_EMPTY_SECTION_PARENT")?></div>
        <div class="catalog-empty-parent-links" data-entity="empty-parent-links">
            <div class="catalog-empty-parent-link active" data-section-id="0"><?=Loc::getMessage("CATALOG_SECTION_LINKS_ALL")?><span><?=intval($arParams["EMPTY_PARENT_ELEMENT_CNT"])?></span></div>
            <?foreach($emptyParentSections as $emptyParentSection) {?>
                <div class="catalog-empty-parent-link" data-section-id="<?=intval($emptyParentSection["ID"])?>"><?=$emptyParentSection["NAME"]?><span><?=intval($emptyParentSection["ELEMENT_CNT"])?></span></div>
            <?}
            unset($emptyParentSection);?>
        </div>
    <?}?>
    <div class="catalog-empty-parent-products" data-entity="empty-parent-products">
        <?$APPLICATION->IncludeComponent("bitrix:catalog.section", ".default", $parentParams, false, array("HIDE_ICONS" => "Y"));?>
    </div>
    <?if(!empty($emptyParentSections)) {
        $obName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $this->GetEditAreaId($arParams["EMPTY_PARENT_ID"]));?>
        <script type="text/javascript">
            BX.message({
                EMPTY_SECTION_LINKS_ALL: "<?=GetMessageJS('CATALOG_EMPTY_SECTION_LINKS_ALL')?>",
                EMPTY_SECTION_LINKS_SHOW_ALL: "<?=GetMessageJS('CATALOG_EMPTY_SECTION_LINKS_SHOW_ALL')?>",
                EMPTY_SECTION_LINKS_HIDE: "<?=GetMessageJS('CATALOG_EMPTY_SECTION_LINKS_HIDE')?>"
            });
            var <?=$obName?> = new JCCatalogEmptyParent({
                ajaxUrl: '<?=CUtil::JSEscape($this->GetFolder()."/ajax.php")?>',
                siteId: '<?=CUtil::JSEscape(SITE_ID)?>',
                signed: '<?=CUtil::JSEscape($signedEmptyParent)?>'
            });
        </script>
    <?}?>
</div>
<?
unset($signedEmptyParent, $parentParams, $obName, $emptyParentSections);
