<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true ) die();

use \Bitrix\Main\Localization\Loc;
$this->setFrameMode(true);

$bMobileScrolledItems = $arParams['MOBILE_SCROLLED'] === 'Y';
$elementInRow = $arParams['ELEMENT_IN_ROW'];
$bItemsTypeAlbums = $arParams['ITEMS_TYPE'] != "PHOTOS";

switch ($elementInRow) {
    case '2':
        $sClassList = 'col-md-6 col-sm-6 col-xs-6 col-xxs-12';
        break;
    case '3':
        $sClassList = 'col-md-4 col-sm-4 col-xs-6 col-xxs-12';
        break;
    case '4':
        $sClassList = 'col-md-3 col-sm-4 col-xs-6 col-xxs-12';
        break;
	default:
		$sClassList = 'col-md-3 col-sm-4 col-xs-6 col-xxs-12';
		break;
}
?>
<?if($arResult['ITEMS']):?>
	<?$rowClass = 'row row-'.$elementInRow;?>
	<?
	if ($bMobileScrolledItems) {
		$rowClass .= ' mobile-offset normal swipeignore mobile-overflow mobile-margin-16 mobile-compact has-bottom-nav ';
	}
	$imageWrapperClasses = 'gallery-list__item-image-wrapper--BG';
	?>
	<?if(!$arParams['IS_AJAX']):?>
		<div class="content_wrapper_block">
			<div class="maxwidth-theme only-on-front">
				<?if($arParams['SHOW_PREVIEW_TEXT'] !== 'N' && $arParams['INCLUDE_FILE']):?>
					<div class="with-text-block-wrapper">
						<div class="top_block">
							<?=Aspro\Functions\CAsproMax::showTitleH($arParams['TITLE_BLOCK']);?>
							<a href="<?=SITE_DIR.$arParams['ALL_URL'];?>" class="pull-right font_upper muted"><?=$arParams['TITLE_BLOCK_ALL'] ;?></a>
							<div class="text_before_items font_xs">
							<?$APPLICATION->IncludeComponent(
								"bitrix:main.include",
								"",
								Array(
									"AREA_FILE_SHOW" => "file",
									"PATH" => SITE_DIR."include/mainpage/inc_files/".$arParams['INCLUDE_FILE'],
									"EDIT_TEMPLATE" => ""
								)
							);?>
						</div>
						</div>						
						<div class="item-views md  normal news2 with-border">
				<?endif;?>
			<div class = 'item-views news2 with-border'> 
				<div class = 'items'> 
					<div class="<?=$rowClass?>">
	
	<?endif;?>
						<?foreach($arResult['ITEMS'] as $i => $arItem):?>
							<?
							// edit/add/delete buttons for edit mode
							if($bItemsTypeAlbums){
								$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
								$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => Loc::getMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
							}

							// detail url
							$detailUrl = $arItem['DETAIL_PAGE_URL'];

							// photos
							$arPhotos = (array_key_exists('PHOTOS', (array)$arItem['PROPERTIES']) && $arItem['PROPERTIES']['PHOTOS']['VALUE']) ? (array)$arItem['PROPERTIES']['PHOTOS']['VALUE'] : (array)$arItem['PROPERTY_PHOTOS_VALUE'];

							// preview image
							$nImageID = is_array($arItem['FIELDS']['PREVIEW_PICTURE']) ? $arItem['FIELDS']['PREVIEW_PICTURE']['ID'] : $arItem['FIELDS']['PREVIEW_PICTURE'];
							if(
								!$nImageID &&
								$arPhotos
							){
								$nImageID = reset($arPhotos);
							}

							$imageSrc = ($nImageID ? CFile::getPath($nImageID) : SITE_TEMPLATE_PATH.'/images/noimage.png');
							$imageDescrPhoto = is_array($arItem['FIELDS']['PREVIEW_PICTURE']) ? $arItem['FIELDS']['PREVIEW_PICTURE']['DESCRIPTION'] : '';
							$imageDescrAlbum = $arItem['PROPERTIES']['PHOTOS']['DESCRIPTION'][0];
							?>
							<div class="item-wrapper <?=$sClassList?>">
								<div class="gallery-list__item rounded3 box-shadow gallery-list__item--has-additional-text darken-bg-animate" <?=($bItemsTypeAlbums ? 'id="'.$this->GetEditAreaId($arItem['ID']).'"' : '')?>>
									<?if($imageSrc):?>
										<div class="gallery-list__item-image-wrapper <?=$imageWrapperClasses?>">
										<a class="gallery-list__item-link" href="<?=$detailUrl?>" <?=($bItemsTypeAlbums ? '' : 'title="'.htmlspecialcharsbx($imageDescr).'" data-big="'.$imageSrc.'"')?>>
												<img class="gallery-list__item-image <?=$imageClasses?>" src="<?=$imageSrc?>" title="<?=$imageDescrAlbum ? htmlspecialcharsbx($imageDescrAlbum) : htmlspecialcharsbx($imageDescrPhoto)?>"/>
											</a>
										</div>
									<?endif;?>
									<?if($bItemsTypeAlbums):?>
										<a class="gallery-list__item-link gallery-list__item-link--absolute" href="<?=$detailUrl?>"></a>
										<div class="gallery-list__item-additional-text-wrapper">
											<div class="gallery-list__item-additional-text-top-part">
												<?if(
													$bItemsTypeAlbums &&
													$arPhotos
												):?>
													<div class="gallery-list__item-photos-count"><?=\Aspro\Functions\CAsproMax::declOfNum(
															count($arPhotos),
															array(
																Loc::getMessage('PHOTOS_COUNT_1'),
																Loc::getMessage('PHOTOS_COUNT_2'),
																Loc::getMessage('PHOTOS_COUNT_5')
															)
														)?></div>
												<?endif;?>

												<div class="gallery-list__item-title">
													<span><?=$arItem['NAME']?></span>										
												</div>
											</div>
										</div>
									<?endif;?>

									<?if(!$bItemsTypeAlbums):?>
										<div class="gallery-list__item-text-wrapper flexbox">
											<div class="gallery-list__item-text-cross-part">
												<div class="cross"></div>
											</div>

											<div class="gallery-list__item-text-top-part">
												<a class="gallery-list__item-link gallery-list__item-link--absolute" href="<?=$detailUrl?>"></a>

												<div class="gallery-list__item-title">
													<a class="dark_link color-theme-target" href="<?=$detailUrl?>"><?=Loc::getMessage('ALBUM_LINK', array('#NAME#' => $arItem['NAME']))?></a>
												</div>
											</div>
										</div>
									<?endif;?>
								</div>
							</div>
						<?endforeach;?>

						<?if($bMobileScrolledItems):?>
							<?if($arParams['IS_AJAX']):?>
								<div class="wrap_nav bottom_nav_wrapper">
							<?endif;?>

									<?$bHasNav = (strpos($arResult["NAV_STRING"], 'more_text_ajax') !== false);?>
									<div class="bottom_nav mobile_slider <?=($bHasNav ? '' : ' hidden-nav');?>" data-parent=".item-views" data-append=".items > .row" <?=($arParams["IS_AJAX"] ? "style='display: none; '" : "");?>>
										<?if ($bHasNav):?>
											<?=CMax::showIconSvg('bottom_nav-icon colored_theme_svg', SITE_TEMPLATE_PATH.'/images/svg/mobileBottomNavLoader.svg');?>
											<?=$arResult["NAV_STRING"]?>
										<?endif;?>
									</div>

							<?if($arParams['IS_AJAX']):?>
								</div>
							<?endif;?>
						<?endif;?>
					</div>
					<div class="bottom_nav_wrapper hidden-slider-nav">
										<div class="bottom_nav animate-load-state" <?=($isAjax ? "style='display: none; '" : "");?> data-parent=".item-views" data-append=".items > .row" data-scroll-class=".swipeignore.mobile-overflow">
											<?if($arParams['DISPLAY_BOTTOM_PAGER']):?>
												<?=$arResult['NAV_STRING']?>
											<?endif;?>
										</div>
									</div>
				</div>
			</div>
			
				<?// bottom pagination?>
					<?if($arParams['TITLE_BLOCK'] || $arParams['TITLE_BLOCK_ALL']):?>
						<?if($arParams['SHOW_PREVIEW_TEXT'] !== 'N' && $arParams['INCLUDE_FILE']):?>	
									</div>
							</div>
						<?endif;?>
					<?endif;?>	

	<?if(!$arParams['IS_AJAX']):?>
			</div>
		</div>
	<?endif;?>
<?endif;?>