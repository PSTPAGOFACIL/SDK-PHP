<?php

include('classes.php');
include('transaction.php');

$request = new Request();

$request->account_id = '';
$request->amount = 0;
$request->currency = 'CLP';
$request->reference = '';
$request->customer_email = '';
$request->url_complete = '';
$request->url_cancel = '';
$request->url_callback = '';
$request->shop_country = 'CL';
$request->session_id = date('Ymdhis').rand(0,9).rand(0,9).rand(0,9);
$transaction = new Transaction($request);
$transaction->environment = 'DESARROLLO';

$transaction->setToken('');

$transaction->initTransaction($request);
