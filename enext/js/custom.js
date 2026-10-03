/***CUSTOM JAVASCRIPT FOR YOUR SITE***/
/* Keep the side background still when a slide panel hides the scrollbar. */
(function() {
	var classes = [
		"slide-panel-active",
		"slide-panel-contacts-active",
		"slide-panel-cart-active",
		"slide-panel-map-active",
		"popup-panel-active",
		"search-yandex-active"
	];
	var apply = function() {
		if(!document.body) return;
		var open = false;
		for(var i = 0; i < classes.length; i++) {
			if(document.body.classList.contains(classes[i])) {
				open = true;
				break;
			}
		}
		var pad = open ? (parseFloat(document.body.style.paddingRight) || 0) : 0;
		document.documentElement.style.setProperty("--pw-sb", pad + "px");
		var sheet = document.querySelector(".page-wrapper");
		if(sheet)
			document.documentElement.style.setProperty("--pw-sheet-left", sheet.getBoundingClientRect().left + "px");
	};
	var start = function() {
		apply();
		if(window.MutationObserver)
			new MutationObserver(apply).observe(document.body, {attributes: true, attributeFilter: ["class", "style"]});
		window.addEventListener("resize", apply);
	};
	if(document.body)
		start();
	else
		document.addEventListener("DOMContentLoaded", start);
})();
