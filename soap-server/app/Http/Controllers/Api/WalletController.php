<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckBalanceRequest;
use App\Models\Customer;
use App\Models\Wallet;
use App\Traits\SoapResponse;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class WalletController extends Controller
{
    use SoapResponse;

    /**
     * @param CheckBalanceRequest $request
     * @return Application|ResponseFactory|Response
     */
    public function checkBalance(CheckBalanceRequest $request)
    {
        try {
            $customer = app(Customer::class)->getCustomer($request);
            if (is_null($customer)) {
                $message = __('No se ha encontrado un cliente registrado para el documento :document y el celular :mobile.', [
                    'document' => $request->document,
                    'mobile' => $request->mobile,
                ]);
                throw new \Exception($message, ResponseAlias::HTTP_NOT_FOUND);
            }

            $wallet = app(Wallet::class)->getWalletOfACustomer($customer);

            $currentBalance = $wallet->balance;

            $this->body['cod_error'] = '00';
            $this->body['success'] = true;
            $this->body['data'] = __('Su balance es de :amount', [
                'amount' => col_amount_format($currentBalance)
            ]);
            $code = ResponseAlias::HTTP_OK;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $code = ResponseAlias::HTTP_CONFLICT;
            $this->body['cod_error'] = $e->getCode();
            $this->body['message_error'] = $e->getMessage();
        }

        return $this->response($this->body, $code);
    }

}
