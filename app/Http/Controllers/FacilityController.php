<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Area;
use App\Models\FacilityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Facility::with(['area', 'facilityType']);

        
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        
        if ($request->filled('facility_type_id')) {
            $query->where('facility_type_id', $request->facility_type_id);
        }

        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $facilities = $query->paginate(12);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($facilities);
        }

        $areas = Area::all();
        $facilityTypes = FacilityType::all();

        return view('facilities.index', compact('facilities', 'areas', 'facilityTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Facility::class);
        
        $areas = Area::all();
        $facilityTypes = FacilityType::all();
        
        return view('facilities.create', compact('areas', 'facilityTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Facility::class);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'area_id' => 'required|exists:areas,id',
            'facility_type_id' => 'required|exists:facility_types,id',
            'status' => 'required|in:available,unavailable',
        ]);

        $facility = Facility::create($validated);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Fasilitas berhasil ditambahkan.',
                'data' => $facility->load(['area', 'facilityType'])
            ], 201);
        }

        return redirect()->route('facilities.index')
                        ->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Facility $facility)
    {
        $facility->load(['area', 'facilityType', 'bookingDetails.booking.user']);
        
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json($facility);
        }
        
        return view('facilities.show', compact('facility'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Facility $facility)
    {
        $this->authorize('update', $facility);
        
        $areas = Area::all();
        $facilityTypes = FacilityType::all();
        
        return view('facilities.edit', compact('facility', 'areas', 'facilityTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Facility $facility)
    {
        $this->authorize('update', $facility);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'area_id' => 'required|exists:areas,id',
            'facility_type_id' => 'required|exists:facility_types,id',
            'status' => 'required|in:available,unavailable',
        ]);

        $facility->update($validated);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Fasilitas berhasil diperbarui.',
                'data' => $facility->load(['area', 'facilityType'])
            ]);
        }

        return redirect()->route('facilities.index')
                        ->with('success', 'Fasilitas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Facility $facility)
    {
        $this->authorize('delete', $facility);
        
        $facility->delete();

        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Fasilitas berhasil dihapus.'
            ]);
        }

        return redirect()->route('facilities.index')
                        ->with('success', 'Fasilitas berhasil dihapus.');
    }

    /**
     * Get available facilities for booking (API)
     */
    public function available(Request $request)
    {
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');
        
        $query = Facility::where('status', 'available')
                        ->with(['area', 'facilityType']);

        
        if ($startTime && $endTime) {
            $query->whereDoesntHave('bookingDetails.booking', function ($q) use ($startTime, $endTime) {
                $q->where('status', '!=', 'ditolak')
                  ->where(function ($q) use ($startTime, $endTime) {
                      $q->whereBetween('start_time', [$startTime, $endTime])
                        ->orWhereBetween('end_time', [$startTime, $endTime])
                        ->orWhere(function ($q) use ($startTime, $endTime) {
                            $q->where('start_time', '<=', $startTime)
                              ->where('end_time', '>=', $endTime);
                        });
                  });
            });
        }

        $facilities = $query->get();

        return response()->json($facilities);
    }
}
