<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arScripts = ['swiper', 'fancybox', 'cross'];
\Aspro\Max\Functions\Extensions::init($arScripts);

if ($arParams['USE_FILTER'] != 'N') {
    \Aspro\Max\Functions\Extensions::init('section_filter');
}?>