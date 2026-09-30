<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;

$this->addExternalCss(SITE_TEMPLATE_PATH."/js/fancybox/jquery.fancybox.css");
$this->addExternalJS(SITE_TEMPLATE_PATH."/js/fancybox/jquery.fancybox.pack.js");

$elementEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
$elementDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
$elementDeleteParams = array('CONFIRM' => GetMessage('GALLERY_ITEM_DELETE_CONFIRM'));

$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($this->randString()));
$containerName = 'gallery-'.$obName;?>

<div class="gallery-wrapper">
	<div class="container">				
		<div class="row gallery" id="<?=$containerName?>">
			<div class="col-xs-12">
				<div class="h1">
					<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
						array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => SITE_DIR."include/block_gallery_title.php"
						),
						$component
					);?>
				</div>
			</div>
			<?foreach($arResult["ITEMS"] as $arItem) {
				$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], $elementEdit);
				$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], $elementDelete, $elementDeleteParams);?>
				<div class="col-xs-6 col-md-3" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
					<a class="gallery-item fancyimage" title="<?=$arItem['NAME']?>" href="<?=$arItem['DETAIL_PICTURE']['SRC']?>" data-fancybox-group="gallery">
						<span class="gallery-item__image">
							<?if(!empty($arItem["PREVIEW_PICTURE"])) {?>
								<img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" width="<?=$arItem['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arItem['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arItem['NAME']?>" />
							<?}?>
						</span>
						<span class="gallery-item__caption-wrap">
							<span class="gallery-item__caption">
								<span class="gallery-item__title"><?=$arItem["NAME"]?></span>
								<?if(!empty($arItem["PREVIEW_TEXT"])) {?>
									<span class="gallery-item__text"><?=$arItem["PREVIEW_TEXT"]?></span>
								<?}?>
							</span>
						</span>
					</a>
				</div>
			<?}?>
		</div>
	</div>
</div>

<script type="text/javascript">
	var <?=$obName?> = new JCNewsListBlockGallery({		
		container: '<?=$containerName?>'
	});
</script>