<?php namespace Padideh\Phenopay;
/**
 * @property-read $token
 * Class GetTokenResponse
 * @package Padideh\Phenopay
 */
class GetTokenResponse extends SuccessResponse {

    /**
     * @return string[]
     */
    protected function fieldsToVerify() {
        return ['token'];
    }

    /**
     * @return string
     */
    public function getPayLink() {
        return $this->getPhenopayInstance()->getPayURL() . $this->token;
    }

}