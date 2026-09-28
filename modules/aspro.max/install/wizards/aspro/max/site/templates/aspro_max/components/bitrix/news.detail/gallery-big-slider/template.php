<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true ) die();
$this->setFrameMode(true);
?>
<?if($arResult && $arResult['PROPERTIES']['PHOTOS']['VALUE']):?>
	<?$countPhoto = count($arResult['PROPERTIES']['PHOTOS']['VALUE']);?>
	<div class="gallery-item gallery-big-slider-template">
		<div class="bordered rounded3">
			<div class="gallery-big">
				<div class="big-gallery-block">
					<div class="owl-carousel owl-theme owl-bg-nav short-nav owl-carousel--button-offset-half" data-plugin-options='{"items": "1", "autoplay" : false, "autoplayTimeout" : "3000", "smartSpeed":1000, "dots": true, "nav": true, "loop": false, "index": true, "margin": 0, "dotsContainer": false, "dots": true, "relatedTo": ".gallery-thumbs .owl-carousel"}'>
						<?foreach($arResult['PROPERTIES']['PHOTOS']['VALUE'] as $photoId):?>
							<?
							$arImage = CFile::GetFileArray($photoId);
							$imageSrc = $arImage['SRC'];
							$imageDescr = $arImage['DESCRIPTION'];
							?>
							<div class="item text-center">
								<a class="fancy gallery-big__link" data-fancybox="big-gallery" href="<?=$imageSrc?>">
									<img class="img-responsive rounded3" data-src loading="lazy" src="<?=$imageSrc?>" title="<?=htmlspecialcharsbx($imageDescr)?>"/>
								</a>
							</div>
						<?endforeach;?>
					</div>
					<?if ($countPhoto > 1):?>
						<div class="gallery-count-info font_13 color_999 text-center hidden-xs">
							<span class="gallery-count-info__js-text">1</span>/<span><?=$countPhoto;?></span>
						</div>
					<?endif;?>
				</div>
			</div>
			<?if ($countPhoto > 1):?>
				<div class="gallery-thumbs hidden-xs">
					<?
					$mouseDragFalsy = ',"mouseDragDynamic": false';
					$mouseDragTrue = ',"mouseDragDynamic": true';
					?>
					<div class="thmb owl-carousel owl-carousel--light owl-carousel--button-wide owl-carousel--button-offset-half" data-plugin-options='{"items": "7", "nav": true, "loop": false, "clickTo": ".gallery-big .owl-carousel", "dots": false, "margin": 30, "mouseDrag": true, "pullDrag": true, "rewind": true, "responsive": {"768": {"items": 4<?=($countPhoto > 4 ? $mouseDragTrue : $mouseDragFalsy)?>}, "992": {"items": 5<?=($countPhoto > 5 ? $mouseDragTrue : $mouseDragFalsy)?>}, "1100": {"items": 6<?=($countPhoto > 6 ? $mouseDragTrue : $mouseDragFalsy)?>}, "1300": {"items": 7<?=($countPhoto > 7 ? $mouseDragTrue : $mouseDragFalsy)?>}}}'>
						<?foreach($arResult['PROPERTIES']['PHOTOS']['VALUE'] as $photoId):?>
							<?
							$arImage = CFile::GetFileArray($photoId);
							$arResizeImage= CFile::ResizeImageGet($arImage['ID'], array('width' => 300, 'height' => 200), BX_RESIZE_IMAGE_EXACT, true);
							$imageSrc = $arResizeImage['src'];
							?>
							<div id="photo-<?=$photoId?>" class="gallery-thumbs__item rounded3">
								<img class="gallery-thumbs__picture rounded3" src="<?=$imageSrc?>" title="<?=htmlspecialcharsbx($imageDescr)?>"/>
							</div>
						<?endforeach;?>
					</div>
				</div>
			<?endif;?>
		</div>
	</div>
<?else:?>
	<div class="alert alert-warning"><?=GetMessage("ELEMENT_PROPERTY_ERROR")?></div>
<?endif;?>