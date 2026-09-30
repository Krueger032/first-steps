<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\Page\Asset;

global $USER;
if(!$USER->IsAuthorized() && $arParams["GUEST_MODE"] !== "Y")
	return;

global $arSettings;

if($arParams["GUEST_MODE"] !== "Y"){
	Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/components/bitrix/sale.order.payment.change/.default/script.js");
	Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/components/bitrix/sale.order.payment.change/.default/style.css");
}

CJSCore::Init(array("clipboard", "fx"));

$APPLICATION->SetTitle(Loc::getMessage("SPOD_TITLE_ORDER_DETAIL", array("#ID#" => $arResult["ACCOUNT_NUMBER"])));

if(!empty($arResult["ERRORS"]["FATAL"])) {
	foreach($arResult["ERRORS"]["FATAL"] as $error)
		ShowError($error);

	$component = $this->__component;

	if($arParams["AUTH_FORM_IN_TEMPLATE"] && isset($arResult["ERRORS"]["FATAL"][$component::E_NOT_AUTHORIZED]))
		$APPLICATION->AuthForm("", false, false, "N", false);
} else {
	if(!empty($arResult["ERRORS"]["NONFATAL"]))
		foreach($arResult["ERRORS"]["NONFATAL"] as $error)
			ShowError($error);?>
	
	<div class="sale-order-detail">
		<?//TOP_BLOCK//?>
		<div class="row sod-top">
			<div class="col-xs-5 col-md-3 sod-top-date">
				<div class="sod-top-date__date"><?=$arResult["DATE_INSERT_FORMATED"]?></div>
			</div>
			<ul class="col-xs-7 col-md-9 sod-top-buttons">
				<?if($arParams["GUEST_MODE"] !== "Y") {
					if($arResult["CAN_CANCEL"] === "Y") {?>
						<li class="sod-top-buttons__btn">
							<a class="btn btn-default btn__order-close" href="<?=$arResult['URL_TO_CANCEL']?>"><i class="icon-close"></i><span class="hidden-xs hidden-sm"><?=Loc::getMessage("SPOD_ORDER_CANCEL")?></span></a>
						</li>
					<?}?>
					<li class="sod-top-buttons__btn">
						<a class="btn btn-default" href="<?=$arResult['URL_TO_COPY']?>"><i class="icon-repeat"></i><span class="hidden-xs hidden-sm"><?=Loc::getMessage("SPOD_ORDER_REPEAT")?></span></a>
					</li>
				<?}?>
				<li class="sod-top-buttons__btn">
					<a class="btn btn-default" href="javascript:window.print(); void(0);"><i class="icon-print"></i><span class="hidden-xs hidden-sm"><?=Loc::getMessage("SPOD_ORDER_PRINT")?></span></a>
				</li>
			</ul>					
		</div>
		<?//ABOUT_ORDER_BLOCK//?>
		<div class="sod-about-order">
			<div class="sod-title-container">
				<div class="sod-title">
					<div class="sod-title__icon"><i class="icon-article"></i></div>
					<div class="sod-title__val"><?=Loc::getMessage("SPOD_LIST_ORDER_INFO")?></div>
				</div>
			</div>
			<div class="sod-block-container">
				<div class="row sod-about-order-inner">
					<div class="col-xs-12 col-md-3 sod-about-order-inner-name">
						<div class="sod-about-order-inner-name__title sod-s-title">
							<?$userName = $arResult["USER"]["NAME"]." ".$arResult["USER"]["SECOND_NAME"]." ".$arResult["USER"]["LAST_NAME"];
							if(strlen($userName) || strlen($arResult["FIO"])) {
								echo Loc::getMessage("SPOD_LIST_FIO");
							} else {
								echo Loc::getMessage("SPOD_LOGIN");
							}?>
						</div>
						<div class="sod-about-order-inner-name__val sod-s-val">
							<?if(strlen($userName)) {
								echo htmlspecialcharsbx($userName);
							} elseif(strlen($arResult["FIO"])) {
								echo htmlspecialcharsbx($arResult["FIO"]);
							} else {
								echo htmlspecialcharsbx($arResult["USER"]["LOGIN"]);
							}?>
						</div>
						<a class="sod-about-order-inner-name__read-less sod-about-order-inner-name__read_style">
							<span><?=Loc::getMessage("SPOD_LIST_LESS")?></span>
							<i class="icon-arrow-up"></i>
						</a>
						<a class="sod-about-order-inner-name__read-more sod-about-order-inner-name__read_style">
							<span><?=Loc::getMessage("SPOD_LIST_MORE")?></span>
							<i class="icon-arrow-down"></i>
						</a>
					</div>
					<div class="col-xs-12 col-md-6 sod-about-order-inner-status">
						<div class="sod-about-order-inner-status__title sod-s-title">
							<?=Loc::getMessage("SPOD_LIST_CURRENT_STATUS", array(
								"#DATE_ORDER_CREATE#" => $arResult["DATE_INSERT_FORMATED"]
							)) ?>
						</div>
						<div class="sod-about-order-inner-status__val <?=($arResult['CANCELED'] !== 'N' ? 'canceled' : mb_strtolower($arResult['STATUS_ID']))?>">
							<span>
								<?if($arResult["CANCELED"] !== "Y") {
									echo htmlspecialcharsbx($arResult["STATUS"]["NAME"]);
								} else {
									echo Loc::getMessage("SPOD_ORDER_CANCELED");
								}?>
							</span>
						</div>
					</div>
					<div class="col-xs-12 col-md-3 sod-about-order-inner-price">
						<div class="sod-about-order-inner-price__title sod-s-title"><?=Loc::getMessage("SPOD_ORDER_PRICE_FULL")?></div>
						<div class="sod-about-order-inner-price__val sod-s-big-black-val"><?=$arResult["PRICE_FORMATED"]?></div>
					</div>
					<div class="col-xs-12 sod-about-order-inner-details">
						<div class="sod-title-container">
							<div class="sod-title">
								<div class="sod-title__val"><?=Loc::getMessage("SPOD_USER_INFORMATION")?></div>
							</div>
						</div>
						<div class="sod-subblock-container">
							<ul class="sod-about-order-inner-details-list">
								<?if(strlen($arResult["USER"]["LOGIN"]) && !in_array("LOGIN", $arParams["HIDE_USER_INFO"])) {?>
									<li class="sod-about-order-inner-details-list__item sod-s-title">
										<?=Loc::getMessage("SPOD_LOGIN")?>
										<div class="sod-about-order-inner-details-list__item-element sod-s-val"><?=htmlspecialcharsbx($arResult["USER"]["LOGIN"]) ?></div>
									</li>
								<?}
								if(strlen($arResult["USER"]["EMAIL"]) && !in_array("EMAIL", $arParams["HIDE_USER_INFO"])) {?>
									<li class="sod-about-order-inner-details-list__item sod-s-title">
										<?=Loc::getMessage("SPOD_EMAIL")?>
										<a class="sod-about-order-inner-details-list__item-link sod-s-val" href="mailto:<?=htmlspecialcharsbx($arResult['USER']['EMAIL'])?>"><?=htmlspecialcharsbx($arResult["USER"]["EMAIL"])?></a>
									</li>
								<?}
								if(strlen($arResult["USER"]["PERSON_TYPE_NAME"]) && !in_array("PERSON_TYPE_NAME", $arParams["HIDE_USER_INFO"])) {?>
									<li class="sod-about-order-inner-details-list__item sod-s-title">
										<?=Loc::getMessage("SPOD_PERSON_TYPE_NAME")?>
										<div class="sod-about-order-inner-details-list__item-element sod-s-val"><?=htmlspecialcharsbx($arResult["USER"]["PERSON_TYPE_NAME"])?></div>
									</li>
								<?}
								if(isset($arResult["ORDER_PROPS"])) {
									foreach($arResult["ORDER_PROPS"] as $property) {?>
										<li class="sod-about-order-inner-details-list__item sod-s-title">
											<?=htmlspecialcharsbx($property["NAME"])?>
											<div class="sod-about-order-inner-details-list__item-element sod-s-val">
												<?if($property["TYPE"] == "Y/N") {
													echo Loc::getMessage("SPOD_".($property["VALUE"] == "Y" ? "YES" : "NO"));
												} else {
													if($property["MULTIPLE"] == "Y" && $property["TYPE"] !== "FILE" && $property["TYPE"] !== "LOCATION") {
														$propertyList = unserialize($property["VALUE"]);
														foreach($propertyList as $propertyElement) {
															echo $propertyElement."</br>";
														}
													} elseif($property["TYPE"] == "FILE") {
														echo $property["VALUE"];
													} else {
														echo htmlspecialcharsbx($property["VALUE"]);
													}
												}?>
											</div>
										</li>
									<?}
								}
								if(strlen($arResult["USER_DESCRIPTION"])) {?>
									<li class="sod-about-order-inner-details-list__item sod-s-title">
										<?=Loc::getMessage("SPOD_ORDER_DESC")?>
										<div class="sod-about-order-inner-details-list__item-element sod-s-val"><?=nl2br(htmlspecialcharsbx($arResult["USER_DESCRIPTION"]))?></div>
									</li>
								<?}?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?//PAYMENT_BLOCK//?>
		<div class="sod-payment">
			<div class="sod-title-container">
				<div class="sod-title">
					<div class="sod-title__icon"><i class="icon-card"></i></div>
					<div class="sod-title__val"><?=Loc::getMessage("SPOD_ORDER_PAYMENT")?></div>
				</div>
			</div>
			<div class="sod-block-container">
				<div class="sod-payment-inner">
					<?foreach($arResult["PAYMENT"] as $payment) {?>
						<div class="sod-payment-inner-payment-container sod-s-separator-blocks">
							<div class="row sod-payment-inner-payment">
								<div class="col-xs-12 col-md-3 sod-payment-inner-info-payment">
									<?$paymentData[$payment["ACCOUNT_NUMBER"]] = array(
										"payment" => $payment["ACCOUNT_NUMBER"],
										"order" => $arResult["ACCOUNT_NUMBER"],
										"allow_inner" => $arParams["ALLOW_INNER"],
										"only_inner_full" => $arParams["ONLY_INNER_FULL"]
									);
									$paymentSubTitle = Loc::getMessage("SPOD_TPL_BILL_DETAIL", array("#ACCOUNT_NUMBER#" => $payment["ACCOUNT_NUMBER"]));
									if(isset($payment["DATE_BILL"]))
										$paymentSubTitle .= Loc::getMessage("SPOD_TPL_BILL_FROM_DETAIL", array("#DATE_BILL#" => $payment["DATE_BILL"]->format($arParams["ACTIVE_DATE_FORMAT"])));?>
									<div class="sod-payment-inner-info-payment__title sod-s-title"><?=htmlspecialcharsbx($paymentSubTitle);?></div>
									<div class="sod-payment-inner-info-payment__name sod-s-val" data-name="<?=$payment['ACCOUNT_NUMBER']?>"><?=$payment["PAY_SYSTEM_NAME"]?></div>
									<?if($payment["PAID"] !== "Y" && $arResult["CANCELED"] !== "Y" && $arParams["GUEST_MODE"] !== "Y" && $arResult["LOCK_CHANGE_PAYSYSTEM"] !== "Y") {?>
										<a href="#" id="<?=$payment['ACCOUNT_NUMBER']?>" class="sod-payment-inner-info-payment__change-link_open"><span><?=Loc::getMessage("SPOD_CHANGE_PAYMENT_TYPE")?></span><i class="icon-arrow-down"></i></a>
										<a href="javascript:void(0)" class="sod-payment-inner-info-payment__change-link_close"><span><?=Loc::getMessage("SPOD_CHANGE_PAYMENT_TYPE")?></span><i class="icon-arrow-up"></i></a>
									<?}?>
								</div>
								<div class="col-xs-12 col-md-6 sod-payment-inner-info-status">
									<div class="sod-payment-inner-info-status__title sod-s-title"><?=Loc::getMessage("SPOD_PAYMENT_STATUS")?></div>
									<div class="sod-payment-inner-info-status__status">
										<?if($payment["PAID"] === "Y") {
											ShowNote(Loc::getMessage("SPOD_PAYMENT_PAID"), "success");
										} elseif($arResult["IS_ALLOW_PAY"] == "N") {
											ShowNote(Loc::getMessage("SPOD_TPL_RESTRICTED_PAID"), "warning");
										} else {
											ShowNote(Loc::getMessage("SPOD_PAYMENT_UNPAID"), "error");
										}?>
									</div>
									<?if($arResult["IS_ALLOW_PAY"] === "N" && $payment["PAID"] !== "Y") {?>
										<div class="sod-payment-inner-info-status__restricted-message sod-s-title"><?=Loc::getMessage("SOPD_TPL_RESTRICTED_PAID_MESSAGE")?></div>
									<?}
									if(!empty($payment["CHECK_DATA"])) {
										$listCheckLinks = "";
										foreach($payment["CHECK_DATA"] as $checkInfo) {
											$title = Loc::getMessage("SPOD_CHECK_NUM", array("#CHECK_NUMBER#" => $checkInfo["ID"]))." - ". htmlspecialcharsbx($checkInfo["TYPE_NAME"]);
											if(strlen($checkInfo["LINK"]) > 0) {
												$link = $checkInfo["LINK"];
												$listCheckLinks .= "<div class=\"sod-payment-inner-info-status-total-check__check\"><a href='$link' target='_blank'>$title</a></div>";
											}
										}
										if(strlen($listCheckLinks) > 0) {?>
											<div class="sod-payment-inner-info-status-total-check">
												<div class="sod-payment-inner-info-status-total-check__title-check sod-s-title"><?=Loc::getMessage("SPOD_CHECK_TITLE")?></div>
												<?=$listCheckLinks?>
											</div>
										<?}
									}?>
								</div>
								<div class="col-xs-12 col-md-3 sod-payment-inner-price">
									<div class="sod-payment-inner-price__title sod-s-title"><?=Loc::getMessage("SPOD_ORDER_PRICE_BILL")?></div>
									<div class="sod-payment-inner-price__val sod-s-big-black-val"><?=$payment["PRICE_FORMATED"]?></div>
									<?if($payment["PAY_SYSTEM"]["IS_CASH"] !== "Y" && $arResult["IS_ALLOW_PAY"] !== "N" && $arResult["CANCELED"] !== "Y") {?>
										<div class="sod-payment-inner-pay-button">
											<?if($payment["PAY_SYSTEM"]["PSA_NEW_WINDOW"] === "Y") {?>
												<a class="btn btn-buy" target="_blank" href="<?=htmlspecialcharsbx($payment['PAY_SYSTEM']['PSA_ACTION_FILE'])?>"><span><?=Loc::getMessage("SPOD_ORDER_PAY")?></span></a>
											<?} elseif($payment["PAID"] !== "Y") {?>
												<a class="btn btn-buy active-button"><span><?=Loc::getMessage("SPOD_ORDER_PAY")?></span></a>
											<?}?>
										</div>
									<?}?>
								</div>
							</div>
							<div class="sod-payment-inner-selectpay hidden">
								<div class="sod-title-container">
									<div class="sod-title">
										<div class="sod-title__val"><?=Loc::getMessage("SPOD_CHANGE_PAYMENT_TYPE")?></div>
									</div>
								</div>
								<div class="sod-subblock-container">
									<div class="sod-payment-inner-select-pay"></div>
									<div class="sod-payment-inner-select-pay__button-back">
										<a class="btn btn-default"><i class="icon-arrow-left"></i><?=Loc::getMessage("SPOD_CANCEL_PAYMENT")?></a>
									</div>
								</div>
							</div>
							<?if($payment["PAID"] !== "Y" && $payment["PAY_SYSTEM"]["IS_CASH"] !== "Y" && $payment["PAY_SYSTEM"]["PSA_NEW_WINDOW"] !== "Y" && $arResult["CANCELED"] !== "Y" && $arResult["IS_ALLOW_PAY"] !== "N") {?>
								<div class="sod-payment-inner-methods">
									<div class="sod-title-container">
										<div class="sod-title">
											<div class="sod-title__val"><?=Loc::getMessage("SPOD_PAY_PAYMENT")?></div>
										</div>
									</div>
									<div class="sod-subblock-container">
										<div class="sod-payment-inner-methods-template"><?=$payment["BUFFERED_OUTPUT"]?></div>
										<div class="sod-payment-inner-methods-template__button-back">
											<a class="btn btn-default active-button"><i class="icon-arrow-left"></i><?=Loc::getMessage("SPOD_CANCEL_PAYMENT")?></a>
										</div>
									</div>
								</div>
							<?}?>
						</div>
					<?}?>
				</div>
			</div>
		</div>
		<?//SHIPMENT_BLOCK//
		if(count($arResult["SHIPMENT"])) {?>
			<div class="sod-shipment">
				<div class="sod-title-container">
					<div class="sod-title">
						<div class="sod-title__icon"><i class="icon-delivery"></i></div>
						<div class="sod-title__val"><?=Loc::getMessage("SPOD_ORDER_SHIPMENT")?></div>
					</div>
				</div>
				<div class="sod-block-container">
					<div class="sod-shipment-list">
						<?foreach($arResult["SHIPMENT"] as $shipment) {?>
							<div class="sod-shipment-list-item-container sod-s-separator-blocks">
								<div class="row sod-shipment-list-item">
									<div class="col-xs-12 col-md-3 sod-shipment-list-item-info">
										<div class="sod-shipment-list-item-info__title sod-s-title">
											<?=Loc::getMessage("SPOD_SUB_ORDER_SHIPMENT", array("#ACCOUNT_NUMBER#" => htmlspecialcharsbx($shipment["ACCOUNT_NUMBER"])))?>
										</div>
										<?if(strlen($shipment["DELIVERY_NAME"])) {?>
											<div class="sod-shipment-list-item-info__val sod-s-val"><?=htmlspecialcharsbx($shipment["DELIVERY_NAME"])?></div>
										<?}
										if(strlen($shipment["TRACKING_NUMBER"])) {?>
											<div class="sod-shipment-list-item__tracking-title sod-s-title"><?=Loc::getMessage("SPOD_ORDER_TRACKING_NUMBER")?></div>
											<div class="sod-shipment-list-item__tracking-number sod-s-val"><?=htmlspecialcharsbx($shipment["TRACKING_NUMBER"])?></div>
										<?}
										if(strlen($shipment["TRACKING_URL"])) {?>
											<div class="sod-shipment-list-item__tracking-url">
												<a href="<?=$shipment['TRACKING_URL']?>"><?=Loc::getMessage("SPOD_ORDER_CHECK_TRACKING")?></a>
											</div>
										<?}?>
										<div class="sod-shipment-list-item-info-link">
											<a class="sod-shipment-list-item-info-link__show sod-shipment-list-item-info-link__style"><span><?=Loc::getMessage("SPOD_LIST_MORE")?></span><i class="icon-arrow-down"></i></a>
											<a class="sod-shipment-list-item-info-link__hide sod-shipment-list-item-info-link__style"><span><?=Loc::getMessage("SPOD_LIST_LESS")?></span><i class="icon-arrow-up"></i></a>
										</div>
									</div>
									<div class="col-xs-12 col-md-6 sod-shipment-list-item-status">
										<div class="sod-shipment-list-item-status__title sod-s-title"><?=Loc::getMessage("SPOD_ORDER_SHIPMENT_STATUS")?></div>
										<div class="sod-shipment-list-item-status__val<?=($shipment['STATUS_ID'] == 'DF' ? ' df' : '')?>"><?=htmlspecialcharsbx($shipment["STATUS_NAME"])?></div>
									</div>
									<div class="col-xs-12 col-md-3 sod-shipment-list-item-price">
										<div class="sod-shipment-list-item-price__title sod-s-title"><?=Loc::getMessage("SPOD_SUB_PRICE_DELIVERY")?></div>
										<div class="sod-shipment-list-item-price__val sod-s-big-black-val"><?=$shipment["PRICE_DELIVERY_FORMATED"]?></div>
									</div>
									<div class="col-xs-12 sod-shipment-list-item-detail-container">
										<div class="sod-shipment-list-item-detail">
											<?$store = $arResult["DELIVERY"]["STORE_LIST"][$shipment["STORE_ID"]];
											if(isset($store)) {?>
												<div class="sod-shipment-list-item-detail-map">
													<div class="sod-title-container">
														<div class="sod-title">
															<div class="sod-title__val"><?=Loc::getMessage("SPOD_SHIPMENT_STORE")?></div>
														</div>
													</div>
													<div class="sod-subblock-container">
														<div class="sod-shipment-list-item-detail-map-inner">
															<?if(strlen($store["ADDRESS"])) {?>
																<div class="sod-shipment-list-item-detail-map-inner__address-title sod-s-title"><?=Loc::getMessage("SPOD_STORE_ADDRESS")?></div>
																<div class="sod-shipment-list-item-detail-map-inner__address-val sod-s-val"><?=htmlspecialcharsbx($store["ADDRESS"])?></div>
															<?}
															if($arSettings["MAP_SERVICE"]["VALUE"] != "YANDEX") {?>
																<?$APPLICATION->IncludeComponent("bitrix:map.google.view", "",
																	array(
																		"API_KEY" => Bitrix\Main\Config\Option::get("fileman", "google_map_api_key"),
																		"CONTROLS" => array(
																			0 => "SMALL_ZOOM_CONTROL",
																		),
																		"INIT_MAP_TYPE" => "ROADMAP",
																		"MAP_DATA" => serialize(
																			array(
																				"google_lon" => $store["GPS_S"],
																				"google_lat" => $store["GPS_N"],
																				"google_scale" => "13",
																				"PLACEMARKS" => array(
																					array(
																						"LON" => $store["GPS_S"],
																						"LAT" => $store["GPS_N"],
																						"TEXT" => htmlspecialcharsbx($store["TITLE"])
																					)
																				)
																			)
																		),
																		"MAP_HEIGHT" => "300",
																		"MAP_ID" => "sod",
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
															<?} else {?>
																<?$APPLICATION->IncludeComponent("altop:map.yandex.view.elektronext", "",
																	array(
																		"API_KEY" => Bitrix\Main\Config\Option::get("fileman", "yandex_map_api_key"),
																		"CONTROLS" => array(
																			0 => "fullscreenControl",
																			1 => "zoomControl",
																		),
																		"INIT_MAP_TYPE" => "map",
																		"MAP_DATA" => serialize(
																			array(
																				"yandex_lon" => $store["GPS_S"],
																				"yandex_lat" => $store["GPS_N"],
																				"yandex_scale" => "14",
																				"PLACEMARKS" => array(
																					array(
																						"LON" => $store["GPS_S"],
																						"LAT" => $store["GPS_N"],
																						"TEXT" => htmlspecialcharsbx($store["TITLE"])
																					)
																				)
																			)
																		),
																		"MAP_HEIGHT" => "300",
																		"MAP_ID" => "sod",
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
												</div>
											<?}?>
											<div class="sod-shipment-list-item-detail__itemlist sod-itemlist">
												<div class="sod-title-container">
													<div class="row sod-title">
														<div class="col-xs-12 col-md-9 sod-title__val"><?=Loc::getMessage("SPOD_ORDER_SHIPMENT_BASKET")?></div><!--
														--><div class="hidden-xs hidden-sm col-xs-3 sod-title__val"><?=Loc::getMessage("SPOD_QUANTITY")?></div>
													</div>
												</div>
												<div class="sod-subblock-container">
													<div class="sod-itemlist-inner">
														<?foreach($shipment["ITEMS"] as $item) {
															$basketItem = $arResult["BASKET"][$item["BASKET_ID"]];?>
															<div class="row sod-itemlist-inner-item nof sod-s-separator-blocks">
																<div class="col-xs-12 col-md-9 sod-itemlist-inner-item-prop">
																	<?if(strlen($basketItem["PICTURE"]["SRC"])) {
																		$image["SRC"] = htmlspecialcharsbx($basketItem["PICTURE"]["SRC"]);
																		$image["WIDTH"] = htmlspecialcharsbx($basketItem["PICTURE"]["WIDTH"]);
																		$image["HEIGHT"] = htmlspecialcharsbx($basketItem["PICTURE"]["HEIGHT"]);
																	} else {
																		$image["SRC"] = SITE_TEMPLATE_PATH."/images/no_photo.png";
																		$image["WIDTH"] = 110;
																		$image["HEIGHT"] = 110;
																	}?>
																	<div class="sod-itemlist-inner-item-prop__img">
																		<img src="<?=$image['SRC']?>" width="<?=$image['WIDTH']?>" height="<?=$image['HEIGHT']?>" alt="<?=htmlspecialcharsbx($basketItem['NAME'])?>" />
																	</div><!--
																	--><div class="sod-itemlist-inner-item-prop-inner">
																		<?if(!empty($basketItem["ARTNUMBER"])) {?>
																			<div class="sod-itemlist-inner-item-prop-inner__article"><?=Loc::getMessage("SPOD_CATALOG_ARTICLE").": ".htmlspecialcharsbx($basketItem["ARTNUMBER"])?></div>
																		<?}?>
																		<div class="sod-itemlist-inner-item-prop-inner__title">
																			<a href="<?=htmlspecialcharsbx($basketItem['DETAIL_PAGE_URL'])?>"><?=htmlspecialcharsbx($basketItem["NAME"])?></a>
																		</div>
																		<?if(!empty($basketItem["PROPS"])) {
																			foreach($basketItem["PROPS"] as $itemProp) {
																				if($itemProp["CODE"] != "ARTNUMBER") {?>
																					<div class="sod-itemlist-inner-item-prop-inner__prop"><?=htmlspecialcharsbx($itemProp["NAME"]).": ".htmlspecialcharsbx($itemProp["VALUE"])?></div>
																				<?}
																			}
																			unset($itemProp);
																		}?>
																	</div>
																</div><!--
																--><div class="col-xs-12 col-md-3 sod-itemlist-inner-item-count">
																	<div class="sod-itemlist-inner-item-count__val"><?=$item["QUANTITY"]." ".htmlspecialcharsbx($item["MEASURE_NAME"])?></div>
																</div>
															</div>
														<?}?>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						<?}?>
					</div>
				</div>
			</div>
		<?}
		//ORDER_ITEMS//?>
		<div class="sod-itemlist">
			<div class="sod-title-container">
				<div class="row sod-title">
					<div class="col-xs-12 col-md-6 sod-title__val">
						<div class="sod-title__val-icon"><i class="icon-bag-shop"></i></div>
						<div class="sod-title__val-text"><?=Loc::getMessage("SPOD_ORDER_BASKET")?></div>
					</div><!--
					--><div class="hidden-xs hidden-sm col-xs-1 sod-title__val sod-s-right-t"><?=(strlen($arResult["SHOW_DISCOUNT_TAB"]) ? Loc::getMessage("SPOD_DISCOUNT") : "")?></div><!--
					--><div class="hidden-xs hidden-sm col-xs-2 sod-title__val sod-s-right-t"><?=Loc::getMessage("SPOD_PRICE")?></div><!--
					--><div class="hidden-xs hidden-sm col-xs-1 sod-title__val sod-s-right-t"><?=Loc::getMessage("SPOD_QUANTITY")?></div><!--
					--><div class="hidden-xs hidden-sm col-xs-2 sod-title__val sod-s-right-t"><?=Loc::getMessage("SPOD_ORDER_PRICE")?></div>
				</div>
			</div>
			<div class="sod-block-container">
				<div class="sod-itemlist-inner">
					<?$sumPrice = 0;
					foreach($arResult["BASKET"] as $basketItem) {
						$sumPrice += $basketItem["BASE_PRICE"] * $basketItem["QUANTITY"];?>
						<div class="row sod-itemlist-inner-item nof sod-s-separator-blocks">
							<div class="col-xs-12 col-md-6 sod-itemlist-inner-item-prop">
								<?if(strlen($basketItem["PICTURE"]["SRC"])) {
									$image["SRC"] = htmlspecialcharsbx($basketItem["PICTURE"]["SRC"]);
									$image["WIDTH"] = htmlspecialcharsbx($basketItem["PICTURE"]["WIDTH"]);
									$image["HEIGHT"] = htmlspecialcharsbx($basketItem["PICTURE"]["HEIGHT"]);
								} else {
									$image["SRC"] = SITE_TEMPLATE_PATH."/images/no_photo.png";
									$image["WIDTH"] = 110;
									$image["HEIGHT"] = 110;
								}?>
								<div class="sod-itemlist-inner-item-prop__img">
									<img src="<?=$image['SRC']?>" width="<?=$image['WIDTH']?>" height="<?=$image['HEIGHT']?>" alt="<?=htmlspecialcharsbx($basketItem['NAME'])?>" />
								</div><!--
								--><div class="sod-itemlist-inner-item-prop-inner">
									<?if(!empty($basketItem["ARTNUMBER"])) {?>
										<div class="sod-itemlist-inner-item-prop-inner__article"><?=Loc::getMessage("SPOD_CATALOG_ARTICLE").": ".htmlspecialcharsbx($basketItem["ARTNUMBER"])?></div>
									<?}?>
									<div class="sod-itemlist-inner-item-prop-inner__title">
										<a href="<?=htmlspecialcharsbx($basketItem['DETAIL_PAGE_URL'])?>"><?=htmlspecialcharsbx($basketItem["NAME"])?></a>
									</div>
									<?if(!empty($basketItem["PROPS"])) {
										foreach($basketItem["PROPS"] as $itemProp) {
											if($itemProp["CODE"] != "ARTNUMBER") {?>
												<div class="sod-itemlist-inner-item-prop-inner__prop"><?=htmlspecialcharsbx($itemProp["NAME"]).": ".htmlspecialcharsbx($itemProp["VALUE"])?></div>
											<?}
										}
										unset($itemProp);
									}?>
								</div>
							</div><!--
							--><div class="hidden-xs hidden-sm col-xs-1 sod-itemlist-inner-item-discount sod-s-right-t">
								<?if(strlen($basketItem["DISCOUNT_PRICE_PERCENT_FORMATED"])) {?>
									<div class="sod-itemlist-inner-item-discount__val"><?=$basketItem["DISCOUNT_PRICE_PERCENT_FORMATED"]?></div>
								<?}?>
							</div><!--
							--><div class="hidden-xs hidden-sm col-xs-2 sod-itemlist-inner-item-price sod-s-right-t">
								<div class="sod-itemlist-inner-item-price__val">
									<?=$basketItem["PRICE_FORMATED"];
									if(strlen($basketItem["DISCOUNT_PRICE_PERCENT_FORMATED"])) {?>
										<div class="sod-itemlist-inner-item-price__base-val"><?=$basketItem["BASE_PRICE_FORMATED"]?></div>
									<?}?>
								</div>
							</div><!--
							--><div class="col-xs-6 col-md-1 sod-itemlist-inner-item-count sod-s-right-t">
								<div class="sod-itemlist-inner-item-count__val"><?=$basketItem["QUANTITY"]." ".(strlen($basketItem["MEASURE_NAME"]) ? htmlspecialcharsbx($basketItem["MEASURE_NAME"]) : Loc::getMessage("SPOD_DEFAULT_MEASURE"));?></div>
							</div><!--
							--><div class="col-xs-6 col-md-2 sod-itemlist-inner-item-sum sod-s-right-t">
								<div class="sod-itemlist-inner-item-sum__val"><?=$basketItem["FORMATED_SUM"]?></div>
							</div>
						</div>
					<?}?>
					<div class="row sod-itemlist-inner-item">
						<div class="col-xs-12 col-md-8"></div>
						<div class="col-xs-12 col-md-4 sod-itemlist-inner-item-total">
							<div class="row sod-itemlist-inner-item-total-row">
								<div class="col-xs-3 sod-itemlist-inner-item-total__title sod-s-title sod-s-right-t"><?=Loc::getMessage("SPOD_COMMON_SUM")?>:</div>
								<div class="col-xs-9 sod-itemlist-inner-item-total__val sod-s-right-t">
									<?=$arResult["PRODUCT_SUM_FORMATED"];
									if($sumPrice - $arResult["PRODUCT_SUM"] != 0) {?>
										<div class="sod-itemlist-inner-item-total__val sod-s-old-price"><?=$arResult["PRICE_NO_DISCOUNT_FORMATED"]?></div>
										<div class="sod-itemlist-inner-item-total__val sod-s-saving-price"><?=Loc::getMessage("SPOD_SAVING")." ".$arResult["DISCOUNT_PRICE_FORMATED"]?></div>
									<?}?>
								</div>
							</div>
							<?if(floatval($arResult["ORDER_WEIGHT"])) {?>
								<div class="row sod-itemlist-inner-item-total-row">
									<div class="col-xs-3 sod-itemlist-inner-item-total__title sod-s-title sod-s-right-t"><?=Loc::getMessage("SPOD_TOTAL_WEIGHT")?>:</div>
									<div class="col-xs-9 sod-itemlist-inner-item-total__val sod-s-right-t"><?=$arResult["ORDER_WEIGHT_FORMATED"]?></div>
								</div>
							<?}
							if(strlen($arResult["PRICE_DELIVERY_FORMATED"])) {?>
								<div class="row sod-itemlist-inner-item-total-row">
									<div class="col-xs-3 sod-itemlist-inner-item-total__title sod-s-title sod-s-right-t"><?=Loc::getMessage("SPOD_DELIVERY")?>:</div>
									<div class="col-xs-9 sod-itemlist-inner-item-total__val sod-s-right-t"><?=$arResult["PRICE_DELIVERY_FORMATED"]?></div>
								</div>
							<?}
							if((float)$arResult["TAX_VALUE"] > 0) {?>
								<div class="row sod-itemlist-inner-item-total-row">
									<div class="col-xs-3 sod-itemlist-inner-item-total__title sod-s-title sod-s-right-t"><?=Loc::getMessage("SPOD_TAX")?>:</div>
									<div class="col-xs-9 sod-itemlist-inner-item-total__val sod-s-right-t"><?=$arResult["TAX_VALUE_FORMATED"]?></div>
								</div>
							<?}?>
							<div class="row sod-itemlist-inner-item-total-row">
								<div class="col-xs-3 sod-itemlist-inner-item-total__title sod-s-title sod-s-right-t"><?=Loc::getMessage("SPOD_SUMMARY")?>:</div>
								<div class="col-xs-9 sod-itemlist-inner-item-total__val sod-s-big-black-val sod-s-right-t"><?=$arResult["PRICE_FORMATED"]?></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?if($arParams["GUEST_MODE"] !== "Y" && $arResult["LOCK_CHANGE_PAYSYSTEM"] !== "Y") {?>
			<div class="sod-buttons">
				<a class="btn btn-default btn__back-to-list-order" href="<?=$arResult['URL_TO_LIST']?>"><i class="icon-arrow-left"></i><?=Loc::getMessage("SPOD_RETURN_LIST_ORDERS")?></a>
			</div>
		<?}?>
	</div>
	
	<?$javascriptParams = array(
		"url" => CUtil::JSEscape($this->__component->GetPath()."/ajax.php"),
		"templateFolder" => CUtil::JSEscape($templateFolder),
		"paymentList" => $paymentData
	);?>
	<script>
		BX.Sale.PersonalOrderComponent.PersonalOrderDetail.init(<?=CUtil::PhpToJSObject($javascriptParams)?>);
	</script>
<?}?>