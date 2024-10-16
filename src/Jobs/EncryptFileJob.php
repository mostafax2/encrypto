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
    protected $path;

    public function __construct($filename,$path)
    {
        $this->filename = $filename;
        $this->path = $path;
    }

    public function handle()
    {
        $encrypto = new Encrypto();
        $encrypto->encryptFile($this->filename,$this->path );
    }
}
