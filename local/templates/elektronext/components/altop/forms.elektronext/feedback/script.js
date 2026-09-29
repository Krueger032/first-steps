(function() {
	'use strict';

	if(!!window.JCFormsFeedbackComponent)
		return;

	window.JCFormsFeedbackComponent = function(params) {
		this.componentPath = params.componentPath || '';
		this.props = params.jsProps || '';
		this.defaultCountry = params.defaultCountry || '';
		this.phoneMask = params.phoneMask || '';
		this.userConsent = params.userConsent || '';
		this.useCaptcha = params.useCaptcha || '';
		this.container = BX(params.container);
		
		BX.ready(BX.delegate(this.init, this));
	};

	window.JCFormsFeedbackComponent.prototype =	{		
		init: function() {
			this.form = this.container.querySelector('form');
			this.alert = this.container.querySelector('.alert');

			var inputSourceUrl = this.form.querySelector('[name="SOURCE_URL"]');
			if(!!inputSourceUrl)
				inputSourceUrl.value = window.location.href;
			
			if(!!this.userConsent) {
				var inputUserConsent = this.form.querySelector('[name="USER_CONSENT"]'),
					inputUserConsentUrl = this.form.querySelector('[name="USER_CONSENT_URL"]');
				
				if(!!inputUserConsentUrl)
					inputUserConsentUrl.value = window.location.href;

				BX.UserConsent.load(this.form);
			}

			if(!!this.useCaptcha) {
				var captchaImg = this.form.querySelector('[alt="CAPTCHA"]'),
					captchaWord = this.form.querySelector('[name="CAPTCHA_WORD"]'),
					captchaSid = this.form.querySelector('[name="CAPTCHA_SID"]');
			}
			
			if(!!this.props || !!this.useCaptcha) {
				BX.ajax({
					url: this.componentPath + '/ajax.php',
					method: 'POST',
					dataType: 'json',
					timeout: 60,
					data: {							
						action: 'getData',
						getCaptcha: !!this.useCaptcha ? 1 : 0,
						props: !!this.props ? this.props : 0,
						phoneMask: !!this.phoneMask ? 1 : 0,
						siteServerName: BX.message('SITE_SERVER_NAME'),
						languageId: BX.message('LANGUAGE_ID')
					},
					onsuccess: BX.delegate(function(result) {
						if(!!this.props) {
							for(var i in this.props) {
								if(this.props.hasOwnProperty(i)) {
									var formInput = this.form.querySelector('[name="' + this.props[i].CODE + '"]');
									if(!!formInput) {
										if(formInput.name == 'PHONE' && !!this.phoneMask) {
											this.inputPhone = formInput;
											this.iti = window.intlTelInput(this.inputPhone, {
												autoHideDialCode: false,
												autoPlaceholder : 'aggressive',
												customContainer: 'iti--dark',
												customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
													return selectedCountryPlaceholder.replace(/[0-9]/g, '_');
												},
												initialCountry: !!result.COUNTRY ? result.COUNTRY : this.defaultCountry,
												nationalMode: false,
												preferredCountries: ['ru', 'by', 'ua', 'kz'],
												separateDialCode: true,
												utilsScript: '/bitrix/js/altop.elektronext/intlTelInput/utils.js'
											});
											this.iti.promise.then(BX.delegate(function() {
												Inputmask(this.inputPhone.placeholder.replace(/_/g, '9')).mask(this.inputPhone);
											}, this));
											this.inputPhone.addEventListener('countrychange', BX.delegate(function(e) {
												var target = e.currentTarget;
												$(this.form).formValidation('resetField', target.name, true);
												Inputmask(target.placeholder.replace(/_/g, '9')).mask(target);
												target.blur();
												target.focus();
											}, this));
										}
									
										if(!!result[this.props[i].CODE] && result[this.props[i].CODE].length > 0)
											formInput.value = result[this.props[i].CODE];
									}
								}
							}
						}
						
						if(!!this.useCaptcha && !!result.captcha) {
							if(!!captchaImg) {
								if(captchaImg.hasAttribute('data-lazyload-src'))
									BX.adjust(captchaImg, {attrs: {'data-lazyload-src': '/bitrix/tools/captcha.php?captcha_sid=' + result.captcha}});
								else
									BX.adjust(captchaImg, {props: {src: '/bitrix/tools/captcha.php?captcha_sid=' + result.captcha}});
								captchaImg.parentNode.style.display = '';
							}
							if(!!captchaWord)
								captchaWord.value = '';
							if(!!captchaSid)
								captchaSid.value = result.captcha;
						}
					}, this)
				});
			}
			
			var fields = {};
			
			if(!!this.props) {
				for(var i in this.props) {
					if(this.props.hasOwnProperty(i)) {
						var formInput = this.form.querySelector('[name="' + this.props[i].CODE + '"]');
						if(!!formInput) {
							fields[this.props[i].CODE] = {
								row: '.form-group',
								validators: {}
							};

							if(this.props[i].REQUIRED == 'Y') {
								fields[this.props[i].CODE].validators.notEmpty = {
									message: BX.message('FORMS_NOT_EMPTY_INVALID')
								};
							}
							
							if(this.props[i].CODE == 'PHONE') {
								if(!!this.phoneMask) {
									fields[this.props[i].CODE].validators.callback = {
										message: BX.message('FORMS_PHONE_WRONG'),
										callback: BX.delegate(function(value, validator, $field) {
											if(!this.iti.isValidNumber()) {
												return false;
											} else {
												return true;
											}
										}, this)
									};
								} else {
									fields[this.props[i].CODE].validators.regexp = {
										regexp: /^[0-9\s\-()+]+$/,
										message: BX.message('FORMS_PHONE_INVALID')
									};
								}
							}
						}
					}
				}
			}
			
			if(!!this.userConsent && !!inputUserConsent) {
				fields.USER_CONSENT = {
					row: '.form-group',
					validators: {
						notEmpty: {
							message: BX.message('FORMS_USER_CONSENT_NOT_EMPTY_INVALID')
						}
					}
				};
			}

			if(!!this.useCaptcha && !!captchaWord) {
				fields.CAPTCHA_WORD = {
					row: '.form-group',
					validators: {
						notEmpty: {
							message: BX.message('FORMS_NOT_EMPTY_INVALID')
						},
						remote: {
							type: 'POST',
							url: this.componentPath + '/ajax.php',
							message: BX.message('FORMS_CAPTCHA_WRONG'),
							data: function() {
								return {
									action: 'checkCaptcha',
									CAPTCHA_SID: !!captchaSid ? captchaSid.value : ''
								};
							},
							delay: 1000
						}
					}
				};
			}
			
			$(this.form).formValidation({
				framework: 'bootstrap',
				icon: {
					valid: 'icon-ok-b',
					invalid: 'icon-close-b',
					validating: 'icon-repeat-b'
				},
				fields: fields				
			});
				
			if(!!this.userConsent && !!inputUserConsent) {
				BX.addCustomEvent(this.form, 'OnFormInputUserConsentChange', BX.delegate(function() {
					$(this.form).formValidation('revalidateField', inputUserConsent.name);
				}, this));
			}
			
			$(this.form).on('success.form.fv', BX.delegate(function() {
				var data = {
					action: 'sendForm',
					phoneMask: !!this.phoneMask ? 1 : 0,
					siteId: BX.message('SITE_ID'),
					siteCharset: BX.message('SITE_CHARSET'),
					siteServerName: BX.message('SITE_SERVER_NAME'),
					languageId: BX.message('LANGUAGE_ID')
				};
				
				var propCollection = this.form.querySelectorAll('input, textarea');
				if(!!propCollection) {
					for(var i in propCollection) {
						if(propCollection.hasOwnProperty(i) && BX.type.isDomNode(propCollection[i])) {
							if(propCollection[i].name == 'PHONE' && !!this.phoneMask) {
								data[propCollection[i].name] = {};
								data[propCollection[i].name]['COUNTRY'] = this.iti.getSelectedCountryData();
								data[propCollection[i].name]['VALUE'] = propCollection[i].value;
								data[propCollection[i].name]['FULL_VALUE'] = this.iti.getNumber(intlTelInputUtils.numberFormat.INTERNATIONAL);
							} else {
								data[propCollection[i].name] = propCollection[i].value;
							}
						}
					}
				}
				
				BX.ajax({
					url: this.componentPath + '/ajax.php',
					method: 'POST',
					dataType: 'json',
					timeout: 60,
					data: data,
					onsuccess: BX.delegate(function(result) {
						$(this.form).formValidation('resetForm', false);

						if(!!result.status) {
							BX.remove(this.form);

							if(!!this.alert) {
								BX.adjust(this.alert, {
									props: {
										className: 'alert alert-success'
									},
									style: {
										display: 'block'
									},
									html: BX.message('FORMS_ALERT_SUCCESS')
								});
							}
						} else {
							if(!!this.alert) {
								BX.adjust(this.alert, {
									props: {
										className: 'alert alert-error'
									},
									style: {
										display: 'block'
									},
									html: BX.message('FORMS_ALERT_ERROR')
								});
							}
						}

						if(!!this.useCaptcha && !!result.captcha_code) {
							if(!!captchaImg)
								BX.adjust(captchaImg, {attrs: {src: '/bitrix/tools/captcha.php?captcha_sid=' + result.captcha_code}});
							if(!!captchaWord)
								captchaWord.value = '';
							if(!!captchaSid)
								captchaSid.value = result.captcha_code;
						}
					}, this)
				});
			}, this));
		}
	}
})();