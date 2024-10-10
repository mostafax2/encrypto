Here's a detailed description for your package that encrypts files by streaming:

---

### **Package Name: Encrypto**

#### **Description**
**Encrypto** is a powerful PHP package designed to efficiently encrypt files using streaming methods. By employing stream-based processing, Encrypto minimizes memory usage and enhances performance, making it ideal for handling large files. This package seamlessly integrates with Laravel, leveraging its robust features and services to provide a secure and flexible solution for file encryption.

#### **Key Features**
- **Streaming Encryption**: Encrypt large files without loading the entire content into memory, ensuring efficient resource usage.
- **Chunked Processing**: Encrypt files in manageable chunks, allowing for real-time processing and minimizing the risk of memory exhaustion.
- **Background Job Support**: Easily integrate with Laravel's job queue system to perform encryption tasks asynchronously.
- **File Compression**: Automatically compress files before encryption to save storage space and reduce encryption time.
- **Secure Encryption**: Utilizes Laravel's built-in Crypt facade for robust encryption algorithms, ensuring data security.
- **Error Handling and Logging**: Comprehensive error handling and logging mechanisms to track encryption processes and troubleshoot issues effectively.
- **Simple API**: A user-friendly API that makes it easy to encrypt and decrypt files with minimal configuration.

#### **Installation**
To install Encrypto, add the package to your Laravel project using Composer:

```bash
composer require mostafax/encrypto
```

#### **Usage**
Here’s a brief example of how to use the Encrypto package:

1. **Encrypting a File**:
   ```php
   use Mostafax\Encrypto\Encrypto;

   $encrypto = new Encrypto();
   $result = $encrypto->encryptFile('example.txt');
   ```

2. **Decrypting a File**:
   ```php
   $result = $encrypto->decryptFile('example.txt.enc');
   ```

3. **Encrypting in Background**:
   ```php
   $result = $encrypto->encryptFileInBackground('example.txt');
   ```

#### **Contributions**
Contributions are welcome! If you find any issues or have suggestions for improvement, please open an issue or submit a pull request.

#### **License**
This package is open-source and available under the MIT License.

---

Feel free to modify any part of this description to better suit your package's style or specific features! If you have more details or want to add anything else, let me know!
