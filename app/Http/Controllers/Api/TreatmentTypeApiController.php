<?php
 
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use App\Models\TreatmentType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
 
class TreatmentTypeApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
 
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            TreatmentType::orderBy('nombre')->get(['id','nombre'])
        );
    }
 
    public function show(TreatmentType $treatmentType): JsonResponse
    {
        return response()->json($treatmentType->only(['id','nombre']));
    }
}