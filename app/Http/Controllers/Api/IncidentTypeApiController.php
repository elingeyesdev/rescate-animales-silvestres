<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\IncidentTypeResource;
use App\Models\IncidentType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IncidentTypeApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = IncidentType::query();

        // Filter by active status if requested
        if ($request->has('activo')) {
            $query->where('activo', $request->boolean('activo'));
        }

        $incidentTypes = $query->orderBy('nombre')->get();

        return IncidentTypeResource::collection($incidentTypes);
    }

    /**
     * Display the specified resource.
     */
    public function show(IncidentType $incidentType): IncidentTypeResource
    {
        return new IncidentTypeResource($incidentType);
    }
}

