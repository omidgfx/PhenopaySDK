<?php namespace Padideh\Phenopay;

class CallbackResult extends SuccessResponse {
    /**
     * @return string[]
     */
    protected function fieldsToVerify() {
        return [];
    }
}