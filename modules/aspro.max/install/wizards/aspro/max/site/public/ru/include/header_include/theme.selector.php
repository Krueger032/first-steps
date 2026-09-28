<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?
global $APPLICATION;

$params = [];
if ($options['PARAMS'] ?? false) {
    $params = array_merge($params, (array)$options['PARAMS']);
}

?>
<?$APPLICATION->IncludeComponent('aspro:theme.selector.max', '', $params, false, ['HIDE_ICONS' => 'Y']);?>
