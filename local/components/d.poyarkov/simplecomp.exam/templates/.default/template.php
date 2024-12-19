<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
	die();
}

// echo '<pre>', print_r($arResult), '</pre>';
use Bitrix\Main\Localization\Loc;
?>
<b><?=Loc::getMessage('CATALOG')?></b>
<ul>
	<?foreach ($arResult['NEWS'] as $news):?>
		<li>
			<b>
				<?=$news['NAME'] . ' - '?> 
			</b>
			<?=$news['DATE_ACTIVE_FROM']?>
			<?= '(' . implode(', ', $news['PROD_SECTIONS_NAME']) . ')'?>
		</li>
		<ul>
			<?foreach ($news['PRODUCTS'] as $prod):?>
				<li>
					<?=$prod['NAME'] . ' - ' . $prod['PROPERTY_PRICE_VALUE'] . ' - ' . 
						$prod['PROPERTY_MATERIAL_VALUE'] . ' - ' . $prod['PROPERTY_ARTNUMBER_VALUE']?>
				</li>
			<?endforeach;?>
		</ul>
	<?endforeach;?>	
</ul>
