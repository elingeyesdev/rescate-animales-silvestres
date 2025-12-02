<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PersonResource;
use App\Models\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PersonApiController extends Controller
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
        $query = Person::query();

        // Eager load relationships if requested
        $with = [];
        if ($request->has('with')) {
            $with = explode(',', $request->get('with'));
            $allowedRelations = ['user', 'cuidadorCenter'];
            $with = array_intersect($with, $allowedRelations);
        }

        if (!empty($with)) {
            $query->with($with);
        }

        // Filter by es_cuidador if requested
        if ($request->has('es_cuidador')) {
            $query->where('es_cuidador', $request->boolean('es_cuidador'));
        }

        // Filter by cuidador_aprobado if requested
        if ($request->has('cuidador_aprobado')) {
            $query->where('cuidador_aprobado', $request->boolean('cuidador_aprobado'));
        }

        // Search by nombre if provided
        if ($request->has('nombre')) {
            $query->where('nombre', 'like', '%' . $request->get('nombre') . '%');
        }

        // Search by ci if provided
        if ($request->has('ci')) {
            $query->where('ci', 'like', '%' . $request->get('ci') . '%');
        }

        $people = $query->orderBy('nombre')->paginate(20);

        return PersonResource::collection($people);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Person $person): PersonResource
    {
        // Eager load relationships if requested
        $with = [];
        if ($request->has('with')) {
            $with = explode(',', $request->get('with'));
            $allowedRelations = ['user', 'cuidadorCenter'];
            $with = array_intersect($with, $allowedRelations);
        }

        if (!empty($with)) {
            $person->load($with);
        }

        return new PersonResource($person);
    }
}

