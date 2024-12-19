<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
    die();
}

if ($arParams['USE_SPECIAL_DATE'] == 'Y') {
    $APPLICATION->SetPageProperty('specialdate', $arResult['specialDate']);
}
