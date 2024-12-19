<?php
AddEventHandler('iblock', 'OnBeforeIBlockElementUpdate', ['ProductDeactivator', 'OnBeforeIBlockElementUpdateHandler']);

use Bitrix\Main\Localization\Loc;

class ProductDeactivator
{
    public static function OnBeforeIBlockElementUpdateHandler(&$arFields)
    {
        $curIblockCode = CIBlock::GetArrayByID($arFields['IBLOCK_ID'], 'CODE');

        if (IBLOCK_CODE_PRODUCTS == $curIblockCode) {

            $showCount = 0;
            $select = ['SHOW_COUNTER'];
            $filter = ['ID' => $arFields['ID']];

            $rsElement = CIBlockElement::GetList([], $filter, false, false, $select);

            while($arElement = $rsElement->GetNext()) {
                $showCount = $arElement['SHOW_COUNTER'];
            }

            if ($arFields['ACTIVE'] != 'Y' && $showCount > 2) {
                global $APPLICATION;
                $APPLICATION->ThrowException(Loc::getMessage('ERROR_DEACTIVATE_PRODUCT', ['#COUNT#' => $showCount]));
                return false;
            }
        }
    }
}
