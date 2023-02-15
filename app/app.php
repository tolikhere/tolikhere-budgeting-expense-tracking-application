<?php

declare(strict_types = 1);

function getTransactionFiles(string $dirPath): array
{
    return array_map(
        fn($file) => $dirPath . $file,
        array_diff(scandir($dirPath), ['.', '..'])
    );
}

function getTransactions(string $fileName, ?callable $transactionHandler = null): array
{
    if (! file_exists($fileName)) {
        trigger_error("File {$fileName} does not exist.", E_USER_ERROR);
    }

    $transactions = array_map(
        function ($line) use ($transactionHandler) {
            if (is_null($transactionHandler)) {
                return str_getcsv($line);
            }

            return extractTransaction(str_getcsv($line));

        },
        file($fileName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
    );

    return array_slice($transactions, 1);
}

function extractTransaction(array $transactionRow): array
{
    [$date, $checkNumber, $description, $amount] = $transactionRow;

    $amount = (float) str_replace(['$', ','], '', $amount);

    return [
        'date'        => $date,
        'checkNumber' => $checkNumber,
        'description' => $description,
        'amount'      => $amount,
    ];
}

function calculateTotals(array $transactions): array
{
    $totals = [
        'netTotal'     => 0,
        'totalIncome'  => 0,
        'totalExpense' => 0,
    ];

    foreach ($transactions as $transaction) {
        $totals['netTotal'] += $transaction['amount'];

        if ($transaction['amount'] >= 0) {
            $totals['totalIncome'] += $transaction['amount'];
        } else {
            $totals['totalExpense'] += $transaction['amount'];
        }
    }

    return $totals;
}
