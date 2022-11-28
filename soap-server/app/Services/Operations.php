<?php

namespace App\Services;

use App\Models\Transaction;

class Operations
{
    private array $operations = [];

    public function __construct()
    {
        $this->operations = [
            Transaction::INCOME => fn($a, $b) => $a + $b,
            Transaction::EGRESS => fn($a, $b) => $a - $b,
        ];
    }

    public function calculateBalance(string $type, $balance, $amount)
    {
        return $this->operations[$type]($balance, $amount);
    }
}
