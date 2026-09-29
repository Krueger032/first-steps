BX.namespace('BX.Sale.PersonalOrderComponent');

(function() {
	BX.Sale.PersonalOrderComponent.PersonalOrderDetail = {
		init : function(params) {
			var linkMoreOrderInformation = document.getElementsByClassName('sod-about-order-inner-name__read-more')[0],
				linkLessOrderInformation = document.getElementsByClassName('sod-about-order-inner-name__read-less')[0],
				clientInformation = document.getElementsByClassName('sod-about-order-inner-details')[0],
				listShipmentWrapper = document.getElementsByClassName('sod-shipment-list-item-container'),
				listPaymentWrapper = document.getElementsByClassName('sod-payment-inner-payment-container');

			BX.bind(linkMoreOrderInformation, 'click', function() {
				clientInformation.style.display = 'inline-block';
				linkMoreOrderInformation.style.display = 'none';
				linkLessOrderInformation.style.display = 'flex';
			},this);
			
			BX.bind(linkLessOrderInformation, 'click', function() {
				clientInformation.style.display = 'none';
				linkMoreOrderInformation.style.display = 'flex';
				linkLessOrderInformation.style.display = 'none';
			},this);

			Array.prototype.forEach.call(listShipmentWrapper, function(shipmentWrapper) {
				var detailShipmentBlock = shipmentWrapper.getElementsByClassName('sod-shipment-list-item-detail-container')[0],
					showInformation = shipmentWrapper.getElementsByClassName('sod-shipment-list-item-info-link__show')[0],
					hideInformation = shipmentWrapper.getElementsByClassName('sod-shipment-list-item-info-link__hide')[0];

				BX.bindDelegate(shipmentWrapper, 'click', { 'class': 'sod-shipment-list-item-info-link__show' }, BX.proxy(function() {
					showInformation.style.display = 'none';
					hideInformation.style.display = 'flex';
					detailShipmentBlock.style.display = 'block';
				}, this));
				
				BX.bindDelegate(shipmentWrapper, 'click', { 'class': 'sod-shipment-list-item-info-link__hide' }, BX.proxy(function() {
					showInformation.style.display = 'flex';
					hideInformation.style.display = 'none';
					detailShipmentBlock.style.display = 'none';
				}, this));
			});

			Array.prototype.forEach.call(listPaymentWrapper, function(paymentWrapper) {
				var rowPayment = paymentWrapper.getElementsByClassName('sod-payment-inner-selectpay')[0],
					btn = paymentWrapper.getElementsByClassName('sod-payment-inner-pay-button')[0];
				
				BX.bindDelegate(paymentWrapper, 'click', { 'class': 'active-button' }, BX.proxy(function() {
					BX.toggleClass(paymentWrapper, 'sod-active-event');
					if (btn) BX.toggleClass(btn, 'hidden');
				}, this));

				BX.bindDelegate(paymentWrapper, 'click', { 'class': 'sod-payment-inner-info-payment__change-link_open' }, BX.proxy(function(event) {
					event.preventDefault();
						
					var selectPayBlock = rowPayment.getElementsByClassName('sod-payment-inner-select-pay')[0],
						changeLink = paymentWrapper.getElementsByClassName('sod-payment-inner-info-payment__change-link_open')[0],
						linkReturn = rowPayment.parentNode.getElementsByClassName('sod-payment-inner-select-pay__button-back')[0],
						linkReturn2 = paymentWrapper.getElementsByClassName('sod-payment-inner-info-payment__change-link_close')[0];
					
					BX.ajax({
						method: 'POST',
						dataType: 'html',
						url: params.url,
						data: {
							sessid: BX.bitrix_sessid(),
							orderData: params.paymentList[event.target.parentNode.id]
						},
						onsuccess: BX.proxy(function(result) {
							changeLink.parentNode.removeChild(changeLink);
							selectPayBlock.innerHTML = result;
							if(btn)
								btn.parentNode.removeChild(btn);
							linkReturn.style.display = "flex";
							linkReturn2.style.display = "flex";
							BX.toggleClass(rowPayment, 'hidden');
							BX.bind(linkReturn, 'click', function() {
								window.location.reload();
							}, this);
							BX.bind(linkReturn2, 'click', function() {
								window.location.reload();
							}, this);
						}, this),
						onfailure: BX.proxy(function() {
							return this;
						}, this)
					}, this);
				}, this));
			});
		}
	};
})();