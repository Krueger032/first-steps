<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;

$elementEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
$elementDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
$elementDeleteParams = array('CONFIRM' => GetMessage('NEWS_ITEM_DELETE_CONFIRM'));?>

<div class="news-wrapper">
	<div class="container">				
		<div class="row news<?=(CSite::inDir(SITE_DIR."index.php") ? '' : ' last');?>">
			<div class="col-xs-12">
				<div class="h1"><?=(CSite::inDir(SITE_DIR."index.php") ? GetMessage("NEWS") : GetMessage("LAST_NEWS"));?></div>
			</div>
			<?foreach($arResult["ITEMS"] as $arItem) {
				$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], $elementEdit);
				$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], $elementDelete, $elementDeleteParams);?><!--
				--><div class="col-xs-12 col-md-4" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
					<a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="news-item">
						<div class="news-item__pic-wrapper">
							<div class="news-item__pic">
								<?if(!empty($arItem["PREVIEW_PICTURE"])) {?>
									<img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arItem['NAME']?>" />
								<?}?>
							</div>
						</div>
						<div class="news-item__caption">
							<span class="news-item__date"><?=$arItem["DISPLAY_ACTIVE_FROM"]?></span>
							<div class="news-item__title"><?=$arItem["NAME"]?></div>
						</div>
					</a>
				</div><!--				
			--><?}
			if(CSite::inDir(SITE_DIR."index.php")) {?>
				<div class="col-xs-12">					
					<div class="all-news">
						<a class="btn btn-default" href="<?=$arParams['ALL_NEWS_HREF']?>" role="button"><?=GetMessage("NEWS_ALL")?></a>
					</div>
				</div>
			<?}?>
		</div>
	</div>
</div>