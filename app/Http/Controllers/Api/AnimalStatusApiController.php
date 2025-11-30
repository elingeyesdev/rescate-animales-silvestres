<?php
 
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use App\Models\AnimalStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
 
class AnimalStatusApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
 
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            AnimalStatus::orderBy('nombre')->get(['id','nombre'])
        );
    }
 
    public function show(AnimalStatus $animalStatus): JsonResponse
    {
        return response()->json($animalStatus->only(['id','nombre']));
    }
}