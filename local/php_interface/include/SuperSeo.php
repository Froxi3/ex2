<?php
AddEventHandler('main', 'OnBeforeProlog', ['SuperSeo', 'OnBeforePrologHandler']);

use Bitrix\Main\Loader;

final class SuperSeo
{
    public static function OnBeforePrologHandler()
    {
        if (Loader::includeModule('iblock')) {

            global $APPLICATION;
            $curPage = $APPLICATION->GetCurPage();

            $filter = [
                '=IBLOCK_CODE' => IBLOCK_META_NAME,
                '=NAME' => $curPage,
            ];
            $select = [
                'ID',
                'IBLOCK_ID',
                'PROPERTY_META_TITLE',
                'PROPERTY_META_DESCRIPTION',
            ];

            $rsElement = CIBlockElement::GetList([], $filter, false, false, $select);
            $result = [];
            while ($arElement = $rsElement->GetNext()) {
                $result = $arElement;
            }

            if ($result['PROPERTY_META_TITLE_VALUE']) {
                $APPLICATION->SetPageProperty('title', $result['PROPERTY_META_TITLE_VALUE']);
            }
            if ($result['PROPERTY_META_DESCRIPTION_VALUE']) {
                $APPLICATION->SetPageProperty('description', $result['PROPERTY_META_DESCRIPTION_VALUE']);
            }
        }
    }
}
