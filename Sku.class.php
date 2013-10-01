<?php

class Sku {

    public $propertiesName = '';
    public $price = '';
    public $quantity = '';

    function __construct($propertiesName, $price, $quantity) {
        $this->propertiesName = $propertiesName;
        $this->price = $price;
        $this->quantity = $quantity;
    }

}

?>