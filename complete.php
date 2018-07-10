<?php
include('classes.php');
include('transaction.php');

$transaction = new Transaction();
$transaction->setToken('71843ce42eeae0f1cded7dda3b846ca6087cf75d6b7e7f589f59eb1c2e41b7ab');

error_log('COMPLETE: ');
error_log(print_r($_POST,true));

if($transaction->validate($_POST)){
  echo 'Orden recibida exitosamente';
  error_log('TRANSACCION CORRECTA');
}else{
  echo 'Error en firma';
  error_log('ERROR FIRMA');
}
