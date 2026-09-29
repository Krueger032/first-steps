<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

global $arSettings;

if($arResult["SHOW_SMS_FIELD"] == true)
	CJSCore::Init("phone_auth");?>

<div class="bx-authform">
	<?if($arResult["SHOW_EMAIL_SENT_CONFIRMATION"]) {
		ShowNote(Loc::getMessage("AUTH_EMAIL_SENT"), "success");
	} else {?>
		<div class="bx-authform-title-container">
			<div class="bx-authform-title">
				<div class="bx-authform-title-icon"><i class="icon-login"></i></div>
				<div class="bx-authform-title-val"><?=Loc::getMessage("AUTH_FORM_TITLE")?></div>
			</div>
		</div>
		<?if($arResult["SHOW_SMS_FIELD"] == true) {?>
			<form method="post" action="<?=$arResult['AUTH_URL']?>" name="regform">
				<div class="bx-authform-content-container">
					<div class="col-xs-12 col-md-4 bx-authform-content">
						<div id="bx_register_error" style="display: none;"></div>
						<div id="bx_register_resend"></div>
						<input type="hidden" name="SIGNED_DATA" value="<?=htmlspecialcharsbx($arResult['SIGNED_DATA'])?>" />
						<div class="form-group">
							<div class="bx-authform-label-container"><?=Loc::getMessage("AUTH_SMS_CODE")?> <span class="bx-authform-starrequired">*</span></div>
							<input type="text" name="SMS_CODE" maxlength="255" value="<?=htmlspecialcharsbx($arResult['SMS_CODE'])?>" autocomplete="off" class="form-control" />
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="form-group bx-authform-buttons-container">
					<button type="submit" class="btn btn-buy" name="code_submit_button" value="<?=Loc::getMessage('AUTH_SMS_SEND')?>"><span><?=Loc::getMessage("AUTH_SMS_SEND")?></span></button>
				</div>
			</form>
			<script type="text/javascript">
				new BX.PhoneAuth({
					containerId: 'bx_register_resend',
					errorContainerId: 'bx_register_error',
					interval: <?=$arResult["PHONE_CODE_RESEND_INTERVAL"]?>,
					data:
						<?=CUtil::PhpToJSObject([
							'signedData' => $arResult["SIGNED_DATA"],
						])?>,
					onError:
						function(response) {
							var errorNode = BX('bx_register_error');
							BX.cleanNode(errorNode);
							for(var i = 0; i < response.errors.length; i++) {
								BX.append(BX.create('SPAN', {
									props: {
										className: 'alert alert-error'
									},
									html: BX.util.htmlspecialchars(response.errors[i].message)
								}), errorNode);
							}
							BX.style(errorNode, 'display', '');
						}
				});
			</script>
		<?} else {?>
			<form method="post" action="<?=$arResult["AUTH_URL"]?>" name="bform" enctype="multipart/form-data">			
				<div class="bx-authform-content-container">
					<div class="col-xs-12 col-md-4 bx-authform-content">
						<?if(!empty($arParams["~AUTH_RESULT"])) {
							if(is_array($arParams["~AUTH_RESULT"]))
								ShowNote($arParams["~AUTH_RESULT"]["MESSAGE"], ($arParams["~AUTH_RESULT"]["TYPE"] == "OK" ? "success" : "error"));
							else
								ShowMessage($arParams["~AUTH_RESULT"]);
						}
						if($arResult["USE_EMAIL_CONFIRMATION"] === "Y")
							ShowMessage(Loc::getMessage("AUTH_EMAIL_WILL_BE_SENT"));?>
						
						<input type="hidden" name="AUTH_FORM" value="Y" />
						<input type="hidden" name="TYPE" value="REGISTRATION" />
						<div class="form-group">
							<div class="bx-authform-label-container"><?=Loc::getMessage("AUTH_NAME")?></div>
							<input type="text" name="USER_NAME" maxlength="255" value="<?=$arResult['USER_NAME']?>" class="form-control" />
						</div>
						<div class="form-group">
							<div class="bx-authform-label-container"><?=Loc::getMessage("AUTH_LAST_NAME")?></div>
							<input type="text" name="USER_LAST_NAME" maxlength="255" value="<?=$arResult['USER_LAST_NAME']?>" class="form-control" />
						</div>
						<div class="form-group">
							<div class="bx-authform-label-container"><?=Loc::getMessage("AUTH_LOGIN_MIN")?> <span class="bx-authform-starrequired">*</span></div>
							<input type="text" name="USER_LOGIN" maxlength="255" value="<?=$arResult['USER_LOGIN']?>" class="form-control" />
						</div>
						<div class="form-group bx-authform-psw-container">
							<div class="bx-authform-label-container"><?=Loc::getMessage("AUTH_PASSWORD_REQ")?> (<?=$arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?>) <span class="bx-authform-starrequired">*</span></div>
							<?if($arResult["SECURE_AUTH"]) {?>								
								<div class="bx-authform-psw-protected" id="bx_auth_secure" style="display: none;">
									<div class="bx-authform-psw-protected-desc"><?=Loc::getMessage("AUTH_SECURE_NOTE")?></div>
								</div>
								<script type="text/javascript">
									document.getElementById("bx_auth_secure").style.display = "";
								</script>								
							<?}?>								
							<input type="password" name="USER_PASSWORD" maxlength="255" value="<?=$arResult['USER_PASSWORD']?>" autocomplete="off" class="form-control" />
						</div>
						<div class="form-group bx-authform-psw-container">
							<div class="bx-authform-label-container"><?=Loc::getMessage("AUTH_CONFIRM")?> <span class="bx-authform-starrequired">*</span></div>
							<?if($arResult["SECURE_AUTH"]) {?>								
								<div class="bx-authform-psw-protected" id="bx_auth_secure_conf" style="display: none;">
									<div class="bx-authform-psw-protected-desc"><?=Loc::getMessage("AUTH_SECURE_NOTE")?></div>
								</div>
								<script type="text/javascript">
									document.getElementById("bx_auth_secure_conf").style.display = "";
								</script>								
							<?}?>								
							<input type="password" name="USER_CONFIRM_PASSWORD" maxlength="255" value="<?=$arResult['USER_CONFIRM_PASSWORD']?>" autocomplete="off" class="form-control" />
						</div>
						<?if($arResult["EMAIL_REGISTRATION"]) {?>
							<div class="form-group">
								<div class="bx-authform-label-container"><?=Loc::getMessage("AUTH_EMAIL").($arResult["EMAIL_REQUIRED"] ? " <span class='bx-authform-starrequired'>*</span>" : "");?></div>
								<input type="text" name="USER_EMAIL" maxlength="255" value="<?=$arResult['USER_EMAIL']?>" class="form-control" />
							</div>
						<?}
						if($arResult["PHONE_REGISTRATION"]) {
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
								<div class="bx-authform-label-container"><?=Loc::getMessage("AUTH_PHONE_NUMBER").($arResult["PHONE_REQUIRED"] ? " <span class='bx-authform-starrequired'>*</span>" : "");?></div>
								<input type="text" name="USER_PHONE_NUMBER<?=($arSettings['FORMS_PHONE_MASK']['VALUE'] == 'Y' ? '_ITI' : '')?>" maxlength="255" value="<?=$arResult['USER_PHONE_NUMBER']?>" class="form-control" />
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
												utilsScript: '/bitrix/js/altop.enext/intlTelInput/utils.js'
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
						if($arResult["USER_PROPERTIES"]["SHOW"] == "Y") {
							foreach($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField) {?>
								<div class="form-group">
									<div class="bx-authform-label-container"><?=$arUserField["EDIT_FORM_LABEL"].($arUserField["MANDATORY"] == "Y" ? " <span class='bx-authform-starrequired'>*</span>" : "");?></div>
									<?$APPLICATION->IncludeComponent("bitrix:system.field.edit", $arUserField["USER_TYPE"]["USER_TYPE_ID"],
										array(
											"bVarsFromForm" => $arResult["bVarsFromForm"],
											"arUserField" => $arUserField,
											"form_name" => "bform"
										),
										null,
										array("HIDE_ICONS" => "Y")
									);?>
								</div>
							<?}
							unset($FIELD_NAME, $arUserField);
						}
						if($arSettings["FORMS_USER_CONSENT"]["VALUE"] == "Y") {?>
							<div class="form-group form-group-checkbox">
								<div class="checkbox">
									<?$APPLICATION->IncludeComponent("bitrix:main.userconsent.request", "",
										array(
											"ID" => $arSettings["FORMS_USER_CONSENT_ID"]["VALUE"],
											"IS_CHECKED" => $arSettings["FORMS_USER_CONSENT_IS_CHECKED"]["VALUE"],
											"AUTO_SAVE" => "N",
											"IS_LOADED" => $arSettings["FORMS_USER_CONSENT_IS_LOADED"]["VALUE"],
											"ORIGINATOR_ID" => $arResult["AGREEMENT_ORIGINATOR_ID"],
											"ORIGIN_ID" => $arResult["AGREEMENT_ORIGIN_ID"],
											"INPUT_NAME" => $arResult["AGREEMENT_INPUT_NAME"],
											"REPLACE" => array(
												"button_caption" => Loc::getMessage("AUTH_REGISTER"),
												"fields" => array(
													Loc::getMessage("AUTH_NAME"),
													Loc::getMessage("AUTH_LAST_NAME"),
													Loc::getMessage("AUTH_LOGIN_MIN"),
													Loc::getMessage("AUTH_PASSWORD_REQ"),
													Loc::getMessage("AUTH_EMAIL")
												)
											)
										)
									);?>
									<script type="text/javascript">
										BX.UserConsent.load(document.bform);
									</script>
								</div>
							</div>
						<?}
						if($arResult["USE_CAPTCHA"] == "Y") {?>
							<div class="bx-authform-label-container"><?=Loc::getMessage("AUTH_CAPTCHA")?> <span class="bx-authform-starrequired">*</span></div>
							<div class="form-group captcha">
								<div class="pic">
									<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult['CAPTCHA_CODE']?>" width="100" height="36" alt="CAPTCHA">
								</div>
								<input type="text" name="captcha_word" maxlength="50" value="" autocomplete="off" class="form-control" />
								<input type="hidden" name="captcha_sid" value="<?=$arResult['CAPTCHA_CODE']?>" />
							</div>
						<?}?>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="form-group bx-authform-buttons-container">
					<button type="submit" class="btn btn-buy" name="Register" value="<?=Loc::getMessage('AUTH_REGISTER')?>"><span><?=Loc::getMessage("AUTH_REGISTER")?></span></button>
					<a href="<?=$arResult['AUTH_AUTH_URL']?>" rel="nofollow" class="btn btn-default"><?=Loc::getMessage("AUTH_AUTH")?></a>
				</div>
			</form>
			<script type="text/javascript">
				document.bform.USER_NAME.focus();
			</script>
		<?}
	}?>
</div>