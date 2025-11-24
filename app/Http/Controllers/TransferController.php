<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\Rescuer;
use App\Models\Center;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TransferRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Animal;
use App\Services\Animal\AnimalTransferTransactionalService;
use Illuminate\Http\JsonResponse;

class TransferController extends Controller
{
    public function __construct(
        private readonly AnimalTransferTransactionalService $transferService
    ) {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $transfers = Transfer::with(['person','center'])->paginate();

        return view('transfer.index', compact('transfers'))
            ->with('i', ($request->input('page', 1) - 1) * $transfers->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $transfer = new Transfer();
        $rescuers = Rescuer::with('person')->where('aprobado', true)->orderBy('id')->get();
        $centers = Center::orderBy('nombre')->get(['id','nombre','latitud','longitud']);
        $animals = Animal::orderByDesc('id')->get(['id','nombre']);
        $people = \App\Models\Person::orderBy('nombre')->get(['id','nombre']);
        return view('transfer.create', compact('transfer','rescuers','centers','animals','people'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransferRequest $request): RedirectResponse
    {
        try {
            $this->transferService->create($request->validated());
        } catch (\Throwable $e) {
            return Redirect::back()->withInput()->with('error', 'No se pudo registrar el traslado: '.$e->getMessage());
        }

        return Redirect::route('transfers.index')
            ->with('success', 'Transferencia creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $transfer = Transfer::find($id);

        return view('transfer.show', compact('transfer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $transfer = Transfer::find($id);
        $rescuers = Rescuer::with('person')->where('aprobado', true)->orderBy('id')->get();
        $centers = Center::orderBy('nombre')->get(['id','nombre']);
        $animals = Animal::orderByDesc('id')->get(['id','nombre']);
        $people = \App\Models\Person::orderBy('nombre')->get(['id','nombre']);
        return view('transfer.edit', compact('transfer','rescuers','centers','animals','people'));
    }

    public function currentCenterByAnimal(Animal $animal): JsonResponse
    {
        $last = \App\Models\Transfer::where('animal_id', $animal->id)->orderByDesc('id')->first();
        $currentCenter = $last?->center;
        $centers = Center::orderBy('nombre')->get(['id','nombre','latitud','longitud']);
        $destinations = $centers->when($currentCenter, function ($c) use ($currentCenter) {
            return $c->where('id', '!=', $currentCenter->id);
        })->values();
        return response()->json([
            'current' => $currentCenter ? ['id' => $currentCenter->id, 'nombre' => $currentCenter->nombre, 'latitud' => $currentCenter->latitud, 'longitud' => $currentCenter->longitud] : null,
            'destinations' => $destinations,
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(TransferRequest $request, Transfer $transfer): RedirectResponse
    {
        $transfer->update($request->validated());

        return Redirect::route('transfers.index')
            ->with('success', 'Transferencia actualizada correctamente');
    }

    public function destroy($id): RedirectResponse
    {
        Transfer::find($id)->delete();

        return Redirect::route('transfers.index')
            ->with('success', 'Transferencia eliminada correctamente');
    }
}
