<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use App\Traits\SoapResponse;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CustomerController extends Controller
{
    use SoapResponse;

    /**
     * @param StoreCustomerRequest $request
     * @return Application|ResponseFactory|Response
     */
    public function store(StoreCustomerRequest $request)
    {
        try {
            Customer::query()->create($request->all());
            $this->body['cod_error'] = '00';
            $this->body['success'] = true;
            $this->body['data'] = 'Cliente creado satisfactoriamente';
            $code = ResponseAlias::HTTP_CREATED;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $code = ResponseAlias::HTTP_CONFLICT;
            $this->body['cod_error'] = $e->getCode();
            $this->body['message_error'] = $e->getMessage();
        }

        return $this->response($this->body, $code);
    }
}
