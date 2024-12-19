<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Экзамен2");
?><?$APPLICATION->IncludeComponent(
	"d.poyarkov:simplecomp.exam-71",
	"",
	Array(
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"DETAIL_PAGE_TEMPLATE" => "/catalog_exam/#SECTION_ID#/#ELEMENT_CODE#/",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"ELEMENTS_COUNT" => "2",
		"IBLOCK_ID_CATALOG_PRODUCTS" => "2",
		"IBLOCK_ID_CLASSIFIER" => "7",
		"LINK_CLASSIFIER" => "PROPERTY_COMPANY",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => ".default",
		"PAGER_TITLE" => "Фирмы",
		"TEMPLATE_LINK_DETAIL_PRODUCT" => "/"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>