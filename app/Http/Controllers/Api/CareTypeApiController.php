<?php
 
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use App\Models\CareType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
 
class CareTypeApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
 
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            CareType::orderBy('nombre')->get(['id','nombre'])
        );
    }
 
    public function show(CareType $careType): JsonResponse
    {
        return response()->json($careType->only(['id','nombre']));
    }
}