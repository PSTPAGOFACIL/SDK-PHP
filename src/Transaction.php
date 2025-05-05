<?php

namespace PagoFacil\lib;

class Transaction
{
    const DESARROLLO = 'https://gw-dev.pagofacil.cl/initTransaction';
    const BETA = 'https://gw-beta.pagofacil.cl/initTransaction';
    const PRODUCCION = 'https://gw.pagofacil.cl/initTransaction';
    private $environment;
    private $token_secret;
    private $request = [];

    /**
     * Transaction's contructor
     *
     * @param Request $request
     */
    public function __construct(Requestable $request, $token)
    {
        $this->request = $request->getParsedData();
        $this->token_secret = $token;
        $this->environment = self::DESARROLLO;
    }

    /**
     * Set the transaction enviroment
     *
     * @param string $environment
     * @return void
     */
    public function setEnviroment($environment)
    {
        $this->environment = $environment;
    }

    /**
     * Get the transaction enviroment
     *
     * @return string
     */
    public function getEnviroment() : string
    {
        return $this->environment;
    }

    /**
     * Init transaction
     *
     * @return void
     */
    public function initTransaction()
    {
        $this->buildSelfCalledForm();
    }

    /**
     * Generate signature
     *
     * @return void
     */
    public static function generateSignature($request, $token)
    {
        if (isset($request['x_signature'])) {
            unset($request['x_signature']);
        }

        ksort($request);

        $message = '';

        foreach ($request as $key => $value) {
            $message .= $key . $value;
        }

        return hash_hmac('sha256', $message, $token);
    }

    /**
     * Get request data
     *
     * @return void
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Validate array signature
     *
     * @param array $data
     * @return void
     */
    public static function validate(array $data, $token)
    {
        if (empty($data['x_signature'])) {
            throw new \Exception('Missing signature index on array', 422);
        }

        return $data['x_signature'] === static::generateSignature($data, $token);
    }

    /**
     * Build a self called form with the required data by Pagofacil
     *
     * @return void
     */
    private function buildSelfCalledForm()
    {
        $generatedSignature = self::generateSignature($this->request, $this->token_secret);

        $html = '';
        $html .= '<html>';
        $html .= '  <head>  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script></head>';
        $html .= '  <body>';
        $html .= '    <form name="requestForm" id="requestForm" action=' . $this->environment . ' method="POST">';
        foreach ($this->request as $key => $value) {
            $html .= '    <input type="hidden" name="' . $key . '" value="' . $value . '" />';
        }
        $html .= '      <input type="hidden" name="x_signature" value="' . $generatedSignature . '" />';
        $html .= '    </form>';
        $html .= '    <script type="text/javascript">';
        $html .= '      document.getElementById("requestForm").submit();';
        $html .= '    </script>';
        $html .= '  </body>';
        $html .= '</html>';

        echo $html;
    }
}
