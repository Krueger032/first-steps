<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

$obName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $this->GetEditAreaId($arParams["MAP_ID"]));
$containerName = "objects-map-".$obName;?>

<div id="<?=$containerName?>" class="objects-map">
	<?$APPLICATION->IncludeComponent("altop:map.yandex.system.enext", "",
		array(
			"INIT_MAP_TYPE" => $arParams["INIT_MAP_TYPE"],
			"MAP_WIDTH" => $arParams["MAP_WIDTH"],
			"MAP_HEIGHT" => $arParams["MAP_HEIGHT"],
			"CONTROLS" => $arParams["CONTROLS"],
			"OPTIONS" => $arParams["OPTIONS"],
			"MAP_ID" => $arParams["MAP_ID"],
			"API_KEY" => $arParams["API_KEY"],
			"INIT_MAP_LON" => $arResult["POSITION"]["yandex_lon"],
			"INIT_MAP_LAT" => $arResult["POSITION"]["yandex_lat"],
			"INIT_MAP_SCALE" => $arResult["POSITION"]["yandex_scale"],
			"ONMAPREADY" => "BX_SetPlacemarks_".$arParams["MAP_ID"]
		),
		false,
		array("HIDE_ICONS" => "Y")
	);?>
</div>

<script type="text/javascript">
	var <?=$obName?> = new JCYMapObjects({		
		container: '<?=$containerName?>'
	});

	if(!window.BX_YMapEnextObjAddPlacemark) {
		window.BX_YMapEnextObjAddPlacemark = function(arPlacemark) {
			if(!arPlacemark.LAT || !arPlacemark.LON)
				return false;

			var props = {};
			if(arPlacemark.TEXT != null && arPlacemark.TEXT.length > 0) {
				props.balloonContent = arPlacemark.TEXT.replace(/\n/g, '<br />');
			}
			
			var obPlacemark = new ymaps.Placemark(
				[arPlacemark.LAT, arPlacemark.LON],
				props,
				{
					preset: 'islands#violetIcon',						
					balloonCloseButton: true,
					objectId: arPlacemark.OBJECT_ID
				}
			);
			
			return obPlacemark;
		}
	}

	if(!window.BX_YMapEnextObjSetCenter) {
		function BX_YMapEnextObjSetCenter(map) {
			if(null == map)
				return false;
			
			map.setBounds(map.geoObjects.getBounds(), {
				checkZoomRange: true
			}).then(function() {
				if(map.getZoom() > 14)
					map.setZoom(14);
			});
		}
	}

	function BX_SetPlacemarks_<?=$arParams["MAP_ID"]?>(map, clusterer) {
		var arObjects = [];
		
		<?if(is_array($arResult["POSITION"]["PLACEMARKS"]) && ($cnt = count($arResult["POSITION"]["PLACEMARKS"]))) {
			for($i = 0; $i < $cnt; $i++) {?>
				arObjects[<?=$i?>] = BX_YMapEnextObjAddPlacemark(<?=CUtil::PhpToJsObject($arResult["POSITION"]["PLACEMARKS"][$i])?>);
			<?}?>

			clusterer.events.add(['mouseenter', 'mouseleave'], function(e) {
				var target = e.get('target'),
					type = e.get('type');
					
				if(typeof target.getGeoObjects != 'undefined') {
					if(type == 'mouseenter') {
						target.options.set('preset', 'islands#invertedDarkGreenClusterIcons');
					} else {
						target.options.set('preset', 'islands#invertedVioletClusterIcons');
					}
				} else {
					if(type == 'mouseenter') {
						target.options.set('preset', 'islands#darkGreenIcon');
					} else {
						 target.options.set('preset', 'islands#violetIcon');
					}
				}
			});

			clusterer.add(arObjects);
			map.geoObjects.add(clusterer);
			
			if(arObjects.length > 1) {
				BX_YMapEnextObjSetCenter(map);
				BX.bind(window, 'resize', function() {
					BX_YMapEnextObjSetCenter(map);
				});
				BX.addCustomEvent(window, 'slideMenu', function() {
					map.container.fitToViewport();
					BX_YMapEnextObjSetCenter(map);
				});
			}
			
			var objectsItem = document.body.querySelectorAll('.object-item');
			for(var i in objectsItem) {
				if(objectsItem.hasOwnProperty(i)) {
					BX.bind(objectsItem[i], 'mouseenter', function() {						
						var objectId = this.getAttribute('data-object-id');
						for(var j = 0; j < arObjects.length; j++) {
							if(objectId == arObjects[j].options.get('objectId'))
								arObjects[j].options.set('preset', 'islands#darkGreenIcon');
						}
					});
					BX.bind(objectsItem[i], 'mouseleave', function() {						
						var objectId = this.getAttribute('data-object-id');
						for(var j = 0; j < arObjects.length; j++) {
							if(objectId == arObjects[j].options.get('objectId'))
								arObjects[j].options.set('preset', 'islands#violetIcon');
						}
					});
				}
			}
		<?}?>
	}
</script>