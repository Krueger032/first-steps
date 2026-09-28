BX.Aspro.Utils.readyDOM(() => {
  $.extend($.validator.messages, {
    required: BX.message("JS_REQUIRED"),
    email: BX.message("JS_FORMAT"),
    equalTo: BX.message("JS_PASSWORD_COPY"),
    minlength: BX.message("JS_PASSWORD_LENGTH"),
    remote: BX.message("JS_ERROR"),
    licenses_popup: BX.message("JS_REQUIRED_LICENSES"),
  });

  $.validator.addMethod(
    "regexp",
    function (value, element, regexp) {
      var re = new RegExp(regexp);
      return this.optional(element) || re.test(value);
    },
    BX.message("JS_FORMAT")
  );

  $.validator.addMethod(
    "filesize",
    function (value, element, param) {
      return this.optional(element) || element.files[0].size <= param;
    },
    BX.message("JS_FILE_SIZE")
  );

  $.validator.addMethod(
    "date",
    function (value, element, param) {
      var status = false;
      if (!value || value.length <= 0) {
        status = false;
      } else {
        // html5 date allways yyyy-mm-dd
        var re = new RegExp("^([0-9]{4})(.)([0-9]{2})(.)([0-9]{2})$");
        var matches = re.exec(value);
        if (matches) {
          var composedDate = new Date(matches[1], matches[3] - 1, matches[5]);
          status =
            composedDate.getMonth() == matches[3] - 1 &&
            composedDate.getDate() == matches[5] &&
            composedDate.getFullYear() == matches[1];
        } else {
          // firefox
          var re = new RegExp("^([0-9]{2})(.)([0-9]{2})(.)([0-9]{4})$");
          var matches = re.exec(value);
          if (matches) {
            var composedDate = new Date(matches[5], matches[3] - 1, matches[1]);
            status =
              composedDate.getMonth() == matches[3] - 1 &&
              composedDate.getDate() == matches[1] &&
              composedDate.getFullYear() == matches[5];
          }
        }
      }
      return status;
    },
    BX.message("JS_DATE")
  );

  $.validator.addMethod(
    "extension",
    function (value, element, param) {
      param = typeof param === "string" ? param.replace(/,/g, "|") : "png|jpe?g|gif";
      return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
    },
    BX.message("JS_FILE_EXT")
  );

  $.validator.addMethod(
    "captcha",
    function (value, element, params) {
      let $sid = $(element).closest("form").find('input[name="captcha_sid"]');
      if (!$sid.length) {
        $sid = $(element).closest("form").find('input[name="captcha_code"]');
      }

      let sid = $sid.val();

      return $.validator.methods.remote.call(this, value, element, {
        url: arAsproOptions["SITE_DIR"] + "ajax/check-captcha.php",
        type: "post",
        data: {
          captcha_word: value,
          captcha_sid: sid,
        },
      });
    },
    BX.message("JS_ERROR")
  );

  $.validator.addMethod(
    "recaptcha",
    function (value, element, param) {
      if (BX.Aspro?.Captcha) {
        return BX.Aspro.Captcha.validate(element);
      }

      return true;
    },
    BX.message("JS_RECAPTCHA_ERROR")
  );

  $.validator.addClassRules({
    confirm_password: {
      equalTo: 'input[name="REGISTER[PASSWORD]"]',
      minlength: 6,
    },
    password: {
      minlength: 6,
    },
    inputfile: {
      extension: arAsproOptions["THEME"]["VALIDATE_FILE_EXT"],
      filesize: 5000000,
    },
    captcha: {
      captcha: "",
    },
    recaptcha: {
      recaptcha: "",
    },
  });

  $.validator.setDefaults({
    highlight: function (element) {
      $(element).parent().addClass("error");
    },
    unhighlight: function (element) {
      $(element).parent().removeClass("error");
    },
    errorPlacement: function (error, element) {
      error.insertBefore(element);
    },
    messages:{
        licenses_popup: {
            required : BX.message('JS_REQUIRED_LICENSES'),
        },
    },
  });
});

$(document).on("click", ".captcha_reload", function (event) {
  event.preventDefault();

  const $captcha = $(this).parents(".captcha-row");
  $.ajax({
    url: arAsproOptions["SITE_DIR"] + "ajax/captcha.php",
    success: function (text) {
      $captcha.find("input[name=captcha_sid],input[name=captcha_code]").val(text);
      $captcha.find("img").attr("src", "/bitrix/tools/captcha.php?captcha_sid=" + text);
      $captcha.find("input[name=captcha_word]").val("").removeClass("error");
      $captcha.find(".captcha_input").removeClass("error").find(".error").remove();
    },
  });
});

BX.addCustomEvent("onSubmitForm", function (eventdata) {
  try {
    if (typeof eventdata === "object" && eventdata && eventdata.form) {
      if (!(eventdata.form instanceof Node)) {
        eventdata.form = eventdata.form[0];
      }

      new Promise((resolve, reject) => {
        if (BX.Aspro?.Captcha) {
          BX.Aspro.Captcha.onSubmit(eventdata)
            .then((result) => {
              resolve(result);
            })
            .catch((e) => {
              reject(e);
            });

          return;
        }

        resolve(true);
      }).then((result) => {
        if (result) {
          eventdata.form.submit();
          if (eventdata.form.closest(".form")) {
            eventdata.form.closest(".form").classList.add("sending");
          }
        }
      });
    }
  } catch (e) {
    console.error(e);
  }
});
