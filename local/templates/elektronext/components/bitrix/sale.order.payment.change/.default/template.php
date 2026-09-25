<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

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
	$wrapperId = rand(0, 10000);?>
	<div class="row bx-sopc" id="bx-sopc<?=$wrapperId?>">
		<div class="col-xs-12 sopc-pp">
			<div class="row sopc-pp-list">
				<?foreach($arResult["PAYSYSTEMS_LIST"] as $key => $paySystem) {?>
					<div class="col-xs-12 col-md-3 sopc-pp-company">
						<div class="sopc-pp-company-graf-container">
							<div class="sopc-pp-company-graf">
								<input type="hidden" class="sopc-pp-company-hidden" name="PAY_SYSTEM_ID" value="<?=$paySystem['ID']?>"<?=($key == 0 ? " checked='checked'" :"")?> />
								<?if(empty($paySystem["LOGOTIP"]))
									$paySystem["LOGOTIP"] = "/bitrix/images/sale/nopaysystem.gif";?>
								<div class="sopc-pp-company-image">
									<img src="<?=htmlspecialcharsbx($paySystem['LOGOTIP'])?>" alt="<?=CUtil::JSEscape(htmlspecialcharsbx($paySystem['NAME']))?>" />
								</div>
								<div class="sopc-pp-company-descr"><i class="icon-info-circle"></i></div>
								<div class="sopc-pp-company-popup">
									<span class="sopc-pp-company-popup-title"><?=CUtil::JSEscape(htmlspecialcharsbx($paySystem["NAME"]))?></span>
									<?=CUtil::JSEscape(htmlspecialcharsbx($paySystem["DESCRIPTION"]))?>
								</div>
							</div>
							<div class="sopc-pp-company-smalltitle-container">
								<div class="sopc-pp-company-smalltitle-block">
									<div class="sopc-pp-company-smalltitle"><?=CUtil::JSEscape(htmlspecialcharsbx($paySystem["NAME"]))?></div>
								</div>
							</div>
						</div>
					</div>
				<?}?>
			</div>
		</div>
	</div>
	<?$javascriptParams = array(
		"url" => CUtil::JSEscape($this->__component->GetPath()."/ajax.php"),
		"templateFolder" => CUtil::JSEscape($templateFolder),
		"accountNumber" => $arParams["ACCOUNT_NUMBER"],
		"paymentNumber" => $arParams["PAYMENT_NUMBER"],
		"inner" => $arParams["ALLOW_INNER"],
		"onlyInnerFull" => $arParams["ONLY_INNER_FULL"],
		"wrapperId" => $wrapperId
	);
	$javascriptParams = CUtil::PhpToJSObject($javascriptParams);?>
	<script>
		var sc = new BX.Sale.OrderPaymentChange(<?=$javascriptParams?>);
	</script>
<?}?>