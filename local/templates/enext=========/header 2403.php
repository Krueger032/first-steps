<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
IncludeTemplateLangFile(__FILE__);
CJSCore::Init(array("fx"));
$scheme = CMain::IsHTTPS() ? "https" : "http"; ?>


<?
$result = explode('?', $_SERVER["REQUEST_URI"]);
$result_code = explode('/', $result[0]);

$num_i = count($result_code);
$num_i = $num_i - 2;

$res = CIBlockSection::GetList(array(), array('IBLOCK_ID' => 25, 'CODE' => $result_code[$num_i], 'SITE_ID' => "s1"));
$section = $res->Fetch();
  
$res = CIBlockSection::GetByID( $section["ID"]);
if($ar_res = $res->GetNext())
  
if($ar_res['SECTION_PAGE_URL']){
	if($ar_res['SECTION_PAGE_URL']<>$result[0]){
		if($_SERVER["QUERY_STRING"]){
			$url_code = $ar_res['SECTION_PAGE_URL'] .'?'.$_SERVER["QUERY_STRING"];
		} else {
			$url_code = $ar_res['SECTION_PAGE_URL'];
		}
	
	if($url_code){	
	if($url_code!="/catalog/santekhnika/"){		
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: https://test.santehpodbor.ru".$url_code,TRUE,301);
		?>
		<pre style="display:none" class="test-1"> <? var_dump($url_code) ?> </pre>
		<?
	}
	}
	}
} 

function getIdByCode($code,$iblock_id,$type){
	if(CModule::IncludeModule("iblock")){
		if($type=='IBLOCK_ELEMENT'){
			$arFilter=array("IBLOCK_ID"=>$iblock_id,"CODE"=>$code);
			$res=CIBlockElement::GetList(array(),$arFilter,false,array("nPageSize"=>1),array('ID'));
			$element=$res->Fetch();
			if($res->SelectedRowsCount()!=1) return '';
			else return $element['ID'];
		}
		else if($type=='IBLOCK_SECTION'){
			$res=CIBlockSection::GetList(array(),array('IBLOCK_ID'=>$iblock_id,'CODE'=>$code));
			$section=$res->Fetch();
			if($res->SelectedRowsCount()!=1) return '';
			else return $section['ID'];
		}
		else{
			return '<p style="font-weight:bold;color:#ff0000">Укажите тип</p>';
		}
	}
}
$tovar_id=getIdByCode($result_code[$num_i],25,'IBLOCK_ELEMENT');


$el_res= CIBlockElement::GetByID( $tovar_id );
if ( $el_arr= $el_res->GetNext() ) {
	
}

  
if($el_arr[ 'DETAIL_PAGE_URL' ]){
	if($el_arr[ 'DETAIL_PAGE_URL' ]<>$result[0]){
	if($_SERVER["QUERY_STRING"]){
		$url_code = $el_arr[ 'DETAIL_PAGE_URL' ] .'?'.$_SERVER["QUERY_STRING"];
	} else {
		$url_code = $el_arr[ 'DETAIL_PAGE_URL' ];
	}
	
	if($url_code){	
		if($url_code!="/catalog/santekhnika/"){	
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: https://test.santehpodbor.ru".$url_code,TRUE,301);
		?>
		<pre style="display:none" class="test-2"> <? var_dump($url_code) ?> </pre>
		<?
	}
	}
	}
} 
?>
<!DOCTYPE html>
<html lang="<?= LANGUAGE_ID ?>">

<head>
<script type="text/javascript" async src="https://app.comagic.ru/static/cs.min.js?k=Uv3SdOF385Al7mOAHkXiZ74HZIMfo51H"></script>
<!-- Yandex.Metrika counter -->
<script type="text/javascript"  data-skip-moving="true" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();
   for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
   k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(78427446, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/78427446" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

<style>


	#bx_651765591_77 > div.contacts-item-row.contacts-item-whatsapp > div > i {color: #39AE41}

</style>




<?php $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$url = explode('?', $url);
	$url = $url[0];	?>
	<link rel="canonical" href="<?php echo $url; ?>" />
	<?php
	set_include_path($_SERVER["DOCUMENT_ROOT"]);
	$newPatn = get_include_path();
	if (file_exists($newPatn . '/aweb_seo/_meta.php')) include_once($newPatn . '/aweb_seo/_meta.php');
	?>

	<?= $APPLICATION->ShowProperty("countersScriptsHead"); ?>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
	<link rel="shortcut icon" href="<?= SITE_TEMPLATE_PATH ?>/images/fav111-120x120.png" type="image/x-icon" />
	<link rel="preload" href="<?= SITE_TEMPLATE_PATH ?>/fonts/MuseoSansCyrl-300.woff" as="font" type="font/woff" crossorigin />
	<link rel="preload" href="<?= SITE_TEMPLATE_PATH ?>/fonts/MuseoSansCyrl-500.woff" as="font" type="font/woff" crossorigin />
	<link rel="preload" href="<?= SITE_TEMPLATE_PATH ?>/fonts/MuseoSansCyrl-700.woff" as="font" type="font/woff" crossorigin />
	<? if (isset($metaTitle) && !empty($metaTitle)) {?>
	<title>
		<?
				echo $metaTitle; 
			?>
	</title>
	<?} else {
		$APPLICATION->ShowHead();
		//$APPLICATION->ShowTitle(); 

		?>
		<title><?$APPLICATION->ShowTitle()?></title>
		<?
	} ?>
			
	<? 
	if(strpos($_SERVER['HTTP_USER_AGENT'],'Chrome-Lighthouse')) {
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/css/bootstrap.min.css");
	} else {
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/colors.min.css", true);
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/css/animation.min.css");
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/css/csshake-default.min.css");
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/js/scrollbar/jquery.scrollbar.min.css");
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/css/bootstrap.min.css");
	}
	CJSCore::Init(array("jquery2", "enextIntlTelInput"));
	if(strpos($_SERVER['HTTP_USER_AGENT'],'Chrome-Lighthouse')) {
		
	} else {
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/bootstrap.min.js");
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/formValidation.min.js");
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/inputmask.min.js");
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/jquery.hoverIntent.min.js");
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/moremenu.min.js");
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/scrollbar/jquery.scrollbar.min.js");
	// $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/main.min.js"); при данном подключении дублируется скрипт, из-за чего не работает кнопка "поделиться" и поиск
	?>
	<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/main.min.js"></script> 
	<?
	}
	?>
	
	<? if (empty($D) || !isset($D)) {
		$APPLICATION->ShowMeta("description");
	} else {
		echo $metaDesc;
	}
	if (!isset($K)) {
		$APPLICATION->ShowMeta("keywords");
	} else {
		echo $metaKey;
	} ?>
	<? $APPLICATION->ShowCSS(); ?>
	<? $APPLICATION->ShowHeadStrings(); ?>
	<? $APPLICATION->ShowHeadScripts(); ?>
	<script type="text/javascript" src="/exform/exform.js"></script>

	<? $smartSpeed = $arSettings["SMART_SPEED"]["VALUE"] ? $arSettings["SMART_SPEED"]["VALUE"] : 1000; ?>
	<style type="text/css">
		.owl-carousel .animated{
			-webkit-animation-duration: <?=$smartSpeed?>ms;
			animation-duration: <?=$smartSpeed?>ms;
		}
	</style>
	
</head>

<body class="<?= $APPLICATION->ShowProperty('catalogMenu') . $APPLICATION->ShowProperty('smartFilterView') ?>">
	<?= $APPLICATION->ShowProperty("countersScriptsBodyStart");
	echo $APPLICATION->ShowPanel();
	global $arSettings;
	$arSettings = $APPLICATION->IncludeComponent("altop:settings.enext", "", array(), false, array("HIDE_ICONS" => "Y"));
	$isSiteClosed = COption::GetOptionString("main", "site_stopped") == "Y" && !$USER->CanDoOperation("edit_other_settings") ? true : false; ?>
	<div class="page-wrapper">
		<? if (!$isSiteClosed) { ?>
			<div class="hidden-xs hidden-sm<?= (!in_array('TOP_MENU', $arSettings['SITE_BLOCKS']['VALUE']) ? ' hidden-md hidden-lg' : '') ?> hidden-print top-menu-wrapper">
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
			<div class="top-panel<?= (!$APPLICATION->GetDirProperty('PERSONAL_SECTION') && ($arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-4' || $arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-5') ? ' catalog-menu-outside' : '') ?>">
				<div class="top-panel__cols">
					<div class="top-panel__col top-panel__thead">
						<div class="top-panel__cols">
							<? //MENU_ICON//
							if (!$isSiteClosed) { ?>
								<div class="top-panel__col top-panel__menu-icon-container<?= ($arSettings['CATALOG_MENU']['VALUE'] == 'INTERFACE-2-0-2' || $arSettings['CATALOG_MENU']['VALUE'] == 'INTERFACE-2-0-3' || $arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-3' || $arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-4' || $arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-5' ? ' hidden-md hidden-lg' : '') ?>" data-entity="menu-icon">
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
										<div class="hidden-md hidden-lg top-panel__col top-panel__catalog-icon" data-entity="catalog-icon">
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
									<div class="top-panel__col top-panel__search-container<?= ($arSettings['TOP_PANEL_SEARCH_BUTTON']['VALUE'] == 'Y' ? '-button' : '') ?>">
										<a class="top-panel__search-btn<?= ($arSettings['TOP_PANEL_SEARCH_BUTTON']['VALUE'] != 'Y' ? ' hidden-md hidden-lg' : '') ?>" href="javascript:void(0)" data-entity="showSearch">
											<span class="top-panel__search-btn-block">
												<i class="icon-search"></i>
												<span class="top-panel__search-btn-title"><?= GetMessage("ENEXT_SEARCH") ?></span>
											</span>
										</a>
										<div class="top-panel__search <?= ($arSettings['TOP_PANEL_SEARCH_BUTTON']['VALUE'] != 'Y' ? 'hidden-xs hidden-sm' : 'hidden') ?>">
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
													"altop:search.yandex.enext",
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
									<div class="<?= ($arSettings['TOP_PANEL_GEO_LOCATION']['VALUE'] != 'Y' ? 'hidden' : 'hidden-xs hidden-sm') ?> top-panel__col top-panel__geo-location">
										<? //GEO_LOCATION//
										?>
										<? $APPLICATION->IncludeComponent(
											"altop:geo.location.enext",
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
											"altop:user.enext",
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
		<? if($_SERVER['REQUEST_URI'] != '/'){
			echo '<a href="/promotions/sales/" class="sale-image"><img src="/bitrix/templates/enext/images/santehpodbor-sale-top-header.png" alt=""></a>';
		}?>
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
		<? }
		if (!$isSiteClosed) {
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
													<div class="navigation-share-icon" data-entity="showShare"><i class="icon-share"></i></div>
													<div class="navigation-share-content" data-entity="shareContent">
														<div class="navigation-share-content-title"><?= GetMessage("ENEXT_SHARE") ?></div>
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
