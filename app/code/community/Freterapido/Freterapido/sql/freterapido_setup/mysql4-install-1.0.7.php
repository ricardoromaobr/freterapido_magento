<?php

/**
 * @category Freterapido
 * @package Freterapido_Freterapido
 * @author freterapido.com <suporte@freterapido.com>
 * @copyright Frete Rápido (https://freterapido.com)
 * @license https://github.com/freterapido/freterapido_magento/blob/master/LICENSE MIT
*/
$installer = $this;

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$installer->startSetup();

//adiciona o atributo 'fr_category' no cadastro de categorias da loja, para que as categorias do frete rápido possam ser vinculadas à loja

$setup->addAttribute('catalog_category', 'fr_category', array(
    'group' => 'General Information',
    'input' => 'select',
    'type' => 'int',
    'label' => 'Categoria no Frete Rápido',
    'backend' => '',
    'visible' => true,
    // 'position' => 1,
    'required' => true,
    'source' => 'freterapido/source_frcategory',
    'user_defined' => true,
    'apply_to' => 'simple,bundle,grouped,configurable',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
));

// adiciona o atributo 'fr_volume_comprimento' no cadastro de produtos da loja, para realização do cálculo do frete
$setup->addAttribute('catalog_product', 'fr_volume_comprimento', array(
    'position' => 1,
    'required' => 1,
    'label'    => 'Comprimento (cm)',
    'type'     => 'int',
    'input'    => 'text',
    'apply_to' => 'simple,bundle,grouped,configurable',
    'note'     => 'Comprimento da embalagem do produto (Para cálculo do frete)'
));

// adiciona o atributo 'fr_volume_altura' no cadastro de produtos da loja, para realização do cálculo do frete
$setup->addAttribute('catalog_product', 'fr_volume_altura', array(
    'position' => 1,
    'required' => 1,
    'label'    => 'Altura (cm)',
    'type'     => 'int',
    'input'    => 'text',
    'apply_to' => 'simple,bundle,grouped,configurable',
    'note'     => 'Altura da embalagem do produto (Para cálculo do frete)'
));

// adiciona o atributo 'fr_volume_largura' no cadastro de produtos da loja, para realização do cálculo do frete
$setup->addAttribute('catalog_product', 'fr_volume_largura', array(
    'position' => 1,
    'required' => 1,
    'label'    => 'Largura (cm)',
    'type'     => 'int',
    'input'    => 'text',
    'apply_to' => 'simple,bundle,grouped,configurable',
    'note'     => 'Largura da embalagem do produto (Para cálculo do frete)'
));

// adiciona o atributo 'fr_volume_prazo_fabricacao' no cadastro de produtos da loja, para realização do cálculo do frete
$setup->addAttribute('catalog_product', 'fr_volume_prazo_fabricacao', array(
    'position' => 1,
    'required' => false,
    'label'    => 'Prazo de fabricação (dias)',
    'type'     => 'int',
    'input'    => 'text',
    'apply_to' => 'simple,bundle,grouped,configurable',
    'note'     => 'Será acrescido no prazo do frete'
));

// adiciona a tab 'Frete Rápido' no cadastro de produtos da loja
$setIds = $setup->getAllAttributeSetIds('catalog_product');

$attributes = array(
    'fr_volume_comprimento',
    'fr_volume_altura',
    'fr_volume_largura',
    'fr_volume_prazo_fabricacao'
);

foreach ($setIds as $setId) {
    $setup->addAttributeGroup('catalog_product', $setId, 'Frete Rápido', 2);
    $groupId = $setup->getAttributeGroupId('catalog_product', $setId, 'Frete Rápido');

    foreach ($attributes as $attribute) {
        $attributeId = $setup->getAttributeId('catalog_product', $attribute);
        $setup->addAttributeToGroup('catalog_product', $setId, $groupId, $attributeId);
    }
}

$installer->endSetup();
