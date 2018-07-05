<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CelularRequest extends FormRequest
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
            'id_marca' => 'required',
            'modelo' => 'required',
            'imei' => 'required |max:15',
        ];
    }
    public function messages()
    {
        return [
            'id_marca.required' => 'Campo marca es requerido',
            'modelo.required'  => 'Campo modelo es requerido',
            'imei.required'  => 'Campo imei es requerido',
            'imei.max'  => 'Longitud campo imei no debe ser mayor a 15',
        ];
    }

    protected function failedValidation(Validator $validator) { throw new HttpResponseException(response()->json($validator->errors(), 422)); }
}
