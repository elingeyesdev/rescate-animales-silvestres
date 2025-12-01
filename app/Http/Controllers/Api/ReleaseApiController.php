<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReleaseRequest;
use App\Models\Release;
use App\Models\AnimalFile;
use App\Services\Animal\AnimalReleaseTransactionalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReleaseApiController extends Controller
{
    public function __construct(
        private readonly AnimalReleaseTransactionalService $releaseService
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * Listado de liberaciones.
     * Permite filtrar por ?animal_file_id=ID.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Release::with('animalFile.animal')
            ->orderByDesc('id');

        if ($request->filled('animal_file_id')) {
            $query->where('animal_file_id', (int) $request->input('animal_file_id'));
        }

        $releases = $query->paginate(20);

        return response()->json($releases);
    }

    /**
     * Crear una liberación usando el servicio transaccional
     * que valida el estado de salud y registra historial.
     */
    public function store(ReleaseRequest $request): JsonResponse
    {
        try {
            $release = $this->releaseService->create($request->validated());
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'No se pudo registrar la liberación.',
                'error'   => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Liberación creada correctamente.',
            'release' => $release->load('animalFile.animal'),
        ], 201);
    }

    /**
     * Detalle de una liberación.
     */
    public function show(Release $release): JsonResponse
    {
        return response()->json(
            $release->load('animalFile.animal')
        );
    }
}


