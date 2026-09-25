<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);?><div id="BX_YMAP_<?=$arParams['MAP_ID']?>" class="bx-yandex-map" style="width: <?=$arParams['MAP_WIDTH']?>; height: <?=$arParams['MAP_HEIGHT'];?>;"><?=GetMessage("MYS_LOADING".($arParams["WAIT_FOR_EVENT"] ? "_WAIT" : ""));?></div>

<script type="text/javascript">

	function init_<?=$arParams["MAP_ID"]?>() {
		if(!window.ymaps)
			return;

		var node = BX('BX_YMAP_<?=$arParams["MAP_ID"]?>');
		node.innerHTML = '';
		
		var map = new ymaps.Map(node, {
            center: [<?=$arParams["INIT_MAP_LAT"]?>, <?=$arParams["INIT_MAP_LON"]?>],
            zoom: <?=$arParams["INIT_MAP_SCALE"]?>,
			type: 'yandex#<?=$arParams["INIT_MAP_TYPE"]?>'
        }, {
            searchControlProvider: 'yandex#search'
        }),
		clusterer = new ymaps.Clusterer({
			preset: 'islands#invertedVioletClusterIcons',
			clusterHideIconOnBalloonOpen: false,
			geoObjectHideIconOnBalloonOpen: false
		});
			
		<?foreach($arResult["ALL_MAP_OPTIONS"] as $option) {
			if(in_array($option, $arParams["OPTIONS"])) {?>
				map.behaviors.enable('<?=$option?>');
			<?} else {?>
				if(map.behaviors.isEnabled('<?=$option?>'))
					map.behaviors.disable('<?=$option?>');
			<?}
		}
		unset($option);
 
		foreach($arResult["ALL_MAP_CONTROLS"] as $control) {
			if(in_array($control, $arParams["CONTROLS"])) {?>
				map.controls.add('<?=$control?>');
			<?} else {?>
				map.controls.remove('<?=$control?>');
			<?}
		}
		unset($control);
		
		if($arParams["ONMAPREADY"]) {?>
			if(window.<?=$arParams["ONMAPREADY"]?>)
				window.<?=$arParams["ONMAPREADY"]?>(map, clusterer);
		<?}?>
	}
		
	(function bx_ymaps_waiter() {
		if(typeof ymaps !== 'undefined')
			ymaps.ready(init_<?=$arParams["MAP_ID"]?>);
		else
			setTimeout(bx_ymaps_waiter, 1000);
	})();

</script>