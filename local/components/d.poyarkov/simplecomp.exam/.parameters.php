<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
	die();
}

use Bitrix\Main\Localization\Loc;

$arComponentParameters = [
	'PARAMETERS' => [
		'IBLOCK_ID_CATALOG_PRODUCTS' => [
			'NAME' => Loc::getMessage('T_IBLOCK_ID_CATALOG_PRODUCTS'),
			'TYPE' => 'STRING'
		],
		'IBLOCK_ID_NEWS' => [
			'NAME' => Loc::getMessage('T_IBLOCK_ID_NEWS'),
			'TYPE' => 'STRING'
		],
		'PROPETY_CODE_NEWS_LINK' => [
			'NAME' => Loc::getMessage('T_PROPETY_CODE_NEWS_LINK'),
			'TYPE' => 'STRING'
		],
		'CACHE_TIME' => [
			'DEFAULT' => 36000000
		],
	]
];
