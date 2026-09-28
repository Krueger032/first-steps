<?php

namespace Aspro\Max;

use Bitrix\Main\Config\Option;
use Bitrix\Main\IO\File;
use Bitrix\Main\Web\Uri;
use CMax as Solution;

class Utils
{
    public static function implodeClasses(array $arClasses, string $delimiter = ' '): string
    {
        return implode($delimiter, $arClasses);
    }

    public static function getPathWithTimestamp(string $path): string
    {
        $file = new File($_SERVER['DOCUMENT_ROOT'].$path);
        if (!$file->isExists()) {
            return $path;
        }

        return $path.'?'.$file->getModificationTime();
    }

    public static function checkShowSearchFilter(array $arItem, array $arParams): bool
    {
        $displayTypesAllowed = ['F', 'K', 'G', 'H'];
        $minValuesCount = 5;

        $filterProps = explode(',', Solution::GetFrontParametrValue('CATALOG_SEARCH_FILTER_PROP'));
        if ($arItem['IBLOCK_ID'] != $arParams['IBLOCK_ID']) {
            $filterProps = explode(',', Solution::GetFrontParametrValue('CATALOG_SKU_SEARCH_FILTER_PROP'));
        }

        $hasValues = !empty($arItem['VALUES']);
        $isAllowedDisplayType = in_array($arItem['DISPLAY_TYPE'], $displayTypesAllowed);
        $hasMoreThanMinimum = count($arItem['VALUES']) > $minValuesCount;
        $isInFilterProps = in_array($arItem['CODE'], $filterProps, true);

        return $hasValues && $isAllowedDisplayType && $hasMoreThanMinimum && $isInFilterProps;
    }



    public static function getNumVisibleCountFilter(){
        $numShowAllFromOption = Solution::GetFrontParametrValue('CATALOG_FILTER_SHOWALL_FROM');

        return $numShowAllFromOption > 0 ? $numShowAllFromOption : INF;
    }

    public static function getSiteURL(): string
    {
        return rtrim((new Uri(SITE_DIR))->toAbsolute()->getUri(), '/');
    }

    public static function getCurrentUrl(): string
    {
        global $APPLICATION;

        return self::getSiteURL().$APPLICATION->GetCurPage(false);
    }

    public static function getAbsolutePath(string $path): string
    {
        return self::getSiteURL().$path;
    }
}
