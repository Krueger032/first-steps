<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

if (!Loader::includeModule('iblock'))
	return;
$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$siteId = $request->get("src_site") ?? $request->get("site");

/* get component template pages & params array */
$arPageBlocksParams = array();
if(\Bitrix\Main\Loader::includeModule('aspro.max')){
	$arPageBlocks = CMax::GetComponentTemplatePageBlocks(__DIR__);
	$arPageBlocksParams = CMax::GetComponentTemplatePageBlocksParams($arPageBlocks);
	$arCurrentValues['SECTION_ELEMENTS_TYPE_VIEW'] = $arCurrentValues['SECTION_ELEMENTS_TYPE_VIEW'] === 'FROM_MODULE' ? CMax::GetFrontParametrValue('DOCUMENTS_PAGE', $siteId) : $arCurrentValues['SECTION_ELEMENTS_TYPE_VIEW'];
	CMax::AddComponentTemplateModulePageBlocksParams(__DIR__, $arPageBlocksParams); // add option value FROM_MODULE
}

$arTemplateParameters = array_merge($arPageBlocksParams, array());
if (strpos($arCurrentValues['SECTION_ELEMENTS_TYPE_VIEW'], 'list_elements_1') !== false) {
	$arTemplateParameters = array_merge($arTemplateParameters, array(
		'COUNT_IN_LINE' => array(
			'PARENT' => 'LIST_SETTINGS',
			'NAME' => GetMessage('COUNT_IN_LINE'),
			'TYPE' => 'STRING',
			'DEFAULT' => '3',
		),
	));
}?>