    <?php

/**
 * @category Freterapido
 * @package Freterapido_Freterapido
 * @author freterapido.com <suporte@freterapido.com>
 * @copyright Frete Rápido (https://freterapido.com)
 * @license http://opensource.org/licenses/MIT MIT
 */

class Freterapido_Freterapido_Model_Carrier_Freterapido
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'freterapido';

    protected $_result = null;

    protected $_title = 'Frete Rápido';

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

    protected $_billing_type = 1;
    protected $_freight_type = 1;
    protected $_ecommerce = true;

    protected $_correios = array(
        'valor_declarado' => false,
        'mao_propria' => false,
        'aviso_recebimento' => false
    );

    protected $_carriers = array();

    protected $_handling_fee = 0;
    protected $_leadtime = 0; // Adiciona ao prazo de entrega a quantidade de dias para postagem
    protected $_limit = 0;
    protected $_filter = 0;
    protected $_token = null;

    protected $_post_cost = 0;

    protected $_manufacturing_time = 0; // Maior tempo de fabricação entre os produtos selecionados
    protected $_volumes = array();

    protected $_shipping_methods = array();

    // Função chamada pelo Magento para calcular frete
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {

        try {
            // Realiza a checagem inicial
            if (!$this->_initialCheck($request)) {
                return false;
            }

            // Recupera o valor dos produtos que estão no carrinho.
            $this->_result = Mage::getModel('shipping/rate_result');

            $this->_handling_fee = $this->getConfigData('handling_fee');
            $this->_leadtime = $this->getConfigData('leadtime');
            $this->_limit = $this->getConfigData('limit');
            $this->_filter = $this->getConfigData('filter');
            $this->_token = $this->getConfigData('token');

            // Obtém a configuração dos coreios
            $this->_getCorreiosConfig();

            // Obtém os volumes
            $this->_getVolumes($request);

            // Realiza a cotação
            $this->_getQuoteApi();

            // Retorna o XML com o resultado para o Magento.
            return $this->_result;
        } catch (Exception $e) {
            $this->_throwError('apierror', $e->getMessage() . ' - [' . __LINE__ . '] ' . $e->getFile());
        }
    }

    public function getAllowedMethods()
    {
        return array($this->_code => $this->_title);
    }

    /**
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return bool
     */
    protected function _initialCheck(Mage_Shipping_Model_Rate_Request $request)
    {
        // Verifica se o módulo está ativo
        if (!$this->getConfigFlag('active')) {
            $this->_log('Desabilitado');
            return false;
        }

        // Verifica se origem e destino estão dentro da área de atuação do Frete Rápido
        $origin_country = Mage::getStoreConfig('shipping/origin/country_id', $this->getStore());
        $destination_country = $request->getDestCountryId();

        if ($origin_country != 'BR' || $destination_country != 'BR') {
            $this->_log('Fora da área de atuação do Frete Rápido');
            return false;
        }

        if (!$this->_checkZipCode($request))
            return false;

        return true;
    }

    /**
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return bool
     */
    protected function _checkZipCode(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->_getSender()) {
            $this->_log('Não foi possível recuperar os dados de origem.');

            return false;
        }

        if (!$this->_getReceiver($request)) {
            $this->_log('Não foi possível recuperar os dados do destinatário.');

            return false;
        }

        return true;
    }

    /**
     * Obtém os dados da origem
     */
    protected function _getSender()
    {
        $this->_sender = array();
        $this->_sender['cnpj'] = $this->getConfigData('shipper_cnpj');
        $this->_sender['inscricao_estadual'] = $this->getConfigData('shipper_ie');
        // Pega o CEP da configuração da loja
        $this->_sender['endereco']['cep'] = $this->_formatZipCode(Mage::getStoreConfig('shipping/origin/postcode', $this->getStore()));

        return true;
    }

    protected function _getReceiver(Mage_Shipping_Model_Rate_Request $request)
    {
        $this->_receiver = array();
        // Recupera o CEP digitado pelo usuário
        $this->_receiver['tipo_pessoa'] = 1;
        $this->_receiver['endereco']['cep'] = $this->_formatZipCode($request->getDestPostcode());

        return true;
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

    /**
     * Obtém as configurações dos correios definidas na configuração do módulo
     */
    protected function _getCorreiosConfig()
    {
        $this->_correios = array();

        $this->_correios['valor_declarado'] = (boolean)$this->getConfigData('correios_valor_declarado');
        $this->_correios['mao_propria'] = (boolean)$this->getConfigData('correios_mao_propria');
        $this->_correios['aviso_recebimento'] = (boolean)$this->getConfigData('correios_aviso_recebimento');
    }

    /**
     * Realiza a cotação na API do Frete Rápido
     */
    protected function _getQuoteApi()
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

        $this->_log('Realizando cotação...');

        // Realiza a chamada POST
        $response = $client->request('POST');

        if ($response->getStatus() != 200) {
            $this->_throwError('apierror', 'Erro ao tentar se comunicar com a API - Code: ' . $response->getStatus() . '. Error: ' . $response->getMessage());
            return $this->_result;
        }

        $response = json_decode($response->getBody());
        $this->_carriers = isset($response->transportadoras) ? $response->transportadoras : [];

        $this->_log('Foram retornadas ' . count($this->_carriers) . ' Transportadoras na consulta');

        // Se não retornar nenhuma transportadora na chamada, retorna o resultado vazio
        if (empty($this->_carriers))
            return $this->_result;

        foreach ($this->_carriers as $carrier) {
            if (empty($carrier))
                continue;

            $this->_appendShippingReturn($carrier);
        }
    }

    /**
     * Adiciona o retorno de cada Transportadora no resultado
     *
     * @param $carrier
     */
    protected function _appendShippingReturn($carrier)
    {
        if ($carrier->nome == 'Correios')
            $carrier->nome = strtoupper($carrier->nome . ' - ' . $carrier->servico);

        // Gera um nome para o método de cada transportadora
        $shipping_method = preg_replace("/\W/", '', strtolower($carrier->nome));

        // Se o nome para o método já existir, é acrescentado um valor para diferenciá-lo
        if (in_array($shipping_method, $this->_shipping_methods))
            $shipping_method = $shipping_method . '-' . number_format($carrier->preco, 2, '', '');

        // Adiciona o nome do método na lista
        $this->_shipping_methods[] = $shipping_method;

        /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
        $method = Mage::getModel('shipping/rate_result_method');

        $method->setCarrier($this->_code);
        $method->setMethod($shipping_method);

        $deadline = $carrier->prazo_entrega + $this->_leadtime + $this->_manufacturing_time;
        $deadline_msg = $deadline > 1 ? 'dias úteis' : 'dia útil';

        $method->setMethodTitle(sprintf($this->getConfigData('msgprazo'),
            $carrier->nome, $deadline, $deadline_msg));

        $shipping_price = $carrier->preco + $this->_handling_fee + $this->_post_cost;
        // Diz ao Magento qual será o valor do frete
        $method->setPrice($shipping_price);
        // Diz qual será o custo do frete para a loja. Esta informação não é exibida
        $method->setCost($carrier->preco);

        $this->_result->append($method);
    }

    /**
     * Prepara os volumes para serem enviados à API
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     */
    protected function _getVolumes(Mage_Shipping_Model_Rate_Request $request)
    {
        $volumes = array();

        foreach ($request->getAllItems() as $item) {

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
        Mage::log('Frete Rápido: ' . $mensagem);
    }

    /**
     * Prepara mensagem de erro para ser exibida
     *
     * @param $message
     * @param null $log
     */
    protected function _throwError($message, $log = null)
    {

        $this->_result = null;
        $this->_result = Mage::getModel('shipping/rate_result');

        // Recupera o model de erro da transportadora
        $error = Mage::getModel('shipping/rate_result_error');
        $error->setCarrier($this->_code);
        $error->setCarrierTitle($this->getConfigData('title'));

        // Armazena o erro no log do sistema
        $this->_log($log);
        $error->setErrorMessage($this->getConfigData($message));

        // Exibe o erro
        $this->_result->append($error);
    }
}