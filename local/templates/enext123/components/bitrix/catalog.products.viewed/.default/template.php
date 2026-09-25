<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$elementEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
$elementDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
$elementDeleteParams = array('CONFIRM' => GetMessage('CT_CPV_TPL_ELEMENT_DELETE_CONFIRM'));

$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($this->randString()));
$containerName = 'catalog-products-viewed-container';

$frame = $this->createFrame($containerName)->begin("");?>

<div class="catalog-products-viewed" data-entity="<?=$containerName?>">
	<?if(!empty($arResult['ITEMS'])) {?>
		<div class="row">
			<?$areaIds = array();
			foreach($arResult['ITEMS'] as $item) {
				$uniqueId = $item['ID'].'_'.md5($this->randString().$component->getAction());
				$areaIds[$item['ID']] = $this->GetEditAreaId($uniqueId);
				$this->AddEditAction($uniqueId, $item['EDIT_LINK'], $elementEdit);
				$this->AddDeleteAction($uniqueId, $item['DELETE_LINK'], $elementDelete, $elementDeleteParams);?>
				<div class="col-xs-4 col-md-2 col-lg-1">
					<?$APPLICATION->IncludeComponent("bitrix:catalog.item", "viewed",
						array(
							"RESULT" => array(
								"ITEM" => $item,
								"AREA_ID" => $areaIds[$item["ID"]],
								"TYPE" => "CARD",
								"BIG_LABEL" => "N",
								"BIG_DISCOUNT_PERCENT" => "N",
								"BIG_BUTTONS" => "N",
								"SCALABLE" => "N"
							),
							"PARAMS" => array()
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);?>
				</div>
			<?}?>
		</div>
	<?} else {
		//load css for bigData/deferred load
		$APPLICATION->IncludeComponent('bitrix:catalog.item', '',
			array(),
			$component,
			array('HIDE_ICONS' => 'Y')
		);
	}?>
</div>

<script type="text/javascript">
	var <?=$obName?> = new JCCatalogProductsViewedComponent({
		initiallyShowHeader: '<?=!empty($arResult["ITEMS"])?>',
		container: '<?=$containerName?>'
	});
</script>

<?$frame->end();?>