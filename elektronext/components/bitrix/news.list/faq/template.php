<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;

$elementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$elementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$elementDeleteParams = array("CONFIRM" => GetMessage("FAQ_ITEM_DELETE_CONFIRM"));

$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($this->randString()));
$containerName = 'faq-'.$obName;?>

<div class="faq-wrapper" itemscope itemtype="https://schema.org/FAQPage">
	<h2>
		<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
			array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => SITE_DIR."include/faq_title.php"
			),
			$component
		);?>
	</h2>
	<div class="faq-items" id="<?=$containerName?>">
		<?foreach($arResult["ITEMS"] as $arItem) {
			$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], $elementEdit);
			$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], $elementDelete, $elementDeleteParams);?>
			<div class="faq-item" data-entity="item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
				<div class="faq-item-title-container" data-entity="title">
					<div class="faq-item-icon-ok"><i class="icon-ok"></i></div>
					<h3 class="faq-item-title" itemprop="name"><?=$arItem["NAME"]?></h3>
					<div class="faq-item-icon-arrow"><i class="icon-arrow-down" data-entity="icon"></i></div>
				</div>
				<div class="faq-item-descr" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
					<div itemprop="text"><?=$arItem["PREVIEW_TEXT"]?></div>
				</div>
			</div>
		<?}
		unset($arItem);?>
	</div>
</div>

<script type="text/javascript">
	var <?=$obName?> = new JCNewsListFaq({
		container: '<?=$containerName?>'
	});
</script>