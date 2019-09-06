<?php

namespace Tests;

use PagoFacil\lib\Request;
use PagoFacil\lib\Transaction;

class TransactionTest extends TestCase
{
    /** @test */
    public function it_generate_the_right_signature()
    {
        $token = '69454a334e4a6776644245336d4357485661494b334f4d4a5378335738326a31';
        $request = [
            'x_account_id' => '4c5a78505555684e6d38374f4572473631745272544b7834554c75397045306c',
            'x_amount' => '9000',
            'x_currency' => 'CLP',
            'x_shop_country' => 'CL',
            'x_customer_email' => 'me@acme.com',
            'x_url_callback' => 'http://0125a127.ngrok.io/callback.php',
            'x_url_cancel' => 'http://0125a127.ngrok.io/cancel.php',
            'x_url_complete' => 'http://0125a127.ngrok.io/complete.php',
            'x_reference' => 'mycustomorder-588',
            'x_session_id' => '20190902032313289',
            'x_signature' => '8e05b6aa5ee2cf28b727704e310d464750aaddf65924b5318ba76d4390b3ff18',
        ];

        $this->assertEquals(
            '8e05b6aa5ee2cf28b727704e310d464750aaddf65924b5318ba76d4390b3ff18',
            Transaction::generateSignature($request, $token)
        );
    }

    /** @test */
    public function it_validate_the_right_signature()
    {
        $token = '69454a334e4a6776644245336d4357485661494b334f4d4a5378335738326a31';

        $request = [
            'x_account_id' => '4c5a78505555684e6d38374f4572473631745272544b7834554c75397045306c',
            'x_amount' => '9000',
            'x_currency' => 'CLP',
            'x_shop_country' => 'CL',
            'x_customer_email' => 'me@acme.com',
            'x_url_callback' => 'http://0125a127.ngrok.io/callback.php',
            'x_url_cancel' => 'http://0125a127.ngrok.io/cancel.php',
            'x_url_complete' => 'http://0125a127.ngrok.io/complete.php',
            'x_reference' => 'mycustomorder-588',
            'x_session_id' => '20190902032313289',
            'x_signature' => '8e05b6aa5ee2cf28b727704e310d464750aaddf65924b5318ba76d4390b3ff18',
        ];

        $this->assertTrue(
            Transaction::validate($request, $token)
        );
    }

    /** @test */
    public function it_may_not_validate_the_right_signature()
    {
        $token = '69454a334e4a6776644245336d4357485661494b334f4d4a5378335738326a31';

        $request = [
            'x_account_id' => '4c5a78505555684e6d38374f4572473631745272544b7834554c75397045306c',
            'x_amount' => '9000',
            'x_currency' => 'CLP',
            'x_shop_country' => 'CL',
            'x_customer_email' => 'me@acme.com',
            'x_url_callback' => 'http://0125a127.ngrok.io/callback.php',
            'x_url_cancel' => 'http://0125a127.ngrok.io/cancel.php',
            'x_url_complete' => 'http://0125a127.ngrok.io/complete.php',
            'x_reference' => 'mycustomorder-588',
            'x_session_id' => '20190902032313289',
            'x_signature' => 'notavalidsignature',
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
