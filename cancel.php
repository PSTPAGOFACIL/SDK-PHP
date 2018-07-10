<?php
include('classes.php');
include('transaction.php');

$transaction = new Transaction();
$transaction->setToken('71843ce42eeae0f1cded7dda3b846ca6087cf75d6b7e7f589f59eb1c2e41b7ab');

error_log('CANCEL: ');
error_log(print_r($_POST,true));

if($transaction->validate($_POST)){
  error_log('TRANSACCION CORRECTA');
}else{
  error_log('ERROR FIRMA');
}
