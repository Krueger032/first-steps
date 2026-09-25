<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

global $USER;
if(!$USER->IsAuthorized())
	return;?>

<div class="sale-order-cancel-container">
	<?if(strlen($arResult["ERROR_MESSAGE"]) <= 0) {?>
		<form method="post" action="<?=POST_FORM_ACTION_URI?>">
			<div class="row">
				<div class="col-xs-12 col-md-4 sale-order-cancel-container">
					<input type="hidden" name="CANCEL" value="Y">
					<?=bitrix_sessid_post()?>
					<input type="hidden" name="ID" value="<?=$arResult['ID']?>">
					<div class="form-group">
						<label for="reasonCanceled"><?=Loc::getMessage("SALE_CANCEL_ORDER")?>:</label>
						<textarea id="reasonCanceled" name="REASON_CANCELED" class="form-control"></textarea>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="sale-order-buttons-container">
				<input type="submit" name="action" value="<?=Loc::getMessage('SALE_CANCEL_ORDER_BTN')?>" class="btn btn-buy" />
				<a href="<?=$arResult['URL_TO_LIST']?>" class="btn btn-default"><?=Loc::getMessage("SALE_RECORDS_LIST")?></a>
			</div>
		</form>
	<?} else {
		echo ShowError($arResult["ERROR_MESSAGE"]);
	}?>
</div>