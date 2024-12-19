<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Простой компонент ex2-97");
?><?$APPLICATION->IncludeComponent(
	"d.poyarkov:simplecomp.exam-materials",
	"",
	Array(
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"IBLOCK_PROPERTY_AUTHOR" => "PROPERTY_AUTHOR ",
		"ID_IBLOCK_NEWS" => "1",
		"USER_PROPERTY_AUTHOR_TYPE" => "UF_AUTHOR_TYPE"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>