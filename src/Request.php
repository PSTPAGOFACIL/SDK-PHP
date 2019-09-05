<?php

namespace PagoFacil\lib;

class Request implements Requestable
{
    protected $customer_email;
    protected $url_complete;
    protected $url_cancel;
    protected $url_callback;
    protected $shop_country = 'CL';
    protected $session_id;
    protected $ammount;
    protected $currency = 'CLP';
    protected $reference;
    protected $signature;

    /**
     * Class constructor
     *
     * @param string $account_id
     * @param integer $ammount
     * @param string $url_callback
     * @param string $url_complete
     * @param string $url_cancel
     * @param string $session_id
     * @param string $reference
     * @param string $customer_email
     */
    public function __construct(
        string $account_id,
        int $ammount,
        string $url_callback,
        string $url_complete,
        string $url_cancel,
        string $session_id,
        string $reference,
        string $customer_email
    ) {
        $this->account_id = $account_id;
        $this->ammount = $ammount;
        $this->url_callback = $url_callback;
        $this->url_complete = $url_complete;
        $this->url_cancel = $url_cancel;
        $this->session_id = $session_id;
        $this->reference = $reference;
    }

    /**
     * Get parsed and sorted data
     *
     * @return array
     */
    public function getParsedData()
    {
        $parseableData = [
            'x_account_id' => $this->account_id,
            'x_customer_email' => $this->customer_email,
            'x_url_complete' => $this->url_complete,
            'x_url_cancel' => $this->url_cancel,
            'x_url_callback' => $this->url_callback,
            'x_shop_country' => $this->shop_country,
            'x_session_id' => $this->session_id,
            'x_amount' => $this->ammount,
            'x_currency' => $this->currency,
            'x_reference' => $this->reference,
            'x_signature' => $this->signature,
        ];

        ksort($parseableData);

        return $parseableData;
    }
}
