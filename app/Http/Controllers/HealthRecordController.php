<?php

namespace App\Http\Controllers;

use App\Models\HealthRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\HealthRecordRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class HealthRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $healthRecords = HealthRecord::paginate();

        return view('health-record.index', compact('healthRecords'))
            ->with('i', ($request->input('page', 1) - 1) * $healthRecords->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $healthRecord = new HealthRecord();

        return view('health-record.create', compact('healthRecord'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HealthRecordRequest $request): RedirectResponse
    {
        HealthRecord::create($request->validated());

        return Redirect::route('health-records.index')
            ->with('success', 'HealthRecord created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $healthRecord = HealthRecord::find($id);

        return view('health-record.show', compact('healthRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $healthRecord = HealthRecord::find($id);

        return view('health-record.edit', compact('healthRecord'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HealthRecordRequest $request, HealthRecord $healthRecord): RedirectResponse
    {
        $healthRecord->update($request->validated());

        return Redirect::route('health-records.index')
            ->with('success', 'HealthRecord updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        HealthRecord::find($id)->delete();

        return Redirect::route('health-records.index')
            ->with('success', 'HealthRecord deleted successfully');
    }
}
