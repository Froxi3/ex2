<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
	die();
}
use Bitrix\Main\Localization\Loc;
?>

<div>
	<span><?=Loc::getMessage('FILTER')?></span>
	<?$linkFilter = $APPLICATION->getCurPage() . '?F=Y';?>
	<a href="<?=$linkFilter?>"><?echo $linkFilter;?></a>
</div>
<br>
<?= Loc::getMessage('TIME', ['#TIME#' => time()]) ?>
<br>
<b><?=Loc::getMessage('CATALOG')?></b>
<?
$this->AddEditAction('add_element', $arResult['ADD_LINK'], CIBlock::GetArrayByID(2, "ELEMENT_ADD"));
?>
<ul id='<?=$this->GetEditAreaId('add_element');?>'>
	<?foreach ($arResult['COMPANY'] as $key => $company):?>
	<li>
		<b><?=$company['NAME']?></b>
		<ul>
			<?foreach ($company['PRODUCTS'] as $key => $prod):?>
				<?
				$this->AddEditAction($company['ID'] . $prod['ID'], $prod['EDIT_LINK'], 
					CIBlock::GetArrayByID($prod["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($company['ID'] . $prod['ID'], $prod['DELETE_LINK'], 
					CIBlock::GetArrayByID($prod["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				?>

			<li id="<?=$this->GetEditAreaId($company['ID'] . $prod['ID']);?>">
				<?=$prod['NAME'] . ' - ' . $prod['PROPERTY_PRICE_VALUE'] . ' - ' . $prod['PROPERTY_MATERIAL_VALUE'] . 
				'(' . $prod['DETAIL_PAGE_URL'] . ')'?>
			</li>
			<?endforeach;?>
		</ul>
	</li>
	<?endforeach;?>
</ul>
<br/><?=$arResult["NAV_STRING"]?>
