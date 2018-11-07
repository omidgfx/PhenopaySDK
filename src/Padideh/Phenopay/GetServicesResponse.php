<?php namespace Padideh\Phenopay;

/**
 * @property-read $services
 * Class GetServicesResponse
 * @package Padideh\Phenopay
 */
class GetServicesResponse extends SuccessResponse {

    //region Fields

    private $availables   = [];
    private $unavailables = [];

    //endregion

    //region Abstraction

    /**
     * @return string[]
     */
    protected function fieldsToVerify() {
        return ['services'];
    }
    //endregion

    //region Constructors
    public function __construct(array $response, $phenopayInstance) {
        parent::__construct($response, $phenopayInstance);


        # Parse and store payment service list
        $l = [true => &$this->availables, false => &$this->unavailables];
        foreach($this->services as $name => $available)
            $l[$available][] = $name;
    }

    //endregion

    //region Iterators

    /**
     * ### Walk through payment services
     *
     * @param callable $fn Callback.<br><code>function($name, $available) { &#47;* code *&#47; } </code>
     * @return $this
     */
    public function walk(callable $fn) {
        foreach($this->services as $name => $available)
            $fn($name, $available);
        return $this;
    }

    /**
     * ### Walk through available payment services
     *
     * @param callable $fn Callback.<br><code>function($name) { &#47;* code *&#47; } </code>
     * @return $this
     */
    public function walkThroughAvailables(callable $fn) {
        foreach($this->availables as $name)
            $fn($name);
        return $this;
    }

    /**
     * ### Walk through unavailable payment services
     *
     * @param callable $fn Callback.<br><code>function($name) { &#47;* code *&#47; } </code>
     * @return $this
     */
    public function walkThroughUnavailables(callable $fn) {
        foreach($this->unavailables as $name)
            $fn($name);
        return $this;
    }

    //endregion

    //region List methods

    /**
     * ### Returns available payment services
     * @return array
     */
    public function getAvailables() {
        return $this->availables;
    }

    /**
     * ### Returns unavailable payment services
     * @return array
     */
    public function getUnavailables() {
        return $this->unavailables;
    }

    //endregion

    //region Helpers
    /**
     * ### Check if given payment service is available or not
     * @param string $name
     * @return bool
     */
    public function isAvailable($name) {
        return in_array($name, $this->availables);
    }

    //endregion

}