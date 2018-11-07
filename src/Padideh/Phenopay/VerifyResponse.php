<?php namespace Padideh\Phenopay;

/**
 * @property-read string $result
 * @property-read int $resultCode
 * @property-read string $token
 * @property-read int $amount TOMANS
 * @property-read int $dateCreated
 * @property-read int $dateReversed
 * Class ReverseResponse
 * @package Padideh\Phenopay
 */
class VerifyResponse extends TransactionalResponse {

    /**
     * @return string[]
     */
    protected function fieldsToVerify() {
        return ['result', 'resultCode', 'token', 'amount', 'dateCreated', 'dateVerified'];
    }

    /**
     * @return bool
     */
    public function already() {
        return $this->result == 'ALREADY_VERIFIED';
    }

}