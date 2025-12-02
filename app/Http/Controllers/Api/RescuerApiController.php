<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RescuerResource;
use App\Models\Rescuer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RescuerApiController extends Controller
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
        $query = Rescuer::with('person');

        // Filter by approved status if requested
        if ($request->has('aprobado')) {
            $query->where('aprobado', $request->boolean('aprobado'));
        }

        $rescuers = $query->orderBy('id')->get();

        return RescuerResource::collection($rescuers);
    }

    /**
     * Display the specified resource.
     */
    public function show(Rescuer $rescuer): RescuerResource
    {
        $rescuer->load('person');
        return new RescuerResource($rescuer);
    }
}

