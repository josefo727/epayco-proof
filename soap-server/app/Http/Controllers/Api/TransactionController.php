<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChargeBalanceRequest;
use App\Http\Requests\ConfirmPaymentRequest;
use App\Http\Requests\RequestPaymentRequest;
use App\Jobs\SendTokenNotificationJob;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Operations;
use App\Traits\SoapResponse;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TransactionController extends Controller
{
    use SoapResponse;

    /**
     * @param ChargeBalanceRequest $request
     * @return Application|ResponseFactory|Response
     */
    public function chargeBalance(ChargeBalanceRequest $request)
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

            $data = [
              ...$request->all(),
              'type' => Transaction::INCOME,
              'wallet_id' => $wallet->_id
            ];

            $currentBalance = $wallet->balance;

            $balance = app(Operations::class)->calculateBalance(Transaction::INCOME, $currentBalance, $request->amount);

            Transaction::query()
                ->create($data);

            app(Wallet::class)->setBalanceOfACustomer($customer, $balance);

            $this->body['cod_error'] = '00';
            $this->body['success'] = true;
            $this->body['data'] = __('Saldo añadido correctamente, ahora cuenta con :amount', [
                    'amount' => col_amount_format($balance)
                ]);
            $code = ResponseAlias::HTTP_CREATED;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $code = ResponseAlias::HTTP_CONFLICT;
            $this->body['cod_error'] = $e->getCode();
            $this->body['message_error'] = $e->getMessage();
        }

        return $this->response($this->body, $code);
    }

    /**
     * @param RequestPaymentRequest $request
     * @return Application|ResponseFactory|Response
     */
    public function requestPayment(RequestPaymentRequest $request)
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

            if ($request->amount > $wallet->balance) {
                $message = 'La solicitud de pago ha sido rechazada por no contar con el saldo soficiente para esta operacion.';
                throw new \Exception($message, ResponseAlias::HTTP_NOT_ACCEPTABLE);
            }

            $payment = $wallet->payments()->create([
                'session_id' => session()->getId(),
                'token' => generate_token(),
                'amount' => $request->amount,
                'approved' => false
            ]);

            SendTokenNotificationJob::dispatch($customer, $payment)/*->onQueue('default')*/;

            $this->body['cod_error'] = '00';
            $this->body['success'] = true;
            $this->body['data'] = 'Por su seguridad, y para confirmar su pago, se ha enviado el token y el id de sesion a su correo electrónico.';
            $code = ResponseAlias::HTTP_CREATED;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $code = ResponseAlias::HTTP_CONFLICT;
            $this->body['cod_error'] = $e->getCode();
            $this->body['message_error'] = $e->getMessage();
        }

        return $this->response($this->body, $code);
    }

    /**
     * @param ConfirmPaymentRequest $request
     * @return Application|ResponseFactory|Response
     */
    public function confirmPayment(ConfirmPaymentRequest $request)
    {
        try {
            $customer = Customer::query()
                ->where('document', $request->document)
                ->first();
            if (is_null($customer)) {
                $message = __('No se ha encontrado un cliente registrado para el documento :document.', [
                    'document' => $request->document
                ]);
                throw new \Exception($message, ResponseAlias::HTTP_NOT_FOUND);
            }

            $payment = Payment::query()
                ->whereHas('wallet', function ($query) use ($customer) {
                    $query->where('customer_id', $customer->_id);
                })
                ->where('token', $request->token)
                ->where('session_id', $request->session_id)
                ->first();

            if(is_null($payment)) {
                $message = __('No se ha encontrado un registro de pago con estos datos para el cliente :name', [
                    'name' => $customer->name
                ]);
                throw new \Exception($message, ResponseAlias::HTTP_NOT_FOUND);
            }

            if($payment->approved) {
                $message = __('Esta petición de pago ya ha sido aprobada para el cliente :name', [
                    'name' => $customer->name
                ]);
                throw new \Exception($message, ResponseAlias::HTTP_CONFLICT);
            }

            $payment->update([
                'approved' => true
            ]);

            $currentBalance = $customer->wallet->balance;

            $balance = app(Operations::class)->calculateBalance(Transaction::EGRESS, $currentBalance, $payment->amount);

            $data = [
                'document' => $customer->document,
                'mobile' => $customer->mobile,
                'amount' => $payment->amount,
                'type' => Transaction::EGRESS,
                'wallet_id' => $customer->wallet->_id
            ];

            Transaction::query()->create($data);

            app(Wallet::class)->setBalanceOfACustomer($customer, $balance);

            $this->body['cod_error'] = '00';
            $this->body['success'] = true;
            $this->body['data'] = __('Pago procesado exitosamente, ahora cuenta con :amount', [
                    'amount' => col_amount_format($balance)
                ]);
            $code = ResponseAlias::HTTP_CREATED;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $code = ResponseAlias::HTTP_CONFLICT;
            $this->body['cod_error'] = $e->getCode();
            $this->body['message_error'] = $e->getMessage();
        }

        return $this->response($this->body, $code);
    }
}
