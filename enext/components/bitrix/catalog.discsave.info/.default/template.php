<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(empty($arResult)) {?>
	<span class="alert alert-warning"><?=GetMessage("BX_CMP_CDI_TPL_MESS_NO_DISCOUNT_SAVE")?></span>
<?} else {
	foreach($arResult as $arDiscountSave) {?>
		<div class="catalog-discsave">
			<div class="catalog-discsave-caption">
				<div class="catalog-discsave-block">
					<div class="catalog-discsave-title"><?=GetMessage("BX_CMP_CDI_TPL_MESS_SIZE")?></div>
					<div class="catalog-discsave-name"><?=$arDiscountSave["NAME"]?></div>
				</div>
				<div class="catalog-discsave-value">
					<?if($arDiscountSave["VALUE_TYPE"] == "P") {
						echo $arDiscountSave["VALUE"]."&nbsp;%";
					} else {
						echo CCurrencyLang::CurrencyFormat($arDiscountSave["VALUE"], $arDiscountSave["CURRENCY"], true);
					}?>
				</div>
			</div>
			<?if(isset($arDiscountSave["NEXT_LEVEL"]) && is_array($arDiscountSave["NEXT_LEVEL"])) {?>
				<div class="catalog-discsave-next-level">
					<?$nextLevelSize = "<span>".($arDiscountSave["NEXT_LEVEL"]["VALUE_TYPE"] == "P" ? $arDiscountSave["NEXT_LEVEL"]["VALUE"]."&nbsp;%" : CCurrencyLang::CurrencyFormat($arDiscountSave["NEXT_LEVEL"]["VALUE"], $arDiscountSave["CURRENCY"], true))."</span>";
					$nextLevelSumm = "<span>".CCurrencyLang::CurrencyFormat(($arDiscountSave["NEXT_LEVEL"]["RANGE_FROM"] - $arDiscountSave["RANGE_SUMM"]), $arDiscountSave["CURRENCY"], true)."</span>";
					
					echo str_replace(array("#SIZE#", "#SUMM#"), array($nextLevelSize, $nextLevelSumm), GetMessage("BX_CMP_CDI_TPL_MESS_NEXT_LEVEL"));?>
				</div>
			<?}?>
		</div>
	<?}
	unset($arDiscountSave);
}