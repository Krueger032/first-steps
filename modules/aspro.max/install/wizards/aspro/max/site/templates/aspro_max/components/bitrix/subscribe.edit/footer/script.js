BX.Aspro.Utils.readyDOM(() => {
  BX.Aspro.Loader.addExt('validate').then(() => {
    $("form.subscribe-form").validate({
        submitHandler: function (form) {
        if ($(form).valid()) {
          BX.onCustomEvent('onSubmitForm', [{
              type: 'form_submit',
              form: form,
              form_name: 'subscribe-footer',
          }]);
        }
      },
      rules: {
        EMAIL: {
          email: true
        }
      },
      messages: {
          licenses_subscribe_footer: {
              required: BX.message('JS_REQUIRED_LICENSES')
          }
      }
    });
	});
});
