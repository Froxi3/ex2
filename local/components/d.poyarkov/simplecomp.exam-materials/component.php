<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
    die();
}

use Bitrix\Main\Application;
use Bitrix\Iblock;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Engine\CurrentUser;

if ((int)$arParams['ID_IBLOCK_NEWS'] <= 0) {
	die(Loc::getMessage('ERROR_ID_IBLOCK_NEWS', ['#IBLOCK#' => 'IBLOCK_ID_NEWS']));
}

if (trim($arParams['IBLOCK_PROPERTY_AUTHOR']) == '') {
	die(Loc::getMessage('ERORR_IBLOCK_PROPERTY_AUTHOR'));
}
else {
	$arParams['IBLOCK_PROPERTY_AUTHOR'] = trim($arParams['IBLOCK_PROPERTY_AUTHOR']);
}
if (trim($arParams['USER_PROPERTY_AUTHOR_TYPE']) == '') {
	die(Loc::getMessage('ERORR_USER_PROPERTY_AUTHOR_TYPE'));
}
else {
	$arParams['USER_PROPERTY_AUTHOR_TYPE'] = trim($arParams['USER_PROPERTY_AUTHOR_TYPE']);
}

if (!isset($arParams['CACHE_TIME'])) {
	$arParams['CACHE_TIME'] = 36000000;
}

$curUser = CurrentUser::get();

if ($this->startResultCache(false, $curUser->getID())) {
	if (!Loader::includeModule('iblock')) {
		$this->abortResultCache();
		ShowError(Loc::getMessage('IBLOCK_NOT_CONNECT'));
		return;
	}

	$rsUser = CUser::GetByID($curUser->getID());
	$arUser = $rsUser->Fetch();
	$userGroupType = (int)$arUser[$arParams['USER_PROPERTY_AUTHOR_TYPE']];

	//
	$filter = [
		$arParams['USER_PROPERTY_AUTHOR_TYPE'] => $userGroupType,
	];

	$arParams['FIELDS'] = [
		'ID',
		'LOGIN',
	];

	$rsUsers = CUser::GetList(false, false, $filter, $arParams);

	while ($rsUser = $rsUsers->GetNext()) {
		$arResult["USERS"][$rsUser['ID']] = $rsUser;
	}

	$idUsers = array_column($arResult["USERS"], 'ID');
	$authorProperty = $arParams['IBLOCK_PROPERTY_AUTHOR'] . '_VALUE';
	$curIdUser = $curUser->getID();

	$filter = [
		$arParams['IBLOCK_PROPERTY_AUTHOR'] => $idUsers,
	];

	$rsNews = CIBlockElement::GetList(
		[],
		$filter,
		false,
		false,
		[
			'ID',
			'NAME',
			$arParams['IBLOCK_PROPERTY_AUTHOR'],
			'DATE_ACTIVE_FROM',
		]
	);
	
	$idNews = [];
	$news = [];

	while ($arNews = $rsNews->GetNext()) {
		if ($arNews[$authorProperty] == $curIdUser) {
			$idNews[] = $arNews['ID'];
		}
		$news[] = $arNews;
	}

	$idUniqueNews = [];

	foreach ($news as $key => $value) {
		if (!in_array($value['ID'], $idNews)) {
			$idUniqueNews[$value['ID']] = $value['ID'];
			$arResult['ITEMS'][$value[$authorProperty]]['NEWS'][] = $value;
		}
	}
	
	foreach ($arResult['ITEMS'] as $key => $value) {     
		$arResult['ITEMS'][$key]['ID'] = $key;
		$arResult['ITEMS'][$key]['LOGIN'] = $arResult['USERS'][$key]['LOGIN'];
	}

	$arResult['COUNT'] = count($idUniqueNews);

	unset($arResult['USERS']);

	$this->setResultCacheKeys([
		'COUNT',
	]);

	$this->includeComponentTemplate();
}

$APPLICATION->SetTitle(Loc::getMessage('SIMPLECOMP_EX_2_97_TITLE') . $this->arResult['COUNT']);
