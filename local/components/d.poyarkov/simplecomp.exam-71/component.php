<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use Bitrix\Main\Application;
use Bitrix\Iblock;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Engine\CurrentUser;

if ((int)$arParams['IBLOCK_ID_CATALOG_PRODUCTS'] <= 0) {
	die(Loc::getMessage('ERROR_ID_IBLOCK', ['#IBLOCK#' => 'IBLOCK_ID_CATALOG_PRODUCTS']));
}
if ((int)$arParams['ELEMENTS_COUNT'] <= 0) {
	die(Loc::getMessage('ERROR_ELEMENTS_COUNT'));
}

if ((int)$arParams['IBLOCK_ID_CLASSIFIER'] <= 0) {
	die(Loc::getMessage('ERROR_ID_IBLOCK', ['#IBLOCK#' => 'IBLOCK_ID_CLASSIFIER']));
}

$arParams['TEMPLATE_LINK_DETAIL_PRODUCT'] = trim($arParams['TEMPLATE_LINK_DETAIL_PRODUCT']);
if ($arParams['TEMPLATE_LINK_DETAIL_PRODUCT'] == '') {
	die(Loc::getMessage('ERROR_TEMPLATE_LINK_DETAIL'));
}

$arParams['LINK_CLASSIFIER'] = trim($arParams['LINK_CLASSIFIER']);
if ($arParams['LINK_CLASSIFIER'] == '') {
	die(Loc::getMessage('ERROR_LINK_CLASSIFIER'));
}

if (!isset($arParams['CACHE_TIME'])) {
	$arParams['CACHE_TIME'] = 36000000;
}

$arParams['DETAIL_PAGE_TEMPLATE'] = trim($arParams['DETAIL_PAGE_TEMPLATE'] ?? '');

$curUser = CurrentUser::get();

$request = \Bitrix\Main\Context::getCurrent()->getRequest();
$getParamF = $request->getQuery('F');

//2-60
$nav = [
	'nPageSize' => $arParams['ELEMENTS_COUNT'],
];
$arNavigation = CDBResult::GetNavParams($nav);
//2-60

//В параметры кеша передали группы пользователя
if ($this->startResultCache(false, [$curUser->getUserGroups(), $arNavigation, $getParamF])) {
	if (!Loader::includeModule('iblock')) {
		$this->abortResultCache();
		ShowError(Loc::getMessage('IBLOCK_NOT_CONNECT'));
		return;
	}
	
	//2-107
	if (defined('BX_COMP_MANAGED_CACHE')) {
		global $CACHE_MANAGER;
		$CACHE_MANAGER->RegisterTag('iblock_id_' . IBLOCK_ID_SERVICES);
	}
	//2-107

	$arResult['COUNT'] = 0;
	$propClassifierValue = $arParams['LINK_CLASSIFIER'].'_VALUE';
	$arResult['PRODUCTS'] = [];

	//2-49
	if ($getParamF) {
		$addFilter = [
			'LOGIC' => 'OR',
			[
				'<PROPERTY_PRICE' => 1500,
				'PROPERTY_MATERIAL' => 'Металл, пластик',
			],
			[
				'<=PROPERTY_PRICE' => 1700,
				'PROPERTY_MATERIAL' => 'Дерево, ткань',
			],
		];
	}
	//2-49

	$idCompanyPageList = [];
	$arResult['COMPANY'] = [];
	$filter = [
		'IBLOCK_ID' => $arParams['IBLOCK_ID_CLASSIFIER'],
		'CHECK_PERMISSIONS' => 'Y',
		'ACTIVE' => 'Y',
	];
	$select = [
		'ID',
		'NAME',
	];

	$rsElements = CIBlockElement::GetList([], $filter, false, $nav, $select);
	$arResult['COUNT'] = $rsElements->SelectedRowsCount();
	if (!$arResult['COUNT']) {
		$this->abortResultCache();
	}

	//2-60
	$arResult['NAV_STRING'] = $rsElements->GetPageNavStringEx(
		$navComponentObject,
		$arParams['PAGER_TITLE'],
		$arParams['PAGER_TEMPLATE'],
		false,
	);
	//2-60

	while ($element = $rsElements->GetNext()) {
		$idCompanyPageList[] = $element['ID'];
		$arResult['COMPANY'][$element['ID']] = $element;
		$arResult['COMPANY'][$element['ID']]['PRODUCTS'] = [];
	}

	if ($arResult['COMPANY']) {
		//Товары с установленным классификатором
		$sort = [
			'NAME' => 'ASC',
			'SORT' => 'ASC',
		];
		$filter = [
			'IBLOCK_ID' => $arParams['IBLOCK_ID_CATALOG_PRODUCTS'],
			'CHECK_PERMISSIONS' => 'Y',
			$arParams['LINK_CLASSIFIER'] => $idCompanyPageList,
			$addFilter
		];
		$select = [
			'ID',
			'IBLOCK_ID',
			'NAME',
			'IBLOCK_SECTION_ID',
			'CODE',
			$arParams['LINK_CLASSIFIER'],
			'PROPERTY_PRICE',
			'PROPERTY_MATERIAL',
			'PROPERTY_ARTNUMBER',
		];

		$rsElements = CIBlockElement::GetList($sort, $filter, false, false, $select);
		$rsElements->SetUrlTemplates($arParams['DETAIL_PAGE_TEMPLATE'], '', '');

		if ($rsElements->SelectedRowsCount() == 0) {
			$this->abortResultCache();
			unset($arResult['COMPANY']);
			$arResult['COUNT'] = 0;
		}

		//Заполняем массив ид классификаторов и группируем по классификатору товары
		while ($arElement = $rsElements->GetNext()) {
			//2-58
			$arButtons = CIBlock::GetPanelButtons(
				$arElement['IBLOCK_ID'],
				$arElement['ID'],
				0,
				['SECTION_BUTTONS'=>false, 'SESSID'=>false]
			);

			$arElement['EDIT_LINK'] = $arButtons['edit']['edit_element']['ACTION_URL'] ?? '';
			$arElement['DELETE_LINK'] = $arButtons['edit']['delete_element']['ACTION_URL'] ?? '';
			$arResult['ADD_LINK'] = $arButtons['edit']['add_element']['ACTION_URL'] ?? '';
			//2-58
			$arResult['COMPANY'][$arElement[$propClassifierValue]]['PRODUCTS'][] = $arElement;
		}
	}
	

	
	$this->setResultCacheKeys([
		'COUNT'
	]);

	$this->includeComponentTemplate();
}

$arButtons = CIBlock::GetPanelButtons($arResult['LAST_ITEM_IBLOCK_ID'], 0, 0, array('SECTION_BUTTONS'=>false));
$this->addIncludeAreaIcons(CIBlock::GetComponentMenu($APPLICATION->GetPublicShowMode(), $arButtons));

$APPLICATION->SetTitle(Loc::getMessage('COMPONENT_71_TITLE') . $this->arResult['COUNT']);