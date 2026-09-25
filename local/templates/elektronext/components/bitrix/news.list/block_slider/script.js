(function() {
	'use strict';

	if(!!window.JCNewsListBlockSlider)
		return;

	window.JCNewsListBlockSlider = function(params) {
		this.container = BX(params.container);
		this.smartSpeed = params.smartSpeed;
		this.loop = params.loop;
		this.autoplayTimeout = params.autoplayTimeout;
		this.animateOut = params.animateOut;
		this.animateIn = params.animateIn;

		BX.ready(BX.delegate(this.init, this));
	};

	window.JCNewsListBlockSlider.prototype = {		
		init: function() {
			BX.addClass(this.container, 'owl-carousel');
			$(this.container).owlCarousel({
				items: 1,
				loop: this.loop ? true : false,
				nav: true,
				navText: ['<i class="icon-arrow-left"></i>', '<i class="icon-arrow-right"></i>'],				
				autoplay: true,
				autoplayTimeout: this.autoplayTimeout,			
				autoplayHoverPause: true,
				smartSpeed: this.smartSpeed,
				responsiveRefreshRate: 0,
				animateOut: this.animateOut ? this.animateOut : false,
				animateIn: this.animateIn ? this.animateIn : false,
				navContainer: '.slider'
			});

			this.adjustBlockSlider();
			BX.bind(window, 'resize', BX.proxy(this.adjustBlockSlider, this));
			
			$(this.container).on('translate.owl.carousel', BX.delegate(function(event) {
				var sliderItemsVideo = this.container.querySelectorAll('.slider-item__video');					
				if(!!sliderItemsVideo) {
					for(var i in sliderItemsVideo) {
						if(sliderItemsVideo.hasOwnProperty(i)) {
							sliderItemsVideo[i].pause();
						}
					}
				}
				
				var sliderItemsBlock = this.container.querySelectorAll('.slider-item__block');
				if(!!sliderItemsBlock) {
					for(var i in sliderItemsBlock) {
						if(sliderItemsBlock.hasOwnProperty(i)) {
							BX.removeClass(sliderItemsBlock[i], 'fadeInLeftBig');
							BX.style(sliderItemsBlock[i], 'opacity', '0');
						}
					}
				}
			}, this));
			
			$(this.container).on('translated.owl.carousel', BX.delegate(function(event) {
				var sliderItemsVideo = this.container.querySelectorAll('.slider-item__video');					
				if(!!sliderItemsVideo) {
					for(var i in sliderItemsVideo) {
						if(sliderItemsVideo.hasOwnProperty(i)) {
							var owlItem = BX.findParent(sliderItemsVideo[i], {className: 'owl-item'});
							if(!!owlItem && BX.hasClass(owlItem, 'active'))
								sliderItemsVideo[i].play();
						}
					}
				}
				
				var sliderItemsBlock = this.container.querySelectorAll('.slider-item__block');
				if(!!sliderItemsBlock) {
					for(var i in sliderItemsBlock) {
						if(sliderItemsBlock.hasOwnProperty(i)) {
							var owlItem = BX.findParent(sliderItemsBlock[i], {className: 'owl-item'});
							if(!!owlItem && BX.hasClass(owlItem, 'active')) {
								BX.style(sliderItemsBlock[i], 'opacity', '1');
								BX.addClass(sliderItemsBlock[i], 'fadeInLeftBig');								
							}								
						}
					}
				}
			}, this));
			
			BX.addCustomEvent(window, 'slideMenu', BX.delegate(function() {
				$(this.container).trigger('refresh.owl.carousel');
			}, this));
		},

		adjustBlockSlider: function() {
			var sliderItems = this.container.querySelectorAll('.slider-item');
			if(!!sliderItems) {
				for(var i in sliderItems) {
					if(sliderItems.hasOwnProperty(i)) {
						var sliderItemImageSrc = sliderItems[i].getAttribute('data-image-src'),
							sliderItemVideoSrc = sliderItems[i].getAttribute('data-video-src');
						
						if(window.innerWidth >= 1043) {
							if(!!sliderItemVideoSrc) {
								if(!!sliderItemImageSrc) {
									var sliderItemImage = sliderItems[i].querySelector('.slider-item__pic');
									if(!!sliderItemImage)
										BX.remove(sliderItemImage);
								}

								var sliderItemVideo = sliderItems[i].querySelector('.slider-item__video');
								if(!sliderItemVideo) {
									var sliderItemVideoWidth = sliderItems[i].getAttribute('data-video-width');

									$(sliderItems[i]).prepend('<video class="slider-item__video" muted loop' +
										(!!sliderItemVideoWidth && sliderItemVideoWidth > this.container.parentNode.offsetWidth ? ' style="max-height: 100%;"' : '') +
										'><source src="' + sliderItemVideoSrc + '" type="video/mp4"></video>'
									);

									if(BX.hasClass(sliderItems[i].parentNode, 'active')) {
										sliderItemVideo = sliderItems[i].querySelector('.slider-item__video');
										if(!!sliderItemVideo)
											sliderItemVideo.play();
									}
								}
							} else if(!!sliderItemImageSrc) {
								var sliderItemImage = sliderItems[i].querySelector('.slider-item__pic');
								if(!sliderItemImage) {
									var sliderItemImageWidth = sliderItems[i].getAttribute('data-image-width'),
										sliderItemImageHeight = sliderItems[i].getAttribute('data-image-height'),
										sliderItemImageAlt = sliderItems[i].getAttribute('data-image-alt');

									$(sliderItems[i]).prepend(
										'<div class="slider-item__pic"><img src="' + sliderItemImageSrc + '" width="' + sliderItemImageWidth + '" height="' + sliderItemImageHeight + '" alt="' + sliderItemImageAlt + '" /></div>'
									);
								}
							}
						} else {
							if(!!sliderItemVideoSrc) {
								var sliderItemVideo = sliderItems[i].querySelector('.slider-item__video');
								if(!!sliderItemVideo)
									BX.remove(sliderItemVideo);
							}
							
							if(!!sliderItemImageSrc) {
								var sliderItemImage = sliderItems[i].querySelector('.slider-item__pic');
								if(!sliderItemImage) {
									var sliderItemImageWidth = sliderItems[i].getAttribute('data-image-width'),
										sliderItemImageHeight = sliderItems[i].getAttribute('data-image-height'),
										sliderItemImageAlt = sliderItems[i].getAttribute('data-image-alt');

									$(sliderItems[i]).prepend(
										'<div class="slider-item__pic"><img src="' + sliderItemImageSrc + '" width="' + sliderItemImageWidth + '" height="' + sliderItemImageHeight + '" alt="' + sliderItemImageAlt + '" /></div>'
									);
								}
							}
						}
					}
				}
			}
		}
	}
})();