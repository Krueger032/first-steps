<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;?>

<span class="alert alert-warning"><?=Loc::getMessage('SOA_MIN_ORDER_SUM').' '.CCurrencyLang::CurrencyFormat($arParams['MIN_ORDER_SUM'], $arResult['BASE_LANG_CURRENCY'], true);?></span>