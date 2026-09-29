<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$this->addExternalCss(SITE_TEMPLATE_PATH."/js/owlCarousel/owl.carousel.min.css");
$this->addExternalJS(SITE_TEMPLATE_PATH."/js/owlCarousel/owl.carousel.min.js");?>

<div class="sale-personal-section-private">
	<div class="spsp-tabs-container">
		<div class="spsp-tabs-scroll">
			<ul class="spsp-tabs">
				<li class="spsp-tab active">
					<a href="<?=$arResult['PATH_TO_PRIVATE']?>" class="spsp-tab-link">
						<span><?=Loc::getMessage("SPSP_MAIN_PROFILE")?></span>
					</a>
				</li>
				<li class="spsp-tab">
					<a href="<?=$arResult['PATH_TO_PROFILE']?>" class="spsp-tab-link">
						<span><?=Loc::getMessage("SPSP_PROFILE_LIST")?></span>
					</a>
				</li>
				<div class="clearfix"></div>
			</ul>
		</div>
	</div>
	<div class="spsp-main-profile">
		<?$APPLICATION->IncludeComponent("bitrix:main.profile", "",
			array(
				"SET_TITLE" => "N",
				"AJAX_MODE" => $arParams["AJAX_MODE_PRIVATE"],
				"SEND_INFO" => $arParams["SEND_INFO_PRIVATE"],
				"CHECK_RIGHTS" => $arParams["CHECK_RIGHTS_PRIVATE"],
				"EDITABLE_EXTERNAL_AUTH_ID" => $arParams["EDITABLE_EXTERNAL_AUTH_ID"]
			),
			$component
		);?>
	</div>	
</div>

<?//BREADCRUMBS//
$APPLICATION->AddChainItem(Loc::getMessage("SPS_CHAIN_PRIVATE"));

//TITLE//
if($arParams["SET_TITLE"] == "Y")
	$APPLICATION->SetTitle(Loc::getMessage("SPS_TITLE_PRIVATE"));