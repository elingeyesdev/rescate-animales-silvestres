<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnimalResource;
use App\Models\Animal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AnimalApiController extends Controller
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
        $query = Animal::query();

        // Eager load relationships if requested
        $with = [];
        if ($request->has('with')) {
            $with = explode(',', $request->get('with'));
            $allowedRelations = ['report', 'animalFiles'];
            $with = array_intersect($with, $allowedRelations);
        }

        if (!empty($with)) {
            $query->with($with);
        }

        // Filter by reporte_id if provided
        if ($request->has('reporte_id')) {
            $query->where('reporte_id', $request->get('reporte_id'));
        }

        // Search by nombre if provided
        if ($request->has('nombre')) {
            $query->where('nombre', 'like', '%' . $request->get('nombre') . '%');
        }

        $animals = $query->orderBy('id', 'desc')->paginate(20);

        return AnimalResource::collection($animals);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Animal $animal): AnimalResource
    {
        // Eager load relationships if requested
        $with = [];
        if ($request->has('with')) {
            $with = explode(',', $request->get('with'));
            $allowedRelations = ['report', 'animalFiles'];
            $with = array_intersect($with, $allowedRelations);
        }

        if (!empty($with)) {
            $animal->load($with);
        }

        return new AnimalResource($animal);
    }
}

