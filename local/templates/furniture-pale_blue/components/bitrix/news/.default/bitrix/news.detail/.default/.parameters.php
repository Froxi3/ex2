<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
	die();
}

$arTemplateParameters = [
	'DISPLAY_DATE' => [
		'NAME' => GetMessage('T_IBLOCK_DESC_NEWS_DATE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	],
	'DISPLAY_NAME' => [
		'NAME' => GetMessage('T_IBLOCK_DESC_NEWS_NAME'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	],
	'DISPLAY_PICTURE' => [
		'NAME' => GetMessage('T_IBLOCK_DESC_NEWS_PICTURE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	],
	'DISPLAY_PREVIEW_TEXT' => [
		'NAME' => GetMessage('T_IBLOCK_DESC_NEWS_TEXT'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	],
	'ID_IBLOCK_CANONICAL' => [
		'NAME' => GetMessage('T_IBLOCK_ID_CANONICAL'),
		'TYPE' => 'STRING',
		'DEFAULT' =>'N',
		'REFRESH'=> 'N',
	],
];
