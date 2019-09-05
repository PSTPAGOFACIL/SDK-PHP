<?php

namespace Tests;

use PagoFacil\lib\Request;
use PagoFacil\lib\Transaction;

class TransactionTest extends TestCase
{
    /** @test */
    public function it_generate_the_right_signature()
    {
        $token = '483fd31ad78d7065e6d506033ca0499a01fbe85653e5c98e9cc4a66234835317';

        $request = [
            'x_account_id' => 'a6d26a46b42ee1955ffca9e0d2af1c00cb4a379fa8e1caa2fd900941f72430ea',
            'x_amount' => '5000.00',
            'x_currency' => 'CLP',
            'x_gateway_reference' => '9838',
            'x_reference' => 'Y13lbj8YnG4GgdQ0REmd',
            'x_result' => 'completed',
            'x_test' => 'true',
            'x_timestamp' => '2019-08-27T15:24:11.092Z',
            'x_message' => 'X',
            'x_signature' => 'b1122dbdd6d22dfbdd44986930e1408e5980c837640cad013d8a1cdc216ae268'
        ];

        $this->assertEquals(
            'b1122dbdd6d22dfbdd44986930e1408e5980c837640cad013d8a1cdc216ae268',
            Transaction::generateSignature($request, $token)
        );
    }

    /** @test */
    public function it_validate_the_right_signature()
    {
        $token = '483fd31ad78d7065e6d506033ca0499a01fbe85653e5c98e9cc4a66234835317';

        $request = [
            'x_account_id' => 'a6d26a46b42ee1955ffca9e0d2af1c00cb4a379fa8e1caa2fd900941f72430ea',
            'x_amount' => '5000.00',
            'x_currency' => 'CLP',
            'x_gateway_reference' => '9838',
            'x_reference' => 'Y13lbj8YnG4GgdQ0REmd',
            'x_result' => 'completed',
            'x_test' => 'true',
            'x_timestamp' => '2019-08-27T15:24:11.092Z',
            'x_message' => 'X',
            'x_signature' => 'b1122dbdd6d22dfbdd44986930e1408e5980c837640cad013d8a1cdc216ae268'
        ];

        $this->assertTrue(
            Transaction::validate($request, $token)
        );
    }

    /** @test */
    public function it_may_not_validate_the_right_signature()
    {
        $token = '483fd31ad78d7065e6d506033ca0499a01fbe85653e5c98e9cc4a66234835317';

        $request = [
            'x_account_id' => 'a6d26a46b42ee1955ffca9e0d2af1c00cb4a379fa8e1caa2fd900941f72430ea',
            'x_amount' => '5000.00',
            'x_currency' => 'CLP',
            'x_gateway_reference' => '9838',
            'x_reference' => 'Y13lbj8YnG4GgdQ0REmd',
            'x_result' => 'completed',
            'x_test' => 'true',
            'x_timestamp' => '2019-08-27T15:24:11.092Z',
            'x_message' => 'X',
            'x_signature' => 'notavalidsignature'
        ];

        $this->assertFalse(
            Transaction::validate($request, $token)
        );
    }

    /** @test */
    public function it_get_the_right_enviroment()
    {
        $token = '483fd31ad78d7065e6d506033ca0499a01fbe85653e5c98e9cc4a66234835317';

        $request = new Request(
            '98f13708210194c475687be6106a3b84',
            2000,
            'http://doe.com/callback',
            'http://doe.com/complete',
            'http://doe.com/cancel',
            'avalidsessionid',
            'FAKEORDER1',
            'jhon@doe.com'
        );

        $transaction = new Transaction($request, $token);

        $this->assertEquals(
            'https://gw-dev.pagofacil.cl/initTransaction',
            $transaction->getEnviroment()
        );
    }

    /** @test */
    public function it_set_the_right_production_enviroment()
    {
        $token = '483fd31ad78d7065e6d506033ca0499a01fbe85653e5c98e9cc4a66234835317';

        $request = new Request(
            '98f13708210194c475687be6106a3b84',
            2000,
            'http://doe.com/callback',
            'http://doe.com/complete',
            'http://doe.com/cancel',
            'avalidsessionid',
            'FAKEORDER1',
            'jhon@doe.com'
        );

        $transaction = new Transaction($request, $token);
        $transaction->setEnviroment(Transaction::PRODUCCION);

        $this->assertEquals(
            'https://gw.pagofacil.cl/initTransaction',
            $transaction->getEnviroment()
        );
    }

    /** @test */
    public function it_set_the_right_beta_enviroment()
    {
        $token = '483fd31ad78d7065e6d506033ca0499a01fbe85653e5c98e9cc4a66234835317';

        $request = new Request(
            '98f13708210194c475687be6106a3b84',
            2000,
            'http://doe.com/callback',
            'http://doe.com/complete',
            'http://doe.com/cancel',
            'avalidsessionid',
            'FAKEORDER1',
            'jhon@doe.com'
        );

        $transaction = new Transaction($request, $token);
        $transaction->setEnviroment(Transaction::BETA);

        $this->assertEquals(
            'https://gw-beta.pagofacil.cl/initTransaction',
            $transaction->getEnviroment()
        );
    }

    /** @test */
    public function it_trigger_a_form_when_its_processed()
    {
        $token = '483fd31ad78d7065e6d506033ca0499a01fbe85653e5c98e9cc4a66234835317';

        $request = new Request(
            '98f13708210194c475687be6106a3b84',
            2000,
            'http://doe.com/callback',
            'http://doe.com/complete',
            'http://doe.com/cancel',
            'avalidsessionid',
            'FAKEORDER1',
            'jhon@doe.com'
        );

        $transaction = new Transaction($request, $token);
        $transaction->initTransaction();

        $this->expectOutputString(
            $this->getOutputForm($request->getParsedData(), $token)
        );
    }

    /**
     * Echo the output form for a given enviroment
     *
     * @param array $parsedRequestData
     * @return string
     */
    public function getOutputForm(array $request, $token)
    {
        $html = '';
        $html .= '<html>';
        $html .= '  <head>  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script></head>';
        $html .= '  <body>';
        $html .= '    <form name="requestForm" id="requestForm" action=https://gw-dev.pagofacil.cl/initTransaction method="POST">';
        foreach ($request as $key => $value) {
            $html .= '    <input type="hidden" name="' . $key . '" value="' . $value . '" />';
        }
        $html .= '      <input type="hidden" name="x_signature" value="' . Transaction::generateSignature($request, $token) . '" />';
        $html .= '    </form>';
        $html .= '    <script type="text/javascript">';
        $html .= '      document.getElementById("requestForm").submit();';
        $html .= '    </script>';
        $html .= '  </body>';
        $html .= '</html>';

        return $html;
    }
}
