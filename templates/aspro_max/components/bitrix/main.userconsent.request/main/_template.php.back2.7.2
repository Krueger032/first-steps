<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/** @var array $arParams */
/** @var array $arResult */

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__DIR__ . '/user_consent.php');
$config = \Bitrix\Main\Web\Json::encode($arResult['CONFIG']);

$linkClassName = 'main-user-consent-request-announce';
if ($arResult['URL'])
{
	$url = htmlspecialcharsbx(\CUtil::JSEscape($arResult['URL']));
	$label = htmlspecialcharsbx($arResult['LABEL']);
	$label = explode('%', $label);
	$label = implode('', array_merge(
		array_slice($label, 0, 1),
		['<a href="' . $url  . '" target="_blank">'],
		array_slice($label, 1, 1),
		['</a>'],
		array_slice($label, 2)
	));
}
else
{
	$label = htmlspecialcharsbx($arResult['INPUT_LABEL']);
	$linkClassName .= '-link';
}
$inputName = $arParams["INPUT_NAME"] ?? 'licenses_popup';
$inputRequired = !isset($arParams["INPUT_REQUIRED"]) || $arParams["INPUT_REQUIRED"] !== 'N';
?>
<div class="licence_block filter onoff label_block consent-outer-wrap">
	<?$unicId = "consent_".\Bitrix\Main\Security\Random::getString(6);?>
    <?if(isset($arParams['HIDDEN_ERROR']) && $arParams['HIDDEN_ERROR'] === 'Y'):?>
        <label data-for="<?=$unicId;?>" class="hidden error"><?=GetMessage("ERROR_FORM_LICENSE");?></label>
    <?endif;?>
	<div class="main-user-consent-request" data-bx-user-consent="<?=htmlspecialcharsbx($config)?>">
        <input id="<?=$unicId;?>" type="checkbox" <?=$inputRequired ? 'required' : ''?> value="Y" <?=($arParams['IS_CHECKED'] ? 'checked' : '')?> name="<?=htmlspecialcharsbx($arParams['INPUT_NAME'])?>">
		<label for="<?=$unicId;?>" class="license">
            <span class="<?=$linkClassName?> "><?=$label?></span>
		</label>
        <input type="hidden" name="<?=VENDOR_PARTNER_NAME.'_'.VENDOR_SOLUTION_NAME;?>_form_validate">
	</div>
	<div data-bx-template="main-user-consent-request-loader" style="display: none;">
		<div class="main-user-consent-request-popup">
			<div class="main-user-consent-request-popup-cont rounded-n">
				<div data-bx-head="" class="main-user-consent-request-popup-header option-font-bold font_lg darken"></div>
				<div class="main-user-consent-request-popup-body">
					<div data-bx-loader="" class="main-user-consent-request-loader">
						<svg class="main-user-consent-request-circular" viewBox="25 25 50 50">
							<circle class="main-user-consent-request-path" cx="50" cy="50" r="20" fill="none" stroke-width="1" stroke-miterlimit="10"></circle>
						</svg>
					</div>
					<div data-bx-content="" class="main-user-consent-request-popup-content">
						<div class="main-user-consent-request-popup-textarea-block">
							<div data-bx-textarea="" class="main-user-consent-request-popup-text bordered rounded-6"></div>
							<div data-bx-link="" style="display: none;" class="main-user-consent-request-popup-link">
								<div><?=Loc::getMessage('MAIN_USER_CONSENT_REQUEST_URL_CONFIRM')?></div>
								<div><a target="_blank"></a></div>
							</div>
						</div>
						<div class="main-user-consent-request-popup-buttons">
							<span data-bx-btn-accept="" class="main-user-consent-request-popup-button main-user-consent-request-popup-button-acc btn btn-lg btn-default">Y</span>
							<span data-bx-btn-reject="" class="main-user-consent-request-popup-button main-user-consent-request-popup-button-rej btn btn-lg btn-transparent-border-color">N</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
