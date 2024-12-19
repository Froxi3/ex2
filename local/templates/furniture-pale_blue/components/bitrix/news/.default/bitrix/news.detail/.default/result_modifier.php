<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
    die();
}

if ((int)$arParams['ID_IBLOCK_CANONICAL'] > 0) {
    $arSelect = [
        'ID',
        'NAME',
        'PROPERTY_NEWS'
    ];

    $arFilter = [
        'IBLOCK_ID' => $arParams['ID_IBLOCK_CANONICAL'],
        'PROPERTY_NEWS' => $arResult['ID'],
    ];

    $rsElement = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);

    while ($arElem = $rsElement->GetNext()) { 
        $arResult['CANONICAL_ELEMENT'][] = $arElem;
    }

    $component = $this->__component;
    
    if (is_object($component)) {
        $arResult['CANONICAL'] = $arResult['CANONICAL_ELEMENT'][0]['NAME'];
        $component->SetResultCacheKeys(['CANONICAL']);	
    }
}

