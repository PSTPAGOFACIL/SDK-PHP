<?php

namespace Tests;

use PagoFacil\lib\Request;
use PagoFacil\lib\Transaction;

class TransactionTest extends TestCase
{
    /** @test */
    public function it_generate_the_right_signature_from_a_given_request()
    {
        $callbackRequest = [
            'x_account_id' => 'a6d26a46b42ee1955ffca9e0d2af1c00cb4a379fa8e1caa2fd900941f72430ea',
            'x_amount' => '5000.00',
            'x_currency' => 'CLP',
            'x_gateway_reference' => '9838',
            'x_reference' => 'Y13lbj8YnG4GgdQ0REmd',
            'x_result' => 'completed',
            'x_test' => 'true',
            'x_timestamp' => '2019-08-27T15:24:11.092Z',
            'x_message' => 'X',
            'x_signature' => '01133b4555d39daddff0c8fc73fdbd6bef154ee384004b42a95aec9ff493d7e0'
        ];

        $token = '483fd31ad78d7065e6d506033ca0499a01fbe85653e5c98e9cc4a66234835317';
        $transaction = new Transaction();
        $transaction->setToken($token);

        $this->assertTrue($transaction->validate($callbackRequest));
    }

    /** @test */
    public function it_may_not_validate_a_wrong_signature()
    {
        $callbackRequest = [
            'x_account_id' => 'a6d26a46b42ee1955ffca9e0d2af1c00cb4a379fa8e1caa2fd900941f72430ea',
            'x_amount' => '5000.00',
            'x_currency' => 'CLP',
            'x_gateway_reference' => '9838',
            'x_reference' => 'Y13lbj8YnG4GgdQ0REmd',
            'x_result' => 'completed',
            'x_test' => 'true',
            'x_timestamp' => '2019-08-27T15:24:11.092Z',
            'x_message' => 'X',
            'x_signature' => 'a not valid signature'
        ];

        $token = '483fd31ad78d7065e6d506033ca0499a01fbe85653e5c98e9cc4a66234835317';
        $transaction = new Transaction();
        $transaction->setToken($token);

        $this->assertFalse($transaction->validate($callbackRequest));
    }

    /** @test */
    public function it_may_not_validate_an_empty_signature()
    {
        $transaction = new Transaction();

        $this->assertFalse($transaction->validate([]));
    }

    /** @test */
    public function it_generate_the_right_form()
    {
        $token = 'd3d9446802a44259755d38e6d163e820';

        $request = new Request();
        $request->account_id = '98f13708210194c475687be6106a3b84';
        $request->amount = 20;
        $request->currency = 'CLP';
        $request->reference = 'FAKEORDER1';
        $request->customer_email = 'jhon@doe.com';
        $request->url_complete = 'http://doe.com/complete';
        $request->url_cancel = 'http://doe.com/cancel';
        $request->url_callback = 'http://doe.com/callback';
        $request->shop_country = 'CL';
        $request->session_id = 'avalidsessionid';

        $transaction = new Transaction($request);
        $transaction->setToken($token);
        $transaction->environment = 'DESARROLLO';

        $parsedRequestData = [];
        foreach ($request as $key => $value) {
            $parsedRequestData['x_' . $key] = $value;
        }

        $transaction->generarFirma($parsedRequestData);

        $transaction->_initTransaction($parsedRequestData);

        $this->expectOutputString($this->getOutputForm($parsedRequestData));
    }

    /**
     * Echo the output form for a given enviroment
     *
     * @param array $parsedRequestData
     * @return string
     */
    public function getOutputForm($parsedRequestData)
    {
        $html = '';
        $html .= '<html>';
        $html .= '  <head>  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script></head>';
        $html .= '  <body>';
        $html .= '    <form name="requestForm" id="requestForm" action=https://gw-dev.pagofacil.cl/initTransaction method="POST">';
        foreach ($parsedRequestData as $key => $value) {
            $html .= '    <input type="hidden" name="' . $key . '" value="' . $value . '" />';
        }
        $html .= '    </form>';
        $html .= '    <script type="text/javascript">';
        $html .= '      $(document).ready(function () {';
        $html .= '        $("#requestForm").submit(); ';
        $html .= '      });';
        $html .= '    </script>';
        $html .= '  </body>';
        $html .= '</html>';

        return $html;
    }
}
