<?php

namespace App\Http\Controllers;

use App\Models\Release;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ReleaseRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ReleaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $releases = Release::paginate();

        return view('release.index', compact('releases'))
            ->with('i', ($request->input('page', 1) - 1) * $releases->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $release = new Release();

        return view('release.create', compact('release'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReleaseRequest $request): RedirectResponse
    {
        Release::create($request->validated());

        return Redirect::route('releases.index')
            ->with('success', 'Release created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $release = Release::find($id);

        return view('release.show', compact('release'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $release = Release::find($id);

        return view('release.edit', compact('release'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReleaseRequest $request, Release $release): RedirectResponse
    {
        $release->update($request->validated());

        return Redirect::route('releases.index')
            ->with('success', 'Release updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Release::find($id)->delete();

        return Redirect::route('releases.index')
            ->with('success', 'Release deleted successfully');
    }
}
