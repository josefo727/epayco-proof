<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\Model;

class Customer extends Model
{
    use Notifiable;

    protected $collection = 'customers';

    protected $primaryKey = '_id';

    protected $guarded = [];

    public function wallet(): HasOne|\Jenssegers\Mongodb\Relations\HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * @param $request
     * @return Customer|null
     */
    public function getCustomer($request): null|Customer
    {
        return Customer::query()
            ->where('document', $request->document)
            ->where('mobile', $request->mobile)
            ->first();
    }
}
