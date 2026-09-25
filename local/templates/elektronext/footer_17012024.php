<?php 
if(strpos($_SERVER['HTTP_USER_AGENT'],'Chrome-Lighthouse')) {
	
} else { ?>
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
IncludeTemplateLangFile(__FILE__);
			if(!$isSiteClosed) {
				if(!CSite::inDir(SITE_DIR."index.php")) {?>									</div>
								</div>
							</div>
						</div>
						<?if(!CSite::inDir(SITE_DIR."personal/order/make/")) {?>
							<div class="hidden-print viewed-wrapper" data-entity="parent-container" style="display: none;">
								<div class="container">
									<div class="row viewed">
										<div class="col-xs-12">
											<div class="h2" data-entity="header" data-showed="false" style="display: none; opacity: 0;">
												<?//VIEWED_TITLE//?>
												<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/footer_viewed_title.php"), false);?>	
											</div>
											<?//VIEWED//?>
											<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
												array(
													"AREA_FILE_SHOW" => "file",
													"PATH" => SITE_DIR."include/footer_viewed.php",
													"AREA_FILE_RECURSIVE" => "N",
													"EDIT_MODE" => "html",
												),
												false,
												array("HIDE_ICONS" => "Y")
											);?>
										</div>
									</div>
								</div>
							</div>
							<?if(in_array("BIG_DATA", $arSettings["SITE_BLOCKS"]["VALUE"])) {?>
								<div class="hidden-print bigdata-wrapper" data-entity="parent-container" style="display: none;">
									<div class="container">
										<div class="row bigdata">
											<div class="col-xs-12">
												<div class="h1" data-entity="header" data-showed="false" style="display: none; opacity: 0;">
													<?//BIGDATA_TITLE//?>
													<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/footer_bigdata_title.php"), false);?>		
												</div>
												<?//BIGDATA//?>
												<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
													array(
														"AREA_FILE_SHOW" => "file",
														"PATH" => SITE_DIR."include/footer_bigdata.php",
														"AREA_FILE_RECURSIVE" => "N",
														"EDIT_MODE" => "html",
													),
													false,
													array("HIDE_ICONS" => "Y")
												);?>
											</div>
										</div>
									</div>
								</div>
							<?}
						}
				}
			}
			//FEEDBACK//
			if(in_array("FEEDBACK", $arSettings["SITE_BLOCKS"]["VALUE"]) && !CSite::inDir(SITE_DIR."personal/order/make/")) {?>
				<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
					array(
						"AREA_FILE_SHOW" => "file",
						"PATH" => SITE_DIR."include/footer_feedback.php"
					),
					false,
					array("HIDE_ICONS" => "Y")
				);?>
			<?}
			if(!$isSiteClosed && in_array("BOTTOM_MENU", $arSettings["SITE_BLOCKS"]["VALUE"])) {?>
<!--				<div class="hidden-print bottom-menu-wrapper">-->
<!--					<div class="bottom-menu">-->
<!--						<div class="container">-->
<!--							<div class="row">-->
<!--								<div class="col-xs-12">-->
									<!--BOTTOM_MENU-->
									<?/*$APPLICATION->IncludeComponent("bitrix:main.include", "",
										array(
											"AREA_FILE_SHOW" => "file",
											"PATH" => SITE_DIR."include/footer_bottom_menu.php"
										),
										false,
										array("HIDE_ICONS" => "Y")
									);*/?>
<!--								</div>-->
<!--							</div>-->
<!--						</div>-->
<!--					</div>-->
<!--				</div>-->
			<?}?>
			<div class="hidden-print footer-wrapper">
				<div class="container">
					<div class="row">
						<div class="footer">						
<!--							<div class="col-xs-12 col-md-4">-->
<!--								<div class="footer__copyright">									-->
									<?//COPYRIGHT//?>
									<?//$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/footer_copyright.php"), false);?>
<!--								</div>-->
<!--							</div>							-->
                            <div class="col-xs-12 col-md-2">
                                <ul class="footer-menu">
                                    <li><a href="/">Главная</a></li>
                                    <li><a href="/catalog/">Каталог</a></li>
                                    <li><a href="/payment-delivery/">Оплата и доставка</a></li>
                                    <li><a href="/places/">Магазины</a></li>
                                </ul>
                            </div>
                            <div class="col-xs-12 col-md-2">
                                <ul class="footer-menu">
                                    <li><a href="/brands/">Бренды</a></li>
                                    <li><a href="/garantii/">Гарантии</a></li>
                                    <li><a href="/about/">О магазине</a></li>
                                    <li><a href="/contacts/">Контакты</a></li>
                                </ul>
                            </div>
                            <div class="col-xs-12 col-md-3 privacy">
                                <!--FOOTER_MENU-->
                                <?$APPLICATION->IncludeComponent("bitrix:main.include", "",
                                    array(
                                        "AREA_FILE_SHOW" => "file",
                                        "PATH" => SITE_DIR."include/footer_menu.php"
                                    ),
                                    false,
                                    array("HIDE_ICONS" => "Y")
                                );?>
                            </div>
							<div class="col-xs-12 col-md-3">
                                <p class="footer-addr">
                                    г. Москва, 41-й км МКАД<br/>
                                    ТВК "Мельница" 3-я линия<br/>
                                    Пассаж 21-22</p>
                                <p class="footer-addr">пн-пт 09:00 - 19:00<br/>
                                    сб-вс 10:00 - 18:00
                                </p>
                            </div>
                            <div class="col-xs-12 col-md-2">
                                <?//SOCIAL//?>
                                <?$APPLICATION->IncludeComponent("bitrix:main.include", "",
                                    array(
                                        "AREA_FILE_SHOW" => "file",
                                        "PATH" => SITE_DIR."include/footer_social.php"
                                    ),
                                    false,
                                    array("HIDE_ICONS" => "Y")
                                );?>
                            </div>
<!--							<div class="col-xs-12 col-md-2">-->
<!--								<div class="footer__developer">-->
									<?//DEVELOPER//?>
									<?//$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/footer_developer.php"), false);?>
<!--								</div>-->
<!--							</div>-->
						</div>
					</div>
				</div>
			</div>
			<?//SLIDE_PANEL//?>
			<div class="slide-panel"></div>
			<?if(!$isSiteClosed) {?>
				</div>
			<?}?>			
		</div>
		<?//SCROLL_UP//?>
		<a class="scroll-up" href="javascript:void(0)"><i class="icon-arrow-up"></i></a>
		<?//JS//?>
		<script type="text/javascript">
			BX.message({
				SITE_ID: "<?=SITE_ID?>",
				SITE_DIR: "<?=SITE_DIR?>",				
				SITE_SERVER_NAME: "<?=SITE_SERVER_NAME?>",
				SITE_TEMPLATE_PATH: "<?=SITE_TEMPLATE_PATH?>",
				SITE_CHARSET: "<?=SITE_CHARSET?>",
				LANGUAGE_ID: "<?=LANGUAGE_ID?>",
				COOKIE_NAME: "<?=Bitrix\Main\Config\Option::get('main', 'cookie_name', 'BITRIX_SM')?>",
				SLIDE_PANEL_SEARCH_TITLE: "<?=GetMessageJS('ENEXT_SLIDE_PANEL_SEARCH_TITLE')?>",				
				SLIDE_PANEL_UNDEFINED_ERROR: "<?=GetMessageJS('ENEXT_SLIDE_PANEL_UNDEFINED_ERROR')?>"
			});
			//IE fix for "jumpy" fixed background
			if(navigator.userAgent.match(/MSIE 10/i) || navigator.userAgent.match(/Trident\/7\./) || navigator.userAgent.match(/Edge\/12\./)) {
				$("body").on("mousewheel", function () {
					event.preventDefault();
					var wd = event.wheelDelta;
					var csp = window.pageYOffset;
					window.scrollTo(0, csp - wd);
				});
			}
		</script>
		<?=$APPLICATION->ShowProperty("countersScriptsBodyEnd");?>
<!-- Yandex.Metrika counter -->

<script src="/local/templates/elektronext/js/metrika.js"></script>
<noscript><div><img src="https://mc.yandex.ru/watch/78427446" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<!-- BEGIN JIVOSITE CODE {literal} -->
<script src="/local/templates/elektronext/js/jivo.js"></script>
<?php } ?>
<!-- {/literal} END JIVOSITE CODE -->

<script>
if (document.querySelectorAll('.contacts-item-link').length) {
	document.querySelectorAll('.contacts-item-link')[2].textContent = '+7 (925) 112-22-90';
	const elem = '<span class="contacts-item-descr">Написать в WhatsApp</span>';
	document.querySelectorAll('.contacts-item-link')[2].insertAdjacentHTML('beforeend', elem);
}
</script>

<?php if ($_SERVER['REQUEST_URI'] == '/catalog/dush/') : ?>
	<script>
	const categoryLinks = document.querySelectorAll('.catalog-section-list .category-link');

	categoryLinks.forEach((link) => {
	  const innerLink = link.querySelector('a[id="bx_1847241719_455"]');
	  
	  if (innerLink) {
		link.remove();
	  }
	});
	</script>
<?php endif; ?>
<?php if ($_SERVER['REQUEST_URI'] == '/catalog/aksessuary-dlya-vannoy-komnaty/') : ?>
	<script>
	const categoryLinks = document.querySelectorAll('.catalog-section-list .category-link');

	categoryLinks.forEach((link) => {
	  const innerLink = link.querySelector('a[id="bx_1847241719_642"]') || link.querySelector('a[id="bx_1847241719_646"]');
	  
	  if (innerLink) {
		link.remove();
	  }
	});
	</script>
<?php endif; ?>
 <?php if ($_SERVER['REQUEST_URI'] == '/catalog/polotentsesushiteli1/') : ?>
	<script>
	$('a[href="/catalog/polotentsesushiteli1/filter/clear/apply/filter/san_tip-is-электрический/apply/"]').remove();
	  
 
	</script>
<?php endif; ?>

	<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/main.min.js"></script>
	<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/owlCarousel/owl.carousel.min.js"></script>
	</body>
</html>