<?php

use Mostafax\Encrypto\Encrypto;



Route::get(
    'knet/check/{paymentId}/{amount}',
     'Mostafax\Payment\Knet@checkPayment'
     );


Route::get('/encrypt-file/{filename}', [Encrypto::class, 'encryptFile']);
Route::get('/decrypt-file/{filename}', [Encrypto::class, 'decryptFile']);



