<?php

namespace App\Imports\TransactionLedger;

use App\Models\Tenant\TransactionLedger;
use App\Traits\ImportHistoryTrait;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TransactionLedgerTrigger implements WithMultipleSheets
{
    use ImportHistoryTrait;

    private $totalRows;
    private $importedRows;
    private TransactionLedger $transaction_ledger;
    private $ledgers;

    public function __construct($transaction_ledger, &$ledgers)
    {
        $this->totalRows = 0;
        $this->importedRows = 0;
        $this->transaction_ledger = $transaction_ledger;
        $this->ledgers = &$ledgers;

        $this->_IH_init("transaction_ledger");

    }

    public function sheets(): array
    {
        return [
            // First we render payables
            //  - Total amount will calculate from it
            'payable' => new TransactionLedgerImport($this->transaction_ledger, "payable", $this->ledgers, $this),

            // Then we render chargeables so everything that is paid charged to drivers/booking
            'chargeable' => new TransactionLedgerImport($this->transaction_ledger, "chargeable", $this->ledgers, $this),
        ];
    }

}
