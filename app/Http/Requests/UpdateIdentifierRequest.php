<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateIdentifierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'min:1', 'max:256',
                Rule::unique('identifiers', 'name')->where('user_id', auth()->id())
                    ->ignore($this->route('identifier')->id),
            ],
            'avatar' => ['nullable', 'image', 'mimetypes:image/jpeg,image/jpg,image/png'],
            'phone' => ['nullable', "regex:/\(\d{2}\)\s\d{4,5}-\d{4}/"],
            'description' => ['nullable'],
        ];
    }
}
