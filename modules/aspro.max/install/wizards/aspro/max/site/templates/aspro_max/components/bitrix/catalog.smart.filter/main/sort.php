<?php

if (!include_once ($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/vendor/php/solution.php')) {
    throw new SystemException('Error include solution constants');
}

$sort = [
    'ASPRO_FILTER_SORT' => [
        'NAME' => GetMessage('ASPRO_FILTER_SORT'),
        'DISPLAY_TYPE' => 'ASPRO_FILTER_SORT',
        'DISPLAY_EXPANDED' => 'Y',
        'CODE' => 'ASPRO_FILTER_SORT',
        'ID' => 'ASPRO_FILTER_SORT',
        'ASPRO_FILTER_SORT' => 'Y',
        'VALUES' => [],
    ],
];

$arResult['ITEMS'] = $sort + $arResult['ITEMS'];

$parentTemplate = $this?->__component?->__parent?->GetTemplate()?->GetName();

$catalogSort = new TSolution\Template\CatalogSort(
    params: $arParams,
    template: ($parentTemplate ?: ''),
    sortField: mb_strtoupper($arParams['SORT_FIELD_DEFAULT'] ?? 'sort'),
    orderField: mb_strtolower($arParams['SORT_ORDER_DEFAULT'] ?? 'asc')
);

$arAvailableSort = $catalogSort->getAvailableSort();
foreach ($arAvailableSort as $key => $arSort) {
    foreach ($arSort['ORDER_VALUES'] as $order => $title) {
        $current_url = $APPLICATION->GetCurPageParam('sort='.$arSort['SORT'].'&order='.$order, ['sort', 'order']);
        $url = str_replace('+', '%2B', $current_url);
        $bSelected = $arSort['CURRENT'] && $arSort['CURRENT'] === $order;

        $controlClassList = ['sort_btn', $order, $key];
        if ($bSelected) {
            $controlClassList[] = 'current';
        }

        $arResult['ITEMS']['ASPRO_FILTER_SORT']['VALUES'][] = [
            'CONTROL_HTML' => '<a href="'.$url.'" class="'.TSolution\Utils::implodeClasses($controlClassList).'" rel="nofollow"><span>'.$title.'</span></a>',
            'CHECKED' => $bSelected ? 'Y' : 'N',
            'VALUE' => $title,
        ];
    }
}
