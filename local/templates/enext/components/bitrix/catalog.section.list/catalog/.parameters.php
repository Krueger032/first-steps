<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arSectionRowList = array(
	'4' => GetMessage('CPT_BCSL_SECTION_ROW_4'),
	'6' => GetMessage('CPT_BCSL_SECTION_ROW_6')
);

$arTemplateParameters = array(
	'SECTION_ROW' => array(
		'PARENT' => 'DATA_SOURCE',
		'NAME' => GetMessage('CPT_BCSL_SECTION_ROW'),
		'TYPE' => 'LIST',
		'VALUES' => $arSectionRowList,
		'MULTIPLE' => 'N',
		'DEFAULT' => '6',
		'REFRESH' => 'N'
	)
);