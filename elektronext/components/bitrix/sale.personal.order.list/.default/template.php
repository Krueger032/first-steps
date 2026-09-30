<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

global $USER;
if(!$USER->IsAuthorized())
	return;

CJSCore::Init(array("clipboard", "fx"));

$this->addExternalCss(SITE_TEMPLATE_PATH."/js/owlCarousel/owl.carousel.min.css");
$this->addExternalJS(SITE_TEMPLATE_PATH."/js/owlCarousel/owl.carousel.min.js");

Loc::loadMessages(__FILE__);

if(!empty($arResult["ERRORS"]["FATAL"])) {
	foreach($arResult["ERRORS"]["FATAL"] as $error)
		ShowError($error);
		
	$component = $this->__component;
	if($arParams["AUTH_FORM_IN_TEMPLATE"] && isset($arResult["ERRORS"]["FATAL"][$component::E_NOT_AUTHORIZED]))
		$APPLICATION->AuthForm("", false, false, "N", false);
} else {
	$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($this->randString()));
	$containerName = 'sale-order-list-'.$obName;?>
	<div class="row sale-order-list" id="<?=$containerName?>">
		<?if($arParams["ONLY_LAST_ORDERS"] != "Y") {?>
			<div class="col-xs-12">
				<div class="sale-order-tabs-block">
					<div class="sale-order-tabs-scroll">
						<ul class="sale-order-tabs-list">
							<?$nothing = !isset($_REQUEST["filter_history"]) && !isset($_REQUEST["show_all"]);
							$clearFromLink = array("filter_history", "filter_status", "show_all", "show_canceled");?>
							<li class="sale-order-tab<?=($nothing ? ' active': '')?>">
								<a href="<?=$APPLICATION->GetCurPageParam("", $clearFromLink, false)?>" class="sale-order-tab-link">
									<span><?=Loc::getMessage("SPOL_TPL_CUR_ORDERS")?></span>
									<span class="sale-order-tab-count"><?=$arResult["COUNT_ORDERS"]["order"]?></span>
								</a>
							</li>
							<li class="sale-order-tab<?=($_REQUEST["filter_history"] == "Y" && $_REQUEST["show_canceled"] != "Y"? ' active': '')?>">
								<a href="<?=$APPLICATION->GetCurPageParam("filter_history=Y", $clearFromLink, false)?>" class="sale-order-tab-link">
									<span><?=Loc::getMessage("SPOL_TPL_VIEW_ORDERS_HISTORY")?></span>
									<span class="sale-order-tab-count"><?=$arResult["COUNT_ORDERS"]["history"]?></span>
								</a>
							</li>
							<li class="sale-order-tab<?=($_REQUEST["filter_history"] == "Y" && $_REQUEST["show_canceled"] == "Y"? ' active': '')?>">
								<a href="<?=$APPLICATION->GetCurPageParam("filter_history=Y&show_canceled=Y", $clearFromLink, false)?>" class="sale-order-tab-link">
									<span><?=Loc::getMessage("SPOL_TPL_VIEW_ORDERS_CANCELED")?></span>
									<span class="sale-order-tab-count"><?=$arResult["COUNT_ORDERS"]["cancel"]?></span>
								</a>
							</li>
							<div class="clearfix"></div>
						</ul>
					</div>
				</div>
			</div>
		<?}
		if(!empty($arResult["ERRORS"]["NONFATAL"])) {?>
			<div class="col-xs-12 sale-order-alert">
				<?foreach($arResult["ERRORS"]["NONFATAL"] as $error)
					ShowError($error);?>
			</div>
		<?}
		if(!count($arResult["ORDERS"])) {?>
			<div class="col-xs-12 sale-order-alert">
				<?if($_REQUEST["filter_history"] == "Y") {
					if($_REQUEST["show_canceled"] == "Y") {
						ShowNote(Loc::getMessage("SPOL_TPL_EMPTY_CANCELED_ORDER"), "warning");
					} else {
						ShowNote(Loc::getMessage("SPOL_TPL_EMPTY_HISTORY_ORDER_LIST"), "warning");
					}
				} else {
					ShowNote(Loc::getMessage("SPOL_TPL_EMPTY_ORDER_LIST"), "warning");
				}?>
			</div>
		<?}
		foreach($arResult["ORDERS"] as $arOrder) {?>
			<div class="col-xs-12 col-md-3 sale-order-item-container" style="height: auto;">
				<div class="sale-order-item">	
					<div class="sale-order-item-image-wrapper">		
						<a class="sale-order-item-image" href="<?=htmlspecialcharsbx($arOrder['ORDER']['URL_TO_DETAIL'])?>" title="<?=Loc::getMessage("SPOL_TPL_ORDER_TITLE", array("#ACCOUNT_NUMBER#" => $arOrder['ORDER']['ACCOUNT_NUMBER']))?>">
							<?if(is_array($arOrder["PREVIEW_PICTURE"])) {?>
								<img src="<?=$arOrder['PREVIEW_PICTURE']['SRC']?>" width="<?=$arOrder['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arOrder['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arOrder['PREVIEW_PICTURE']['ALT']?>" />
							<?}?>
							<div class="sale-order-item-sticker-wrap">
								<span class="sale-order-item-sticker <?=($arOrder['ORDER']['CANCELED'] !== 'N' ? 'canceled' : mb_strtolower($arOrder['ORDER']['STATUS_ID']))?>" title="<?=($arOrder['ORDER']['CANCELED'] !== "N" ? Loc::getMessage('SPOL_TPL_ORDER_CANCELED_STICKER') : $arResult['INFO']['STATUS'][$arOrder['ORDER']['STATUS_ID']]['NAME'])?>">
									<?=($arOrder["ORDER"]["CANCELED"] !== "N" ? Loc::getMessage("SPOL_TPL_ORDER_CANCELED_STICKER") : $arResult["INFO"]["STATUS"][$arOrder["ORDER"]["STATUS_ID"]]["NAME"])?>
								</span>
							</div>
						</a>
					</div>
					<div class="sale-order-item-date"><?=$arOrder["ORDER"]["DATE_INSERT_FORMATED"]?></div>
					<a class="sale-order-item-title" href="<?=htmlspecialcharsbx($arOrder['ORDER']['URL_TO_DETAIL'])?>" title="<?=Loc::getMessage('SPOL_TPL_ORDER_TITLE', array('#ACCOUNT_NUMBER#' => $arOrder['ORDER']['ACCOUNT_NUMBER']))?>"><?=Loc::getMessage("SPOL_TPL_ORDER_TITLE", array("#ACCOUNT_NUMBER#" => $arOrder["ORDER"]["ACCOUNT_NUMBER"]))?></a>
					<div class="sale-order-item-info">
						<?=count($arOrder["BASKET_ITEMS"]);?>
						<?$count = count($arOrder["BASKET_ITEMS"]) % 10;
						if($count == "1")
							echo Loc::getMessage("SPOL_TPL_GOOD");
						elseif($count >= "2" && $count <= "4")
							echo Loc::getMessage("SPOL_TPL_TWO_GOODS");
						else
							echo Loc::getMessage("SPOL_TPL_GOODS");?>
						<?=Loc::getMessage("SPOL_TPL_SUMOF")." ".$arOrder["ORDER"]["FORMATED_PRICE"];?>
					</div>
					<div class="sale-order-item-buttons">
						<?if($arOrder["ORDER"]["IS_ALLOW_PAY"] !== "N" && $arOrder["ORDER"]["CANCELED"] === "N" && count($arOrder["PAYMENT"]) <= 1 && $arOrder["ORDER"]["PAYED"] !== "Y" && $arOrder["PAYMENT"][0]["NEW_WINDOW"] === "Y") {?>
							<a class="btn btn-buy" title="<?=Loc::getMessage('SPOL_TPL_PAY')?>" href="<?=htmlspecialcharsbx($arOrder['PAYMENT'][0]['PSA_ACTION_FILE'])?>" target="_blank"><span><?=Loc::getMessage("SPOL_TPL_PAY")?></span></a>
						<?}?>
						<a class="btn btn-default" title="<?=Loc::getMessage('SPOL_TPL_REPEAT_ORDER')?>" href="<?=htmlspecialcharsbx($arOrder['ORDER']['URL_TO_COPY'])?>"><i class="icon-repeat"></i><span><?=Loc::getMessage("SPOL_TPL_REPEAT_ORDER")?></span></a>
						<?if($arOrder["ORDER"]["CAN_CANCEL"] == "Y") {?>
							<a class="btn btn-default" title="<?=Loc::getMessage('SPOL_TPL_CANCEL_ORDER')?>" href="<?=htmlspecialcharsbx($arOrder['ORDER']['URL_TO_CANCEL'])?>"><i class="icon-close"></i><span><?=Loc::getMessage("SPOL_TPL_CANCEL_ORDER")?></span></a>
						<?}?>
						<a class="btn btn-default" title="<?=Loc::getMessage('SPOL_TPL_MORE_ON_ORDER')?>" href="<?=htmlspecialcharsbx($arOrder['ORDER']['URL_TO_DETAIL'])?>"><span><?=Loc::getMessage("SPOL_TPL_MORE_ON_ORDER")?></span><i class="icon-arrow-right"></i></a>
					</div>
				</div>
			</div>
		<?}
		unset($arOrder);
		if($arParams["ONLY_LAST_ORDERS"] != "Y")
			echo $arResult["NAV_STRING"];?>
	</div>
	<script type="text/javascript">
		var <?=$obName?> = new JCSalePersonalOrderList({		
			container: '<?=$containerName?>'
		});
	</script>
<?}?>