readyDOM(function () {
	initCollapsedBlock();
});

function initCollapsedBlock() {
	let detailWrapTextContent = $(".ordered-block__content .ordered-block__text-wrap");
	detailWrapTextContent.each(function () {
		let _this = $(this);
		if (
			!_this.closest('.tab-pane').length
			|| _this.closest('.tab-pane.active').length
		) {
			let detailTextBlock = _this.find('.ordered-block__text');
			let nodeDetailSpoilerButton = _this.find('.ordered-block__spoiler-btn');
			showCollapsedBlock(detailTextBlock, nodeDetailSpoilerButton);
		}
	});
}

function showCollapsedBlock(detailTextBlock, nodeDetailSpoilerButton) {
	if (!detailTextBlock.hasClass('_inited')) {
		detailTextBlock.addClass('_inited');

		nodeDetailSpoilerButton.on("click", function () {
			let _thisButton = $(this);
			_thisButton.toggleClass('show');

			detailTextBlock.toggleClass('show-collapsed-js opened');
		});
	}

	if (detailTextBlock.prop('scrollHeight') > detailTextBlock.prop('clientHeight')) {
		nodeDetailSpoilerButton.removeClass('hidden');
	} else {
		if (!nodeDetailSpoilerButton.hasClass('show')) {
			nodeDetailSpoilerButton.addClass('hidden');
		}
	}
}

(function (window) {
	window.SetActiveTab = function (wrapperTabs) {
		wrapperTabs.each(function () {
			var _this = $(this);
			if ((_this.hasClass("active") && _this.hasClass("hidden"))) {
				_this.next().find("a").click();
			} else if ($(this).hasClass("hidden")) {
				_this.next().find("a").click();
			}
		});
	};
})(window);

