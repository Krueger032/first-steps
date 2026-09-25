<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$component = $this->__component;
$component::scaleImages($arResult["JS_DATA"], $arParams["SERVICES_IMAGES_SCALING"]);

if(Bitrix\Main\Loader::includeModule("currency")) {
	CJSCore::Init(array("currency")); 
	$currencyFormat = CCurrencyLang::GetFormatDescription($arResult["BASE_LANG_CURRENCY"]);?>

	<script type="text/javascript">
		BX.Currency.setCurrencyFormat('<?=$arResult["BASE_LANG_CURRENCY"]?>', <?=CUtil::PhpToJSObject($currencyFormat, false, true, true)?>);
	</script>
<?}