<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    exit;
}?>
<?php
$bHideSmartSeo = isset($arParams['HIDE_SMART_SEO']) && $arParams['HIDE_SMART_SEO'] === 'Y';

if (!$bHideSmartSeo && CMax::isSmartSeoInstalled() && $arResult && class_exists(Aspro\Smartseo\General\Smartseo::class)) {
    $url = Aspro\Smartseo\General\Smartseo::getUrlByReal(htmlspecialcharsback($arResult['FILTER_AJAX_URL']), SITE_ID);

    if ($url) {
        $arResult['FILTER_AJAX_URL'] = $url;
        $arResult['SEF_SET_FILTER_URL'] = $url;
        $arResult['FILTER_URL'] = $url;
        $arResult['JS_FILTER_PARAMS']['SEF_SET_FILTER_URL'] = $url;
    }
}

$APPLICATION->RestartBuffer();
unset($arResult['COMBO']);
echo CUtil::PHPToJSObject($arResult, true);

// some fixes from component.php, becouse there are some included components before filter, which will add their scripts in edit mode
$json = ob_get_contents();
Aspro\Max\SearchQuery::replaceUrls($json);
$APPLICATION->RestartBuffer();
while (ob_end_clean()) {
}
header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);
echo $json;
define('PUBLIC_AJAX_MODE', true);
// require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
exit;
?>
