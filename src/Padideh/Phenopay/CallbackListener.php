<?php namespace Padideh\Phenopay;

use Padideh\Phenopay;

abstract class CallbackListener implements CallbackListenerInterface {

    function getSilencer(): string {
        return null;
    }

    function getAutoVerifyToken(): string {
        return null;
    }

    public function onException(Phenopay $phenopay, \Exception $exception) {

    }

    public function onVerify(Phenopay $phenopay, VerifyResponse $response) {

    }
}