<?php namespace Padideh\Phenopay;

use Padideh\Phenopay;

/**
 * @property-read bool $success
 * Class Response
 * @package Padideh\Phenopay
 */
class Response {
    /** @var array */
    protected $_response_data = [];
    /** @var Phenopay */
    private $_phenopayInstance;

    /**
     * Response constructor.
     * @param array $response
     * @param Phenopay $phenopayInstance
     */
    public function __construct(array $response, $phenopayInstance) {
        $this->_response_data    = $response;
        $this->_phenopayInstance = $phenopayInstance;
    }


    public function __get($name) {
        return isset($this->_response_data[$name]) ? $this->_response_data[$name] : null;
    }

    public function __set($name, $value) {
        $this->_response_data[$name] = $value;
    }

    /**
     * @return Phenopay
     */
    public function getPhenopayInstance() {
        return $this->_phenopayInstance;
    }

}