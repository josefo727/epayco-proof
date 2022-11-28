<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Jenssegers\Mongodb\Eloquent\Model;

class Transaction extends Model
{
    protected $collection = 'transactions';

    protected $primaryKey = '_id';

    protected $guarded = [];

    public const INCOME = 'income';
    public const EGRESS = 'egress';

    public array $types = [
        self::INCOME,
        self::EGRESS
    ];

    public function wallet(): BelongsTo|\Jenssegers\Mongodb\Relations\BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

}
