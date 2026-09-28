<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/* get component template pages & params array */
$arPageBlocksParams = array();
if(\Bitrix\Main\Loader::includeModule('aspro.max')){
	$arPageBlocks = CMax::GetComponentTemplatePageBlocks(__DIR__);
	$arPageBlocksParams = CMax::GetComponentTemplatePageBlocksParams($arPageBlocks);
	CMax::AddComponentTemplateModulePageBlocksParams(__DIR__, $arPageBlocksParams); // add option value FROM_MODULE
}

$arTemplateParameters = array_merge($arPageBlocksParams, array(
	'SHOW_SECTION_PREVIEW_DESCRIPTION' => array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 500,
		'NAME' => GetMessage('SHOW_SECTION_PREVIEW_DESCRIPTION'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	)
));

$arTemplateParameters['ADD_REVIEW_BUTTON'] = array(
	'PARENT' => 'LIST_SETTINGS',
	'NAME' => GetMessage('ADD_REVIEW_BUTTON_NAME'),
	'TYPE' => 'STRING',
	'DEFAULT' => '',
);

$arTemplateParameters['STAFF_IBLOCK_ID'] = array(
	'PARENT' => 'LIST_SETTINGS',
	'NAME' => GetMessage('STAFF_IBLOCK_ID_NAME'),
	'TYPE' => 'STRING',
	'DEFAULT' => '',
);


$arTemplateParameters['SHOW_ADD_REVIEW_BUTTON'] = array(
	'PARENT' => 'LIST_SETTINGS',
	'NAME' => GetMessage('SHOW_ADD_REVIEW_BUTTON_NAME'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
);
?>
