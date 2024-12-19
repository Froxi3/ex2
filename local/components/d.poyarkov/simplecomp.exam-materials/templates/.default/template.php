<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;
?>

<p><b><?=Loc::getMessage('SIMPLECOMP_EXAM2_CAT_TITLE')?></b></p>

<ul>
    <?foreach ($arResult['ITEMS'] as $userKey => $userVal):?>
        <li>
            <?='[' . $userVal['ID'] . ']' . ' - ' . $userVal['LOGIN']?>
            <ul>
            <?foreach ($userVal['NEWS'] as $newsKey => $newsVal):?>
                <li>
                    <?=' - ' . $newsVal['NAME']?>
                </li>                
            <?endforeach;?>
            </ul>
        </li>
    <?endforeach;?>
</ul>
