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
		'IBLOCK_ID_CLASSIFIER' => [
			'NAME' => Loc::getMessage('T_IBLOCK_ID_CLASSIFIER'),
			'TYPE' => 'STRING'
		],
		'TEMPLATE_LINK_DETAIL_PRODUCT' => [
			'NAME' => Loc::getMessage('T_TEMPLATE_LINK_DETAIL_PRODUCT'),
			'TYPE' => 'STRING'
		],
		'LINK_CLASSIFIER' => [
			'NAME' => Loc::getMessage('T_LINK_CLASSIFIER'),
			'TYPE' => 'STRING'
		],
		'DETAIL_PAGE_TEMPLATE' => CIBlockParameters::GetPathTemplateParam(
			'DETAIL',
			'DETAIL_PAGE_TEMPLATE',
			GetMessage('T_DETAIL_PAGE_TEMPLATE'),
			'catalog_exam/#SECTION_ID#/#ELEMENT_CODE#',
			'URL_TEMPLATES'
		),
		'ELEMENTS_COUNT' => [
			'NAME' => Loc::getMessage('T_ELEMENTS_COUNT'),
			'TYPE' => 'STRING',
			'DEFAULT' => 2
		],
		'CACHE_TIME' => [
			'DEFAULT' => 36000000
		],
	]
];

CIBlockParameters::AddPagerSettings(
    $arComponentParameters,
    GetMessage('T_CATEGORY_TITLE_NAV_PAGE'),
    false,
    true,
);
