<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
IncludeTemplateLangFile(__FILE__);
CJSCore::Init(array("fx"));
$scheme = CMain::IsHTTPS() ? "https" : "http";
$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();?>
<!DOCTYPE html>
<html lang="<?=LANGUAGE_ID?>">
	<head>
		<?=$APPLICATION->ShowProperty("countersScriptsHead");?>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
		<link rel="preload" href="<?=SITE_TEMPLATE_PATH?>/fonts/MuseoSansCyrl-300.woff2" as="font" type="font/woff2" crossorigin />
		<link rel="preload" href="<?=SITE_TEMPLATE_PATH?>/fonts/MuseoSansCyrl-500.woff2" as="font" type="font/woff2" crossorigin />
		<link rel="preload" href="<?=SITE_TEMPLATE_PATH?>/fonts/MuseoSansCyrl-700.woff2" as="font" type="font/woff2" crossorigin />
		<title><?$APPLICATION->ShowTitle()?></title>
		<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/colors.min.css", true);		
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/animation.min.css");
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/csshake-default.min.css");
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/js/scrollbar/jquery.scrollbar.min.css");
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/bootstrap.min.css");
		CJSCore::Init(array("jquery2", "enextIntlTelInput"));
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/bootstrap.min.js");
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/formValidation.min.js");
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/inputmask.min.js");		
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery.hoverIntent.min.js");
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/moremenu.min.js");		
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/scrollbar/jquery.scrollbar.min.js");
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/main.min.js");
		$APPLICATION->ShowHead();?>
	</head>
	<?//PRO_WS side fields: color and picture come from solution settings//
$pwBgColor = "#fff";
$pwBgUrl = "";
$pwLimited = true;
if(CModule::IncludeModule("altop.enext")){
    $pwOpt = CEnext::GetBackParametrsValues(SITE_ID);
    $pwLimited = !isset($pwOpt["SITE_WIDTH"]) || $pwOpt["SITE_WIDTH"] === "LIMITED";
    if(!empty($pwOpt["PAGE_BACKGROUND_COLOR"]) && preg_match("/^#[0-9a-fA-F]{3,8}$/", $pwOpt["PAGE_BACKGROUND_COLOR"])){
        $pwBgColor = $pwOpt["PAGE_BACKGROUND_COLOR"];
    }
    if(!empty($pwOpt["PAGE_BACKGROUND_IMAGE"])){
        $pwFile = CFile::GetFileArray($pwOpt["PAGE_BACKGROUND_IMAGE"]);
        if(is_array($pwFile) && !empty($pwFile["SRC"])){
            $pwBgUrl = $pwFile["SRC"];
            $pwBgPath = $_SERVER["DOCUMENT_ROOT"].$pwBgUrl;
            if(is_file($pwBgPath)){
                $pwBgUrl .= "?v=".filemtime($pwBgPath);
            }
        }
    }
}
$pwBgStyle = "--pw-bg-color:".$pwBgColor.";";
if($pwBgUrl !== ""){
    $pwBgStyle .= "--pw-bg-image:url('".CUtil::JSEscape($pwBgUrl)."');";
}
?>
<body class="<?=$APPLICATION->ShowProperty('catalogMenu').$APPLICATION->ShowProperty('smartFilterView').$APPLICATION->ShowProperty('shortCardPanelMobile').$APPLICATION->ShowProperty('allowSelection').$APPLICATION->ShowProperty('legacyCookiesAgreement')?><?=($pwLimited ? " pw-side-fields" : "")?>" style="<?=htmlspecialcharsbx($pwBgStyle)?>"> <?//PRO_WS//?>
		<?=$APPLICATION->ShowProperty("countersScriptsBodyStart");
		echo $APPLICATION->ShowPanel();
		global $arSettings;
		$arSettings = $APPLICATION->IncludeComponent("altop:settings.enext", "", array(), false, array("HIDE_ICONS" => "Y"));
		$isSiteClosed = COption::GetOptionString("main", "site_stopped") == "Y" && !$USER->CanDoOperation("edit_other_settings") ? true : false;?>
		
		<?//PRO_WS//?>
		<?if(empty($arSettings["SITE_BLOCKS"]["VALUE"])){
			$arSettings["SITE_BLOCKS"]["VALUE"] = array();
		}?>
		<?//PRO_WS//?>
		
		<div class="page-wrapper">
			<?if(!$isSiteClosed) {?>
			
				<?//PRO_WS//?>
				<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
					array(
						"AREA_FILE_SHOW" => "file",
						"PATH" => SITE_DIR."include/header_streamer.php"
					),
					false,
					array("HIDE_ICONS" => "Y")
				);?>
				<?//PRO_WS//?>
			
				<div class="hidden-xs hidden-sm<?=(!in_array('TOP_MENU', $arSettings['SITE_BLOCKS']['VALUE']) ? ' hidden-md hidden-lg' : '')?> hidden-print top-menu-wrapper">
					<div class="top-menu">
						<?//TOP_MENU//?>
						<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
							array(
								"AREA_FILE_SHOW" => "file",
								"PATH" => SITE_DIR."include/header_top_menu.php"
							),
							false,
							array("HIDE_ICONS" => "Y")
						);?>
					</div>
				</div>
			<?}?>
			<div class="hidden-print top-panel-wrapper">				
				<div class="top-panel<?=(!$APPLICATION->GetDirProperty('PERSONAL_SECTION') && ($arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-4' || $arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-5') ? ' catalog-menu-outside' : '')?>">
					<div class="top-panel__cols">
						<div class="top-panel__col top-panel__thead">								
							<div class="top-panel__cols">								
								<?//MENU_ICON//
								if(!$isSiteClosed) {?>
									<div class="top-panel__col top-panel__menu-icon-container<?=($arSettings['CATALOG_MENU']['VALUE'] == 'INTERFACE-2-0-2' || $arSettings['CATALOG_MENU']['VALUE'] == 'INTERFACE-2-0-3' || $arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-3' || $arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-4' || $arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-5' ? ' hidden-md hidden-lg' : '')?>" data-entity="menu-icon">
										<i class="icon-menu"></i>
										<?if($arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-6') {
											//MENU//?>
											<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
												array(
													"AREA_FILE_SHOW" => "file",
													"PATH" => SITE_DIR."include/slide_menu.php"
												),
												false,
												array("HIDE_ICONS" => "Y")
											);?>
										<?}?>
									</div>
								<?}
								//LOGO//?>								
								<div class="top-panel__col top-panel__logo">
									<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
										array(
											"AREA_FILE_SHOW" => "file",
											"PATH" => SITE_DIR."include/header_logo.php"
										),
										false
									);?>
								</div>
								<?//CONTACTS//
								if($arSettings["CATALOG_MENU"]["VALUE"] == "INTERFACE-2-0-1" || $arSettings["CATALOG_MENU"]["VALUE"] == "INTERFACE-2-0-2" || $arSettings["CATALOG_MENU"]["VALUE"] == "INTERFACE-2-0-3") {
									if($arSettings["TOP_PANEL_CONTACTS"]["VALUE"] == "Y") {?>
										<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
											array(
												"AREA_FILE_SHOW" => "file",
												"PATH" => SITE_DIR."include/header_contacts.php"
											),
											false,
											array("HIDE_ICONS" => "Y")
										);?>
									<?} else {?>
										<div class="hidden-md hidden-lg top-panel__col top-panel__contacts"></div>
									<?}
								}?>
							</div>
						</div>
						<?if((!$isSiteClosed && ($arSettings["CATALOG_MENU"]["VALUE"] == "INTERFACE-2-0-1" || $arSettings["CATALOG_MENU"]["VALUE"] == "INTERFACE-2-0-2" || $arSettings["CATALOG_MENU"]["VALUE"] == "INTERFACE-2-0-3")) || ((!$isSiteClosed || $isSiteClosed) && $arSettings["CATALOG_MENU"]["VALUE"] != "INTERFACE-2-0-1" && $arSettings["CATALOG_MENU"]["VALUE"] != "INTERFACE-2-0-2" && $arSettings["CATALOG_MENU"]["VALUE"] != "INTERFACE-2-0-3")) {?>
							<div class="top-panel__col top-panel__tfoot">
								<div class="top-panel__cols">								
									<?if(!$isSiteClosed) {
										//CATALOG_ICON//
										if($arSettings["CATALOG_MENU"]["VALUE"] == "INTERFACE-2-0-1" || $arSettings["CATALOG_MENU"]["VALUE"] == "INTERFACE-2-0-2" || $arSettings["CATALOG_MENU"]["VALUE"] == "INTERFACE-2-0-3") {?>
											<div class="hidden-md hidden-lg top-panel__col top-panel__catalog-icon" data-entity="catalog-icon">
												<i class="icon-box-list"></i>
												<span class="top-panel__catalog-icon-title"><?=GetMessage("ENEXT_CATALOG")?></span>
											</div>
										<?}
										if($arSettings["CATALOG_MENU"]["VALUE"] == "OPTION-3") {
											//MENU//?>
											<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
												array(
													"AREA_FILE_SHOW" => "file",
													"PATH" => SITE_DIR."include/slide_menu.php"
												),
												false,
												array("HIDE_ICONS" => "Y")
											);?>
										<?} elseif($arSettings["TOP_PANEL_SEARCH_BUTTON"]["VALUE"] == "Y") {?>
											<div class="hidden-xs hidden-sm top-panel__col"></div>
										<?}?>										
										<div class="top-panel__col top-panel__search-container<?=($arSettings['TOP_PANEL_SEARCH_BUTTON']['VALUE'] == 'Y' ? '-button' : '')?>">
											<a class="top-panel__search-btn<?=($arSettings['TOP_PANEL_SEARCH_BUTTON']['VALUE'] != 'Y' ? ' hidden-md hidden-lg' : '')?>" href="javascript:void(0)" data-entity="showSearch">
												<span class="top-panel__search-btn-block">
													<i class="icon-search"></i>
													<span class="top-panel__search-btn-title"><?=GetMessage("ENEXT_SEARCH")?></span>
												</span>
											</a>
											<div class="top-panel__search <?=($arSettings['TOP_PANEL_SEARCH_BUTTON']['VALUE'] != 'Y' ? 'hidden-xs hidden-sm' : 'hidden')?>">
												<?//SEARCH//
												if($arSettings["MAIN_SEARCH"]["VALUE"] == "BITRIX") {?>
													<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
														array(
															"AREA_FILE_SHOW" => "file",
															"PATH" => SITE_DIR."include/header_search.php"
														),
														false,
														array("HIDE_ICONS" => "Y")
													);?>
												<?} else {?>
													<?$APPLICATION->IncludeComponent("altop:search.yandex.enext", ".default",
														array(),
														false,
														array("HIDE_ICONS" => "Y")
													);?>
												<?}?>
											</div>
										</div>									
									<?} else {?>
										<div class="hidden-xs hidden-sm top-panel__col"></div>
									<?}
									if(!$isSiteClosed) {?>
										<div class="<?=($arSettings['TOP_PANEL_GEO_LOCATION']['VALUE'] != 'Y' ? 'hidden' : 'hidden-xs hidden-sm')?> top-panel__col top-panel__geo-location">
											<?//GEO_LOCATION//?>
											<?$APPLICATION->IncludeComponent("altop:geo.location.enext", "",
												array(
													"CACHE_TYPE" => "A",
													"CACHE_TIME" => "36000000"
												),
												false,
												array("HIDE_ICONS" => "Y")
											);?>
										</div>
									<?} 
									//CONTACTS//
									if($arSettings["TOP_PANEL_CONTACTS"]["VALUE"] == "Y") {?>
										<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
											array(
												"AREA_FILE_SHOW" => "file",
												"PATH" => SITE_DIR."include/header_contacts.php"
											),
											false,
											array("HIDE_ICONS" => "Y")
										);?>
									<?}
									if(!$isSiteClosed) {
										//CART//
										if($arSettings["DISABLE_BASKET"]["VALUE"] != "Y" || $arSettings["DISABLE_DELAY"]["VALUE"] != "Y") {?>
											<?$APPLICATION->IncludeComponent("altop:sale.basket.basket.line", "",
												array(
													"PATH_TO_BASKET" => SITE_DIR."personal/cart/"
												),
												false,
												array("HIDE_ICONS" => "Y")
											);?>
										<?}?>
										<div class="top-panel__col top-panel__user">
											<?//USER//?>
											<?$APPLICATION->IncludeComponent("altop:user.enext", ".default",
												array(
													"PATH_TO_PERSONAL" => SITE_DIR."personal/",
													"CACHE_TYPE" => "A",
													"CACHE_TIME" => "36000000"
												),
												false
											);?>
											<?//USER_MENU//?>
											<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
												array(
													"AREA_FILE_SHOW" => "file",
													"PATH" => SITE_DIR."include/user_menu.php"
												),
												false,
												array("HIDE_ICONS" => "Y")
											);?>
										</div>
									<?}?>
								</div>
							</div>
						<?}?>
					</div>
				</div>
			</div>
			<?if($arSettings["CATALOG_MENU"]["VALUE"] == "INTERFACE-2-0-1" || $arSettings["CATALOG_MENU"]["VALUE"] == "INTERFACE-2-0-2" || $arSettings["CATALOG_MENU"]["VALUE"] == "INTERFACE-2-0-3" || $arSettings["CATALOG_MENU"]["VALUE"] == "OPTION-1" || $arSettings["CATALOG_MENU"]["VALUE"] == "OPTION-2" || $arSettings["CATALOG_MENU"]["VALUE"] == "OPTION-4" || $arSettings["CATALOG_MENU"]["VALUE"] == "OPTION-5") {
				//SLIDE_MENU//?>
				<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
					array(
						"AREA_FILE_SHOW" => "file",
						"PATH" => SITE_DIR."include/slide_menu.php"
					),
					false,
					array("HIDE_ICONS" => "Y")
				);?>
			<?}
			if(!$isSiteClosed) {
				//CATALOG_COMPARE_LIST//?>
				<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
					array(
						"AREA_FILE_SHOW" => "file",
						"PATH" => SITE_DIR."include/header_compare.php"
					),
					false,
					array("HIDE_ICONS" => "Y")
				);?>
				<div class="page-container-wrapper">			
					<?if(!CSite::inDir(SITE_DIR."index.php")) {
						if(!CSite::InDir(SITE_DIR."personal/order/make/") && $APPLICATION->GetDirProperty("PERSONAL_SECTION") && $USER->IsAuthorized()) {
							//PERSONAL_MENU//?>
							<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
								array(
									"AREA_FILE_SHOW" => "file",
									"PATH" => SITE_DIR."include/personal_menu.php"
								),
								false,
								array("HIDE_ICONS" => "Y")
							);?>
						<?}
						//SECTION_BANNER//
						$APPLICATION->ShowViewContent("UF_BANNER");
						if(!CSite::InDir(SITE_DIR."personal/")) {
							//NAVIGATION//?>
							<div class="hidden-print navigation-wrapper">
								<div class="container<?=$APPLICATION->ShowProperty('wideScreenMode')?>">
									<div class="row">
										<div class="col-xs-12">
											<div class="navigation-content">
												<div id="navigation" class="navigation">
													<?$APPLICATION->IncludeComponent("bitrix:breadcrumb", "", 
														array(
															"START_FROM" => "0",
															"PATH" => "",
															"SITE_ID" => "-"
														),
														false,
														array("HIDE_ICONS" => "Y")
													);?>
												</div>
												<?//SHARE//
												if($arSettings["BLOCK_SHARE"]["VALUE"] != "NONE") {?>
													<div class="navigation-share">
														<div class="navigation-share-icon" data-entity="showShare"><i class="icon-share"></i></div>
														<div class="navigation-share-content" data-entity="shareContent">
															<div class="navigation-share-content-title"><?=GetMessage("ENEXT_SHARE")?></div>
															<div class="navigation-share-content-block">
																<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
																	array(
																		"AREA_FILE_SHOW" => "file",
																		"PATH" => SITE_DIR."include/footer_share.php"
																	),
																	false
																);?>
															</div>
														</div>
													</div>
												<?}?>
											</div>
										</div>
									</div>
								</div>
							</div>
						<?}
						//SECTION_PANEL//
						$APPLICATION->ShowViewContent("CATALOG_SECTION_PANEL");?>
						<div class="content-wrapper internal">
							<div class="container<?=$APPLICATION->ShowProperty('wideScreenMode')?>">
								<div class="row">
									<div class="col-xs-12">
					<?}
			}