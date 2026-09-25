<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

IncludeTemplateLangFile(__FILE__);

CJSCore::Init(array("fx"));


$scheme = CMain::IsHTTPS() ? "https" : "http";

?>


<!DOCTYPE html>
<html lang="<?= LANGUAGE_ID ?>">

<head>


	<?php
	if (!empty($_GET)) {
		$APPLICATION->SetPageProperty('noindex', 'Y');
		$APPLICATION->SetPageProperty('robots', 'noindex, nofollow');
	}
	?>

	<? if (!isLighthouse()): ?>

		<noscript><img src="https://mc.yandex.ru/watch/78427446" style="position:absolute; left:-9999px;"
				alt="" /></noscript>

		<script type="text/javascript">
		(function(window, document) {
			var loaded = false;
			var events = ['pointerdown', 'touchstart', 'keydown', 'scroll'];

			function loadGudok() {
				if(loaded) return;
				loaded = true;
				events.forEach(function(eventName) {
					window.removeEventListener(eventName, loadGudok);
				});
				(function(window, document, n, project_ids) { window.GudokData = n; if(typeof project_ids !== "object") { project_ids = [project_ids]; } window[n] = {}; window[n]["projects"] = project_ids; config_load(project_ids.join(',')); function config_load(cId) { var a = document.getElementsByTagName("script")[0], s = document.createElement("script"), i = function() { a.parentNode.insertBefore(s, a); }, cMrs = ''; s.async = true; if(document.location.search && document.location.search.indexOf('?gudok_check=') === 0) cMrs += document.location.search.replace('?', '&'); s.src = "//mod.gudok.tel/script.js?sid=" + cId + cMrs; if(window.opera == "[object Opera]") { document.addEventListener("DOMContentLoaded", i, false); } else { i(); } } })(window, document, "gd", "qmk6d0jedn");
			}

			events.forEach(function(eventName) {
				window.addEventListener(eventName, loadGudok, {passive: true, once: true});
			});
			window.setTimeout(loadGudok, 30000);
		})(window, document);
		</script>

		<!-- <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script> -->




	<? endif ?>

	<meta name="yandex-verification" content="13b653948370833e" />


	<style>
		#bx_651765591_77>div.contacts-item-row.contacts-item-whatsapp>div>i {
			color: #39AE41
		}
	</style>

	<?php

	set_include_path($_SERVER["DOCUMENT_ROOT"]);

	?>



	<?= $APPLICATION->ShowProperty("countersScriptsHead"); ?>

	<meta http-equiv="X-UA-Compatible" content="IE=edge" />

	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
	<link rel="icon" sizes="48x48" href="/favicon-48x48.png" type="image/png">
	<link rel="icon" sizes="32x32" href="/favicon-32x32.png" type="image/png">
	<link rel="icon" type="image/x-icon" sizes="32x32" href="/favicon-32x32.ico">
	<link rel="icon" type="image/x-icon" sizes="16x16" href="/favicon-16x16.ico">

	<link rel="icon" sizes="16x16" href="/favicon-16x16.png" type="image/png">
	<link rel="icon" type="image/svg+xml" href="/favicon.svg" />
	<link rel="shortcut icon" href="/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" type="image/svg+xml" href="/favicon180.png" />
	<meta name="apple-mobile-web-app-title" content="MyWebSite" />
	<link rel="manifest" href="/webmanifest.json" />

	<? if (!empty($metaDesc) && isset($metaDesc)) {

	} else {

		echo $metaDesc;

	}

	if (isset($metaKey) && !empty($metaKey)) {

	} else {

		echo $metaKey;

	} ?>

	<? if (isset($metaTitle) && !empty($metaTitle)) { ?>

		<title>

			<?

			echo $metaTitle;

			?>

		</title>

	<? } else {

		?>

		<title><? $APPLICATION->ShowTitle() ?></title>

		<?

	}

	$APPLICATION->ShowHead();



	if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome-Lighthouse')) {

		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/css/bootstrap.min.css");

	} else {

		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/colors.min.css", true);

		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/css/animation.min.css");

		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/css/csshake-default.min.css");

		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/js/scrollbar/jquery.scrollbar.min.css");

		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/css/bootstrap.min.css");

	}

	CJSCore::Init(array("jquery2", "elektronextIntlTelInput"));

	if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome-Lighthouse')) {



	} else {

		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/bootstrap.min.js");

		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/formValidation.min.js");

		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/inputmask.min.js");

		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/jquery.hoverIntent.min.js");

		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/moremenu.min.js");

		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/scrollbar/jquery.scrollbar.min.js");

		// $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/main.min.js"); при данном подключении дублируется скрипт, из-за чего не работает кнопка "поделиться" и поиск
	
		?>

		<?

	}

	?>

	<? //$APPLICATION->ShowCSS(); ?>

	<? $APPLICATION->ShowHeadStrings(); ?>

	<? $APPLICATION->ShowHeadScripts(); ?>

	<script type="text/javascript" src="/exform/exform.js"></script>

	<? $smartSpeed = $arSettings["SMART_SPEED"]["VALUE"] ? $arSettings["SMART_SPEED"]["VALUE"] : 1000; ?>

	<!-- santehpodbor-deferred-comagic-20260901 -->
	<script>
		(function (window, document) {
			var loaded = false;
			var events = ['pointerdown', 'touchstart', 'keydown', 'scroll'];

			function loadComagic() {
				if (loaded) return;
				loaded = true;
				events.forEach(function (eventName) {
					window.removeEventListener(eventName, loadComagic);
				});
				var script = document.createElement('script');
				script.async = true;
				script.src = 'https://app.comagic.ru/static/cs.min.js?k=Uv3SdOF385Al7mOAHkXiZ74HZIMfo51H';
				document.head.appendChild(script);
			}

			events.forEach(function (eventName) {
				window.addEventListener(eventName, loadComagic, { passive: true });
			});
			window.addEventListener('load', function () {
				window.setTimeout(loadComagic, 30000);
			}, { once: true });
		})(window, document);
	</script>
	<div class="cookie-notice">
		Продолжая использовать сайт, вы соглашаетесь с
		<a href="https://test.santehpodbor.ru/privacy/ ">политикой обработки персональных данных.</a>
		<button>Принять</button>
	</div>

<link rel="stylesheet" href="/local/templates/elektronext/css/cart-popup.css?v=4">

	<style>
		.smartfilter label.bx-filter-param-label.disabled {
			display: none !important;
		}
	</style>
	<script>
		(function() {
			var scheduled = false;

			function getFilterBoxes(root) {
				if (!root) {
					return [];
				}

				return root.querySelectorAll('.bx-filter-parameters-box');
			}

			function hasVisibleControls(box) {
				if (!box) {
					return false;
				}

				var selectors = [
					'.bx-filter-parameters-box-block-container',
					'.bx-ui-slider-track-container',
					'input[type="text"]',
					'select'
				];

				for (var i = 0; i < selectors.length; i++) {
					if (box.querySelector(selectors[i])) {
						return true;
					}
				}

				return false;
			}

			function updateBox(box) {
				if (!box) {
					return;
				}

				var labels = box.querySelectorAll('label.bx-filter-param-label');
				var visibleLabelsCount = 0;

				for (var i = 0; i < labels.length; i++) {
					var label = labels[i];
					var input = label.querySelector('input');
					var isDisabled = label.classList.contains('disabled');
					var isChecked = !!(input && input.checked);
					var shouldHide = isDisabled && !isChecked;

					label.style.display = shouldHide ? 'none' : '';

					if (!shouldHide) {
						visibleLabelsCount++;
					}
				}

				var hasLabels = labels.length > 0;
				var boxHasVisibleControls = hasVisibleControls(box);
				box.style.display = (hasLabels && visibleLabelsCount === 0 && !boxHasVisibleControls) ? 'none' : '';

				var propertyBox = box.closest('.bx-filter-parameters-box');
				if (!propertyBox) {
					return;
				}

				var propertyHasLabels = propertyBox.querySelectorAll('label.bx-filter-param-label').length > 0;
				var propertyVisibleLabels = propertyBox.querySelectorAll('label.bx-filter-param-label:not([style*="display: none"])').length;
				var propertyHasVisibleControls = hasVisibleControls(propertyBox);

				propertyBox.style.display = (propertyHasLabels && propertyVisibleLabels === 0 && !propertyHasVisibleControls) ? 'none' : '';
			}

			function updateAllFilters() {
				scheduled = false;

				var filterBlocks = document.querySelectorAll('.smartfilter [data-role="bx_filter_block"]');
				for (var i = 0; i < filterBlocks.length; i++) {
					updateBox(filterBlocks[i]);
				}
			}

			function scheduleUpdate() {
				if (scheduled) {
					return;
				}

				scheduled = true;

				if (window.requestAnimationFrame) {
					window.requestAnimationFrame(updateAllFilters);
				} else {
					setTimeout(updateAllFilters, 0);
				}
			}

			function bindFilterObserver() {
				var filters = document.querySelectorAll('.smartfilter');
				if (!filters.length) {
					return;
				}

				for (var i = 0; i < filters.length; i++) {
					var filter = filters[i];
					var observer = new MutationObserver(function() {
						scheduleUpdate();
					});

					observer.observe(filter, {
						attributes: true,
						attributeFilter: ['class', 'style', 'checked', 'disabled'],
						childList: true,
						subtree: true
					});
				}
			}

			document.addEventListener('DOMContentLoaded', function() {
				scheduleUpdate();
				bindFilterObserver();
			});

			document.addEventListener('click', function(event) {
				if (event.target.closest('.smartfilter')) {
					setTimeout(scheduleUpdate, 0);
				}
			}, true);

			document.addEventListener('change', function(event) {
				if (event.target.closest('.smartfilter')) {
					scheduleUpdate();
				}
			}, true);
		})();
	</script>

</head>



<body class="<?= $APPLICATION->ShowProperty('catalogMenu') . $APPLICATION->ShowProperty('smartFilterView') ?>">


	<?= $APPLICATION->ShowProperty("countersScriptsBodyStart");

	echo $APPLICATION->ShowPanel();

	global $arSettings;

	$arSettings = $APPLICATION->IncludeComponent("altop:settings.elektronext", "", array(), false, array("HIDE_ICONS" => "Y"));

	$isSiteClosed = COption::GetOptionString("main", "site_stopped") == "Y" && !$USER->CanDoOperation("edit_other_settings") ? true : false; ?>

	<div class="page-wrapper">



		<? if (!$isSiteClosed) { ?>

			<div
				class="hidden-xs hidden-sm<?= (!in_array('TOP_MENU', $arSettings['SITE_BLOCKS']['VALUE']) ? ' hidden-md hidden-lg' : '') ?> hidden-print top-menu-wrapper">

				<div class="top-menu">

					<? //TOP_MENU//
					
						?>

					<? $APPLICATION->IncludeComponent(

						"bitrix:main.include",

						"",

						array(

							"AREA_FILE_SHOW" => "file",

							"PATH" => SITE_DIR . "include/header_top_menu.php"

						),

						false,

						array("HIDE_ICONS" => "Y")

					); ?>

				</div>

			</div>

		<? } ?>

		<div class="hidden-print top-panel-wrapper">

			<div
				class="top-panel<?= (!$APPLICATION->GetDirProperty('PERSONAL_SECTION') && ($arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-4' || $arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-5') ? ' catalog-menu-outside' : '') ?>">

				<div class="top-panel__cols">

					<div class="top-panel__col top-panel__thead">

						<div class="top-panel__cols">

							<? //MENU_ICON//
							
							if (!$isSiteClosed) { ?>

								<div class="top-panel__col top-panel__menu-icon-container<?= ($arSettings['CATALOG_MENU']['VALUE'] == 'INTERFACE-2-0-2' || $arSettings['CATALOG_MENU']['VALUE'] == 'INTERFACE-2-0-3' || $arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-3' || $arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-4' || $arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-5' ? '  ' : '') ?>"
									data-entity="menu-icon">

									<i class="icon-menu"></i>

									<? if ($arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-6') {

										//MENU//
								
										?>

										<? $APPLICATION->IncludeComponent(

											"bitrix:main.include",

											"",

											array(

												"AREA_FILE_SHOW" => "file",

												"PATH" => SITE_DIR . "include/slide_menu.php"

											),

											false,

											array("HIDE_ICONS" => "Y")

										); ?>

									<? } ?>

								</div>

							<? }

							//LOGO//
							
							?>

							<div class="top-panel__col top-panel__logo">

								<? $APPLICATION->IncludeComponent(

									"bitrix:main.include",

									"",

									array(

										"AREA_FILE_SHOW" => "file",

										"PATH" => SITE_DIR . "include/header_logo.php"

									),

									false

								); ?>

							</div>

							<? //CONTACTS//
							
							if ($arSettings["CATALOG_MENU"]["VALUE"] == "INTERFACE-2-0-1" || $arSettings["CATALOG_MENU"]["VALUE"] == "INTERFACE-2-0-2" || $arSettings["CATALOG_MENU"]["VALUE"] == "INTERFACE-2-0-3") { ?>

								<? $APPLICATION->IncludeComponent(

									"bitrix:main.include",

									"",

									array(

										"AREA_FILE_SHOW" => "file",

										"PATH" => SITE_DIR . "include/header_contacts.php"

									),

									false,

									array("HIDE_ICONS" => "Y")

								); ?>

							<? } ?>

						</div>

					</div>

					<? if ((!$isSiteClosed && ($arSettings["CATALOG_MENU"]["VALUE"] == "INTERFACE-2-0-1" || $arSettings["CATALOG_MENU"]["VALUE"] == "INTERFACE-2-0-2" || $arSettings["CATALOG_MENU"]["VALUE"] == "INTERFACE-2-0-3")) || ((!$isSiteClosed || $isSiteClosed) && $arSettings["CATALOG_MENU"]["VALUE"] != "INTERFACE-2-0-1" && $arSettings["CATALOG_MENU"]["VALUE"] != "INTERFACE-2-0-2" && $arSettings["CATALOG_MENU"]["VALUE"] != "INTERFACE-2-0-3")) { ?>

						<div class="top-panel__col top-panel__tfoot">

							<div class="top-panel__cols">

								<? if (!$isSiteClosed) {

									//CATALOG_ICON//
							
									if ($arSettings["CATALOG_MENU"]["VALUE"] == "INTERFACE-2-0-1" || $arSettings["CATALOG_MENU"]["VALUE"] == "INTERFACE-2-0-2" || $arSettings["CATALOG_MENU"]["VALUE"] == "INTERFACE-2-0-3") { ?>

										<div class="hidden-md hidden-lg top-panel__col top-panel__catalog-icon"
											data-entity="catalog-icon">

											<i class="icon-box-list"></i>

											<span class="top-panel__catalog-icon-title"><?= GetMessage("ENEXT_CATALOG") ?></span>

										</div>

									<? }

									if ($arSettings["CATALOG_MENU"]["VALUE"] == "OPTION-3") {

										//MENU//
							
										?>

										<? $APPLICATION->IncludeComponent(

											"bitrix:main.include",

											"",

											array(

												"AREA_FILE_SHOW" => "file",

												"PATH" => SITE_DIR . "include/slide_menu.php"

											),

											false,

											array("HIDE_ICONS" => "Y")

										); ?>

									<? } elseif ($arSettings["TOP_PANEL_SEARCH_BUTTON"]["VALUE"] == "Y") { ?>

										<div class="hidden-xs hidden-sm top-panel__col"></div>

									<? } ?>

									<div
										class="top-panel__col top-panel__search-container<?= ($arSettings['TOP_PANEL_SEARCH_BUTTON']['VALUE'] == 'Y' ? '-button' : '') ?>">

										<a class="top-panel__search-btn<?= ($arSettings['TOP_PANEL_SEARCH_BUTTON']['VALUE'] != 'Y' ? ' hidden-md hidden-lg' : '') ?>"
											href="javascript:void(0)" data-entity="showSearch">

											<span class="top-panel__search-btn-block">

												<i class="icon-search"></i>

												<span
													class="top-panel__search-btn-title"><?= GetMessage("ENEXT_SEARCH") ?></span>

											</span>

										</a>

										<div
											class="top-panel__search <?= ($arSettings['TOP_PANEL_SEARCH_BUTTON']['VALUE'] != 'Y' ? 'hidden-xs hidden-sm' : 'hidden') ?>">

											<? //SEARCH//
											
													if ($arSettings["MAIN_SEARCH"]["VALUE"] == "BITRIX") { ?>

												<? $APPLICATION->IncludeComponent(

													"bitrix:main.include",

													"",

													array(

														"AREA_FILE_SHOW" => "file",

														"PATH" => SITE_DIR . "include/header_search.php"

													),

													false,

													array("HIDE_ICONS" => "Y")

												); ?>

											<? } else { ?>

												<? $APPLICATION->IncludeComponent(

													"altop:search.yandex.elektronext",

													".default",

													array(),

													false,

													array("HIDE_ICONS" => "Y")

												); ?>

											<? } ?>

										</div>

									</div>

								<? } else { ?>

									<div class="hidden-xs hidden-sm top-panel__col"></div>

								<? }

								if (!$isSiteClosed) { ?>

									<div
										class="<?= ($arSettings['TOP_PANEL_GEO_LOCATION']['VALUE'] != 'Y' ? 'hidden' : 'hidden-xs hidden-sm') ?> top-panel__col top-panel__geo-location">

										<? //GEO_LOCATION//
										
												?>

										<? $APPLICATION->IncludeComponent(

											"altop:geo.location.elektronext",

											"",

											array(

												"CACHE_TYPE" => "A",

												"CACHE_TIME" => "36000000"

											),

											false,

											array("HIDE_ICONS" => "Y")

										); ?>

									</div>

								<? }

								//CONTACTS//
							
								?>

								<? $APPLICATION->IncludeComponent(

									"bitrix:main.include",

									"",

									array(

										"AREA_FILE_SHOW" => "file",

										"PATH" => SITE_DIR . "include/header_contacts.php"

									),

									false,

									array("HIDE_ICONS" => "Y")

								); ?>

								<? if (!$isSiteClosed) {

									//CART//
							
									if ($arSettings["DISABLE_BASKET"]["VALUE"] != "Y" || $arSettings["DISABLE_DELAY"]["VALUE"] != "Y") { ?>

										<? $APPLICATION->IncludeComponent(

											"altop:sale.basket.basket.line",

											"",

											array(

												"PATH_TO_BASKET" => SITE_DIR . "personal/cart/"

											),

											false,

											array("HIDE_ICONS" => "Y")

										); ?>

									<? } ?>

									<div class="top-panel__col top-panel__user">

										<? //USER//
										
												?>

										<? $APPLICATION->IncludeComponent(

											"altop:user.elektronext",

											".default",

											array(

												"PATH_TO_PERSONAL" => SITE_DIR . "personal/",

												"CACHE_TYPE" => "A",

												"CACHE_TIME" => "36000000"

											),

											false

										); ?>

										<? //USER_MENU//
										
												?>

										<? $APPLICATION->IncludeComponent(

											"bitrix:main.include",

											"",

											array(

												"AREA_FILE_SHOW" => "file",

												"PATH" => SITE_DIR . "include/user_menu.php"

											),

											false,

											array("HIDE_ICONS" => "Y")

										); ?>

									</div>

								<? } ?>

							</div>

						</div>

					<? } ?>

				</div>

			</div>

		</div>
<!--<marquee direction="left" scrollamount="10" bgcolor="#f0f0f0" height="50" style="padding: 15px;">
    Уважаемые покупатели, уведомляем Вас, что 9 мая магазин работать не будет, но Вы можете оставить заявку/заказ на нашем сайте. На следующий день в приоритетном порядке обработаем все Ваши запросы

</marquee>-->
		<!-- РАССКОМЕНТИРУЙ ЕСЛИ НУЖНО ПОСТАВИТЬ БАННЕР НА ВНУТРЕННИЕ -->

		<? if ($APPLICATION->GetCurPage(false) !== '/'): ?>

			<a href="/skidki/" class="sale-image test" style="padding-top: 32px;">
				<img data-lcp-image="true" src="<?= SITE_TEMPLATE_PATH . '/images/group-2085664255_v_2.png'?>" width="1500" height="229" loading="eager" fetchpriority="high" decoding="async" alt="offer" title="offer">
			</a>

		<? endif; ?>

		<? if ($arSettings["CATALOG_MENU"]["VALUE"] == "INTERFACE-2-0-1" || $arSettings["CATALOG_MENU"]["VALUE"] == "INTERFACE-2-0-2" || $arSettings["CATALOG_MENU"]["VALUE"] == "INTERFACE-2-0-3" || $arSettings["CATALOG_MENU"]["VALUE"] == "OPTION-1" || $arSettings["CATALOG_MENU"]["VALUE"] == "OPTION-2" || $arSettings["CATALOG_MENU"]["VALUE"] == "OPTION-4" || $arSettings["CATALOG_MENU"]["VALUE"] == "OPTION-5") {
			//SLIDE_MENU//
		
			?>
			<? $APPLICATION->IncludeComponent(

				"bitrix:main.include",

				"",

				array(

					"AREA_FILE_SHOW" => "file",

					"PATH" => SITE_DIR . "include/slide_menu.php"

				),

				false,

				array("HIDE_ICONS" => "Y")

			); ?>

		<? } ?>

		<? if (!$isSiteClosed) {

			//CATALOG_COMPARE_LIST//
		
			?>

			<? $APPLICATION->IncludeComponent(

				"bitrix:main.include",

				"",

				array(

					"AREA_FILE_SHOW" => "file",

					"PATH" => SITE_DIR . "include/header_compare.php"

				),

				false,

				array("HIDE_ICONS" => "Y")

			); ?>

			<div class="page-container-wrapper">
				<? if (!CSite::inDir(SITE_DIR . "index.php")) {
					if (!CSite::InDir(SITE_DIR . "personal/order/make/") && $APPLICATION->GetDirProperty("PERSONAL_SECTION") && $USER->IsAuthorized()) {
						//PERSONAL_MENU//
						?>
						<? $APPLICATION->IncludeComponent(
							"bitrix:main.include",
							"",
							array(
								"AREA_FILE_SHOW" => "file",
								"PATH" => SITE_DIR . "include/personal_menu.php"
							),
							false,
							array("HIDE_ICONS" => "Y")

						); ?>
					<? }
					//SECTION_BANNER//
					$APPLICATION->ShowViewContent("UF_BANNER");
					if (!CSite::InDir(SITE_DIR . "personal/")) {
						//NAVIGATION//
						?>
						<div class="hidden-print navigation-wrapper">
							<div class="container<?= $APPLICATION->ShowProperty('wideScreenMode') ?>">
								<div class="row">
									<div class="col-xs-12">
										<div class="navigation-content">
											<div id="navigation" class="navigation">
												<? $APPLICATION->IncludeComponent(
													"bitrix:breadcrumb",
													"",
													array(
														"START_FROM" => "0",
														"PATH" => "",
														"SITE_ID" => "-"
													),
													false,
													array("HIDE_ICONS" => "Y")
												); ?>
											</div>

											<? //SHARE//
											
														if ($arSettings["BLOCK_SHARE"]["VALUE"] != "NONE") { ?>

												<div class="navigation-share">

													<div class="navigation-share-icon" data-entity="showShare"><i
															class="icon-share"></i></div>

													<div class="navigation-share-content" data-entity="shareContent">

														<div class="navigation-share-content-title"><?= GetMessage("ENEXT_SHARE") ?>
														</div>

														<div class="navigation-share-content-block">

															<? $APPLICATION->IncludeComponent(

																"bitrix:main.include",

																"",

																array(

																	"AREA_FILE_SHOW" => "file",

																	"PATH" => SITE_DIR . "include/footer_share.php"

																),

																false

															); ?>

														</div>

													</div>

												</div>

											<? } ?>

										</div>

									</div>

								</div>

							</div>

						</div>

					<? }

					//SECTION_PANEL//
			
					$APPLICATION->ShowViewContent("CATALOG_SECTION_PANEL"); ?>

					<div class="content-wrapper internal">

						<div class="container<?= $APPLICATION->ShowProperty('wideScreenMode') ?>">

							<div class="row">

								<div class="col-xs-12">

								<? }

		}

