<?php

namespace Mostafax\Encrypto\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadFileRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Set to true if authorization logic is not required
    }

    public function rules()
    {
        return [
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048000', // Define your validation rules here
        ];
    }

    public function messages()
    {
        return [
            'file.required' => 'You must upload a file.',
            'file.mimes' => 'Only JPG, PNG, PDF, DOC, and DOCX files are allowed.',
            'file.max' => 'File size cannot exceed 2MB.',
        ];
    }
}
