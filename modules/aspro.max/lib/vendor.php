<?php

namespace Aspro\Max;

use Bitrix\Main\Loader;
use CMax as Solution;

class Vendor
{
    public static function includeSkuComponent(string $template = '', array $params = [], ?\CBitrixComponent $parentComponent = null)
    {
        if (Loader::includeModule('aspro.sku')) {
            $GLOBALS['APPLICATION']->IncludeComponent('aspro:sku.list', $template, $params, $parentComponent, ['HIDE_ICONS' => 'Y']);
        }
    }

    public static function onSkuCanBuyHandler(&$arItems, $arParams)
    {
        if (!Loader::includeModule('aspro.sku')) {
            return;
        }

        if ($arParams['USE_REGION'] === 'Y' && $arParams['STORES']) {
            foreach ($arItems as &$arItem) {
                $totalCount = Solution::GetTotalCount($arItem, $arParams);
                $arItem['CAN_BUY'] = Solution::getCanBuyRegion($arItem, $totalCount);
            }
            unset($arItem);
        }
    }
}
