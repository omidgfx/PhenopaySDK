<?php namespace Padideh;

use Padideh\Phenopay\CallbackListener;
use Padideh\Phenopay\CallbackResult;
use Padideh\Phenopay\ErrorResponse;
use Padideh\Phenopay\GetServicesResponse;
use Padideh\Phenopay\GetTokenResponse;
use Padideh\Phenopay\PhenopayException;
use Padideh\Phenopay\ReverseResponse;
use Padideh\Phenopay\VerifyResponse;

class Phenopay {

    //region Fields

    /** @var string */
    private $merchantSecret;

    private $baseURL = 'https://i.padidesoft.com/Phenopay/api/';
    private $payURL  = 'https://i.padidesoft.com/Phenopay/pay/';
    //endregion

    //region Constructor

    /**
     * Phenopay constructor.
     * @param string $merchantSecret
     */
    public function __construct($merchantSecret) { $this->merchantSecret = $merchantSecret; }

    //endregion

    //region Methods

    /**
     * ### Returns services with their availability
     * @return ErrorResponse|GetServicesResponse
     * @throws PhenopayException
     */
    public function getServices() {
        return $this->adaptResponse(
            $this->request('payment/getServices'),
            GetServicesResponse::class);
    }

    /**
     * @param int $amount Tomans >= 100
     * @param string $description 255 Chars maximum, utf-8 support
     * @param string $returnURL 1024 Chars max, URL, http:// or https:// only
     * @param string $silencer 16 Chars max. This makes http[s]://PHENOPAY/pay/{TOKEN} silence and every exceptions message will be passed into returnURL by returnURL[?|&]silencer=error_message.
     * @param string $paymentService 32 Chars max, use getServices result. By passing null or skipping this parameter, Phenopay will decide about which paymentService should be uses for this payment transaction.
     * @return ErrorResponse|GetTokenResponse
     * @throws PhenopayException
     */
    public function getToken($amount, $description, $returnURL, $silencer = null, $paymentService = null) {
        return $this->adaptResponse(
            $this->request('payment/getToken', compact('amount', 'description', 'returnURL', 'silencer', 'paymentService')),
            GetTokenResponse::class);
    }

    /**
     * @param string $token 16 Chars, Alphanumeric, Lowercase, Pattern="8-2-6"
     * @return ErrorResponse|ReverseResponse
     * @throws PhenopayException
     */
    public function reverse($token) {
        return $this->adaptResponse(
            $this->request('payment/reverse', compact('token')),
            ReverseResponse::class);
    }

    /**
     * @param string $token 16 Chars, Alphanumeric, Lowercase, Pattern="8-2-6"
     * @return ErrorResponse|VerifyResponse
     * @throws PhenopayException
     */
    public function verify($token) {
        return $this->adaptResponse(
            $this->request('payment/verify', compact('token')),
            VerifyResponse::class);
    }

    /**
     * @param string $silencer
     * @return null|CallbackResult|ErrorResponse
     * @throws PhenopayException
     */
    private function getCallbackResult($silencer = null) {
        if(isset($_GET['phenopay_success'])) {
            $phSuccess = intval($_GET['phenopay_success']);
            if($phSuccess === 1) { # seems like the clinet has paid
                return new CallbackResult(['success' => true], $this);
            } else {
                # there is an error
                $error = isset($_GET['phenopay_error'])
                    ? $_GET['phenopay_error']
                    : isset($_GET[$silencer])
                        ? $_GET[$silencer]
                        : 'Fatal Error.';
                $error = urldecode($error);
                return new ErrorResponse(['success' => false, 'error' => $error], $this);
            }

        }
        return null;
    }

    /**
     * @param CallbackListener $listener use anonymous classes: <code>new class extends <b>CallbackListener</b>{...}</code>
     */
    public function listenToCallback(CallbackListener $listener) {
        try {
            $callbackResult = $this->getCallbackResult($listener->getSilencer());
            if(!is_null($callbackResult)) {
                if($callbackResult instanceof CallbackResult) {
                    if(!is_null($token = $listener->getAutoVerifyToken())) {
                        $verifyResponse = $this->verify($token);
                        if($verifyResponse instanceof VerifyResponse)
                            $listener->onVerify($this, $verifyResponse);
                        else
                            $listener->onError($this, $verifyResponse);
                    }
                } else
                    $listener->onError($this, $callbackResult);
            }
        } catch(\Exception $exception) {
            $listener->onException($this, $exception);
        }

    }
    //endregion

    //region Setters & Getters

    /**
     * ### Sets base url
     * @param string $baseURL
     */
    public function setBaseURL($baseURL) {
        $this->baseURL = rtrim($baseURL, '/') . '/';
    }

    /**
     * @param string $payURL
     */
    public function setPayURL($payURL) {
        $this->payURL = rtrim($payURL, '/') . '/';
    }

    /**
     * @return string
     */
    public function getPayURL() {
        return $this->payURL;
    }
    //endregion

    //region Privates

    /**
     * @param $api
     * @param array $parameters
     * @return array
     */
    private function request($api, array $parameters = []) {
        $ch = curl_init($this->baseURL . $api);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array_merge($parameters, [
            'merchantSecret' => $this->merchantSecret,
        ]));

        $result = curl_exec($ch);
        curl_close($ch);

        return @json_decode($result, true);

    }

    /**
     * @param $response
     * @param string $className
     * @return mixed
     * @throws PhenopayException
     */
    private function adaptResponse($response, $className) {

        if(is_null($response) || $response == [] || $response === false)
            throw new PhenopayException('Invalid response');

        if(!isset($response['success']))
            throw new PhenopayException('Invalid response data');

        return $response['success'] ? new $className($response, $this) : new ErrorResponse($response, $this);
    }
    //endregion
}
