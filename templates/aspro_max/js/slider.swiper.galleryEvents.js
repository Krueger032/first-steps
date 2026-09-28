BX.addCustomEvent("onInitSlider", eventdata => {
	if ("slider" in eventdata && eventdata.slider) {
		const slider = eventdata.slider;

		if (slider && slider.params && "type" in slider.params) {
			if (slider.params.type === "detail_gallery_main") {
				const $sliderContainer = slider.el.parentElement;
				const $navItems = $sliderContainer.querySelectorAll('.slider-nav');

				for (let i = 0; i < $navItems.length; i++) {
					$navItems[i].removeAttribute('style');
				}
			}

			if (slider.params.type === "detail_gallery_thumb") {
				const S_HIDE_NAVIGATION_CLASS = 'gallery-slider-thumb__container--hide-navigation';
				const S_HIDE_THUMBS_CLASS = 'gallery-wrapper--hide-thumbs';

				const $sliderContainer = slider.el.parentElement;
				const $galleryContainer = $sliderContainer.closest('.detail-gallery-big');
				const $navItems = $sliderContainer.querySelectorAll('.slider-nav');


				UpdateSliderArrows(slider, $sliderContainer, S_HIDE_NAVIGATION_CLASS);
				UpdateThumbsBlock(slider.slides.length, $galleryContainer, S_HIDE_THUMBS_CLASS);

				for (let i = 0; i < $navItems.length; i++) {
					$navItems[i].removeAttribute('style');
				}

				slider.on('update', function () {
					const slidesLength = this.wrapperEl.children.length;

					UpdateSliderArrows(this, $sliderContainer, S_HIDE_NAVIGATION_CLASS);
					UpdateThumbsBlock(slidesLength, $galleryContainer, S_HIDE_THUMBS_CLASS);
				});
			}
		}
	}
});

BX.addCustomEvent("onSlideClick", eventdata => {

	if ("slider" in eventdata && eventdata.slider && "event" in eventdata) {
		const slider = eventdata.slider;
		let curSlide = $(eventdata.event.target).closest('.swiper-slide');

		if (slider && slider.params && "magnifier" in slider.params) {
			let magnifier = slider.params.magnifier;
			if (magnifier) {
				const $parent = curSlide.closest(".product-container");

				if ($parent.find(".zoom_picture").length) {
					const $zoomImg = $parent.find(".zoom_picture");
					const $galleryImg = curSlide.find("img");

					const bigImage = curSlide.data("big"),
						srcImage = $galleryImg.attr("src");

					$zoomImg.attr("data-large", bigImage || srcImage);
					$zoomImg.attr("xoriginal", bigImage || srcImage);
					$zoomImg.attr("src", bigImage || srcImage);
					$zoomImg.attr("data-xoriginalwidth", $galleryImg.attr("data-xoriginalwidth"));
					$zoomImg.attr("data-xoriginalheight", $galleryImg.attr("data-xoriginalheight"));
					$parent.find(".detail-gallery-big__link").attr("href", srcImage);
				}
			}
		}
	}
});

const UpdateSliderArrows = function(slider, $sliderContainer, classList) {
	(slider.isBeginning && slider.isEnd || !slider.slides.length)
		? $sliderContainer.classList.add(classList)
		: $sliderContainer.classList.remove(classList);
};

const UpdateThumbsBlock = function(slidesLength, $container, classList) {
	if (typeof $container !== 'undefined') {
		slidesLength
			? $container.classList.remove(classList)
			: $container.classList.add(classList);
	}
}
