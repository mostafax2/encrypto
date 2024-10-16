<?php

use Mostafax\Encrypto\Encrypto;
use Mostafax\Encrypto\Jobs\EncryptFileJob;


Route::get(
    'knet/check/{paymentId}/{amount}',
     'Mostafax\Payment\Knet@checkPayment'
     );


     Route::get('/encrypt-file/{filename}', function ($filename) {
        $privatePath = 'private/test/'.$filename;
       return (new Encrypto)->encryptFile($filename,$privatePath) ;
    });

// Route::get('/encrypt-file/{filename}', [Encrypto::class, 'encryptFileInBackground']);
Route::get('/decrypt-file/{path}/{filename}', [Encrypto::class, 'decryptFileInBackground']);



