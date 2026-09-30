<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;

$elementEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
$elementDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
$elementDeleteParams = array('CONFIRM' => GetMessage('SERVICES_ITEM_DELETE_CONFIRM'));

$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($this->randString()));
$containerName = 'services-'.$obName;?>

<div class="services-wrapper">
	<div class="container">				
		<div class="row services" id="<?=$containerName?>">
			<div class="col-xs-12">
				<div class="h1">
					<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
						array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => SITE_DIR."include/block_services_title.php"
						),
						$component
					);?>
				</div>
			</div>
			<?foreach($arResult["ITEMS"] as $arItem) {
				$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], $elementEdit);
				$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], $elementDelete, $elementDeleteParams);?><!--
				--><div class="col-xs-12 col-md-3" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
					<a href="<?=(!empty($arItem['DISPLAY_PROPERTIES']['URL']) ? $arItem['DISPLAY_PROPERTIES']['URL']['VALUE'] : 'javascript:void(0)');?>" class="services-item">														
						<?if(!empty($arItem["PREVIEW_PICTURE"])) {?>
							<div class="services-item__pic">
								<img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" width="<?=$arItem['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arItem['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arItem['NAME']?>" />
							</div>
						<?} elseif(!empty($arItem["DISPLAY_PROPERTIES"]["ICON"])) {?>
							<div class="services-item__icon-wrapper">
								<div class="services-item__icon">
									<i class="fa <?=$arItem['DISPLAY_PROPERTIES']['ICON']['VALUE']?>"></i>
								</div>
							</div>
						<?} else {?>
							<div class="services-item__pic"></div>
						<?}?>
						<div class="services-item__caption">
							<div class="services-item__title"><?=$arItem["NAME"]?></div>
							<?=(!empty($arItem["PREVIEW_TEXT"]) ? "<div class='services-item__text'>".$arItem["PREVIEW_TEXT"]."</div>" : "");?>
						</div>
					</a>
				</div><!--
			--><?}?>
		</div>
	</div>
</div>

<script type="text/javascript">
	var <?=$obName?> = new JCNewsListBlockServices({		
		container: '<?=$containerName?>'
	});
</script>