<?php

namespace Sprint\Migration;


class Version20240924133754 extends Version
{
    protected $author = "admin";

    protected $description = "Разделы продукции";

    protected $moduleVersion = "4.12.6";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();

        $iblockId = $helper->Iblock()->getIblockIdIfExists(
            'furniture_products_s1',
            'products'
        );

        $helper->Iblock()->addSectionsFromTree(
            $iblockId,
            array (
  0 => 
  array (
    'NAME' => 'Мягкая мебель',
    'CODE' => '',
    'SORT' => '100',
    'ACTIVE' => 'Y',
    'XML_ID' => '2',
    'DESCRIPTION' => 'Диваны, кресла и прочая мягкая мебель',
    'DESCRIPTION_TYPE' => 'html',
    'UF_NEWS_LINK' => 
    array (
      0 => 1,
      1 => 2,
    ),
  ),
  1 => 
  array (
    'NAME' => 'Офисная мебель',
    'CODE' => '',
    'SORT' => '200',
    'ACTIVE' => 'Y',
    'XML_ID' => '3',
    'DESCRIPTION' => 'Диваны, столы, стулья',
    'DESCRIPTION_TYPE' => 'html',
    'UF_NEWS_LINK' => 
    array (
      0 => 2,
      1 => 3,
    ),
  ),
  2 => 
  array (
    'NAME' => 'Мебель для кухни',
    'CODE' => '',
    'SORT' => '300',
    'ACTIVE' => 'Y',
    'XML_ID' => '4',
    'DESCRIPTION' => 'Полки, ящики, столы и стулья',
    'DESCRIPTION_TYPE' => 'html',
    'UF_NEWS_LINK' => 
    array (
      0 => 2,
      1 => 3,
    ),
  ),
  3 => 
  array (
    'NAME' => 'Детская мебель',
    'CODE' => '',
    'SORT' => '400',
    'ACTIVE' => 'Y',
    'XML_ID' => '5',
    'DESCRIPTION' => 'Кровати, стулья, мягкая детская мебель',
    'DESCRIPTION_TYPE' => 'html',
    'UF_NEWS_LINK' => 
    array (
      0 => 1,
      1 => 3,
    ),
  ),
  4 => 
  array (
    'NAME' => 'Офисная мебель2',
    'CODE' => '',
    'SORT' => '500',
    'ACTIVE' => 'Y',
    'XML_ID' => NULL,
    'DESCRIPTION' => 'Диваны, столы, стулья',
    'DESCRIPTION_TYPE' => 'html',
    'UF_NEWS_LINK' => 
    array (
    ),
  ),
)        );
    }
}
