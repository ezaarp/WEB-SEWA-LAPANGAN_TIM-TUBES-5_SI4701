<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $areas = Area::withCount('facilities')->paginate(10);
        return view('areas.index', compact('areas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('areas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:areas',
        ]);

        Area::create($validated);

        return redirect()->route('areas.index')
                        ->with('success', 'Area berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Area $area)
    {
        $area->load('facilities.facilityType');
        return view('areas.show', compact('area'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Area $area)
    {
        return view('areas.edit', compact('area'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Area $area)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:areas,name,' . $area->id,
        ]);

        $area->update($validated);

        return redirect()->route('areas.index')
                        ->with('success', 'Area berhasil diperbarui.');
    }

    public function destroy(Area $area)
    {
        if ($area->facilities()->count() > 0) {
            return back()->with('error', 'Area tidak dapat dihapus karena masih memiliki fasilitas.');
        }

        $area->delete();

        return redirect()->route('areas.index')
                        ->with('success', 'Area berhasil dihapus.');
    }
}
