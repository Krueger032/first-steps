<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);?>
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
<!--5-->
<script>
	if(!window.BX_YMapEnextAddPlacemark) {
		window.BX_YMapEnextAddPlacemark = function(mapId, arPlacemark) {
			if(!arPlacemark.LAT || !arPlacemark.LON)
				return false;

			var props = {},
				opts = {
					preset: 'islands#' + (mapId == 'object' ? 'darkGreenDot' : (mapId == 'offers' ? (arPlacemark.PRICE > 0 && arPlacemark.CAN_BUY ? 'violet' : 'gray') + 'Stretchy' : 'violet')) + 'Icon',
					balloonCloseButton: true,
					hideIconOnBalloonOpen: true
				};
				
			if(mapId == 'offers') {
				props.iconColor = arPlacemark.PRICE > 0 && arPlacemark.CAN_BUY ? 'violet' : 'gray';
				props.iconContent = arPlacemark.PRINT_PRICE;

				BalloonContentLayout = ymaps.templateLayoutFactory.createClass(
					arPlacemark.TEXT.replace(/\n/g, '<br />'), {
						build: function() {
							BalloonContentLayout.superclass.build.call(this);
							
							var objectBtn = this._element.querySelector('[data-entity="object"]');
							if(!!objectBtn) {
								BX.bind(objectBtn, 'click', BX.delegate(function(e) {
									window[arPlacemark.JS_NAME].checkCurrentSkuItem(BX.proxy_context);
									window[arPlacemark.JS_NAME].skuItemObject.callbackForm ? window[arPlacemark.JS_NAME].objectContactsForm(e) : window[arPlacemark.JS_NAME].objectContacts(e);
								}, this));
							}

							var buyBtn = this._element.querySelector('[data-entity="buy"]');
							if(!!buyBtn) {
								BX.bind(buyBtn, 'click', BX.delegate(function(e) {
									window[arPlacemark.JS_NAME].buyBasket(e);
									e.stopPropagation();
								}, this));
							}

							var addBtn = this._element.querySelector('[data-entity="add"]');
							if(!!addBtn) {
								BX.bind(addBtn, 'click', BX.delegate(function(e) {
									window[arPlacemark.JS_NAME].add2Basket(e);
									e.stopPropagation();
								}, this));
							}

							var partnerBtn = this._element.querySelector('[data-entity="partner-link"]');
							if(!!partnerBtn) {
								BX.bind(partnerBtn, 'click', BX.delegate(function(e) {
									window[arPlacemark.JS_NAME].partnerSiteRedirect();
									e.stopPropagation();
								}, this));
							}
						},					
						clear: function() {
							var objectBtn = this._element.querySelector('[data-entity="object"]');
							if(!!objectBtn)
								BX.unbindAll(objectBtn);

							var buyBtn = this._element.querySelector('[data-entity="buy"]');
							if(!!buyBtn)
								BX.unbindAll(buyBtn);

							var addBtn = this._element.querySelector('[data-entity="add"]');
							if(!!addBtn)
								BX.unbindAll(addBtn);

							var partnerBtn = this._element.querySelector('[data-entity="partner-link"]');
							if(!!partnerBtn)
								BX.unbindAll(partnerBtn);
							
							BalloonContentLayout.superclass.clear.call(this);
						}
					}
				);

				opts.balloonContentLayout = BalloonContentLayout;
			}
			
			if(mapId != 'offers' && arPlacemark.TEXT && arPlacemark.TEXT.length > 0)
				props.balloonContent = arPlacemark.TEXT.replace(/\n/g, '<br />');
			
			var obPlacemark = new ymaps.Placemark(
				[arPlacemark.LAT, arPlacemark.LON],
				props,
				opts
			);
			
			return obPlacemark;
		}
	}

	if(!window.BX_YMapEnextSetCenter) {
		function BX_YMapEnextSetCenter(map, mapId) {
			if(null == map)
				return false;

			var offsetX = 0;
			if(window.innerWidth >= 1043 && mapId == 'contacts') {
				var caption = document.body.querySelector('.contacts-item-caption');
				if(!!caption)
					offsetX = caption.getBoundingClientRect().left + caption.offsetWidth;
			}
			
			map.setBounds(map.geoObjects.getBounds(), {
				checkZoomRange: true,
				zoomMargin: [0,0,0,offsetX]
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
				arObjects[<?=$i?>] = BX_YMapEnextAddPlacemark('<?=$arParams["MAP_ID"]?>', <?=CUtil::PhpToJsObject($arResult["POSITION"]["PLACEMARKS"][$i])?>);
			<?}

			if($arParams["MAP_ID"] != "object") {?>
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
							target.options.set('preset', 'islands#darkGreen<?=($arParams["MAP_ID"] == "offers" ? "Stretchy" : "")?>Icon');
						} else {
							<?if($arParams["MAP_ID"] == "offers") {?>
								target.options.set('preset', 'islands#' + target.properties.get('iconColor') + 'StretchyIcon');
							<?} else {?>
								target.options.set('preset', 'islands#violetIcon');
							<?}?>
						}
					}
				});
			<?}?>

			clusterer.add(arObjects);
			map.geoObjects.add(clusterer);
			
			if(arObjects.length > 1) {
				BX_YMapEnextSetCenter(map, '<?=$arParams["MAP_ID"]?>');
				BX.bind(window, 'resize', function() {
					BX_YMapEnextSetCenter(map, '<?=$arParams["MAP_ID"]?>');
				});
				BX.addCustomEvent(window, 'slideMenu', function() {
					map.container.fitToViewport();
					BX_YMapEnextSetCenter(map, '<?=$arParams["MAP_ID"]?>');
				});
			}
		<?}?>
	}
</script>