<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use Bitrix\Main\Application;
use Bitrix\Iblock;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Data\Cache;

class SimpleComp extends CBitrixComponent
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

		if ((int)$arParams['IBLOCK_ID_NEWS'] <= 0) {
			die(Loc::getMessage('ERROR_ID_IBLOCK', ['#IBLOCK#' => 'IBLOCK_ID_NEWS']));
		}

		if (trim($arParams['PROPETY_CODE_NEWS_LINK']) == '') {
			die(Loc::getMessage('ERORR_PROPERTY_NEWS_LINK'));
		}
		else {
            $arParams['PROPETY_CODE_NEWS_LINK'] = trim($arParams['PROPETY_CODE_NEWS_LINK']);
        }

		if ((int)$arParams['IBLOCK_ID_CATALOG_PRODUCTS'] <= 0) {
			die(Loc::getMessage('ERROR_ID_IBLOCK', ['#IBLOCK#' => 'IBLOCK_ID_CATALOG_PRODUCTS']));
		}
		
		if (!isset($arParams['CACHE_TIME'])) {
            $arParams['CACHE_TIME'] = 36000000;
        }

        return parent::onPrepareComponentParams($arParams);
    }

	private function initializeCache($arParams): void
	{
		$this->cacheKey = self::class . '_' . md5(json_encode($this->arParams)) . '_' . md5(json_encode($_REQUEST));
        $this->cachePatch = self::class;
        $this->cache = Cache::createInstance();
	}
				
	public function executeComponent()
	{
		global $APPLICATION;

        if ($this->cache->initCache($this->arParams['CACHE_TIME'], $this->cacheKey, $this->cachePatch)) {
            $this->arResult = $this->cache->getVars();
        } 
		elseif ($this->cache->startDataCache()) {

            $this->arResult = [];
			//Разделы ИБ 'Продукция'
			$prodSectionList = $this->getSectionList($this->arParams['IBLOCK_ID_CATALOG_PRODUCTS'], $this->arParams['PROPETY_CODE_NEWS_LINK']);
			//Список Ид новостей, прикрепленных к разделам ИБ 'Продукция'
			$idNewsList = $this->createIdNewsList($prodSectionList, $this->arParams['PROPETY_CODE_NEWS_LINK']);
			//Список Ид разделов продукции
			$idSectionList = array_column($prodSectionList, 'ID');
			//Элементы ИБ 'Продукция'
			$productionList = $this->getProdList($this->arParams['IBLOCK_ID_CATALOG_PRODUCTS'], $idSectionList);
			//Элементы ИБ 'Новости'
			$newsList = $this->getNewsList($this->arParams['IBLOCK_ID_NEWS'], $idNewsList);
			//Формирование результирующего массива
			$this->arResult = $this->createResultArray($newsList, $prodSectionList, $productionList, $this->arParams['PROPETY_CODE_NEWS_LINK']);
	
			//ex2-82
			$this->arResult['MIN_MAX'] = $this->calculateMinMax($productionList);

            if (!($this->arResult['NEWS'])) {
                $this->cache->abortDataCache();
            }

            $this->cache->endDataCache($this->arResult); // запись arResult в кеш
        }

		//ex2-82
		if ($this->arResult['MIN_MAX']) {
			$APPLICATION->AddViewContent('MAX_MIN_PRICE', Loc::getMessage('MAX_MIN_PRICE_SECTION', [
				'#MIN#' => $this->arResult['MIN_MAX']['MIN'],
				'#MAX#' => $this->arResult['MIN_MAX']['MAX'],
			]));
		}
		
		//ex2-100
		$rsButtons = CIBlock::GetPanelButtons(
			$this->arParams['IBLOCK_ID_CATALOG_PRODUCTS']
		);
		$this->AddIncludeAreaIcons(
			[ //массив кнопок toolbar'a
				[
					'ID' => 'ex2_100',
					'TITLE' => Loc::getMessage('TITLE_BUTTON_EX_2_100'),
					'URL' => $rsButtons['submenu']['element_list']['ACTION_URL'],
					'IN_PARAMS_MENU' => true, //показать в контекстном меню
				]
			]
		);
		//Конец ex2-100
		$this->IncludeComponentTemplate();

		$APPLICATION->SetTitle(Loc::getMessage('COMPONENT_TITLE_PAGE') . $this->arResult['COUNT']);
	}

	public function getSectionList(int $idIblockProducts, string $newsLinkProperty): array
	{
		$result = [];
		$rsProdSectionList = CIBlockSection::GetList(
			[],
			[
				'IBLOCK_ID' => $idIblockProducts,
				'=ACTIVE' => 'Y',
				'!' . $newsLinkProperty => '', // Разделы с установленными новостями
			],
			false,
			[
				'ID', 
				'IBLOCK_ID', 
				'NAME', 
				$newsLinkProperty,
			],
			false
		);
		
		while ($arSect = $rsProdSectionList->GetNext()) {
			$result[] = $arSect;
		}
		return $result;
	}

	public function createIdNewsList(array $prodSectionList, string $newsLinkProperty): array
	{
		$result = [];
		 
		foreach ($prodSectionList as $key => $value) {
			foreach ($value[$newsLinkProperty] as $id) {
				$result[$id] = $id;
			}
		}
		return $result;
	}

	public function getProdList(int $idIblockProducts, array $idSectionList): array
	{
		$result = [];
		
		$rsProductList = CIBlockElement::GetList(
			[],
			[
				'IBLOCK_ID' => $idIblockProducts,
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
	

		$result['COUNT'] = $rsProductList->SelectedRowsCount();

		while ($arElement = $rsProductList->GetNext()) {
			$result['PRODS'][] = $arElement;
		}

		return $result;
	}

	public function getNewsList(int $idIblockNews, array $idNewsList): array
	{
		$result = [];

		$rsNewsList = CIBlockElement::GetList(
			[],
			[
				'IBLOCK_ID' => $idIblockNews,
				'=ACTIVE' => 'Y',
				'ID' => $idNewsList
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
			$result[] = $arElement;
		}

		return $result;
	}
	public function createResultArray(array $newsList, array $prodSectionList, array $productionList, string $newsLinkProperty): array
	{
		$result = [];
		//Список разделов продукции для каждой новости
		foreach ($newsList as $key => $news) {
			foreach ($prodSectionList as $section) {
				if (in_array($news['ID'], $section[$newsLinkProperty])) {
					$newsList[$key]['PROD_SECTIONS_ID'][] = $section['ID'];
					$newsList[$key]['PROD_SECTIONS_NAME'][] = $section['NAME'];
				}
			}
		}
		
		//Список продукции для каждой новости
		foreach ($newsList as $key => $news) {
			foreach ($productionList['PRODS'] as $prod) {
				if (in_array($prod['IBLOCK_SECTION_ID'], $news['PROD_SECTIONS_ID'])) {
					$newsList[$key]['PRODUCTS'][] = $prod;
				}
			}
		}

		$result['COUNT'] = $productionList['COUNT'];
		$result['NEWS'] = $newsList;

		return $result;
	}
	
	public function calculateMinMax(array $prodSectionList): array
	{
		$result = [];

		if ($prodSectionList) {
			$prices = array_column($prodSectionList['PRODS'], 'PROPERTY_PRICE_VALUE');
			$result['MIN'] = min($prices);
			$result['MAX'] = max($prices);
		}

		return $result;
	}
}
