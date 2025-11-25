<?php

namespace App\Services\Animal;

use App\Models\Transfer;
use Illuminate\Support\Facades\DB;
use App\Services\Animal\AnimalTransferHistoryService;

class AnimalTransferTransactionalService
{
    public function __construct(
        private readonly AnimalTransferHistoryService $historyService
    ) {
    }
	public function create(array $data): Transfer
	{
		return DB::transaction(function () use ($data) {
            $transfer = Transfer::create($data);

			// Registrar historial según el caso
            if (!empty($data['animal_id'])) {
                $this->historyService->logInternalTransfer($transfer);
            } else {
                $this->historyService->logFirstTransfer($transfer, $data['reporte_id'] ?? ($transfer->reporte_id ?? null));
            }

			return $transfer;
		});
	}
}




