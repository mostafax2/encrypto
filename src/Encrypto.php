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
    private $chunkSize;

    public function __construct($chunkSize = 1024)
    {
        $this->chunkSize = $chunkSize; // Allow chunk size to be configurable
    }

    public function encryptFile($filename, $path = "")
    {
        $filePath = storage_path("app/{$path}");
        // dd($filePath);
        if (!file_exists($filePath)) {
            return response()->json([
                'status' => 'error',
                'message' => "File not found: {$filename}"
            ]);
        }

        $startTime = microtime(true);

        $encryptedFilePath =storage_path("app/public/encrypted/{$filename}.enc");

        try {
            $inputStream = fopen($filePath, 'rb');
            if (!$inputStream) {
                throw new Exception("Unable to open file for reading: {$filePath}");
            }

            $outputStream = fopen($encryptedFilePath, 'wb');
            if (!$outputStream) {
                fclose($inputStream);
                throw new Exception("Unable to open output file for writing: {$encryptedFilePath}");
            }

            while (!feof($inputStream)) {
                $chunk = fread($inputStream, $this->chunkSize);
                if ($chunk === false) {
                    throw new Exception("Error reading file chunk.");
                }

                $encryptedChunk = Crypt::encrypt($chunk);
                $chunkSize = strlen($encryptedChunk);
                fwrite($outputStream, pack('N', $chunkSize));
                fwrite($outputStream, $encryptedChunk);
            }

            fclose($inputStream);
            fclose($outputStream);
        } catch (Exception $e) {
            Log::error("Encryption failed: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => "Encryption failed: " . $e->getMessage()
            ]);
        }

        $endTime = microtime(true);
        $timeTaken = $endTime - $startTime;

        return response()->json([
            'status' => 'success',
            'path_encrypt_file' => $encryptedFilePath,
            'url_to_decrypt' => url('decrypt-file', $filename),
            'time_encrypt_file' => round($timeTaken * 1000, 2)
        ]);
    }

    public function decryptFile( $path = "", $filename)
    {
        $encryptedFilePath = storage_path("app/public/encrypted/{$filename}.enc");
        $decryptedFilePath = storage_path("app/public/decrypted/{$filename}");

        // Check if the encrypted file exists
        if (!file_exists($encryptedFilePath)) {
            return response()->json([
                'status' => 'error',
                'message' => "Encrypted file not found: {$filename}"
            ]);
        }

        $startTime = microtime(true);

        try {
            $inputStream = fopen($encryptedFilePath, 'rb');
            if (!$inputStream) {
                throw new Exception("Unable to open encrypted file for reading: {$encryptedFilePath}");
            }

            $outputStream = fopen($decryptedFilePath, 'wb');
            if (!$outputStream) {
                fclose($inputStream);
                throw new Exception("Unable to open output file for writing: {$decryptedFilePath}");
            }

            // Read the encrypted file chunk by chunk
            while (!feof($inputStream)) {
                // Read the size of the next chunk
                $sizeData = fread($inputStream, 4);
                if (strlen($sizeData) < 4) {
                    break; // End of file
                }

                $chunkSize = unpack('N', $sizeData)[1]; // Unpack the chunk size
                $encryptedChunk = fread($inputStream, $chunkSize);

                // Check if the read operation was successful
                if ($encryptedChunk === false || strlen($encryptedChunk) !== $chunkSize) {
                    throw new Exception("Error reading file chunk. Expected size: {$chunkSize}, Actual size: " . strlen($encryptedChunk));
                }

                // Attempt to decrypt the chunk
                try {
                    $decryptedChunk = Crypt::decrypt($encryptedChunk);
                    fwrite($outputStream, $decryptedChunk);
                } catch (DecryptException $e) {
                    Log::error("Decryption failed for chunk: " . $e->getMessage());
                    return response()->json([
                        'status' => 'error',
                        'message' => "Decryption failed: The payload is invalid."
                    ]);
                }
            }

            fclose($inputStream);
            fclose($outputStream);
        } catch (Exception $e) {
            Log::error("Decryption failed: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => "Decryption failed: " . $e->getMessage()
            ]);
        }

        $endTime = microtime(true);
        $timeTaken = $endTime - $startTime;

        return response()->json([
            'status' => 'success',
            'path_decrypt_file' => $decryptedFilePath,
            'time_decrypt_file' => round($timeTaken * 1000, 2)
        ]);
    }



    public function encryptFileInBackground($filename)
    {
        return $this->encryptFile($filename,"private/mostafa/200MB-TESTFILE.ORG.pdf");
        EncryptFileJob::dispatch($filename, $path="app/private/mostafa");
        return response()->json([
            'status' => 'success',
            'message' => "Encryption job for {$filename} has been dispatched."
        ]);
    }

    public function decryptFileInBackground($path, $filename, $deleteAfterDecrypt = true)
    {
        $privatePath = 'private/' . $path;

      return  $this->decryptFile($path, $filename, $deleteAfterDecrypt);
        DecryptFileJob::dispatch($privatePath, $filename, $deleteAfterDecrypt);

        return response()->json([
            'status' => 'success',
            'message' => "Decryption job for {$filename} has been dispatched."
        ]);
    }

    public function encryptFileContents($content)
    {
        try {
            $encryptedContent = Crypt::encryptString($content);
            return response()->json([
                'status' => 'success',
                'encrypted_content' => $encryptedContent
            ]);
        } catch (Exception $e) {
            Log::error("Encryption failed: " . $e->getMessage());
            return response()->json(['error' => 'Encryption failed: ' . $e->getMessage()], 500);
        }
    }

    public function decryptFileContents($encryptedContent)
    {
        try {
            $decryptedContent = Crypt::decryptString($encryptedContent);
            return response()->json([
                'status' => 'success',
                'decrypted_content' => $decryptedContent
            ]);
        } catch (Exception $e) {
            Log::error("Decryption failed: " . $e->getMessage());
            return response()->json(['error' => 'Decryption failed: ' . $e->getMessage()], 500);
        }
    }
}
