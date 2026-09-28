if (typeof window.JThemeSelector === 'undefined') {
	window.JThemeSelector = function(id, params, result) {
		var _private = {
			inited: false,
			id: id,
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
			id: {
				get: function() {
					return _private.id;
				},
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

		this.result = function(key) {
			if (typeof result === 'object') {
				if (typeof key !== 'undefined') {
					if (Object.keys(result).indexOf(key) > -1) {
						return result[key];
					}
				}
			}

			return undefined;
		}

		this.init();
	}

	window.JThemeSelector.prototype = {
		node: null,

		get color() {
			if (this.isDefaultColor) {
				return 'default';
			} else {
				return BX.hasClass(document.body, 'theme-dark') ? 'dark': 'light';
			}
		},
		set color(value) {
			if (
				this.node &&
				(
					value === 'light' ||
					value === 'dark' ||
					value === 'default'
				)
			) {
				let color = value;

				if (value === 'default') {
					color = this.defaultColor;
				}

				let item = this.node.querySelector('.theme-selector__item--' + color);
				if (
					item &&
					!BX.hasClass(item, 'current')
				) {
					BX.addClass(item, 'current');

					this.node.setAttribute('title', BX.message('TS_T_' + color));

					let items = Array.prototype.slice.call(this.node.querySelectorAll('.theme-selector__item.current:not(.theme-selector__item--' + color + ')'));
					if (items.length) {
						for (let i = 0; i < items.length; ++i) {
							BX.removeClass(items[i], 'current');
						}
					}
				}

				let changed = !BX.hasClass(document.body, 'theme-' + value);

				if (changed) {
					BX.removeClass(document.body, 'theme-default theme-dark theme-light');
					BX.addClass(document.body, 'theme-' + value);

					let items = Array.prototype.slice.call(document.querySelectorAll('.style-switcher [data-option-id="THEME_VIEW_COLOR"]'));
					if (items.length) {
						for (let i = 0; i < items.length; ++i) {
							let val = items[i].getAttribute('data-option-value');
							if (
								val &&
								val.toLowerCase() === value
							) {
								BX.addClass(items[i], 'current');
							} else {
								BX.removeClass(items[i], 'current');
							}
						}
					}

					let fd = new FormData();
					fd.set('color', value);
					this.sendAction('setColor', fd);

					BX.onCustomEvent(
						'onChangeThemeColor',
						[{
							value: value,
						}]
					);
				}
			}
		},

		get isDefaultColor() {
			return BX.hasClass(document.body, 'theme-default');
		},

		get defaultColor() {
			return window.matchMedia("(prefers-color-scheme: dark)").matches ? 'dark' : 'light';
		},

		init: function() {
			if (!this.inited) {
				this.inited = true;

				this.node = BX('theme-selector--' + this.id);
				if (this.node) {
					this.node.theme_selector = this;

					this.bindEvents();

					// set current item as current body class
					this.color = this.color;
				}
			}
		},

		invert: function() {
			// current color
			let color = this.isDefaultColor ? this.defaultColor : this.color;

			// invert color
			this.color = color === 'light' ? 'dark' : 'light';
		},

		bindEvents: function() {
			if (this.node) {
				if (typeof this.handlers.onNodeClick === 'function') {
					BX.bind(
						this.node,
						'click',
						BX.proxy(
							this.handlers.onNodeClick,
							this
						)
					);
				}

				if (typeof this.handlers.onKeyDown === 'function') {
					BX.bind(
						document.body,
						'keydown',
						BX.proxy(
							this.handlers.onKeyDown,
							this
						)
					);
				}

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
			}
		},

		unbindEvents: function() {
			if (this.node) {
				if (typeof this.handlers.onNodeClick === 'function') {
					BX.unbind(
						this.node,
						'click',
						BX.proxy(
							this.handlers.onNodeClick,
							this
						)
					);
				}

				if (typeof this.handlers.onKeyDown === 'function') {
					BX.unbind(
						document.body,
						'keydown',
						BX.proxy(
							this.handlers.onKeyDown,
							this
						)
					);
				}

				if (typeof this.handlers.onChangePrefersColorScheme === 'function') {
					window.matchMedia('(prefers-color-scheme: dark)').removeEventListener(
						'change',
						BX.proxy(
							this.handlers.onChangePrefersColorScheme,
							this
						)
					);
				}

				if (typeof this.handlers.onChangeThemeColor === 'function') {
					BX.removeCustomEvent(
						'onChangeThemeColor',
						BX.proxy(
							this.handlers.onChangeThemeColor,
							this
						)
					);
				}
			}
		},

		sendAction: function(componentAction, data, onsuccess, onfailure, oncomplete) {
			let componentName = 'aspro:theme.selector.max';

			data.set('is_ajax_post', 'Y');
			data.set('sessid', BX.message('bitrix_sessid'));
			data.set('lang', BX.message('LANGUAGE_ID'));
			data.set('SITE_ID', this.result('SITE_ID'));
			data.set('SIGNED_PARAMS', this.result('SIGNED_PARAMS'));

			let promise = BX.ajax.runComponentAction(
				componentName,
				componentAction,
				{
					mode: 'ajax',
					data: data,
				}
			);

			promise.then(
				BX.proxy(
					function(response){
						if (typeof onsuccess === 'function') {
							onsuccess(response);
						}

						if (typeof oncomplete === 'function') {
							oncomplete();
						}
					},
					this
				),
				BX.proxy(
					function(response){
						console.error(response);

						let message = '';
						for (let i = 0; i < response.errors.length; ++i) {
							if (
								typeof response.errors[i] === 'object' &&
								response.errors[i].message.length
							) {
								if (
									!message.length ||
									response.errors[i].message.length < message.length
								) {
									message = response.errors[i].message;
								}
							}
						}

						if (typeof onfailure === 'function') {
							onfailure(message);
						}

						if (typeof oncomplete === 'function') {
							oncomplete();
						}
					},
					this
				),
			);
		},

		handlers: {
			onNodeClick: function(event) {
				this.invert();
			},

			onKeyDown: function(event) {
				event = event || window.event;

				if (
					event.ctrlKey &&
					event.shiftKey &&
					event.keyCode == 76
				) {
					this.invert();
					event.stopImmediatePropagation();
				}
			},

			onChangePrefersColorScheme: function(event) {
				if (this.isDefaultColor) {
					if (!event) {
						event = window.event;
					}

					this.color = 'default';
				}
			},

			onChangeThemeColor: function(eventdata) {
				if (
					typeof eventdata === 'object' &&
					eventdata &&
					'value' in eventdata
				) {
					this.color = eventdata.value;
				}
			},
		},
	}
}
