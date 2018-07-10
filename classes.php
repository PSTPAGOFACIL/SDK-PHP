<?php

class Operacion{
  // Variables comunes
  public $account_id; //String
  public $amount; //Number
  public $currency; //String
  public $reference; //String
  public $signature; //String
}

class Request extends Operacion{
  // Variables request
  public $customer_email; //String
  public $url_complete; //String
  public $url_cancel; //String
  public $url_callback; //String
  public $shop_country; //String
  public $session_id; //String
}

class Response extends Operacion{
  // Variables response
  public $gateway_reference; //Number
  public $result; //String
  public $timestamp; //String
  public $test; //Bool
}
