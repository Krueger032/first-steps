<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$isDark = $arParams['DARK'] === 'Y';
?>
<form action="<?=$arParams["PAGE"]?>" method="post" class="subscribe-form">
	<?echo bitrix_sessid_post();?>
    <div class="footer-subscribe-wrap">
        <input type="text" name="EMAIL" class="form-control subscribe-input required" placeholder="<?=GetMessage("EMAIL_INPUT");?>" value="<?=$arResult["USER_EMAIL"] ? $arResult["USER_EMAIL"] : ($arResult["SUBSCRIPTION"]["EMAIL"]!=""?$arResult["SUBSCRIPTION"]["EMAIL"]:$arResult["REQUEST"]["EMAIL"]);?>" size="30" maxlength="255" />

        <?foreach($arResult["RUBRICS"] as $itemID => $itemValue):?>
            <input type="hidden" name="RUB_ID[]" value="<?=$itemValue["ID"]?>" />
        <?endforeach;?>

        <input type="hidden" name="FORMAT" value="html" />
        <button type="submit" class="btn btn-default btn-lg subscribe-btn round-ignore">
            <?=GetMessage("ADD_USER");?>
        </button>
    </div>

	<input type="hidden" name="PostAction" value="Add" />
	<input type="hidden" name="Save" value="Y" />
	<input type="hidden" name="ID" value="<?echo $arResult["SUBSCRIPTION"]["ID"];?>" />

    <? if (TSolution::GetFrontParametrValue('SHOW_LICENCE') == 'Y' && !$arResult['ID']) : ?>
        <div class="footer-form-licence <?=$isDark ? 'theme-dark' : ''?>">
            <?
            TSolution\Functions::showBlockHtml([
                'FILE' => 'consent/userconsent.php',
                'PARAMS' => [
                    'OPTION_CODE' => 'AGREEMENT_SUBSCRIBE',
                    'SUBMIT_TEXT' => GetMessage('ADD_USER'),
                    'REPLACE_FIELDS' => [],
                    'INPUT_NAME' => "licenses_subscribe_footer",
                    'INPUT_ID' => "licenses_subscribe_footer",
                ]
            ]);
            ?>
        </div>
    <? endif ?>
</form>
