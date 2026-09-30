<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;?>

<div class="slide-panel__form">
	<span id="system_auth_form<?=$arResult['RND']?>_alert" class="alert" style="display: none;"></span>
	<form id="system_auth_form<?=$arResult['RND']?>_form" action="javascript:void(0);">
		<input type="hidden" name="action" value="login" />
		<div class="form-group has-feedback">
			<input type="text" name="USER_LOGIN" maxlength="50" class="form-control" placeholder="<?=Loc::getMessage('AUTH_LOGIN')?>" />
			<i class="form-control-feedback fv-icon-no-has icon-user"></i>
		</div>			
		<div class="form-group has-feedback">									
			<input type="password" name="USER_PASSWORD" maxlength="50" class="form-control" autocomplete="off" placeholder="<?=Loc::getMessage('AUTH_PASSWORD')?>" aria-describedby="secureInfo" />
			<i class="form-control-feedback fv-icon-no-has icon-unlock"></i>				
		</div>			
		<?if($arResult["STORE_PASSWORD"] == "Y") {?>
			<div class="form-group">
				<div class="checkbox">
					<label>
						<input type="checkbox" value="Y" name="USER_REMEMBER" />
						<span class="check-cont">
							<span class="check">
								<i class="icon-ok-b"></i>
							</span>
						</span>
						<a class="check-title"><?=Loc::getMessage('AUTH_REMEMBER_ME')?></a>
					</label>
				</div>
			</div>
		<?}
		if($arResult["CAPTCHA_CODE"]) {?>
			<div class="form-group captcha">
				<div class="pic">								
					<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult['CAPTCHA_CODE']?>" width="100" height="36" alt="CAPTCHA" />
				</div>							
				<input type="text" maxlength="5" name="CAPTCHA_WORD" class="form-control" placeholder="<?=Loc::getMessage('AUTH_CAPTCHA_PROMT')?>" />
				<input type="hidden" name="CAPTCHA_SID" value="<?=$arResult['CAPTCHA_CODE']?>" />
			</div>
		<?}?>			
		<div class="form-group">
			<button type="submit" name="Login" id="system_auth_form<?=$arResult['RND']?>_btn" class="btn btn-buy"><span><?=Loc::getMessage('AUTH_LOGIN_BUTTON')?></span></button>
		</div>
		<div class="form-group">
			<noindex>
				<a href="<?=$arParams['FORGOT_PASSWORD_URL']?>?forgot_password=yes" rel="nofollow" class="btn btn-default" role="button"><?=Loc::getMessage('AUTH_FORGOT_PASSWORD_2')?></a>
			</noindex>
		</div>
		<?if($arResult['NEW_USER_REGISTRATION'] == "Y") {?>
			<div class="form-group">
				<noindex>
					<a href="<?=$arParams['REGISTER_URL']?>?register=yes" rel="nofollow" class="btn btn-default" role="button"><?=Loc::getMessage('AUTH_REGISTER')?></a>
				</noindex>
			</div>
		<?}?>
	</form>
	<?if($arResult["AUTH_SERVICES"] && COption::GetOptionString("main", "allow_socserv_authorization", "Y") != "N") {?>				
		<div class="form-group">					
			<label><?=Loc::getMessage("socserv_as_user_form")?></label>
			<?$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "slide_panel",
				array(
					"AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
					"AUTH_URL" => $arParams["PROFILE_URL"],
					"POST" => $arResult["POST"]
				),
				$component, 
				array("HIDE_ICONS" => "Y")
			);?>
			<script type="text/javascript">
				function slidePanelBxSocServPopup(id) {
					var content = BX("bx_slide_panel_socserv_form_" + id);
					if(content) {
						var contentPopup = content.cloneNode(true);
						var popup = BX.PopupWindowManager.create("slidePanelSocServPopup" + id, BX("bx_slide_panel_socserv_icon_" + id), {
							autoHide: true,
							closeByEsc: true,
							angle: {offset: 41},
							content: contentPopup,
							offsetTop: 3,
							zIndex: 1100,									
							events: {
								onPopupClose: function() {
									this.destroy();
								}
							}
						});
							
						popup.show();

						var input = BX.findChild(contentPopup, {'tag': 'input', 'attribute': {'type': 'text'}}, true);
						if(input) {
							input.focus();
							input.className = 'form-control';
						}

						var button = BX.findChild(contentPopup, {'tag': 'input', 'attribute': {'type': 'submit'}}, true);
						if(button)
							BX.remove(button);								
					}
				}
			</script>
		</div>
	<?}?>
</div>

<script type="text/javascript">
	BX.ready(function() {
		var alert = BX('system_auth_form<?=$arResult["RND"]?>_alert'),
			form = BX('system_auth_form<?=$arResult["RND"]?>_form'),
			btn = BX('system_auth_form<?=$arResult["RND"]?>_btn'),
			useCaptcha = false;

		if(!!form) {
			var loginCookie = BX.getCookie('<?=CUtil::JSEscape($arResult["~LOGIN_COOKIE_NAME"])?>');
			if(loginCookie) {
				var loginInput = form.querySelector('[name="USER_LOGIN"]');
				if(!!loginInput)
					loginInput.value = loginCookie;
			}

			<?if($arResult["CAPTCHA_CODE"]) {?>
				useCaptcha = true;
				var captchaImg = form.querySelector('[alt="CAPTCHA"]'),
					captchaWord = form.querySelector('[name="CAPTCHA_WORD"]'),
					captchaSid = form.querySelector('[name="CAPTCHA_SID"]');
			<?}?>

			var fields = {};

			fields.USER_LOGIN = {
				row: '.form-group',
				validators: {
					notEmpty: {
						message: '<?=Loc::getMessage("AUTH_NOT_EMPTY_INVALID")?>'
					}
				}
			};

			fields.USER_PASSWORD = {
				row: '.form-group',
				validators: {
					notEmpty: {
						message: '<?=Loc::getMessage("AUTH_NOT_EMPTY_INVALID")?>'
					}
				}
			};

			if(!!useCaptcha) {
				fields.CAPTCHA_WORD = {
					row: '.form-group',
					validators: {
						notEmpty: {
							message: '<?=Loc::getMessage("AUTH_NOT_EMPTY_INVALID")?>'
						},
						remote: {
							type: 'POST',
							url: '<?=$templateFolder?>/check_captcha.php',
							message: '<?=Loc::getMessage("AUTH_CAPTCHA_WRONG")?>',
							data: function() {
								return {
									CAPTCHA_SID: captchaSid.value
								};
							},
							delay: 1000
						}
					}
				};
			}

			//VALIDATION//
			$(form).formValidation({
				framework: 'bootstrap',
				icon: {
					valid: 'icon-ok-b',
					invalid: 'icon-close-b',
					validating: 'icon-repeat-b'
				},			
				fields: fields
			});

			//SEND_FORM//
			$(form).on('success.form.fv', function(e) {
				e.preventDefault();

				var $form = $(e.target);

				//AJAX//
				$.ajax({
					url: '<?=$templateFolder?>/ajax.php',
					type: 'POST',
					data: $form.serialize(),
					dataType: 'json',
					success: function(response) {
						//CLEAR_FORM//
						$form.formValidation('resetForm', false);

						if(!!response.status) {
							//DISABLE_FORM_INPUTS//
							var formTextInputsAll = form.querySelectorAll('[type="text"]');
							if(!!formTextInputsAll) {
								for(var i in formTextInputsAll) {
									if(formTextInputsAll.hasOwnProperty(i) && BX.type.isDomNode(formTextInputsAll[i])) {
										BX.adjust(formTextInputsAll[i], {props: {disabled: true}});
									}
								}
							}
							
							var formCheckboxInput = form.querySelector('[name="USER_REMEMBER"]');
							if(!!formCheckboxInput)
								BX.adjust(formCheckboxInput, {props: {disabled: true}});
							
							if(!!useCaptcha && !!captchaWord)
								BX.adjust(captchaWord, {props: {disabled: true}});

							//SUBMITTED//
							if(!!btn)
								BX.adjust(btn, {
									props: {
										disabled: true
									}
								});

							//SHOW_MESSAGE//
							if(!!alert)
								BX.adjust(alert, {
									props: {
										className: 'alert alert-success'
									},
									style: {
										display: 'block'
									},
									html: '<?=Loc::getMessage("AUTH_ALERT_SUCCESS")?>'
								});
							
							//REDIRECT//
							setTimeout(function() {
								window.location.href = window.location.pathname + '?login=yes';
							}, 1000);
						} else {
							//SUBMIT//
							if(!!btn)
								BX.adjust(btn, {
									props: {
										disabled: false
									}
								});

							//SHOW_MESSAGE//
							if(!!alert && !!response.message)
								BX.adjust(alert, {
									props: {
										className: 'alert alert-error'
									},
									style: {
										display: 'block'
									},
									html: response.message
								});
						}

						//CAPTCHA//
						if(!!useCaptcha && !!response.captcha_code) {
							if(!!captchaImg)
								BX.adjust(captchaImg, {attrs: {src: '/bitrix/tools/captcha.php?captcha_sid=' + response.captcha_code}});
							if(!!captchaWord)
								captchaWord.value = '';
							if(!!captchaSid)
								captchaSid.value = response.captcha_code;
						}
					}
				});
			});
		}
	});
</script>