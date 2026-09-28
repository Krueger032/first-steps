<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
if(\Bitrix\Main\Loader::includeModule("aspro.max"))
{
    \Aspro\Max\Functions\Extensions::init(['swiper']);

	global $arRegion;
	$arRegion = CMaxRegionality::getCurrentRegion();
}
?>

<script>BX.ready(() => typeof initSwiperSlider === 'function' && initSwiperSlider())</script>
