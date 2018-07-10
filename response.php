<?php
include('classes.php');
include('transaction.php');

$transaction = new Transaction();
$transaction->setToken('');

error_log('CALLBACK: ');
error_log(print_r($_POST,true));

if($transaction->validate($_POST)){
  error_log('TRANSACCION CORRECTA');
}else{
  error_log('ERROR FIRMA');
}
