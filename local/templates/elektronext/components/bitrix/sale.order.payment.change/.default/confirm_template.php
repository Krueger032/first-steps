<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Localization\Loc;

if(!empty($arResult["errorMessage"])) {
	if(!is_array($arResult["errorMessage"])) {
		ShowError($arResult["errorMessage"]);
	} else {
		foreach($arResult["errorMessage"] as $errorMessage) {
			ShowError($errorMessage);
		}
	}
} else {
	if($arResult["IS_ALLOW_PAY"] == "N") {?>
		<div class="sale-paysystem-wrapper">
			<p><b><?=Loc::getMessage("SOPC_PAY_SYSTEM_CHANGED")?></b></p>
			<p><?=Loc::getMessage("SOPC_PAY_SYSTEM_NOT_ALLOW_PAY")?></p>
		</div>
	<?} elseif($arResult["SHOW_INNER_TEMPLATE"] == "Y") {?>
		<div class="bx-sopc" id="bx-sopc<?=$wrapperId?>">
			<div class="sale-order-pay">
				<p>
					<?$paymentSubTitle = Loc::getMessage("SOPC_TPL_BILL")." ".Loc::getMessage("SOPC_TPL_NUMBER_SIGN").$arResult["PAYMENT"]["ACCOUNT_NUMBER"];
					if(isset($arResult["PAYMENT"]["DATE_BILL"])) {
						$paymentSubTitle .= " ".Loc::getMessage("SOPC_TPL_FROM_DATE")." ".$arResult["PAYMENT"]["DATE_BILL"]->format("d.m.Y");
					}
					echo $paymentSubTitle;?>
				</p>
				<p><?=Loc::getMessage("SOPC_TPL_SUM_TO_PAID")?>: <span class="bold-black"><?=SaleFormatCurrency($arResult["PAYMENT"]["SUM"], $arResult["PAYMENT"]["CURRENCY"])?></span></p>
				<p><?=Loc::getMessage("SOPC_INNER_BALANCE")?>: <span class="bold-black"><?=SaleFormatCurrency($arResult["INNER_PAYMENT_INFO"]["CURRENT_BUDGET"], $arResult["INNER_PAYMENT_INFO"]["CURRENCY"])?></span></p>				
				<?$inputSum = $arResult["INNER_PAYMENT_INFO"]["CURRENT_BUDGET"] > $arResult["PAYMENT"]["SUM"] ? $arResult["PAYMENT"]["SUM"] : $arResult["INNER_PAYMENT_INFO"]["CURRENT_BUDGET"];
				if(($arParams["ONLY_INNER_FULL"] !== "Y" &&(float)$arResult["INNER_PAYMENT_INFO"]["CURRENT_BUDGET"] > 0) || ($arParams["ONLY_INNER_FULL"] === "Y" && $arResult["INNER_PAYMENT_INFO"]["CURRENT_BUDGET"] >= $arResult["PAYMENT"]["SUM"])) {
					if($arParams["ONLY_INNER_FULL"] !== "Y") {?>
						<div class="sale-order-pay-block">
							<span><?=Loc::getMessage("SOPC_SUM_OF_PAYMENT")?>:</span>
							<div class="sale-order-pay-block-input">
								<input type="text" placeholder="0.00" class="inner-payment-form-control form-control" value="<?=(float)$inputSum?>" name="payInner" />
							</div>
							<span class="sale-order-pay-block-curr"><?=$arResult["INNER_PAYMENT_INFO"]["FORMATED_CURRENCY"]?></span>
						</div>
					<?}?>
					<div class="sale-order-pay-button"><button type="button" class="sale-order-inner-payment-button btn btn-buy"><?=Loc::getMessage("SOPC_TPL_PAY_BUTTON")?></button></div>
				<?}
				if(($arParams["ONLY_INNER_FULL"] !== "Y" &&(float)$arResult["INNER_PAYMENT_INFO"]["CURRENT_BUDGET"] > 0) || ($arParams["ONLY_INNER_FULL"] === "Y" && $arResult["INNER_PAYMENT_INFO"]["CURRENT_BUDGET"] >= $arResult["PAYMENT"]["SUM"])) {?>
					<div class="sale-order-pay-description"><?=Loc::getMessage("SOPC_HANDLERS_PAY_SYSTEM_WARNING_RETURN");?></div>
				<?} else {?>
					<div class="sale-order-pay-error"><?ShowError(Loc::getMessage("SOPC_LOW_BALANCE"));?></div>
				<?}?>
			</div>
		</div>
		<?if((float)$arResult["INNER_PAYMENT_INFO"]["CURRENT_BUDGET"] > 0) {
			$javascriptParams = array(
				"url" => CUtil::JSEscape($this->__component->GetPath()."/ajax.php"),
				"templateFolder" => CUtil::JSEscape($templateFolder),
				"accountNumber" => $arParams["ACCOUNT_NUMBER"],
				"paymentNumber" => $arParams["PAYMENT_NUMBER"],
				"valueLimit" => $inputSum,
				"onlyInnerFull" => $arParams["ONLY_INNER_FULL"],
				"wrapperId" => $wrapperId
			);
			$javascriptParams = CUtil::PhpToJSObject($javascriptParams);?>
			<script>
				var sc = new BX.Sale.OrderInnerPayment(<?=$javascriptParams?>);
			</script>
		<?}
	} elseif(empty($arResult["PAYMENT_LINK"]) && !$arResult["IS_CASH"] && strlen($arResult["TEMPLATE"])) {
		echo $arResult["TEMPLATE"];
	} else {?>		
		<div class="sopc-order-select-info">
			<p><?=Loc::getMessage("SOPC_ORDER_SUC", array("#ORDER_ID#" => $arResult["ORDER_ID"], "#ORDER_DATE#" => $arResult["ORDER_DATE"]))?></p>
			<p><?=Loc::getMessage("SOPC_PAYMENT_SUC", array("#PAYMENT_ID#" => $arResult["PAYMENT_ID"]))?></p>
			<p><?=Loc::getMessage("SOPC_PAYMENT_SYSTEM_NAME", array("#PAY_SYSTEM_NAME#" => $arResult["PAY_SYSTEM_NAME"]))?></p>
			<?if(!$arResult["IS_CASH"] && strlen($arResult["PAYMENT_LINK"])) {?>
				<p><?=Loc::getMessage("SOPC_PAY_LINK", array("#LINK#" => $arResult["PAYMENT_LINK"]))?></p>
			<?}?>
		</div>		
		<?if(!$arResult["IS_CASH"]) {?>
			<script type="text/javascript">
				window.open("<?=$arResult['PAYMENT_LINK']?>");
			</script>
		<?}
	}
}
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>