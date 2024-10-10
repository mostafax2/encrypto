<?php

namespace Mostafax\Encrypto\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mostafax\Encrypto\Encrypto;

class DecryptFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filename;
    protected $deleteAfterDecrypt;

    public function __construct($filename,$deleteAfterDecrypt)
    {
        $this->filename = $filename;
        $this->deleteAfterDecrypt = $deleteAfterDecrypt;
    }

    public function handle()
    {
        $encrypto = new Encrypto();
        $encrypto->decryptFile($this->filename ,$this->deleteAfterDecrypt);
    }
}
