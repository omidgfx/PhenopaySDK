<?php namespace Padideh\Phenopay;


/**
 * @property-read array $transaction
 * Class TransactionalResponse
 * @package Padideh\Phenopay
 */
abstract class TransactionalResponse extends SuccessResponse {
    public function __construct(array $response, $phenopayInstance) {
        parent::__construct($response, $phenopayInstance);

        # parse transaction
        if(!isset($this->transaction['paymentService']))
            throw new PhenopayException('Invalid transaction response');
    }

    /**
     * @return string[]
     */
    protected function fieldsToVerify() {
        return ['transaction'];
    }
}
