<?php namespace Padideh\Phenopay;

use Padideh\Phenopay;

interface CallbackListenerInterface {

    function getSilencer(): string;

    function getAutoVerifyToken(): string;

    function onError(Phenopay $phenopay, ErrorResponse $error);

    function onSuccess(Phenopay $phenopay, CallbackResult $result);

    function onVerify(Phenopay $phenopay, VerifyResponse $response);

    function onException(Phenopay $phenopay, \Exception $exception);
}