<?php

use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    exit;
}

if (!include_once($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/vendor/php/solution.php')) {
    throw new Exception('Error include solution constants');
}

$APPLICATION->SetTitle(Loc::getMessage('SPS_TITLE_PROFILE'));
$APPLICATION->AddChainItem(Loc::getMessage('SPS_CHAIN_PROFILE'), $arResult['PATH_TO_PROFILE']);
$APPLICATION->AddChainItem(Loc::getMessage('SPS_CHAIN_PROFILE_INFO', ['#ID#' => $arResult['VARIABLES']['ID']]));

$arUserPropValue = [];
$iPersonType = 0;
$rsUserPropValue = CSaleOrderUserPropsValue::GetList(
    ['ID' => 'ASC'],
    ['USER_PROPS_ID' => $arResult['VARIABLES']['ID'], 'IS_PHONE' => 'Y']
);
while($arUserPropValueTmp = $rsUserPropValue->fetch()) {
    $arUserPropValue[$arUserPropValueTmp['ORDER_PROPS_ID']] = $arUserPropValueTmp;
    $iPersonType = $arUserPropValueTmp['PROP_PERSON_TYPE_ID'];
}
if ($arUserPropValue) {
    $arPhoneProp = CSaleOrderProps::GetList(
        ['SORT' => 'ASC'],
        [
            'PERSON_TYPE_ID' => $iPersonType,
            'IS_PHONE' => 'Y',
        ],
        false,
        false,
        []
    )->fetch(); // get phone prop
    if ($arPhoneProp) {
        if ($arUserPropValue[$arPhoneProp['ID']]) {
            if ($arUserPropValue[$arPhoneProp['ID']]['VALUE']) {
                $mask = TSolution::GetFrontParametrValue('PHONE_MASK');
                if (strpos($arUserPropValue[$arPhoneProp['ID']]['VALUE'], '+') === false && strpos($mask, '+') !== false) {
                    CSaleOrderUserPropsValue::Update($arUserPropValue[$arPhoneProp['ID']]['ID'], ['VALUE' => '+'.$arUserPropValue[$arPhoneProp['ID']]['VALUE']]);
                }
            }
            ?>
            <script>
                BX.Aspro.Utils.readyDOM(() => {
                    if (typeof appAspro === 'object' && appAspro && appAspro.phone) {
                        appAspro.phone.init($('input[name=ORDER_PROP_<?=$arPhoneProp['ID']; ?>'), {
                            coutriesData: '<?=TSolution::$arParametrsList['FORMS']['OPTIONS']['USE_INTL_PHONE']['DEPENDENT_PARAMS']['PHONE_CITIES']['TYPE_SELECT']['SRC']; ?>',
                            mask: arAsproOptions['THEME']['PHONE_MASK'],
                            onlyCountries: '<?=TSolution::GetFrontParametrValue('PHONE_CITIES'); ?>',
                            preferredCountries: '<?=TSolution::GetFrontParametrValue('PHONE_CITIES_FAVORITE'); ?>'
                        })
                    }
                })
            </script>
            <?php
            $arExtensions = ['validate', 'phone_input'];
            if (TSolution::GetFrontParametrValue('USE_INTL_PHONE') === 'Y') {
                $arExtensions[] = 'intl_phone_input';
            } elseif (TSolution::GetFrontParametrValue('PHONE_MASK')) {
                $arExtensions[] = 'phone_mask';
            }
            TSolution\Extensions::init($arExtensions);
        }
    }
}
?>

<div class="personal_wrapper">
    <?$APPLICATION->IncludeComponent(
        'bitrix:sale.personal.profile.detail',
        '',
        [
            'PATH_TO_LIST' => $arResult['PATH_TO_PROFILE'],
            'PATH_TO_DETAIL' => $arResult['PATH_TO_PROFILE_DETAIL'],
            'SET_TITLE' => $arParams['SET_TITLE'],
            'USE_AJAX_LOCATIONS' => $arParams['USE_AJAX_LOCATIONS_PROFILE'],
            'ID' => $arResult['VARIABLES']['ID'],
        ],
        $component
    );?>
</div>
