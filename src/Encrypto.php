<?php

namespace Mostafax\Encrypto;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Mostafax\Encrypto\Jobs\EncryptFileJob;
use Mostafax\Encrypto\Jobs\DecryptFileJob;
use Exception;

class Encrypto
{
    private $chunkSize = 1024;

    public function encryptFile($filename)
    {
        $filePath = storage_path("app/public/{$filename}");

        if (!file_exists($filePath)) {
            return "File not found: {$filename}";
        }

        $startTime = microtime(true);

        try {
            $inputStream = fopen($filePath, 'rb');
            $encryptedFilePath = storage_path("app/public/encrypted/{$filename}.enc");
            $outputStream = fopen($encryptedFilePath, 'wb');

            while (!feof($inputStream)) {
                $chunk = fread($inputStream, $this->chunkSize);
                $encryptedChunk = Crypt::encrypt($chunk);
                $chunkSize = strlen($encryptedChunk);
                fwrite($outputStream, pack('N', $chunkSize));
                fwrite($outputStream, $encryptedChunk);
            }

            fclose($inputStream);
            fclose($outputStream);
        } catch (Exception $e) {
            Log::error("Encryption failed: " . $e->getMessage());
            return "Encryption failed: " . $e->getMessage();
        }

        $endTime = microtime(true);
        $timeTaken = $endTime - $startTime;

        return [
            "path_encrypt_file" => $encryptedFilePath,
            "time_ecrypt_file"=> round($timeTaken * 1000, 2)
        ];
    }

    public function decryptFile($filename , $deleteAfterDecrypt )
    {
        $encryptedFilePath = storage_path("app/public/encrypted/{$filename}.enc");

        if (!file_exists($encryptedFilePath)) {
            return "Encrypted file not found: {$filename}.enc";
        }

        $startTime = microtime(true);

        try {
            $inputStream = fopen($encryptedFilePath, 'rb');
            $decryptedFilePath = storage_path("app/public/decrypted/{$filename}");
            $outputStream = fopen($decryptedFilePath, 'wb');

            while (!feof($inputStream)) {
                $sizeData = fread($inputStream, 4);
                if (strlen($sizeData) < 4) break;

                $chunkSize = unpack('N', $sizeData)[1];
                $encryptedChunk = fread($inputStream, $chunkSize);
                $decryptedChunk = Crypt::decrypt($encryptedChunk);
                fwrite($outputStream, $decryptedChunk);
            }

            fclose($inputStream);
            fclose($outputStream);
        } catch (Exception $e) {
            Log::error("Decryption failed: " . $e->getMessage());
            return "Decryption failed: " . $e->getMessage();
        }

        return response()->file($decryptedFilePath)->deleteFileAfterSend(true);
    }


    public function encryptFileInBackground($filename)
    {
        EncryptFileJob::dispatch($filename);
        return "Encryption job for {$filename} has been dispatched.";
    }

    public function decryptFileInBackground($filename,$deleteAfterDecrypt=true)
    {
        DecryptFileJob::dispatch($filename,$deleteAfterDecrypt);

        return [
            "url" => url('storage/decrypted',$filename),
            "message" =>"Encryption job for {$filename} has been dispatched."
        ];
    }
}
