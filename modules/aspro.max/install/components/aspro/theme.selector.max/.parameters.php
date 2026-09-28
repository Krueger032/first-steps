<?
use Bitrix\Main\Localization\Loc;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
Loc::loadMessages(__FILE__);

$arComponentParameters = [
	'GROUPS' => [
		'CONFIG' => [
			'NAME' => GetMessage('TS_P_GROUP_CONFIG_TITLE'),
			'SORT' => '500',
		],
	],
	'PARAMETERS' => [
		'ICON_SIZE' => [
			'NAME' => GetMessage('T_SIZE'),
			'TYPE' => 'LIST',
			'VALUES' => [
				'sm' => GetMessage('T_SIZE_SMALL'),
				'md' => GetMessage('T_SIZE_MEDIUM'),
			],
			'DEFAULT' => 'sm',
		],
		'USE_TEXT' => [
			'NAME' => GetMessage('T_USE_TEXT'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
		],
	],
];
?>