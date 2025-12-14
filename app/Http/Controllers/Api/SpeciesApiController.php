<?php
 
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use App\Models\Species;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
 
class SpeciesApiController extends Controller
{
    public function __construct()
    {
        // Excluir el método index del middleware de autenticación para permitir acceso público
        $this->middleware('auth:sanctum')->except(['index']);
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json(
            Species::orderBy('nombre')->get(['id','nombre'])
        );
    }
 
    public function show(Species $species): JsonResponse
    {
        return response()->json($species->only(['id','nombre']));
    }
}