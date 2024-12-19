<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\Engine\CurrentUser;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
    die();
}

if ($arResult['CANONICAL']) {
    $APPLICATION->SetPageProperty('canonical', $arResult['CANONICAL']);
}

if ($_GET['ID'] && Loader::includeModule('iblock') && $_GET['RESULT'] != 'true') {
    $curUser = CurrentUser::get();
    $curUserId = $curUser->getId();

    if ($curUserId) {
        $userReport = $curUserId . " (" . $curUser->getLogin() . ") " . $curUser->getFullName();
    } else {
        $userReport = Loc::getMessage('NEW_REPORT_NONE_USER_AUTORIZE');
    }

    $FieldList = [
        'IBLOCK_ID' => IBLOCK_ID_REPORTS,
        'NAME' => Loc::getMessage('NEW_REPORT_NEWS') . $_GET['ID'],
        'ACTIVE_FROM' => ConvertTimeStamp(time(), "FULL"),
        'PROPERTY_VALUES' => [
            'USER_REPORT' => $userReport,
            'NEWS_REPORT' => $_GET['ID'],
        ],
    ];

    $newReport = new CIBlockElement();
    if ($iElementId = $newReport->Add($FieldList)) {
        $idNewReport['ID'] = $iElementId;

        if ($_GET['MODE_AJAX'] == 'true') {
            $APPLICATION->RestartBuffer();
            echo json_encode($idNewReport);
            exit;
        } 
        elseif ($_GET['MODE_AJAX'] == 'false') {
            LocalRedirect($APPLICATION->GetCurPage() . "?RESULT=true&ID=" . $idNewReport['ID']);
        }
    }
}
