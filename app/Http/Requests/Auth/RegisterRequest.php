<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    use \App\Traits\ApiResponse;
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
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required'],
        ];
    }


    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Le prénom est obligatoire',
            'first_name.string' => 'Le prénom doit être une chaîne de caractères',
            'first_name.max' => 'Le prénom ne doit pas dépasser :max caractères',
            'last_name.required' => 'Le nom est obligatoire',
            'last_name.string' => 'Le nom doit être une chaîne de caractères',
            'last_name.max' => 'Le nom ne doit pas dépasser :max caractères',
            'email.required' => 'L\'adresse email est obligatoire',
            'email.string' => 'L\'adresse email doit être une chaîne de caractères',
            'email.lowercase' => 'L\'adresse email doit être en minuscules',
            'email.email' => 'L\'adresse email doit être valide',
            'email.max' => 'L\'adresse email ne doit pas dépasser :max caractères',
            'email.unique' => 'L\'adresse email est déjà utilisée',
            'password.required' => 'Le mot de passe est obligatoire',
        ];
    }


    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'first_name' => 'prénom',
            'last_name' => 'nom',
            'email' => 'adresse email',
            'password' => 'mot de passe',
        ];
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string>
     */
    public function validationData(): array
    {
        return array_merge($this->all(), [
            'email' => strtolower($this->email),
        ]);
    }


    protected function failedValidation($validator)
    {
        $response = $this->sendErrorResponse($validator->errors()->first(), 422);
        throw new HttpResponseException($response);
    }

}
