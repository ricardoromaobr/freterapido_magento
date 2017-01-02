<?php

/**
 * @category Freterapido
 * @package Freterapido_Freterapido
 * @author freterapido.com <suporte@freterapido.com>
 * @copyright Frete Rápido (https://freterapido.com)
 * @license http://opensource.org/licenses/MIT MIT
 */

class Freterapido_Freterapido_Model_Observer extends Mage_Core_Model_Abstract
{
    protected $_sender = array(
        'cnpj' => null,
        'inscricao_estadual' => null,
        'endereco' => array(
            'cep' => null
        )
    );

    protected $_receiver = array(
        'tipo_pessoa' => 1,
        'endereco' => array(
            'cep' => null
        )
    );

    protected $_carrier = array(
        'transportadora' => null,
        'valor' => 0
    );

    protected $volumes = array();

    public function quote($observer)
    {
        $shipment = $observer->getEvent()->getShipment();

        $order = $shipment->getOrder();

        $address = $order->getShippingAddress();

        Mage::log($address->debug(), null, 'order.log', true);

//        try {
//            $this->_getSender();
//
//            $this->_getReceiver($order);

//            $items = $order->getAllItems();
//
//            $this->_getVolumes($items);
//
//            $this->_doQuote();
//
//        } catch (Exception $e) {
//            $this->_log($e->getMessage());
//        }

    }

    /**
     * Obtém os dados da origem
     */
    protected function _getSender()
    {
        try {
            Mage::getStoreConfig('shipping/origin', $this->getStore());

            $this->_sender = array();
            $this->_sender['cnpj'] = $this->getConfigData('shipper_cnpj');
            $this->_sender['inscricao_estadual'] = $this->getConfigData('shipper_ie');
            // Pega o CEP da configuração da loja
            $this->_sender['endereco']['cep'] = $this->_formatZipCode();
        } catch (Exception $e) {
            $this->_log('Erro ao tentar obter os dados de origem. Erro: ' . $e->getMessage());
        }
    }

    /**
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return bool
     */
    protected function _getReceiver($order)
    {
        try {
            $cnpj_cpf = preg_replace("/\D/", '', $order->getData('customer_taxvat'));

            $name = $order->getShippingAddress()->getData('firstname') .
                ' ' .
                $order->getShippingAddress()->getData('lastname');

            $this->_receiver = array();
            $this->_receiver['tipo_pessoa'] = 1;
            $this->_receiver['cnpj_cpf'] = $cnpj_cpf;
            $this->_receiver['nome'] = $name;
            $this->_receiver['email'] = $order->getShippingAddress()->getData('email');
            $this->_receiver['telefone'] = $order->getShippingAddress()->getData('telephone');

            $this->_receiver['endereco']['cep'] = $this->_formatZipCode($order->getShippingAddress()->getData('postcode'));
            $this->_receiver['endereco']['rua'] = $order->getShippingAddress()->getData('street');

        } catch (Exception $e) {
            $this->_log('Erro ao tentar obter os dados de origem. Erro: ' . $e->getMessage());
        }
    }

    /**
     * Formata e valida o CEP informado
     *
     * @param string $zipcode
     * @return boolean|string
     */
    protected function _formatZipCode($zipcode)
    {
        $new_zipcode = preg_replace("/\D/", '', trim($zipcode));

        if (strlen($new_zipcode) !== 8) {
            //CEP está errado
            $this->_log('O CEP digitado é inválido');

            return false;
        }

        return $new_zipcode;
    }

    protected function _doQuote()
    {
        // Dados que serão enviados para a API do Frete Rápido
        $request_data = array(
            'remetente' => $this->_sender,
            'destinatario' => $this->_receiver,
            'volumes' => $this->_volumes,
            'correios' => $this->_correios,
            'tipo_cobranca' => $this->_billing_type,
            'tipo_frete' => $this->_freight_type,
            'ecommerce' => $this->_ecommerce,
            'token' => $this->_token
        );

        // Adiciona o filtro caso tenhas sido selecionado
        if ($this->_filter)
            $request_data['filtro'] = (int)$this->_filter;

        // Adiciona o limite de ofertas disponíveis caso tenhas sido selecionado
        if ($this->_limit)
            $request_data['limite'] = (int)$this->_limit;

        $config = array(
            'adapter' => 'Zend_Http_Client_Adapter_Curl',
            'curloptions' => array(
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER, false
            ),
        );

        // Configura o cliente http passando a URL da API e a configuração
        $client = new Zend_Http_Client($this->getConfigData('api_simulation_url'), $config);

        // Adiciona os parâmetros à requisição
        $client->setRawData(json_encode($request_data), 'application/json');

        // Realiza a chamada POST
        $response = $client->request('POST');

        if ($response->getStatus() != 200) {
            $this->_log('Erro ao tentar se comunicar com a API - Code: ' . $response->getStatus() . '. Error: ' . $response->getMessage());
            return false;
        }

        return $this;
    }

    /**
     * Prepara os volumes para serem enviados à API
     *
     * @param $items
     */
    protected function _getVolumes($items)
    {
        $volumes = array();

        foreach ($items as $item) {

            $sku = $item->getProduct()->getSku();

            // O mesmo produto tem item pai e item filho. No item pai não existe informações sobre as medidas.
            // Já no item filho não possui informações como preço e quantidade no carrinho
            // Verifica se é item pai ou filho para pegar as informações necessárias de cada tipo e montar o volume
            if (!$item->getParentItemId()) {

                // Recupera os ids das categorias relacionadas ao produto
                $categories_ids = $item->getProduct()->getCategoryIds();
                $categories = array();

                foreach ($categories_ids as $id) {
                    $_category = Mage::getModel('catalog/category')->load($id);
                    $_level = $_category->getData('level');

                    // Verifica se o Model de categoria não está vazio e se a categoria do FR foi definida para o produto
                    if (!empty($_category) && !empty($_category->getData('fr_category'))) {
                        $categories[$_level] = $_category->getData('fr_category');
                    }
                }

                // Ordena para que a categoria com level maior (mais específica) fique na primeira posição
                krsort($categories);

                // Verifica se a categoria foi encontrada e inserida no array, remove as chaves e extrai o primeiro item do array
                $type = is_array($categories) && !empty(array_values($categories)[0]) ?
                    array_values($categories)[0] : $this->getConfigData('tipo_padrao');

                $quantity = $item->getQty();
                $weight = (float)$item->getWeight() * $quantity;
                $value = (float)$item->getBasePrice() * $quantity;

                $volumes[$sku]['tipo'] = (int)$type;
                $volumes[$sku]['quantidade'] = (int)$quantity;
                $volumes[$sku]['peso'] = $this->_weightVerify($weight);
                $volumes[$sku]['valor'] = $value;

            } else {
                $product_child = $item->getProduct();

                $height = !empty($product_child->getData('fr_volume_altura')) ?
                    $product_child->getData('fr_volume_altura') : $this->getConfigData('altura_padrao');

                $width = !empty($product_child->getData('fr_volume_largura')) ?
                    $product_child->getData('fr_volume_largura') : $this->getConfigData('largura_padrao');

                $lenght = !empty($product_child->getData('fr_volume_comprimento')) ?
                    $product_child->getData('fr_volume_comprimento') : $this->getConfigData('comprimento_padrao');

                if ($product_child->getData('fr_volume_prazo_fabricacao') > $this->_manufacturing_time)
                    $this->_manufacturing_time = $product_child->getData('fr_volume_prazo_fabricacao');

                $volumes[$sku]['sku'] = $sku; // Converte para metros
                $volumes[$sku]['altura'] = (float)$height / 100; // Converte para metros
                $volumes[$sku]['largura'] = (float)$width / 100; // Converte para metros
                $volumes[$sku]['comprimento'] = (float)$lenght / 100; // Converte para metros
            }
        }

        // Define o array sem os Sku como chave
        $this->_volumes = array_values($volumes);
    }

    /**
     * Verifica se o peso está definido em gramas ou quilos e converte, se necessário
     *
     * @param $weight
     * @return float|int
     */
    protected function _weightVerify($weight)
    {
        if ($this->getConfigData('weight_type') == Freterapido_Freterapido_Model_Source_WeightType::WEIGHT_IN_GR) {
            $new_weight = $weight * 1000;
        } else {
            $new_weight = $weight;
        }

        return (float)number_format($new_weight, 2);
    }

    /**
     * Armazena no log a mensagem informada
     *
     * @param string $mensagem
     */
    protected function _log($mensagem)
    {
        Mage::log('Frete Rápido: Shipment observer error - ' . $mensagem);
    }
}