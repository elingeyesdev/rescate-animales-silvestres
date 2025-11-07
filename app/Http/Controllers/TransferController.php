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

class TransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $transfers = Transfer::with(['rescuer.person','center'])->paginate();

        return view('transfer.index', compact('transfers'))
            ->with('i', ($request->input('page', 1) - 1) * $transfers->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $transfer = new Transfer();
        $rescuers = Rescuer::with('person')->orderBy('id')->get();
        $centers = Center::orderBy('nombre')->get(['id','nombre']);
        return view('transfer.create', compact('transfer','rescuers','centers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransferRequest $request): RedirectResponse
    {
        Transfer::create($request->validated());

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
        $rescuers = Rescuer::with('person')->orderBy('id')->get();
        $centers = Center::orderBy('nombre')->get(['id','nombre']);
        return view('transfer.edit', compact('transfer','rescuers','centers'));
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
