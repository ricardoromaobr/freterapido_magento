<?php
/**
 * @category Freterapido
 * @package Freterapido_Freterapido
 * @author freterapido.com <suporte@freterapido.com>
 * @copyright Frete Rápido (https://freterapido.com)
 * @license https://github.com/freterapido/freterapido_magento/blob/master/LICENSE MIT
 */

class Freterapido_Freterapido_Model_Source_Categoriesfr extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    protected $_options = null;
    public function getAllOptions($withEmpty = false){
        if (is_null($this->_options)){
            $this->_options = array();

            $this->_options[] = array('label' => 'Abrasivos', value => 1);
            $this->_options[] = array('label' => 'Adubos / Fertilizantes', value => 2);
            $this->_options[] = array('label' => 'Alimentos', value => 3);
            $this->_options[] = array('label' => 'Artigos para Pesca', value => 4);
            $this->_options[] = array('label' => 'Auto Peças', value => 5);
            $this->_options[] = array('label' => 'Bebidas / Destilados', value => 6);
            $this->_options[] = array('label' => 'Brindes', value => 7);
            $this->_options[] = array('label' => 'Brinquedos', value => 8);
            $this->_options[] = array('label' => 'Calçados', value => 9);
            $this->_options[] = array('label' => 'CD / DVD / Blu-Ray', value => 10);
            $this->_options[] = array('label' => 'Combustíveis / Óleos', value => 11);
            $this->_options[] = array('label' => 'Confecção', value => 12);
            $this->_options[] = array('label' => 'Cosméticos / Perfumaria', value => 13);
            $this->_options[] = array('label' => 'Couro', value => 14);
            $this->_options[] = array('label' => 'Derivados Petróleo', value => 15);
            $this->_options[] = array('label' => 'Descartáveis', value => 16);
            $this->_options[] = array('label' => 'Editorial', value => 17);
            $this->_options[] = array('label' => 'Eletrônicos', value => 18);
            $this->_options[] = array('label' => 'Eletrodomésticos', value => 19);
            $this->_options[] = array('label' => 'Embalagens', value => 20);
            $this->_options[] = array('label' => 'Explosivos / Pirotécnicos', value => 21);
            $this->_options[] = array('label' => 'Farmacêutico / Medicamentos', value => 22);
            $this->_options[] = array('label' => 'Ferragens', value => 23);
            $this->_options[] = array('label' => 'Ferramentas', value => 24);
            $this->_options[] = array('label' => 'Fibras Ópticas', value => 25);
            $this->_options[] = array('label' => 'Fonográfico', value => 26);
            $this->_options[] = array('label' => 'Fotográfico', value => 27);
            $this->_options[] = array('label' => 'Fraldas / Geriátricas', value => 28);
            $this->_options[] = array('label' => 'Higiene / Limpeza', value => 29);
            $this->_options[] = array('label' => 'Impressos', value => 30);
            $this->_options[] = array('label' => 'Informática / Computadores', value => 31);
            $this->_options[] = array('label' => 'Instrumento Musical', value => 32);
            $this->_options[] = array('label' => 'Livro(s)', value => 33);
            $this->_options[] = array('label' => 'Materiais Escolares', value => 34);
            $this->_options[] = array('label' => 'Materiais Esportivos', value => 35);
            $this->_options[] = array('label' => 'Materiais Frágeis', value => 36);
            $this->_options[] = array('label' => 'Material de Construção', value => 37);
            $this->_options[] = array('label' => 'Material de Irrigação', value => 38);
            $this->_options[] = array('label' => 'Material Elétrico / Lâmpada(s)', value => 39);
            $this->_options[] = array('label' => 'Material Gráfico', value => 40);
            $this->_options[] = array('label' => 'Material Hospitalar', value => 41);
            $this->_options[] = array('label' => 'Material Odontológico', value => 42);
            $this->_options[] = array('label' => 'Material Pet Shop / Rações', value => 43);
            $this->_options[] = array('label' => 'Material Veterinário', value => 44);
            $this->_options[] = array('label' => 'Móveis / Utensílios', value => 45);
            $this->_options[] = array('label' => 'Moto Peças', value => 46);
            $this->_options[] = array('label' => 'Mudas / Plantas', value => 47);
            $this->_options[] = array('label' => 'Papelaria / Documentos', value => 48);
            $this->_options[] = array('label' => 'Perfumaria', value => 49);
            $this->_options[] = array('label' => 'Material plástico', value => 50);
            $this->_options[] = array('label' => 'Pneus e Borracharia', value => 51);
            $this->_options[] = array('label' => 'Produtos Cerâmicos', value => 52);
            $this->_options[] = array('label' => 'Produtos Químicos', value => 53);
            $this->_options[] = array('label' => 'Produtos Veterinários', value => 54);
            $this->_options[] = array('label' => 'Revistas', value => 55);
            $this->_options[] = array('label' => 'Sementes', value => 56);
            $this->_options[] = array('label' => 'Suprimentos Agrícolas / Rurais', value => 57);
            $this->_options[] = array('label' => 'Têxtil', value => 58);
            $this->_options[] = array('label' => 'Vacinas', value => 59);
            $this->_options[] = array('label' => 'Vestuário', value => 60);
            $this->_options[] = array('label' => 'Vidros / Frágil', value => 61);
            $this->_options[] = array('label' => 'Cargas refrigeradas/congeladas', value => 62);
            $this->_options[] = array('label' => 'Papelão', value => 63);
            $this->_options[] = array('label' => 'Outros', value => 999);
        }

        $options = $this->_options;

        if ($withEmpty) {
            array_unshift($options, array('value'=>'', 'label'=>''));
        }

        return $options;
    }

    public function getOptionText($value)
    {
        $options = $this->getAllOptions(false);

        foreach ($options as $item) {
            if ($item['value'] == $value) {
                return $item['label'];
            }
        }
        return false;
    }
}