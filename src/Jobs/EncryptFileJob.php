<?php

namespace Mostafax\Encrypto\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mostafax\Encrypto\Encrypto;

class EncryptFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function handle()
    {
        $encrypto = new Encrypto();
        $encrypto->encryptFile($this->filename);
    }
}
