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

class simpleCompEx2_97 extends CBitrixComponent
{
    protected Cache $cache;
    protected string $cacheKey;
    protected string $cachePatch;

    final public function onPrepareComponentParams($arParams): array
    {
        $this->initializeCache($arParams);

        if (!Loader::IncludeModule('iblock')) {
            die(Loc::getMessage('IBLOCK_NOT_CONNECT'));
        }

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

        return parent::onPrepareComponentParams($arParams);
    }

    private function initializeCache($arParams): void
    {
        $curUser = CurrentUser::get();
        $curIdUser = $curUser->getId();

        $this->cacheKey = self::class . '_' . md5(json_encode($this->arParams)) . '_' . md5(json_encode($curIdUser));
        $this->cachePatch = self::class;
        $this->cache = Cache::createInstance();
    }

    final public function executeComponent(): void
    {
        global $APPLICATION;
        $curUser = CurrentUser::get();

        if ($curUser->getId()) {

            if ($this->cache->InitCache($this->arParams['CACHE_TIME'], $this->cacheKey, $this->cachePatch)) { // если кеш есть
                $this->arResult = $this->cache->getVars();
            } 
            elseif ($this->cache->startDataCache()) {

                $this->arResult = [];
                //Получаем тип группы
                $userGroupType = $this->getUserGroupType($this->arParams['USER_PROPERTY_AUTHOR_TYPE']);
                //Получаем список пользователей в группе
                $userList = $this->getUserList($this->arParams['USER_PROPERTY_AUTHOR_TYPE'], $userGroupType);
                //Получаем список новостей и формируем необходимую структуру
                $this->arResult = $this->getNewsList($this->arParams['ID_IBLOCK_NEWS'], $this->arParams['IBLOCK_PROPERTY_AUTHOR'], $userList);

                $this->cache->endDataCache($this->arResult);
            }
            
            $this->includeComponentTemplate();
            $APPLICATION->SetTitle(Loc::getMessage('SIMPLECOMP_EX_2_97_TITLE') . $this->arResult['COUNT']);
        }
    }

    public function getUserGroupType(string $propetyAuthorType): int
    {
        $curUser = CurrentUser::get();

        $rsUser = CUser::GetByID($curUser->getID());
	    $arUser = $rsUser->Fetch();
        $userGroupType = (int)$arUser[$propetyAuthorType];

        return $userGroupType;
    }

    public function getUserList(string $propetyAuthorType, int $userGroupType): array
    {
        $result = [];

        $filter = [
            $propetyAuthorType => $userGroupType,
        ];

        $arParams['FIELDS'] = [
            'ID',
            'LOGIN',
        ];

        $rsUsers = CUser::GetList(false, false, $filter, $arParams);

        while ($rsUser = $rsUsers->GetNext()) {
            $result[$rsUser['ID']] = $rsUser;
        }

        return $result;
    }

    public function getNewsList(int $idIblcokNews, string $author, array $userList): array
    {
        $curUser = CurrentUser::get();
        $result = [];
        $idUsers = array_column($userList, 'ID');
        $authorProperty = $author . '_VALUE';
        $curIdUser = $curUser->getID();

        $filter = [
            $author => $idUsers,
        ];

        $rsNews = CIBlockElement::GetList(
            [],
            $filter,
            false,
            false,
            [
                'ID',
                'NAME',
                $author,
                'DATE_ACTIVE_FROM',
            ]
        );

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
                $result['ITEMS'][$value['PROPERTY_AUTHOR_VALUE']]['NEWS'][] = $value;
            }
        }

        foreach ($result['ITEMS'] as $key => $value) {     
            $result['ITEMS'][$key]['ID'] = $key;
            $result['ITEMS'][$key]['LOGIN'] = $userList[$key]['LOGIN'];
        }

        $result['COUNT'] = count($idUniqueNews);
        return $result;
    }
}
