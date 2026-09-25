BX.ready(function() {
	if(window.location.hash.indexOf('reviews') > -1) {
		var sMenuInt2 = BX.hasClass(document.body, 'slide-menu-interface-2-0-1-inner'),
			topPanel = document.body.querySelector('.top-panel'),
			topPanelHeight = 0,
			topPanelThead = !!topPanel && topPanel.querySelector('.top-panel__thead'),
			reviewsContainer = document.body.querySelector('.reviews');

		if(!!reviewsContainer) {
			if(window.innerWidth < 1043 && !sMenuInt2) {
				if(!!topPanelThead)
					topPanelHeight = topPanelThead.offsetHeight;
			} else if(window.innerWidth >= 1043) {
				if(!!topPanel)
					topPanelHeight = topPanel.offsetHeight;
			}

			new BX.easing({
				duration: 500,
				start: {scroll: 0},
				finish: {scroll: BX.pos(reviewsContainer).top - topPanelHeight},
				transition: BX.easing.makeEaseOut(BX.easing.transitions.quint),
				step: function(state) {
					window.scrollTo(0, state.scroll);
				}
			}).animate();

			window.history.pushState('', document.title, window.location.pathname + window.location.search);
		}
	}
});