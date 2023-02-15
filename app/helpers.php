<?php

declare(strict_types = 1);

function formatDollarAmount(float $amount): string
{
    $isNegative = $amount < 0;

    return ($isNegative ? '-' : '') . '$' . number_format(abs($amount), 2);
}

function formatDate(string $date): string
{
    return date('M j, Y', strtotime($date));
}

function changeColorAmount(float $amount): string 
{
    return match($amount <=> 0) {
        1       => 'green',
        -1      => 'red',
        default => 'black'
    };
}
