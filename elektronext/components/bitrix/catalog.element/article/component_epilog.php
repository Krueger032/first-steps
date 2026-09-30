<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

//CURRENCIES//
if(!empty($templateData["TEMPLATE_LIBRARY"])) {
	$loadCurrency = false;
	if(!empty($templateData["CURRENCIES"])) {
		$loadCurrency = Bitrix\Main\Loader::includeModule("currency");
	}
	CJSCore::Init($templateData["TEMPLATE_LIBRARY"]);
	if($loadCurrency) {?>
		<script type="text/javascript">
			BX.Currency.setCurrencies(<?=$templateData["CURRENCIES"]?>);
		</script>
	<?}
}