<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;?>

<div class="bx-authform">
	<div class="bx-authform-title-container">
		<div class="bx-authform-title">
			<div class="bx-authform-title-icon"><i class="icon-unlock"></i></div>
			<div class="bx-authform-title-val"><?=Loc::getMessage("AUTH_FORM_TITLE")?></div>
		</div>
	</div>
	<form name="bform" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
		<div class="bx-authform-content-container">
			<div class="col-xs-12 col-md-4 bx-authform-content">
				<?if(!empty($arParams["~AUTH_RESULT"]))
					if(is_array($arParams["~AUTH_RESULT"]))
						ShowNote($arParams["~AUTH_RESULT"]["MESSAGE"], ($arParams["~AUTH_RESULT"]["TYPE"] == "OK" ? "success" : "error"));
					else
						ShowMessage($arParams["~AUTH_RESULT"]);
				
				if($arResult["BACKURL"] <> "") {?>
					<input type="hidden" name="backurl" value="<?=$arResult['BACKURL']?>">
				<?}?>
				<input type="hidden" name="AUTH_FORM" value="Y" />
				<input type="hidden" name="TYPE" value="SEND_PWD" />
				<div class="form-group"><?=Loc::getMessage("AUTH_FORM_NOTE")?></div>
				<div class="form-group">
					<div class="bx-authform-label-container"><?=Loc::getMessage("AUTH_LOGIN_EMAIL")?></div>
					<input type="text" name="USER_LOGIN" maxlength="255" value="<?=$arResult['LAST_LOGIN']?>" class="form-control" />
					<input type="hidden" name="USER_EMAIL" />
					<div class="bx-authform-note-container"><?=Loc::getMessage("AUTH_LOGIN_EMAIL_NOTE")?></div>
				</div>
				<?if($arResult["PHONE_REGISTRATION"]) {
					if($arSettings["FORMS_PHONE_MASK"]["VALUE"] == "Y") {
						$initialCountry = Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getCookie("ENEXT_FORMS_COUNTRY");
						if(empty($initialCountry)) {
							$initialCountry = Bitrix\Main\Service\GeoIp\Manager::getCountryCode(Bitrix\Main\Service\GeoIp\Manager::getRealIp(), LANGUAGE_ID);
							if(!empty($initialCountry)) {
								$initialCountry = strtolower($initialCountry);
							} else {
								$initialCountry = strtolower($arSettings["FORMS_DEFAULT_COUNTRY"]["VALUE"]);
							}
						}
					}?>
					<div class="form-group">
						<div class="bx-authform-label-container"><?=Loc::getMessage("AUTH_PHONE_NUMBER")?></div>
						<input type="text" name="USER_PHONE_NUMBER<?=($arSettings['FORMS_PHONE_MASK']['VALUE'] == 'Y' ? '_ITI' : '')?>" maxlength="255" value="<?=$arResult['USER_PHONE_NUMBER']?>" class="form-control" />
						<div class="bx-authform-note-container"><?=Loc::getMessage("AUTH_PHONE_NUMBER_NOTE")?></div>
						<?if($arSettings["FORMS_PHONE_MASK"]["VALUE"] == "Y") {?>
							<script type="text/javascript">
								var inputPhone = document.bform.querySelector('[name="USER_PHONE_NUMBER_ITI"]');
								if(!!inputPhone) {
									var iti = window.intlTelInput(inputPhone, {
										autoHideDialCode: false,
										autoPlaceholder : 'aggressive',
										customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
											return selectedCountryPlaceholder.replace(/[0-9]/g, '_');
										},
										hiddenInput: 'USER_PHONE_NUMBER',
										initialCountry: '<?=$initialCountry?>',
										nationalMode: false,
										preferredCountries: ['ru', 'by', 'ua', 'kz'],
										separateDialCode: true,
										utilsScript: '/bitrix/js/altop.elektronext/intlTelInput/utils.js'
									});
									iti.promise.then(function() {
										Inputmask(inputPhone.placeholder.replace(/_/g, '9')).mask(inputPhone);
									});
									inputPhone.addEventListener('countrychange', function(e) {
										var target = e.currentTarget;
										target.value = '';
										Inputmask(target.placeholder.replace(/_/g, '9')).mask(target);									
										target.blur();
										target.focus();
									});
								}
							</script>
						<?}?>
					</div>
				<?}
				if($arResult["USE_CAPTCHA"]) {?>
					<div class="bx-authform-label-container"><?=Loc::getMessage("AUTH_CAPTCHA")?></div>
					<div class="form-group captcha">
						<div class="pic">
							<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult['CAPTCHA_CODE']?>" width="100" height="36" alt="CAPTCHA" />
						</div>
						<input type="text" name="captcha_word" maxlength="50" value="" autocomplete="off" class="form-control" />
						<input type="hidden" name="captcha_sid" value="<?=$arResult['CAPTCHA_CODE']?>" />
					</div>
				<?}?>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="form-group bx-authform-buttons-container">
			<button type="submit" class="btn btn-buy" name="send_account_info" value="<?=Loc::getMessage('AUTH_SEND')?>"><span><?=Loc::getMessage("AUTH_SEND")?></span></button>
			<a href="<?=$arResult['AUTH_AUTH_URL']?>" class="btn btn-default"><?=Loc::getMessage("AUTH_AUTH")?></a>
		</div>
	</form>
</div>

<script type="text/javascript">
	document.bform.onsubmit = function(){
		document.bform.USER_EMAIL.value = document.bform.USER_LOGIN.value;
	};
	document.bform.USER_LOGIN.focus();
</script>
