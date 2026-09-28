if (typeof window.JLogo  === 'undefined') {
    window.JLogo = function(node, params) {
        var _private = {
            inited: false,
        };

        Object.defineProperties(this, {
            inited: {
                get: function() {
                    return _private.inited;
                },
                set: function(value) {
                    if (value) {
                        _private.inited = true;
                    }
                }
            },
            node: {
                get: function() {
                    return node;
                }
            },
            img: {
                get: function() {
                    return node ? node.querySelector('img') : null;
                }
            },
            svg: {
                get: function() {
                    return node ? node.querySelector('svg') : null;
                }
            },
        });

        this.params = function(key) {
            if (typeof params === 'object') {
                if (typeof key !== 'undefined') {
                    if (Object.keys(params).indexOf(key) > -1) {
                        return params[key];
                    }
                }
            }

            return undefined;
        }

        this.init();
    }

    window.JLogo.prototype = {
        get src() {
            return this.img ? this.img.getAttribute('src') : '';
        },

        set src(value) {
            if (this.img) {
                this.img.setAttribute('src', value);
            }
        },

        get srcDark() {
            return arAsproOptions.THEME.LOGO_IMAGE;
        },

        get srcLight() {
            return arAsproOptions.THEME.LOGO_IMAGE_LIGHT ? arAsproOptions.THEME.LOGO_IMAGE_LIGHT : arAsproOptions.THEME.LOGO_IMAGE;
        },

        get srcFixed() {
            return arAsproOptions.THEME.LOGO_IMAGE_FIXED;
        },

        get color() {
            if (this.img) {
                let src = this.src;

                return src === this.srcDark ? 'dark' : (src === this.srcLight ? 'light' : '');
            }

            return '';
        },

        set color(value) {
            if (this.canChangeColor) {
                if (this.color !== value) {
                    this.src = (value === 'light' ? this.srcLight : this.srcDark);
                }
            }
        },

        get isMainLogo() {
            return Boolean(this.node.closest('header'));
        },

        get isFixedLogo() {
            return Boolean(this.node.closest('#headerfixed'));
        },

        get canChangeColor() {
            let result = false;

            if (this.img) {
                // logo is picture (not svg)
                if (this.isMainLogo) {
                    if (
                        !this.node.closest('.long_banner') &&
                        !this.node.closest('.long_banner_contents')
                    ) {
                        result = true;        
                    }
                }
                else if (this.isFixedLogo) {
                    if (!this.srcFixed) {
                        return true;
                    }
                }
                else {
                    result = true;
                }
            }

            return result;
        },

        setColorOfBanner: function (banner) {
            if (
                this.img &&
                banner
            ) {
                if (typeof window.headerLogo !== 'undefined') {
                    value = BX.data(banner, 'text_color');
                    value = value ? value : 'dark';
                    if (this.color !== value) {
                        this.src = value === 'light' ? this.srcLight : this.srcDark;
                    }
                }
            }
        },

        get isPreferColor() {
            return BX.hasClass(document.body, 'theme-default');
        },

        get preferColor() {
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        },

        get themeColor () {
            if (this.isPreferColor) {
                return this.preferColor;
            }
            else {
                return BX.hasClass(document.body, 'theme-dark') ? 'dark': 'light';
            }
        },

        init: function() {
            if (!this.inited) {
                this.inited = true;

                if (this.img) {
                    this.color = this.getInvertedColor(this.themeColor);

                    this.bindEvents();
                }
            }
        },

        getInvertedColor: function(value) {
            if (value === 'default') {
                value = this.preferColor;
            }

            return value === 'light' ? 'dark' : (value === 'dark' ? 'light' : '');
        },

        bindEvents: function() {
            if (typeof this.handlers.onChangePrefersColorScheme === 'function') {
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener(
                    'change', 
                    BX.proxy(
                        this.handlers.onChangePrefersColorScheme,
                        this
                    )
                );
            }

            if (typeof this.handlers.onChangeThemeColor === 'function') {
                BX.addCustomEvent(
                    'onChangeThemeColor',
                    BX.proxy(
                        this.handlers.onChangeThemeColor,
                        this
                    )
                );
            }
        },

        unbindEvents: function() {

        },

        handlers: {
            onChangePrefersColorScheme: function(event) {                
                if (!event) {
                    event = window.event;
                }

                let newColor = event.matches ? 'dark' : 'light';

                if (this.isPreferColor) {
                    this.color = this.getInvertedColor(newColor);
                }

                BX.setCookie('prefers-color-scheme', newColor);
            },

            onChangeThemeColor: function(eventdata) {
                if (
                    typeof eventdata === 'object' &&
                    eventdata &&
                    'value' in eventdata
                ) {
                    this.color = this.getInvertedColor(eventdata.value);
                }
            },
        }
    }

    let readyFunc = typeof readyDOM === 'function' ? readyDOM : BX.ready;
    readyFunc(function() {
        window.headerLogo = new JLogo(document.querySelector('header .logo'));
        window.headerfixedLogo = new JLogo(document.querySelector('#headerfixed .logo'));
        window.megamenuLogo = new JLogo(document.querySelector('.mega_fixed_menu .logo'));
        window.mobileheaderLogo = new JLogo(document.querySelector('#mobileheader .logo'));
        BX.setCookie('prefers-color-scheme', window.headerLogo.preferColor);
    });
}