<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true ) die();
$this->setFrameMode(true);
?>
<?if($arResult && $arResult['PROPERTIES']['PHOTOS']['VALUE']):?>
	<?
	$itemClasses = 'flexbox dark-block-hover gallery-list__item--has-bg gallery-list__item--photos gallery-list__item--has-additional-text';
	$imageWrapperClasses = 'gallery-list__item-image-wrapper--PICTURES gallery-list__item-image-wrapper--BG';
	?>
	<div class="gallery-item <?=$templateName?>-template row">
		<?foreach($arResult['PROPERTIES']['PHOTOS']['VALUE'] as $photoId):?>
			<?
			$arImage = CFile::GetFileArray($photoId);
			$imageSrc = $arImage['SRC'];
			$imageDescr = $arImage['DESCRIPTION'];
			?>
			<div class="item-wrapper col-md-6 col-sm-6 col-xs-6 col-xxs-12">
				<div class="gallery-list__item rounded3 <?=$itemClasses?> darken-bg-animate">
					<div class="gallery-list__item-image-wrapper <?=$imageWrapperClasses?>">
						<a class="gallery-list__item-link" href="javascript:void(0);" title="<?=htmlspecialcharsbx($imageDescr)?>" data-big="<?=$imageSrc?>">
							<span class="gallery-list__item-image" style="background-image: url(<?=$imageSrc?>);"></span>
						</a>
					</div>
					<div class="gallery-list__item-text-wrapper flexbox ">
						<div class="gallery-list__item-text-cross-part">
							<div class="cross"></div>
						</div>
					</div>
				</div>
			</div>
		<?endforeach;?>
	</div>
<?else:?>
	<div class="alert alert-warning"><?=GetMessage("ELEMENT_PROPERTY_ERROR")?></div>
<?endif;?>