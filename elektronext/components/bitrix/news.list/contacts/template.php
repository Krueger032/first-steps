<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;

global $arSettings;

$elementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$elementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$elementDeleteParams = array("CONFIRM" => Loc::getMessage("CONTACTS_ITEM_DELETE_CONFIRM"));?>

<div class="contacts">
	<?foreach($arResult["ITEMS"] as $arItem) {
		$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], $elementEdit);
		$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], $elementDelete, $elementDeleteParams);
		
		$strMainID = $this->GetEditAreaId($arItem["ID"]);	
		$strObName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);?>
		
		<div class="contacts-item">
			<div class="container">				
				<div class="row">
					<div class="col-xs-12 col-md-4">
						<div class="contacts-item-caption" id="<?=$strMainID?>">
							<div class="contacts-item-title"><?=$arItem["NAME"]?></div>
							<?if(!empty($arItem["ADDRESS"])) {?>
								<div class="contacts-item-row contacts-item-address">
									<div class="contacts-item-icon"><i class="icon-map-marker"></i></div>
									<div class="contacts-item-text"><?=$arItem["ADDRESS"]?></div>
								</div>
							<?}?>
							<div class="contacts-item-row contacts-item-working-hours contacts-item-working-hours-hidden"></div>
							<?if(!empty($arItem["PHONE"])) {
								foreach($arItem["PHONE"]["VALUE"] as $key => $val) {?>
									<div class="contacts-item-row contacts-item-phone">
										<div class="contacts-item-icon"><i class="icon-phone"></i></div>
										<a class="contacts-item-text contacts-item-link" href="tel:<?=preg_replace('/[^0-9+]/', '', $val)?>"><?=$val.(!empty($arItem["PHONE"]["DESCRIPTION"][$key]) ? "<span class='contacts-item-descr'>".$arItem["PHONE"]["DESCRIPTION"][$key]."</span>" : "")?></a>
									</div>
								<?}
								unset($key, $val);
							}
							if(!empty($arItem["WHATSAPP"])) {
								foreach($arItem["WHATSAPP"]["VALUE"] as $key => $val) {?>
									<div class="contacts-item-row contacts-item-whatsapp">
										<div class="contacts-item-icon"><i class="fa fa-whatsapp"></i></div>
										<a target="_blank" class="contacts-item-text contacts-item-link" href="https://wa.me/<?=preg_replace('/[^0-9]/', '', $val)?>"><?=$val.(!empty($arItem["WHATSAPP"]["DESCRIPTION"][$key]) ? "<span class='contacts-item-descr'>".$arItem["WHATSAPP"]["DESCRIPTION"][$key]."</span>" : "")?></a>
									</div>
								<?}
								unset($key, $val);
							}
							if(!empty($arItem["VIBER"])) {
								foreach($arItem["VIBER"]["VALUE"] as $key => $val) {?>
									<div class="contacts-item-row contacts-item-viber">
										<div class="contacts-item-icon"><i class="fa fa-phone"></i></div>
										<a class="contacts-item-text contacts-item-link" href="viber://chat?number=<?=preg_replace('/[^0-9+]/', '', $val)?>"><?=$val.(!empty($arItem["VIBER"]["DESCRIPTION"][$key]) ? "<span class='contacts-item-descr'>".$arItem["VIBER"]["DESCRIPTION"][$key]."</span>" : "")?></a>
										<a class="contacts-item-text contacts-item-link" href="viber://add?number=<?=preg_replace('/[^0-9]/', '', $val)?>"><?=$val.(!empty($arItem["VIBER"]["DESCRIPTION"][$key]) ? "<span class='contacts-item-descr'>".$arItem["VIBER"]["DESCRIPTION"][$key]."</span>" : "")?></a>
									</div>
								<?}
								unset($key, $val);
							}
							if(!empty($arItem["TELEGRAM"])) {
								foreach($arItem["TELEGRAM"]["VALUE"] as $key => $val) {?>
									<div class="contacts-item-row contacts-item-telegram">
										<div class="contacts-item-icon"><i class="fa fa-telegram"></i></div>
										<a target="_blank" class="contacts-item-text contacts-item-link" href="https://t.me/<?=$val?>"><?=$val.(!empty($arItem["TELEGRAM"]["DESCRIPTION"][$key]) ? "<span class='contacts-item-descr'>".$arItem["TELEGRAM"]["DESCRIPTION"][$key]."</span>" : "")?></a>
									</div>
								<?}
								unset($key, $val);
							}
							if(!empty($arItem["INSTAGRAM"])) {
								foreach($arItem["INSTAGRAM"]["VALUE"] as $key => $val) {?>
									<div class="contacts-item-row contacts-item-instagram">
										<div class="contacts-item-icon"><i class="fa fa-instagram"></i></div>
										<a target="_blank" class="contacts-item-text contacts-item-link" href="https://www.instagram.com/<?=$val?>"><?=$val.(!empty($arItem["INSTAGRAM"]["DESCRIPTION"][$key]) ? "<span class='contacts-item-descr'>".$arItem["INSTAGRAM"]["DESCRIPTION"][$key]."</span>" : "")?></a>
									</div>
								<?}
								unset($key, $val);
							}
							if(!empty($arItem["MAX"])) {
								foreach($arItem["MAX"]["VALUE"] as $key => $val) {?>
									<div class="contacts-item-row contacts-item-max">
										<div class="contacts-item-icon"><i class="ri-max"></i></div>
										<a target="_blank" class="contacts-item-text contacts-item-link" href="https://max.ru/u/<?=$val?>"><?=Loc::getMessage("CONTACTS_ITEM_MAX").(!empty($arItem["MAX"]["DESCRIPTION"][$key]) ? "<span class='contacts-item-descr'>".$arItem["MAX"]["DESCRIPTION"][$key]."</span>" : "")?></a>
									</div>
								<?}
								unset($key, $val);
							}
							if(!empty($arItem["EMAIL"])) {
								foreach($arItem["EMAIL"]["VALUE"] as $key => $val) {?>
									<div class="contacts-item-row contacts-item-email">
										<div class="contacts-item-icon"><i class="icon-mail"></i></div>
										<a class="contacts-item-text contacts-item-link" href="mailto:<?=$val?>"><?=$val.(!empty($arItem["EMAIL"]["DESCRIPTION"][$key]) ? "<span class='contacts-item-descr'>".$arItem["EMAIL"]["DESCRIPTION"][$key]."</span>" : "")?></a>
									</div>
								<?}
								unset($key, $val);
							}
							if(!empty($arItem["SKYPE"])) {
								foreach($arItem["SKYPE"]["VALUE"] as $key => $val) {?>
									<div class="contacts-item-row contacts-item-skype">
										<div class="contacts-item-icon"><i class="fa fa-skype"></i></div>
										<a class="contacts-item-text contacts-item-link" href="skype:<?=$val?>?chat"><?=$val.(!empty($arItem["SKYPE"]["DESCRIPTION"][$key]) ? "<span class='contacts-item-descr'>".$arItem["SKYPE"]["DESCRIPTION"][$key]."</span>" : "")?></a>
									</div>
								<?}
								unset($key, $val);
							}
							if($arParams["USE_REVIEW"] != "N" || (isset($arResult["REVIEWS_COUNT"]) && $arResult["REVIEWS_COUNT"] > 0)) {
								if($arResult["REVIEWS_COUNT"] > 0) {?>
									<div class="contacts-item-row">
										<a class="contacts-item-rating-link" href="<?=$arResult['REVIEWS_PAGE_LINK']?>">
											<span class="contacts-item-rating">
												<span class="contacts-item-rating-val"<?=($arResult["RATING_VALUE"] <= 4.4 ? " data-rate='".intval($arResult["RATING_VALUE"])."'" : "")?>><?=$arResult["RATING_VALUE"]?></span>
												<span class="contacts-item-rating-reviews-count"><?=$arResult["REVIEWS_COUNT"]." ".$arResult["REVIEWS_DECLENSION"]?></span>
											</span>
											<span class="contacts-item-rating-text"><?=Loc::getMessage("CONTACTS_ITEM_SEE_REVIEWS")?></span>
										</a>
									</div>
								<?} else {?>
									<div class="contacts-item-btn">
										<a class="btn btn-default" href="<?=$arResult['REVIEWS_PAGE_LINK']?>" role="button"><span><?=Loc::getMessage("CONTACTS_ITEM_ADD_REVIEW")?></span></a>
									</div>
								<?}
							}?>
							<div class="contacts-item-btn">
								<a class="btn btn-primary" href="javascript:void(0)" data-entity="callback" role="button"><i class="icon-phone"></i><span><?=Loc::getMessage("CONTACTS_ITEM_CALLBACK")?></span></a>
							</div>
						</div>
						<?$arJSParams = array(				
							"ITEM" => array(			
								"TIMEZONE" => $arItem["TIMEZONE"],
								"WORKING_HOURS" => $arItem["WORKING_HOURS"]
							),
							"VISUAL" => array(
								"ID" => $strMainID
							)
						);?>
						<script type="text/javascript">
							BX.message({
								CONTACTS_ITEM_TODAY: '<?=GetMessageJS("CONTACTS_ITEM_TODAY");?>',
								CONTACTS_ITEM_24_HOURS: '<?=GetMessageJS("CONTACTS_ITEM_24_HOURS");?>',
								CONTACTS_ITEM_OFF: '<?=GetMessageJS("CONTACTS_ITEM_OFF");?>',
								CONTACTS_ITEM_BREAK: '<?=GetMessageJS("CONTACTS_ITEM_BREAK");?>',
								CONTACTS_LOADING: '<?=GetMessageJS("CONTACTS_LOADING");?>',
								CONTACTS_TEMPLATE_PATH: '<?=CUtil::JSEscape($templateFolder)?>'
							});
							var <?=$strObName;?> = new JCNewsListContacts(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
						</script>
					</div>
				</div>
			</div>
			<div class="contacts-item-map">
				<?$mapData = array();
				if(!empty($arItem["MAP"])) {
					$arTmp = explode(",", $arItem["MAP"]);
					$mapData["PLACEMARKS"][] = array(
						"OBJECT_ID" => $arItem["ID"],
						"LON" => $arTmp[1],
						"LAT" => $arTmp[0],
						"TEXT" => "<div class='object-item-marker'>".(is_array($arItem["PREVIEW_PICTURE"]) ? "<div class='object-item-marker-image'><img src='".$arItem["PREVIEW_PICTURE"]["SRC"]."' /></div>" : "")."<div class='object-item-marker-caption'><div class='object-item-marker-title'>".$arItem["NAME"]."</div>".(!empty($arItem["ADDRESS"]) ? "<div class='object-item-marker-address'><i class='icon-map-marker'></i><span>".$arItem["ADDRESS"]."</span></div>" : "")."</div></div>"
					);
					unset($arTmp);
				}
				if(!empty($arResult["OBJECTS"])) {
					foreach($arResult["OBJECTS"] as $arObject) {
						if(!empty($arObject["PARENT_ID"])) {
							foreach($arObject["PARENT_ID"] as $arId) {
								$mapData["PLACEMARKS"][] = array(
									"OBJECT_ID" => $arObject["ID"],
									"LON" => $arObject["LON"],
									"LAT" => $arObject["LAT"],
									"TEXT" => "<div class='object-item-marker'>".(!empty($arObject["PREVIEW_PICTURE_SRC"]) ? "<div class='object-item-marker-image'><img src='".$arObject["PREVIEW_PICTURE_SRC"]."' /></div>" : "")."<div class='object-item-marker-caption'><div class='object-item-marker-title'>".$arObject["NAME"]."</div>".(!empty($arObject["ADDRESS"]) ? "<div class='object-item-marker-address'><i class='icon-map-marker'></i><span>".$arObject["ADDRESS"]."</span></div>" : "")."<a target='_blank' class='object-item-marker-link' href='".$arResult["OBJECTS"][$arId]["DETAIL_PAGE_URL"]."'>".GetMessage("CONTACTS_ITEM_OBJECT_MORE")."</a></div></div>"
								);
							}
							unset($arId);
						} else {
							$mapData["PLACEMARKS"][] = array(
								"OBJECT_ID" => $arObject["ID"],
								"LON" => $arObject["LON"],
								"LAT" => $arObject["LAT"],
								"TEXT" => "<div class='object-item-marker'>".(!empty($arObject["PREVIEW_PICTURE_SRC"]) ? "<div class='object-item-marker-image'><img src='".$arObject["PREVIEW_PICTURE_SRC"]."' /></div>" : "")."<div class='object-item-marker-caption'><div class='object-item-marker-title'>".$arObject["NAME"]."</div>".(!empty($arObject["ADDRESS"]) ? "<div class='object-item-marker-address'><i class='icon-map-marker'></i><span>".$arObject["ADDRESS"]."</span></div>" : "")."<a target='_blank' class='object-item-marker-link' href='".$arObject["DETAIL_PAGE_URL"]."'>".GetMessage("CONTACTS_ITEM_OBJECT_MORE")."</a></div></div>"
							);
						}
					}
					unset($arObject);
				}
				if($arSettings["MAP_SERVICE"]["VALUE"] != "YANDEX") {
					if(count($mapData["PLACEMARKS"]) == 1) {
						$mapData["google_lat"] = $mapData["PLACEMARKS"][0]["LAT"];
						$mapData["google_lon"] = $mapData["PLACEMARKS"][0]["LON"];
						$mapData["google_scale"] = "13";
					}?>
					<?$APPLICATION->IncludeComponent("bitrix:map.google.view", "",
						array(
							"API_KEY" => Bitrix\Main\Config\Option::get("fileman", "google_map_api_key"),
							"CONTROLS" => array(
								0 => "SMALL_ZOOM_CONTROL",
							),
							"INIT_MAP_TYPE" => "ROADMAP",
							"MAP_DATA" => serialize($mapData),
							"MAP_HEIGHT" => "100%",
							"MAP_ID" => "contacts",
							"MAP_WIDTH" => "100%",
							"OPTIONS" => array(
								0 => "ENABLE_DBLCLICK_ZOOM",
								1 => "ENABLE_DRAGGING",
								2 => "ENABLE_KEYBOARD",
							),
							"COMPONENT_TEMPLATE" => ".default"
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);?>
				<?} else {
					if($mapData["PLACEMARKS"] != false && count($mapData["PLACEMARKS"]) == 1) { //PRO_WS//
						$mapData["yandex_lat"] = $mapData["PLACEMARKS"][0]["LAT"];
						$mapData["yandex_lon"] = $mapData["PLACEMARKS"][0]["LON"];
						$mapData["yandex_scale"] = "14";
					}?>
					<?$APPLICATION->IncludeComponent("altop:map.yandex.view.elektronext", "",
						array(
							"API_KEY" => Bitrix\Main\Config\Option::get("fileman", "yandex_map_api_key"),
							"CONTROLS" => array(
								0 => "fullscreenControl",
								1 => "zoomControl",
							),
							"INIT_MAP_TYPE" => "map",
							"MAP_DATA" => serialize($mapData),
							"MAP_HEIGHT" => "100%",
							"MAP_ID" => "contacts",
							"MAP_WIDTH" => "100%",
							"OPTIONS" => array(
								0 => "drag",
								1 => "dblClickZoom",
								2 => "multiTouch",
								3 => "rightMouseButtonMagnifier",
							)
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);?>
				<?}?>
			</div>
		</div>
	<?}?>
</div>