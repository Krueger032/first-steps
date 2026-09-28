if (typeof BX.Aspro?.Captcha === "undefined") {
  BX.namespace("Aspro.Captcha");

  (() => {
    /**
     * class JAsproCaptcha
     */

    const _loaderId = "captchaApiLoader";
    let _private = {};

    class JAsproCaptcha {
      constructor() {}

      get options() {
        return JSON.parse(JSON.stringify(_private.options));
      }

      get params() {
        let params = _private.options.params;

        return JSON.parse(JSON.stringify(params));
      }

      get key() {
        return _private.options.key;
      }

      get hl() {
        return _private.options.params.hl;
      }

      get type() {
        return _private.options.type;
      }

      isYandexSmartCaptcha() {
        return this.type == "ya.smartcaptcha";
      }

      isGoogleRecaptcha() {
        return !this.isYandexSmartCaptcha();
      }

      isGoogleRecaptcha3() {
        return this.isGoogleRecaptcha() && _private.options.ver == 3;
      }

      isInvisible() {
        if (this.isYandexSmartCaptcha()) {
          return _private.options.params.invisible;
        }

        if (this.isGoogleRecaptcha() && !this.isGoogleRecaptcha3()) {
          return _private.options.params.size == "invisible";
        }
      }

      get className() {
        if (this.isYandexSmartCaptcha()) {
          return "smart-captcha";
        }

        if (this.isGoogleRecaptcha()) {
          return "g-recaptcha";
        }

        return "";
      }

      get selector() {
        return "." + this.className;
      }

      get clientResponseSelector() {
        if (this.isYandexSmartCaptcha()) {
          return '[name="smart-token"]';
        }

        if (this.isGoogleRecaptcha()) {
          return ".g-recaptcha-response";
        }

        return "";
      }

      get api() {
        if (this.isYandexSmartCaptcha()) {
          return window.smartCaptcha || null;
        }

        if (this.isGoogleRecaptcha()) {
          return window.grecaptcha || null;
        }

        return null;
      }

      getApiResponse(widgetId) {
        if (typeof widgetId !== "undefined") {
          return this.api.getResponse(widgetId);
        }

        return "";
      }

      init(options) {
        if (!_private.options) {
          _private.options = typeof options === "object" && options ? options : {};

          window.onLoadCaptcha = window.onLoadCaptcha || this.onLoad.bind(this);
          window.renderCaptcha = window.renderCaptcha || this.render.bind(this);
          window.onPassedCaptcha = window.onPassedCaptcha || this.onPassed.bind(this);

          // due for legacy
          window.renderRecaptchaById = window.renderRecaptchaById || window.renderCaptcha;
          if (this.isYandexSmartCaptcha()) {
            window.asproRecaptcha = _private.options;
          }

          if (this.isGoogleRecaptcha()) {
            window.asproRecaptcha.params = {
              sitekey: _private.options.key,
              recaptchaLang: _private.options.params.hl,
              callback: _private.options.params.callback,
            };

            if (!this.isGoogleRecaptcha3()) {
              window.asproRecaptcha.params.recaptchaSize = _private.options.params.size;
              window.asproRecaptcha.params.recaptchaColor = _private.options.params.theme;
              window.asproRecaptcha.params.recaptchaLogoShow = _private.options.params.showLogo;
              window.asproRecaptcha.params.recaptchaBadge = _private.options.params.badge;
            }
          }
        }
      }

      load() {
        _private.loadPromise =
          _private.loadPromise ||
          new Promise((resolve, reject) => {
            try {
              _private.onResolveLoadPromise = () => {
                resolve();
              };

              if (document.getElementById(_loaderId)) {
                throw "Another Api loader already exists";
              }

              let js = document.createElement("script");
              js.id = _loaderId;

              if (this.isYandexSmartCaptcha()) {
                js.src = "https://smartcaptcha.yandexcloud.net/captcha.js?render=onload&onload=onLoadCaptcha";
              } else {
                js.src =
                  "//www.google.com/recaptcha/api.js?hl=" +
                  this.hl +
                  "&onload=onLoadCaptcha&render=" +
                  (this.isGoogleRecaptcha3() ? this.key : "explicit");
              }

              document.head.appendChild(js);
            } catch (e) {
              console.error(e);
              reject(e);
            }
          });

        return _private.loadPromise;
      }

      onLoad() {
        if (typeof _private.onResolveLoadPromise === "function") {
          _private.onResolveLoadPromise();
        }
      }

      validate(inputRecaptcha) {
        if (inputRecaptcha) {
          let node = inputRecaptcha.closest("form")?.querySelector(this.selector);
          if (node) {
            let widgetId = node.getAttribute("data-widgetid");
            if (typeof widgetId !== "undefined") {
              return this.getApiResponse(widgetId) != "";
            }
          }
        }

        return true;
      }

      onPassed(response) {
        if (response) {
          document.querySelectorAll(this.selector).forEach((node) => {
            let widgetId = node.getAttribute("data-widgetid");
            if (typeof widgetId !== "undefined") {
              let tmp = this.getApiResponse(widgetId);
              if (tmp == response) {
                let form = node.closest("form");
                if (form) {
                  if (this.isInvisible()) {
                    let sToken = this.clientResponseSelector;
                    if (form.querySelector(sToken) && !form.querySelector(sToken).value) {
                      form.querySelector(sToken).value = response;
                    }

                    document.querySelectorAll('iframe[src*="recaptcha"]').forEach((iframe) => {
                      let block = iframe.parentElement?.parentElement;
                      if (block) {
                        if (!block.classList.contains("grecaptcha-badge")) {
                          block.style.width = "100%";
                        }
                      }
                    });

                    if (form.getAttribute("id") == "one_click_buy_form") {
                      BX.submit(BX("one_click_buy_form"));
                    } else if (form.getAttribute("name") == "form_comment") {
                      BX.submit(BX("form_comment"));
                    } else if (form.getAttribute("id")?.indexOf("auth-page-form") !== -1) {
                      BX.submit(form);
                    } else {
                      // submit form again, but without validation`s submitHandler
                      form.submit();
                    }
                  } else {
                    this.Replacer.addValidationInput(form);

                    if (form.querySelector("input.recaptcha")) {
                      if (this.api) {
                        $(form.querySelector("input.recaptcha")).valid();
                      }
                    }
                  }
                }
              }
            }
          });
        }
      }

      onSubmit(eventdata) {
        return new Promise((resolve, reject) => {
          try {
            if (typeof eventdata === "object" && eventdata && eventdata.form && this.api) {
              let node = eventdata.form.querySelector(this.selector);
              if (node) {
                let widgetId = node.getAttribute("data-widgetid");
                if (typeof widgetId !== "undefined") {
                  let sToken = this.clientResponseSelector;
                  if (eventdata.form.querySelector(sToken) && !eventdata.form.querySelector(sToken).value) {
                    if (eventdata.form.closest(".form")) {
                      eventdata.form.closest(".form").classList.add("sending");
                    }

                    if (this.isInvisible()) {
                      this.api.execute(widgetId);

                      resolve(false);
                    } else {
                      if (this.isGoogleRecaptcha3()) {
                        this.api.execute(this.key, { action: "maxscore" }).then((token) => {
                          eventdata.form.querySelector(sToken).value = token;

                          if (eventdata.form.getAttribute("id") == "one_click_buy_form") {
                            BX.submit(BX("one_click_buy_form"));
                          } else if (eventdata.form.getAttribute("name") == "form_comment") {
                            BX.submit(BX("form_comment"));
                          } else if (eventdata.form.getAttribute("id")?.indexOf("auth-page-form") !== -1) {
                            BX.submit(eventdata.form);
                          } else {
                            // submit form again, but without validation`s submitHandler
                            eventdata.form.submit();
                          }
                        });

                        resolve(false);
                      }
                    }
                  }
                }
              }
            }

            resolve(true);
          } catch (e) {
            console.error(e);
            reject(e);
          }
        });
      }

      render(id) {
        return new Promise((resolve, reject) => {
          this.load()
            .then(() => {
              try {
                if (!this.api) {
                  throw "Captcha api not loaded";
                }

                const render = (node) => {
                  if (!node.classList.contains(this.className)) {
                    throw "Node is not a captcha #" + id;
                  }

                  let widgetId;

                  if (this.isGoogleRecaptcha3()) {
                    node.innerHTML =
                      '<textarea class="g-recaptcha-response" style="display:none;resize:0;" name="g-recaptcha-response"></textarea>';

                    resolve(node);
                  } else {
                    if (!!node.children.length) {
                      resolve(node);
                    }

                    widgetId = this.api.render(node, this.params);
                    node.setAttribute("data-widgetid", widgetId);

                    resolve(node);
                  }
                };

                let node = document.getElementById(id);
                if (node) {
                  render(node);
                } else {
                  let counter = 0;
                  const interval = setInterval(() => {
                    node = document.getElementById(id);

                    if (node) {
                      clearInterval(interval);
                      render(node);
                    } else {
                      if (++counter >= 10) {
                        clearInterval(interval);
                        throw "Captcha not finded #" + id;
                      }
                    }
                  }, 100);
                }
              } catch (e) {
                console.error(e);
                reject(e);
              }
            })
            .catch((e) => {
              console.error(e);
              reject(e);
            });
        });
      }

      reset() {
        if (this.isGoogleRecaptcha() || this.isYandexSmartCaptcha()) {
          this.api.reset();
        }
      }
    }

    BX.Aspro.Captcha = new JAsproCaptcha();

    /**
     * class JAsproCaptchaReplacer
     */

    class JAsproCaptchaReplacer {
      constructor() {
        this.bindEvents();
      }

      bindEvents() {
        BX.addCustomEvent(window, "onRenderCaptcha", BX.proxy(this.replace, this));
        BX.addCustomEvent(window, "onAjaxSuccess", BX.proxy(this.replace, this));
      }

      replace(e) {
        try {
          this.fixExists();

          let forms = this.getForms();
          for (let i = 0; i < forms.length; ++i) {
            let form = forms[i];

            this.hideLabel(form);
            this.removeReload(form);

            if (BX.Aspro.Captcha.isGoogleRecaptcha3()) {
              this.hideRow(form);
            }

            if (!BX.Aspro.Captcha.isGoogleRecaptcha3() && !BX.Aspro.Captcha.isInvisible()) {
              this.addValidationInput(form);
            }

            let inputs = this.getInputs(form);
            for (let j = 0; j < inputs.length; ++j) {
              this.replaceInput(inputs[j]);
            }

            let images = this.getImages(form);
            for (let j = 0; j < images.length; ++j) {
              this.hideImage(images[j]);
            }
          }

          return true;
        } catch (e) {
          console.error(e);

          return false;
        }
      }

      fixExists() {
        let elements = document.getElementsByClassName(BX.Aspro.Captcha.className);
        if (elements.length) {
          for (let i = 0; i < elements.length; ++i) {
            let element = elements[i];
            let id = element.id;
            if (typeof id === "string" && id.length !== 0) {
              let form = element.closest("form");
              if (form) {
                this.hideLabel(form);
                this.removeReload(form);

                if (BX.Aspro.Captcha.isGoogleRecaptcha() && BX.Aspro.Captcha.isGoogleRecaptcha3()) {
                  this.hideRow(form);
                }

                if (!BX.Aspro.Captcha.isGoogleRecaptcha3() && !BX.Aspro.Captcha.isInvisible()) {
                  this.addValidationInput(form);
                }

                if (BX.Aspro.Captcha.isYandexSmartCaptcha() || !BX.Aspro.Captcha.isGoogleRecaptcha3()) {
                  let captchaRow = element.closest(".captcha-row");
                  if (captchaRow) {
                    if (BX.Aspro.Captcha.isYandexSmartCaptcha()) {
                      if (BX.Aspro.Captcha.isInvisible()) {
                        captchaRow.classList.add(
                          "logo_captcha_" + BX.Aspro.Captcha.params.hideShield ? "n" : "y",
                          BX.Aspro.Captcha.params.shieldPosition
                        );
                      }
                    } else {
                      captchaRow.classList.add(
                        "logo_captcha_" + BX.Aspro.Captcha.params.showLogo,
                        window.BX.Aspro.Captcha.params.badge
                      );
                    }

                    if (BX.Aspro.Captcha.isInvisible()) {
                      captchaRow.classList.add("invisible");
                    }

                    captchaRow.querySelector(".captcha_image")?.classList.add("recaptcha_tmp_img");
                    captchaRow.querySelector(".captcha_input")?.classList.add("recaptcha_text");
                  }
                }
              }
            }
          }
        }
      }

      getForms() {
        let forms = [];

        let inputs = this.getInputs();
        for (let j = 0; j < inputs.length; ++j) {
          let form = inputs[j].closest("form");
          if (form) {
            forms.push(form);
          }
        }

        return forms;
      }

      getInputs(parentNode = null) {
        let inputs = [];

        parentNode = parentNode || document;
        parentNode.querySelectorAll('form input[name="captcha_word"]').forEach((input) => {
          inputs.push(input);
        });

        return inputs;
      }

      getImages(parentNode = null) {
        let images = [];

        parentNode = parentNode || document;
        parentNode.querySelectorAll("img[src]").forEach((image) => {
          if (
            /\/bitrix\/tools\/captcha.php\?(captcha_code|captcha_sid)=[^>]*?/i.test(image.src) ||
            image.id === "captcha"
          ) {
            images.push(image);
          }
        });

        return images;
      }

      replaceInput(node) {
        if (!node) {
          return;
        }

        // generate unic id
        let captchaId = "recaptcha-dynamic-" + new Date().getTime();

        if (document.getElementById(captchaId) !== null) {
          let elementExists = false;
          let additionalIdParameter = null;
          let maxRandomValue = 65535;

          do {
            additionalIdParameter = Math.floor(Math.random() * maxRandomValue);
            elementExists = document.getElementById(captchaId + additionalIdParameter) !== null;
          } while (elementExists);
          captchaId += additionalIdParameter;
        }

        let cwReplacement = document.createElement("div");
        cwReplacement.id = captchaId;
        cwReplacement.className = BX.Aspro.Captcha.className;
        cwReplacement.setAttribute("data-sitekey", BX.Aspro.Captcha.key);

        if (node.parentNode) {
          node.parentNode.classList.add("recaptcha_text");
          node.parentNode.replaceChild(cwReplacement, node);
        }

        BX.Aspro.Captcha.render(captchaId);
      }

      hideImage(node) {
        if (!node) {
          return;
        }

        node.style.display = "none";

        const srcValue = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7";
        node.setAttribute("src", srcValue);

        if (node.parentNode) {
          node.parentNode.classList.add("recaptcha_tmp_img");
        }
      }

      hideLabel(parentNode = null) {
        parentNode = parentNode || document;

        parentNode.querySelectorAll(".captcha-row label:not(.error)").forEach((node) => {
          node.style.display = "none";
        });
      }

      removeReload(parentNode = null) {
        parentNode = parentNode || document;

        parentNode.querySelectorAll(".captcha-row .refresh").forEach((node) => {
          node.remove();
        });

        parentNode.querySelectorAll(".captcha_reload").forEach((node) => {
          node.remove();
        });
      }

      hideRow(parentNode = null) {
        parentNode = parentNode || document;

        parentNode.querySelectorAll(".captcha-row").forEach((node) => {
          node.style.display = "none";
        });
      }

      addValidationInput(parentNode = null) {
        parentNode = parentNode || document;

        parentNode.querySelectorAll(".captcha-row").forEach((node) => {
          if (!node.querySelector("input.recaptcha")) {
            node.appendChild(
              BX.create({
                tag: "input",
                attrs: {
                  type: "text",
                  class: "recaptcha",
                },
                html: "",
              })
            );
          }
        });
      }
    }

    BX.Aspro.Captcha.Replacer = new JAsproCaptchaReplacer();
  })();
}
