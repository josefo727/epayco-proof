<?php

namespace App\Traits;

use App\Services\ArrayToXml;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

trait SoapResponse
{
    public array $body = [
            'success' => false,
            'cod_error' => null,
            'message_error' => null,
            'data' => null
        ];

    /**
     * @param $body
     * @param int $code
     * @return Application|ResponseFactory|Response
     */
    public function response($body, int $code = 200): Response|Application|ResponseFactory
    {
        $response = $this->makeBody($body);

        return response($response, $code)->withHeaders([
            'Content-Type' => 'text/xml'
        ]);
    }

    /**
     * @param $body
     * @return bool|string
     */
    public function makeBody($body): bool|string
    {
        $data['soapBody']['response'] = $body;

        return app( ArrayToXml::class)->convert($data, 'soapEnvelope');
    }

}
