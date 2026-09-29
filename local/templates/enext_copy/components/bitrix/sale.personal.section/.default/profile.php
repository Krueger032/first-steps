<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$this->addExternalCss(SITE_TEMPLATE_PATH."/js/owlCarousel/owl.carousel.min.css");
$this->addExternalJS(SITE_TEMPLATE_PATH."/js/owlCarousel/owl.carousel.min.js");?>

<div class="sale-personal-section-private">
	<div class="spsp-tabs-container">
		<div class="spsp-tabs-scroll">
			<ul class="spsp-tabs">
				<li class="spsp-tab">
					<a href="<?=$arResult['PATH_TO_PRIVATE']?>" class="spsp-tab-link">
						<span><?=Loc::getMessage("SPSP_MAIN_PROFILE")?></span>
					</a>
				</li>
				<li class="spsp-tab active">
					<a href="<?=$arResult['PATH_TO_PROFILE']?>" class="spsp-tab-link">
						<span><?=Loc::getMessage("SPSP_PROFILE_LIST")?></span>
					</a>
				</li>
				<div class="clearfix"></div>
			</ul>
		</div>
	</div>
	<div class="spsp-profile-list">
		<?$APPLICATION->IncludeComponent("bitrix:sale.personal.profile.list", "",
			array(
				"PATH_TO_DETAIL" => $arResult["PATH_TO_PROFILE_DETAIL"],
				"PATH_TO_DELETE" => $arResult["PATH_TO_PROFILE_DELETE"],
				"PER_PAGE" => $arParams["PROFILES_PER_PAGE"],
				"SET_TITLE" => "N",
			),
			$component
		);?>
	</div>
</div>

<?//BREADCRUMBS//
$APPLICATION->AddChainItem(Loc::getMessage("SPS_CHAIN_PROFILE"));

//TITLE//
if($arParams["SET_TITLE"] == "Y")
	$APPLICATION->SetTitle(Loc::getMessage("SPS_TITLE_PROFILE"));