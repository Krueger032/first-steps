<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;

$this->addExternalCss(SITE_TEMPLATE_PATH."/js/owlCarousel/owl.carousel.min.css");
$this->addExternalJS(SITE_TEMPLATE_PATH."/js/owlCarousel/owl.carousel.min.js");

$this->addExternalJS(SITE_TEMPLATE_PATH."/js/countdown/jquery.plugin.min.js");
$this->addExternalJS(SITE_TEMPLATE_PATH."/js/countdown/jquery.countdown.min.js");