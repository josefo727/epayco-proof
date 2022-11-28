<?php

namespace App\Http\Requests;

use App\Services\XmlToArray;
use App\Traits\SoapResponse;
use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class CheckBalanceRequest extends FormRequest
{
    use SoapResponse;

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
     * @return void
     * @throws Exception
     */
    protected function prepareForValidation()
    {
        $xml_data = $this->getContent();
        $data = app(XmlToArray::class)->convert($xml_data, 'check-balance');
        $this->merge($data);
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'document' => 'required|string',
            'mobile' => 'required|numeric|digits:10',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        $message_error = collect($errors)->values()->collapse()->implode(' ');

        $errorCode = Response::HTTP_UNPROCESSABLE_ENTITY;

        $body = [
            'success' => false,
            'cod_error' => $errorCode,
            'message_error' => $message_error,
            'data' => null
        ];

        $response = $this->makeBody($body);

        throw new HttpResponseException(
            response($response, $errorCode)->withHeaders([
                'Content-Type' => 'text/xml'
            ])
        );
    }
}
