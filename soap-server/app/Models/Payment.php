<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Jenssegers\Mongodb\Eloquent\Model;

class Payment extends Model
{
    protected $collection = 'payments';

    protected $primaryKey = '_id';

    protected $guarded = [];

    public function wallet(): BelongsTo|\Jenssegers\Mongodb\Relations\BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
