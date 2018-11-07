<?php namespace Padideh\Phenopay;

abstract class SuccessResponse extends Response {
    public function __construct(array $response, $phenopayInstance) {
        parent::__construct($response, $phenopayInstance);

        foreach($this->fieldsToVerify() as $field)
            if(is_null($this->{$field}))
                throw new PhenopayException('Invalid response structure');

    }

    /**
     * @return string[]
     */
    abstract protected function fieldsToVerify();


}