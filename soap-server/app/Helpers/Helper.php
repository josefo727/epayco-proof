<?php

use Illuminate\Support\Str;

/**
 * @param $amount
 * @return string
 */
function col_amount_format($amount): string
{
    $amount = isset($amount) && is_numeric($amount) ? (float) $amount : 0;
    return 'COP $' . number_format($amount, 2, ',', '.');
}

/**
 * @throws Exception
 */
function generate_token(): string
{
    return (string) random_int(100000, 999999);
}
