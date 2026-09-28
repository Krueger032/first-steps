<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$arExtensions = [];
if(isset($templateData['USE_FILTER_SEARCH']) && $templateData['USE_FILTER_SEARCH'] === true){
    $arExtensions[] = 'filter_search';
}
if(!empty($arExtensions)){
    TSolution\Extensions::init('filter_search');
}
?>
