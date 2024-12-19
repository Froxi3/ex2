<?php

namespace Sprint\Migration;


class test20241122114828 extends Version
{
    protected $author = "admin";

    protected $description = "";

    protected $moduleVersion = "4.12.6";

    public function up()
    {

        $userList = [];
        $userList = \Bitrix\Main\UserTable::getList([
            'select'=>['ID', 'LOGIN', 'UF_*'],
        ])->fetchAll();

        $iblock = \Bitrix\Iblock\IblockTable::getList([
        'filter' => ['=CODE' => 'furniture_products_s1'], // Фильтруем по символьному коду
        'select' => ['ID'], // Получаем только ID инфоблока
        ])->fetch();

        if ($iblock) {
        // Получаем список разделов инфоблока по ID
            $sectionList = [];
            $rsSection = \Bitrix\Iblock\SectionTable::getList([
                'select' => ['ID', 'NAME'],
                'filter' => ['=IBLOCK_ID' => $iblock],
            ]);

            while ($arSection = $rsSection->fetch()) {
                $sectionList[$arSection['ID']] = $arSection['ID'];
            }
        }
        $user = new \CUser;
        foreach ($userList as $key => $value) {
            if (in_array($value['UF_AUTHOR_TYPE'], $sectionList)) {
                echo "<pre>", print_r($value), "</pre>";
            }
            else {

                $user->Update($value['ID'], [
                    'UF_AUTHOR_TYPE' => 2
                ]);
            }
        }
    }

    public function down()
    {
        //your code ...
    }
}
