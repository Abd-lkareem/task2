<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignUpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|unique:users|email',
            'phone_number' => 'required|numeric',
            'password' => 'required|min:7|confirmed',
            'username' => 'required|string' ,
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', 
            'certificate' => 'required|mimes:pdf|max:2048',
        ];
    }
}
