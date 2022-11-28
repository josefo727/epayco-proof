<?php

namespace App\Http\Requests;

use App\Services\XmlToArray;
use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\SoapResponse;

class StoreCustomerRequest extends FormRequest
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
        $data = app(XmlToArray::class)->convert($xml_data, 'customer');
        $this->merge($data);
    }

    /**
     * @return string[]
     */
    #[ArrayShape(['document' => "string", 'name' => "string", 'email' => "string", 'mobile' => "string"])]
    public function rules(): array
    {
        return [
            'document' => 'required|string|unique:customers',
            'name' => 'required|string',
            'email' => 'required|email|unique:customers',
            'mobile' => 'required|numeric|digits:10|unique:customers',
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
