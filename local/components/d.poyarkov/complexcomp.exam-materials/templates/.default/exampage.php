<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//Вывести значения переменных
foreach ($arResult["VARIABLES"] as $key => $value):?>
    <div>
        <?=$key . " = " . $value;?>
    </div> 
<?endforeach;?>