<?php

namespace App\Http\Controllers;

use App\Models\FacilityType;
use Illuminate\Http\Request;

class FacilityTypeController extends Controller
{
    
    public function index()
    {
        $facilityTypes = FacilityType::withCount('facilities')->paginate(10);
        return view('facility-types.index', compact('facilityTypes'));
    }

    public function create()
    {
        return view('facility-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:facility_types',
        ]);

        FacilityType::create($validated);

        return redirect()->route('facility-types.index')
                        ->with('success', 'Jenis fasilitas berhasil ditambahkan.');
    }

    public function show(FacilityType $facilityType)
    {
        $facilityType->load('facilities.area');
        return view('facility-types.show', compact('facilityType'));
    }

    public function edit(FacilityType $facilityType)
    {
        return view('facility-types.edit', compact('facilityType'));
    }

    public function update(Request $request, FacilityType $facilityType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:facility_types,name,' . $facilityType->id,
        ]);

        $facilityType->update($validated);

        return redirect()->route('facility-types.index')
                        ->with('success', 'Jenis fasilitas berhasil diperbarui.');
    }

    public function destroy(FacilityType $facilityType)
    {
        if ($facilityType->facilities()->count() > 0) {
            return back()->with('error', 'Jenis fasilitas tidak dapat dihapus karena masih digunakan.');
        }

        $facilityType->delete();

        return redirect()->route('facility-types.index')
                        ->with('success', 'Jenis fasilitas berhasil dihapus.');
    }
}
