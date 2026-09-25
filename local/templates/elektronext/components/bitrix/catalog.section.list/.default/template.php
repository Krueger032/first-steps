<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$this->setFrameMode(true);

if($arResult["SECTIONS_COUNT"] < 1)
	return;

$strSectionEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_EDIT");
$strSectionDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_DELETE");
$arSectionDeleteParams = array("CONFIRM" => GetMessage('SECTION_ITEM_DELETE_CONFIRM'));

$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($this->randString()));
$containerName = 'sections-'.$obName;?>

<div class="row sections" id="<?=$containerName?>">
	<?foreach($arResult["SECTIONS"] as $arSection) {
		$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
		$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);?><!--
		--><div class="col-xs-12 col-md-3" id="<?=$this->GetEditAreaId($arSection['ID'])?>">
			<a href="<?=$arSection['SECTION_PAGE_URL']?>" class="section-item">
				<?if(!empty($arSection["PICTURE"])) {?>
					<div class="section-item__pic">
						<img src="<?=$arSection['PICTURE']['SRC']?>" width="<?=$arSection['PICTURE']['WIDTH']?>" height="<?=$arSection['PICTURE']['HEIGHT']?>" alt="<?=$arSection['NAME']?>" />
					</div>
				<?} elseif(!empty($arSection["UF_ICON"])) {?>
					<div class="section-item__icon">
						<i class="fa <?=$arSection['UF_ICON']?>"></i>
					</div>
				<?} else {?>
					<div class="section-item__pic"></div>
				<?}?>							
				<div class="section-item__caption">
					<div class="section-item__title"><?=$arSection["NAME"]?></div>
					<?=(!empty($arSection["UF_SHORT_DESC"]) ? "<div class='section-item__text'>".$arSection["UF_SHORT_DESC"]."</div>" : "");?>
				</div>
			</a>
		</div><!--
	--><?}?>
</div>

<script type="text/javascript">
	var <?=$obName?> = new JCCatalogSectionList({		
		container: '<?=$containerName?>'
	});
</script>