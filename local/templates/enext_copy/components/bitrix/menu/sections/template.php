<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

if(count($arResult) < 1)
	return;?>

<div class="catalog-sections-wrapper">	
	<div class="container-ws">
		<div class="h1">
			Категории товаров
		</div>
		<div class="row block-catalog-sections">
			<?foreach($arResult as $arItem) {
				if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1)
					continue;?>
				<div class="col-xs-12 col-md-2">
					<a href="<?=$arItem['LINK']?>" class="catalog-section-item">
						<?if($arItem['PARAMS']['ELEMENT_CNT'] > 0) {?>
							<span class="catalog-section-item__count"><?=$arItem["PARAMS"]["ELEMENT_CNT"]?></span>
						<?}?>
						<span class="catalog-section-item__graph-wrapper">
							<span class="catalog-section-item__graph<?=(!empty($arItem["PARAMS"]["ICON"]) || is_array($arItem["PARAMS"]["PICTURE"]) ? '' : ' empty')?>">
								<?if(!empty($arItem["PARAMS"]["ICON"])) {?>									
									<i class="<?=$arItem['PARAMS']['ICON']?>" aria-hidden="true"></i>									
								<?} elseif(is_array($arItem["PARAMS"]["PICTURE"])) {?>									
									<img src="<?=$arItem['PARAMS']['PICTURE']['SRC']?>" width="<?=$arItem['PARAMS']['PICTURE']['WIDTH']?>" height="<?=$arItem['PARAMS']['PICTURE']['HEIGHT']?>" alt="<?=$arItem['PARAMS']['PICTURE']['ALT']?>" title="<?=$arItem['PARAMS']['PICTURE']['TITLE']?>" />									
								<?} else {?>
									<img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo.png" width="134" height="134" alt="<?=$arItem['PARAMS']['PICTURE']['ALT']?>" title="<?=$arItem['PARAMS']['PICTURE']['TITLE']?>" />
								<?}?>
							</span>
						</span>
						<span class="catalog-section-item__title"><?=$arItem["TEXT"]?></span>
					</a>	
				</div>
			<?}?>
		</div>
		<div class="container catalog-sections-wrapper__desc">
			<h2>Интернет-магазин сантехники</h2>
			<p> Сантехника для ванной комнаты поможет обустроить санузел, который станет гордостью квартиры или частного дома. Ведь сантехника и ванная — это практически синонимы. Довольно сложно представить современную ванную комнату, в которой отсутствует раковина, душ или такой аксессуар, как вешалки для полотенец. Качественная сантехника создает особенную атмосферу в санузле: ведь мы посещаем ванную комнату, чтобы отдохнуть, расслабиться и восстановиться после тяжелого рабочего дня, да и просто освежиться, принимая водные процедуры. Поэтому к выбору сантехники нужно подойти очень серьезно: она должна не только оптимально сочетаться с интерьером ванной комнаты, но и быть качественной, изготовленной из материалов стойких к износу, коррозии.</p>
		</div>
	</div>
</div>