<?php

namespace Mostafax\Encrypto;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mostafax\Encrypto\Encrypto;
use Exception;

class Uploader
{
    protected $encrypt_after_upload = true;
    protected $delete_org_file_after_upload = true;

    /**
     * Upload a file, optionally encrypting it based on the user preferences.
     *
     * @param UploadedFile $file
     * @param string $path
     * @return string|null
     */
    public function upload(UploadedFile $file, string $path): ?string
    {
        // Define the private storage path
        $privatePath = 'private/' . $path;

        // Get a unique filename for the"http://learn.test/decrypt-file/123.webp" file
        $filename = $this->generateUniqueFilename($file, $privatePath);

        // Initialize Encrypto instance
        $encrypto = new Encrypto();
        $filePath = Storage::putFileAs($privatePath, $file, $filename);
        try {
            if ($this->encrypt_after_upload) {
                 $encrypto->encryptFile($filename, $filePath);
            }
            // Delete the original file after successful encryption
            if ( $this->delete_org_file_after_upload ) {
                unlink(storage_path("app/{$filePath}"));
            }
            // Return the URL to the uploaded file
            return $filePath ? Storage::url($filePath) : null;

        } catch (Exception $e) {
            // Log the error and return null on failure
            \Log::error("File upload failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Encrypt the file contents and store the encrypted file.
     *
     * @param UploadedFile $file
     * @param string $filename
     * @param string $privatePath
     * @param Encrypto $encrypto
     * @return string|null
     */
    private function storeEncryptedFile(UploadedFile $file, string $filename, string $privatePath, Encrypto $encrypto): ?string
    {

        // Encrypt the file contents
        $encryptedContents = $encrypto->encryptFile($$filename,$privatePath);

        // Create a temporary file for the encrypted contents
        $tempFile = tmpfile();
        fwrite($tempFile, $encryptedContents);

        // Move the encrypted file into storage
        $metaData = stream_get_meta_data($tempFile);
        $tempFilePath = $metaData['uri'];

        // Store the encrypted file using Laravel's storage system
        $filePath = Storage::putFileAs($privatePath, new UploadedFile($tempFilePath, $filename), $filename);

        // Close and remove the temporary file
        fclose($tempFile);

        return $filePath;
    }

    /**
     * Generate a unique filename by appending a counter if the filename already exists.
     *
     * @param UploadedFile $file
     * @param string $privatePath
     * @return string
     */
    private function generateUniqueFilename(UploadedFile $file, string $privatePath): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $filename = $originalName . '.' . $extension;
        $counter = 1;

        // Check if the file already exists, if so, append a counter to make it unique
        while (Storage::exists($privatePath . '/' . $filename)) {
            $filename = $originalName . '_' . $counter++ . '.' . $extension;
        }

        return $filename;
    }
}
