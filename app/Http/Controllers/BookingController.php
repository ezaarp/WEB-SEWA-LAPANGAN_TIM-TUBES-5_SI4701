<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Facility;
use App\Models\BookingDetail;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->isMahasiswa()) {
            // Mahasiswa only sees their own bookings
            $query = $user->bookings();
        } else {
            // Admin and penanggung jawab see all bookings
            $query = Booking::query();
        }

        $query->with(['user', 'bookingDetails.facility.area', 'payment']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('start_time', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('end_time', '<=', $request->end_date);
        }

        // Search by facility name
        if ($request->filled('search')) {
            $query->whereHas('bookingDetails.facility', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $bookings = $query->latest()->paginate(10);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($bookings);
        }

        return view('bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $facilities = Facility::where('status', 'available')
                             ->with(['area', 'facilityType'])
                             ->get();
        
        return view('bookings.create', compact('facilities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'facility_ids' => 'required|array|min:1',
            'facility_ids.*' => 'exists:facilities,id',
            'booking_type' => 'required|in:perseorangan,organisasi',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'surat_izin' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Validate duration (max 2 hours)
        $startTime = Carbon::parse($validated['start_time']);
        $endTime = Carbon::parse($validated['end_time']);
        
        if ($startTime->diffInHours($endTime) > 2) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Durasi peminjaman maksimal 2 jam.',
                    'errors' => ['end_time' => ['Durasi peminjaman maksimal 2 jam.']]
                ], 422);
            }
            return back()->withErrors(['end_time' => 'Durasi peminjaman maksimal 2 jam.']);
        }

        // Check facility availability
        $conflictingBookings = Booking::whereIn('id', function ($query) use ($validated) {
            $query->select('booking_id')
                  ->from('booking_details')
                  ->whereIn('facility_id', $validated['facility_ids']);
        })
        ->where('status', '!=', 'ditolak')
        ->where(function ($q) use ($startTime, $endTime) {
            $q->whereBetween('start_time', [$startTime, $endTime])
              ->orWhereBetween('end_time', [$startTime, $endTime])
              ->orWhere(function ($q) use ($startTime, $endTime) {
                  $q->where('start_time', '<=', $startTime)
                    ->where('end_time', '>=', $endTime);
              });
        })->exists();

        if ($conflictingBookings) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Salah satu fasilitas tidak tersedia pada waktu yang dipilih.',
                    'errors' => ['facility_ids' => ['Salah satu fasilitas tidak tersedia pada waktu yang dipilih.']]
                ], 422);
            }
            return back()->withErrors(['facility_ids' => 'Salah satu fasilitas tidak tersedia pada waktu yang dipilih.']);
        }

        $booking = null;
        DB::transaction(function () use ($validated, $request, &$booking) {
            // Handle file upload
            $suratIzinPath = null;
            if ($request->hasFile('surat_izin')) {
                $suratIzinPath = $request->file('surat_izin')->store('surat-izin', 'public');
            }

            // Create booking
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'booking_type' => $validated['booking_type'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'surat_izin' => $suratIzinPath,
                'status' => 'menunggu',
            ]);

            // Create booking details
            foreach ($validated['facility_ids'] as $facilityId) {
                BookingDetail::create([
                    'booking_id' => $booking->id,
                    'facility_id' => $facilityId,
                ]);
            }

            // Create payment record
            Payment::create([
                'booking_id' => $booking->id,
                'amount' => 20000,
                'status' => 'belum_dibayar',
            ]);
        });

        if ($request->expectsJson() || $request->is('api/*')) {
            $booking->load(['user', 'bookingDetails.facility.area', 'payment']);
            return response()->json([
                'success' => true,
                'message' => 'Peminjaman berhasil diajukan. Menunggu persetujuan.',
                'data' => $booking
            ], 201);
        }

        return redirect()->route('bookings.index')
                        ->with('success', 'Peminjaman berhasil diajukan. Menunggu persetujuan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
        
        $booking->load([
            'user', 
            'bookingDetails.facility.area', 
            'payment', 
            'penanggungjawabApproval.approver',
            'adminApproval.approver'
        ]);
        
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json($booking);
        }
        
        return view('bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        $this->authorize('update', $booking);
        
        if ($booking->status !== 'menunggu') {
            return redirect()->route('bookings.show', $booking)
                           ->with('error', 'Peminjaman yang sudah diproses tidak dapat diubah.');
        }
        
        $facilities = Facility::where('status', 'available')
                             ->with(['area', 'facilityType'])
                             ->get();
        
        return view('bookings.edit', compact('booking', 'facilities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);
        
        if ($booking->status !== 'menunggu') {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Peminjaman yang sudah diproses tidak dapat diubah.'
                ], 422);
            }
            return redirect()->route('bookings.show', $booking)
                           ->with('error', 'Peminjaman yang sudah diproses tidak dapat diubah.');
        }

        $validated = $request->validate([
            'facility_ids' => 'required|array|min:1',
            'facility_ids.*' => 'exists:facilities,id',
            'booking_type' => 'required|in:perseorangan,organisasi',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'surat_izin' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Validate duration (max 2 hours)
        $startTime = Carbon::parse($validated['start_time']);
        $endTime = Carbon::parse($validated['end_time']);
        
        if ($startTime->diffInHours($endTime) > 2) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Durasi peminjaman maksimal 2 jam.',
                    'errors' => ['end_time' => ['Durasi peminjaman maksimal 2 jam.']]
                ], 422);
            }
            return back()->withErrors(['end_time' => 'Durasi peminjaman maksimal 2 jam.']);
        }

        DB::transaction(function () use ($validated, $request, $booking) {
            // Handle file upload
            if ($request->hasFile('surat_izin')) {
                $suratIzinPath = $request->file('surat_izin')->store('surat-izin', 'public');
                $booking->surat_izin = $suratIzinPath;
            }

            // Update booking
            $booking->update([
                'booking_type' => $validated['booking_type'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'surat_izin' => $booking->surat_izin,
            ]);

            // Update booking details
            $booking->bookingDetails()->delete();
            foreach ($validated['facility_ids'] as $facilityId) {
                BookingDetail::create([
                    'booking_id' => $booking->id,
                    'facility_id' => $facilityId,
                ]);
            }
        });

        if ($request->expectsJson() || $request->is('api/*')) {
            $booking->load(['user', 'bookingDetails.facility.area', 'payment']);
            return response()->json([
                'success' => true,
                'message' => 'Peminjaman berhasil diperbarui.',
                'data' => $booking
            ]);
        }

        return redirect()->route('bookings.show', $booking)
                        ->with('success', 'Peminjaman berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        $this->authorize('delete', $booking);
        
        if ($booking->status !== 'menunggu') {
            if (request()->expectsJson() || request()->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Peminjaman yang sudah diproses tidak dapat dibatalkan.'
                ], 422);
            }
            return redirect()->route('bookings.index')
                           ->with('error', 'Peminjaman yang sudah diproses tidak dapat dibatalkan.');
        }

        $booking->delete();

        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Peminjaman berhasil dibatalkan.'
            ]);
        }

        return redirect()->route('bookings.index')
                        ->with('success', 'Peminjaman berhasil dibatalkan.');
    }
}
