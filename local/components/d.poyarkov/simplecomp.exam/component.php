<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use Bitrix\Main\Application;
use Bitrix\Iblock;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Data\Cache;

if ((int)$arParams['IBLOCK_ID_NEWS'] <= 0) {
	die(Loc::getMessage('ERROR_ID_IBLOCK', ['#IBLOCK#' => 'IBLOCK_ID_NEWS']));
}

if ((int)$arParams['IBLOCK_ID_CATALOG_PRODUCTS'] <= 0) {
	die(Loc::getMessage('ERROR_ID_IBLOCK', ['#IBLOCK#' => 'IBLOCK_ID_CATALOG_PRODUCTS']));
}

if (trim($arParams['PROPETY_CODE_NEWS_LINK']) == '') {
	die(Loc::getMessage('ERORR_PROPERTY_NEWS_LINK'));
}
else {
	$arParams['PROPETY_CODE_NEWS_LINK'] = trim($arParams['PROPETY_CODE_NEWS_LINK']);
}

if (!isset($arParams['CACHE_TIME'])) {
	$arParams['CACHE_TIME'] = 36000000;
}

if ($this->startResultCache()) {
	if (!Loader::includeModule('iblock')) {
		$this->abortResultCache();
		ShowError(Loc::getMessage('IBLOCK_NOT_CONNECT'));
		return;
	}

	$idSectionList = [];
	$arResult['PROD_SECTIONS'] = [];

	//Список разделов ИБ 'Продукция' с установленными новостями
	$rsProdSectionList = CIBlockSection::GetList(
		[],
		[
			'IBLOCK_ID' => $arParams['IBLOCK_ID_CATALOG_PRODUCTS'],
			'=ACTIVE' => 'Y',
			'!' . $arParams['PROPETY_CODE_NEWS_LINK'] => '',
		],
		false,
		[
			'ID', 
			'IBLOCK_ID', 
			'NAME', 
			$arParams['PROPETY_CODE_NEWS_LINK'],
		],
		false
	);
	
	while ($arSect = $rsProdSectionList->GetNext()) {
		$idSectionList[] = $arSect['ID'];
		$arResult['PROD_SECTIONS'][] = $arSect;
	}

	$arResult['PRODS'] = [];
	//Список элементов ИБ 'Продукция'
	$rsProductList = CIBlockElement::GetList(
		[],
		[
			'IBLOCK_ID' => $arParams['IBLOCK_ID_CATALOG_PRODUCTS'],
			'=ACTIVE' => 'Y',
			'IBLOCK_SECTION_ID' => $idSectionList,
		],
		false,
		false,
		[
			'ID', 
			'IBLOCK_ID', 
			'IBLOCK_SECTION_ID', 
			'NAME', 
			'PROPERTY_MATERIAL', 
			'PROPERTY_ARTNUMBER', 
			'PROPERTY_PRICE',
		]
	);
	
	$arResult['COUNT'] = $rsProductList->SelectedRowsCount();

	while ($arElement = $rsProductList->GetNext()) {
		$arResult['PRODS'][] = $arElement;
	}

	$arResult['NEWS_ID_LIST'] = [];
	//Формируем список id новостей для выборки
	foreach ($arResult['PROD_SECTIONS'] as $key => $value) {
		foreach ($value[$arParams['PROPETY_CODE_NEWS_LINK']] as $id) {
			$arResult['NEWS_ID_LIST'][$id] = $id;
		}
	}

	//ex2-82
	if ($arResult['PRODS']) {
		$prices = [];
		$prices = array_column($arResult['PRODS'], 'PROPERTY_PRICE_VALUE');
		$arResult['MIN'] = min($prices);
		$arResult['MAX'] = max($prices);
	}
	//Конец ex2-82

	$arResult['NEWS'] = [];
	//Элементы ИБ 'Новости'
	$rsNewsList = CIBlockElement::GetList(
		[],
		[
			'IBLOCK_ID' => $arParams['IBLOCK_ID_NEWS'],
			'=ACTIVE' => 'Y',
			'ID' => $arResult['NEWS_ID_LIST']
		],
		false,
		false,
		[
			'ID', 
			'NAME', 
			'DATE_ACTIVE_FROM',
		]
	);
	
	while ($arElement = $rsNewsList->GetNext()) {
		$arResult['NEWS'][] = $arElement;
	}
	
	//Список разделов продукции для каждой новости
	foreach ($arResult['NEWS'] as $key => $news) {
		$arResult['NEWS'][$key]['PROD_SECTIONS_ID'] = [];
		$arResult['NEWS'][$key]['PROD_SECTIONS_NAME'] = [];
		
		foreach ($arResult['PROD_SECTIONS'] as $section) {
			if (in_array($news['ID'], $section[$arParams['PROPETY_CODE_NEWS_LINK']])) {
				$arResult['NEWS'][$key]['PROD_SECTIONS_ID'][] = $section['ID'];
				$arResult['NEWS'][$key]['PROD_SECTIONS_NAME'][] = $section['NAME'];
			}
		}
	}
	
	//Список продукции для каждой новости
	foreach ($arResult['NEWS'] as $key => $news) {
		$arResult['NEWS'][$key]['PRODUCTS'] = [];

		foreach ($arResult['PRODS'] as $prod) {
			if (in_array($prod['IBLOCK_SECTION_ID'], $news['PROD_SECTIONS_ID'])) {
				$arResult['NEWS'][$key]['PRODUCTS'][] = $prod;
			}
		}
	}

	unset($arResult['PROD_SECTIONS']);
	unset($arResult['PRODS']);
	unset($arResult['NEWS_ID_LIST']);

	$this->setResultCacheKeys([
		'COUNT',
		'MIN',
		'MAX'
	]);
	$this->includeComponentTemplate();
}

$APPLICATION->SetTitle(Loc::getMessage('COMPONENT_TITLE_PAGE') . $arResult['COUNT']);
//ex2-82
if ($arResult['MIN'] && $arResult['MAX']) {
	$APPLICATION->AddViewContent('MAX_MIN_PRICE', Loc::getMessage('MAX_MIN_PRICE_SECTION', [
		'#MIN#' => $arResult['MIN'],
		'#MAX#' => $arResult['MAX'],
	]));
}
