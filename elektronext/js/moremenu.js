(function (factory) {
	if(typeof define === 'function' && define.amd) {		
		define(['jquery'], factory);
	} else {		
		factory(jQuery);
	}
}(function ($) {
	var moreObjects = [];
	
	function adjustMoreMenu() {
		$(moreObjects).each(function () {
			$(this).moreMenu({
				'undo' : true
			}).moreMenu(this.options);
		});
	}	

	$(window).resize(function () {		
		adjustMoreMenu();
	});

	$.fn.moreMenu = function (options) {
		var checkMoreObject,
			s = $.extend({
				'threshold': 2,				
				'linkText': '...',				
				'undo': false
			}, options);
		this.options = s;
		checkMoreObject = $.inArray(this, moreObjects);
		if(checkMoreObject >= 0) {
			moreObjects.splice(checkMoreObject, 1);
		} else {
			moreObjects.push(this);
		}
		return this.each(function () {
			var $this = $(this),
				isTopMenu = $this.hasClass("horizontal-multilevel-menu"),
				$items = $this.find('> li'),
				$firstItem = $items.first(),
				$lastItem = $items.last(),
				numItems = $this.find('li').length,
				firstItemTop = Math.floor($firstItem.offset().top),
				firstItemHeight = Math.floor($firstItem.outerHeight(true)),
				$lastChild,
				keepLooking,
				$moreItem,				
				numToRemove,				
				$menu,
				i;
			
			function needsMenu($itemOfInterest) {
				var result = (Math.ceil($itemOfInterest.offset().top) >= (firstItemTop + firstItemHeight)) ? true : false;				
				return result;
			}
			
			if(needsMenu($lastItem) && numItems > s.threshold && !s.undo && $this.is(':visible')) {
				var $popup = $('<ul class="' + (!!isTopMenu ? 'horizontal-multilevel-dropdown-menu' : 'catalog-menu-dropdown-menu') + ' more-menu" data-entity="dropdown-menu"></ul>');
				
				for(i = numItems; i > 1; i--) {					
					$lastChild = $this.find('> li:last-child');
					keepLooking = (needsMenu($lastChild));
					if(keepLooking) {
						$lastChild.appendTo($popup);
					} else {
						break;
					}					
				}
				
				$this.append('<li class="more" data-entity="dropdown"><a href="javascript:void(0)">' + s.linkText + '</a></li>');
				
				$moreItem = $this.find('> li.more');
				if(needsMenu($moreItem)) {
					$this.find('> li:nth-last-child(2)').appendTo($popup);
				}				
				
				$popup.children().each(function (i, li) {
					$popup.prepend(li);
				});
				
				$moreItem.append($popup);
			} else if(s.undo && $this.find('ul.more-menu')) {
				$menu = $this.find('ul.more-menu');
				numToRemove = $menu.find('li').length;
				for(i = 1; i <= numToRemove; i++) {
					$menu.find('> li:first-child').appendTo($this);
				}
				$menu.remove();				
				$this.find('> li.more').remove();
				$this.find(!!isTopMenu ? '.horizontal-multilevel-dropdown-menu' : '.catalog-menu-dropdown-menu').removeAttr('style');
			}
		});
	};
}));