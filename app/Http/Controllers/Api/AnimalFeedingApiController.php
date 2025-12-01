<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transactions\FeedingProcessRequest;
use App\Models\CareFeeding;
use App\Services\Animal\AnimalFeedingTransactionalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnimalFeedingApiController extends Controller
{
    public function __construct(
        private readonly AnimalFeedingTransactionalService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * Listado de registros de alimentación.
     * Permite filtrar por hoja de animal con ?animal_file_id=ID.
     */
    public function index(Request $request): JsonResponse
    {
        $query = CareFeeding::with([
            'care.animalFile.animal',
            'feedingType',
            'feedingFrequency',
            'feedingPortion',
        ])->orderByDesc('id');

        if ($request->filled('animal_file_id')) {
            $animalFileId = (int) $request->input('animal_file_id');
            $query->whereHas('care', function ($q) use ($animalFileId) {
                $q->where('hoja_animal_id', $animalFileId);
            });
        }

        $feedings = $query->paginate(20);

        return response()->json($feedings);
    }

    /**
     * Registrar un evento de alimentación para una Hoja de Animal.
     * Usa el servicio transaccional que también crea historial.
     */
    public function store(FeedingProcessRequest $request): JsonResponse
    {
        $result = $this->service->registerFeeding($request->validated());

        return response()->json([
            'message'     => 'Alimentación registrada correctamente.',
            'care'        => $result['care'],
            'careFeeding' => $result['careFeeding']->load([
                'care.animalFile.animal',
                'feedingType',
                'feedingFrequency',
                'feedingPortion',
            ]),
        ], 201);
    }

    /**
     * Detalle de un registro de alimentación.
     */
    public function show(CareFeeding $animal_feeding): JsonResponse
    {
        return response()->json(
            $animal_feeding->load([
                'care.animalFile.animal',
                'feedingType',
                'feedingFrequency',
                'feedingPortion',
            ])
        );
    }
}