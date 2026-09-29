<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$itemCount = count($arResult);
$needReload = isset($_REQUEST["compare_list_reload"]) && $_REQUEST["compare_list_reload"] == "Y";
$idCompareCount = "compareList".$this->randString();
$obCompare = "ob".$idCompareCount;

$strIds = "";
$arCompare = $_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"];
if(!empty($arCompare)) {
	$strIds = "?ids=".implode("%2B", array_keys($arCompare));
}
unset($arCompare);?>

<div class="catalog-compare-list-container" id="<?=$idCompareCount?>">
	<?if($needReload)
		$APPLICATION->RestartBuffer();
	$frame = $this->createFrame($idCompareCount)->begin("");?>
	<div class="catalog-compare-list<?=($itemCount > 0 && !CSite::InDir(SITE_DIR.'catalog/compare/') && !CSite::InDir(SITE_DIR.'personal/') ? ' active' : '')?>">	
		<a class="catalog-compare-link" href="<?=$arParams['COMPARE_URL'].(strlen($strIds) > 0 ? $strIds : '')?>" title="<?=GetMessage('CP_BCCL_TPL_MESS_COMPARE')?>">
			<span class="catalog-compare-count" data-entity="count"><?=$itemCount?></span>
			<span class="catalog-compare-title"><?=GetMessage("CP_BCCL_TPL_MESS_COMPARE")?></span>
			<span class="catalog-compare-icon"><i class="icon-arrow-right"></i></span>
		</a>
	</div>
	<?$frame->end();
	if($needReload)
		die();?>
</div>

<?$jsParams = array(
	"VISUAL" => array(
		"ID" => $idCompareCount,
	),
	"AJAX" => array(
		"url" => $APPLICATION->GetCurPage(),		
		"reload" => array(
			"compare_list_reload" => "Y"
		)
	)
);?>

<script type="text/javascript">
	var <?=$obCompare?> = new JCCatalogCompareList(<?=CUtil::PhpToJSObject($jsParams, false, true)?>)
</script>