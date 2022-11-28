<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\Payment;
use App\Notifications\SendTokenNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTokenNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Customer $customer;
    public Payment $payment;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($customer, $payment)
    {
        $this->customer = $customer;
        $this->payment = $payment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->customer->notify(new SendTokenNotification($this->payment));
    }
}
