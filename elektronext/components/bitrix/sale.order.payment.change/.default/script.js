BX.Sale.OrderPaymentChange = (function() {
	var classDescription = function(params) {
		this.ajaxUrl = params.url;
		this.accountNumber = params.accountNumber || {};
		this.paymentNumber = params.paymentNumber || {};
		this.wrapperId = params.wrapperId || "";
		this.onlyInnerFull = params.onlyInnerFull || "";
		this.inner = params.inner || "";
		this.templateFolder = params.templateFolder;
		this.wrapper = document.getElementById('bx-sopc'+ this.wrapperId);

		this.paySystemsContainer = this.wrapper.getElementsByClassName('sopc-pp')[0];
		BX.ready(BX.proxy(this.init, this));
	};
	
	classDescription.prototype.init = function() {
		var listPaySystems = this.wrapper.getElementsByClassName('sopc-pp-list')[0];
		new BX.easing( {
			duration: 500,
			start: {opacity: 0, height: 50},
			finish: {opacity: 100, height: 'auto'},
			transition: BX.easing.makeEaseOut(BX.easing.transitions.quad),
			step: function(state) {
				listPaySystems.style.opacity = state.opacity / 100;
				listPaySystems.style.height = listPaySystems.height / 450 + 'px';
			},
			complete: function() {
				listPaySystems.style.height = 'auto';
			}
		}).animate();

		BX.bindDelegate(this.paySystemsContainer, 'click', {'className': 'sopc-pp-company'}, BX.proxy(
			function(event) {
				var targetParentNode = event.target.parentNode,
					hidden = targetParentNode.parentNode.getElementsByClassName("sopc-pp-company-hidden")[0];
				
				if(event.target.tagName != "i" && event.target.tagName != "I") {
					BX.ajax( {
							method: 'POST',
							dataType: 'html',
							url: this.ajaxUrl,
							data: {
								sessid: BX.bitrix_sessid(),
								paySystemId: hidden.value,
								accountNumber: this.accountNumber,
								paymentNumber: this.paymentNumber,
								inner: this.inner,
								onlyInnerFull: this.onlyInnerFull
							},
							onsuccess: BX.proxy(function(result) {
								this.paySystemsContainer.innerHTML = result;
								if(document.querySelectorAll('div[data-name="'+this.paymentNumber+'"]').length == 1) {
									var namePaySystemNew = targetParentNode.parentNode.parentNode.getElementsByClassName("sopc-pp-company-smalltitle")[0],
										namePaySystem = document.querySelectorAll('div[data-name="'+this.paymentNumber+'"]')[0];

									if(namePaySystem !== undefined && namePaySystemNew !== undefined) {
										namePaySystem.innerHTML = namePaySystemNew.innerHTML;
									}
								}
							},this),
							onfailure: BX.proxy(function() {
								return this;
							}, this)
						}, this
					);
				}
				return this;
			}, this)
		);
		return this;
	};

	return classDescription;
})();

BX.Sale.OrderInnerPayment = (function() {
	var paymentDescription = function(params) {
		this.ajaxUrl = params.url;
		this.accountNumber = params.accountNumber || {};
		this.paymentNumber = params.paymentNumber || {};
		this.wrapperId = params.wrapperId || "";
		this.valueLimit =  parseFloat(params.valueLimit) || 0;
		this.templateFolder = params.templateFolder;
		this.wrapper = document.getElementById('bx-sopc'+ this.wrapperId);
		this.inputElement = this.wrapper.getElementsByClassName('inner-payment-form-control')[0];
		this.sendPayment = this.wrapper.getElementsByClassName('sale-order-inner-payment-button')[0];
		BX.ready(BX.proxy(this.init, this));
	};

	paymentDescription.prototype.init = function() {
		BX.bind(this.inputElement, 'input', BX.delegate(
			function () {
				this.inputElement.value = this.inputElement.value.replace(/[^\d,.]*/g, '')
					.replace(/,/g, '.')
					.replace(/([,.])[,.]+/g, '$1')
					.replace(/^[^\d]*(\d+([.,]\d{0,2})?).*$/g, '$1');

				var value = parseFloat(this.inputElement.value);

				if(value > this.valueLimit) {
					this.inputElement.value = this.valueLimit;
				}
				if(value <= 0) {
					this.inputElement.value = 0;
					this.sendPayment.classList.add('disabled');
				} else {
					this.sendPayment.classList.remove('disabled');
				}
			}, this)
		);

		BX.bind(this.sendPayment, 'click', BX.delegate(
			function(event) {
				if(event.target.classList.contains("disabled")) {
					return this;
				}
				event.target.classList.add("disabled");
				BX.ajax({
						method: 'POST',
						dataType: 'html',
						url: this.ajaxUrl,
						data: {
							sessid: BX.bitrix_sessid(),
							accountNumber: this.accountNumber,
							paymentNumber: this.paymentNumber,
							inner: "Y",
							onlyInnerFull: this.onlyInnerFull,
							paymentSum :this.inputElement.value
						},
						onsuccess: BX.proxy(function(result) {
							if(result.length > 0)
								this.wrapper.innerHTML = result;
							else
								window.location.reload();
						},this),
						onfailure: BX.proxy(function() {
							return this;
						}, this)
					}, this
				);
				return this;
			}, this)
		);
	};
	
	return paymentDescription;
})();