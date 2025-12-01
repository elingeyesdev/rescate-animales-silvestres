<?php
 
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use App\Models\Veterinarian;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
 
class VeterinarianApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
 
    public function index(Request $request): JsonResponse
    {
        $items = Veterinarian::with('person')
            ->orderBy('id')
            ->get(['id','persona_id','especialidad','aprobado']);
        return response()->json($items);
    }
 
    public function show(Veterinarian $veterinarian): JsonResponse
    {
        return response()->json(
            $veterinarian->load('person')
        );
    }
}