<?php

namespace Mostafax\Encrypto;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class Encrypto
{
    public function encryptFile($filename)
    {
        $filePath = storage_path("app/public/{$filename}");
        $encryptedFilePath = storage_path("app/public/encrypted/{$filename}.enc");
        $inputFile = fopen($filePath, 'rb');
        $outputFile = fopen($encryptedFilePath, 'wb');

        $startTime = microtime(true);

        while (!feof($inputFile)) {
            $chunk = fread($inputFile, 8192);
            $encryptedChunk = Crypt::encrypt($chunk);
            fwrite($outputFile, $encryptedChunk);
        }

        fclose($inputFile);
        fclose($outputFile);

        $endTime = microtime(true);
        $timeTaken = round(($endTime - $startTime) * 1000, 2);

        return "File encrypted and stored! Time taken: {$timeTaken} milliseconds.";
    }

    public function decryptFile($filename)
    {
        $encryptedFilePath = storage_path("app/public/encrypted/{$filename}.enc");
        $tempFilePath = storage_path("app/public/decrypted/{$filename}");
        $inputFile = fopen($encryptedFilePath, 'rb');
        $outputFile = fopen($tempFilePath, 'wb');

        $startTime = microtime(true);

        while (!feof($inputFile)) {
            $encryptedChunk = fread($inputFile, 8192);
            if (!empty($encryptedChunk)) {
                $decryptedChunk = Crypt::decrypt($encryptedChunk);
                fwrite($outputFile, $decryptedChunk);
            }
        }

        fclose($inputFile);
        fclose($outputFile);

        $endTime = microtime(true);
        $timeTaken = round(($endTime - $startTime) * 1000, 2);

        return response()->file($tempFilePath)
            ->deleteFileAfterSend(true)
            ->header('X-Time-Taken', "{$timeTaken} milliseconds");
    }
}
