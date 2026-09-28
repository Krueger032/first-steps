<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    exit;
}

/* @var array $templateData */
/* @var @global CMain $APPLICATION */
use Bitrix\Main\Loader;

if (!$templateData['ITEMS']) {
    return;
}

global $APPLICATION;

$loadCurrency = Loader::includeModule('currency');
CJSCore::Init(['popup', 'currency']);
if (isset($templateData['TEMPLATE_THEME'])) {
    $APPLICATION->SetAdditionalCSS($templateData['TEMPLATE_THEME']);
}
?>
<script type="text/javascript">
	BX.Currency.setCurrencies(<?=$templateData['CURRENCIES'];?>);
</script>
