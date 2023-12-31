<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CandidateProfileUpdate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:3|string|max:32',
            'username' => 'required|min:3|max:32|string|unique:users,username,'.auth()->user()->id,
            'email' => 'required|email|unique:users,email,'.auth()->user()->id,
        ];
    }
}
