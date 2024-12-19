<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
	die();
}

if ($arParams['USE_SPECIAL_DATE'] == 'Y') {
	$component = $this->__component;

	if (is_object($component))
	{
		$arResult['specialDate'] = $arResult['ITEMS'][0]['ACTIVE_FROM'];
		$component->SetResultCacheKeys(['specialDate']);	
	}
}
