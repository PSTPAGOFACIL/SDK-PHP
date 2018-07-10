<?php
include('classes.php');
include('transaction.php');

$transaction = new Transaction();
$transaction->setToken('');

error_log('COMPLETE: ');
error_log(print_r($_POST,true));

if($transaction->validate($_POST)){
  echo 'Orden recibida exitosamente';
  error_log('TRANSACCION CORRECTA');
}else{
  echo 'Error en firma';
  error_log('ERROR FIRMA');
}
