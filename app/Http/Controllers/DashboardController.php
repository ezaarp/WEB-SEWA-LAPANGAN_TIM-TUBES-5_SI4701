<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\User;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        switch ($user->role) {
            case 'mahasiswa':
                return $this->mahasiswaDashboard();
            case 'penanggung_jawab':
                return $this->penanggungjawabDashboard();
            case 'admin':
                return $this->adminDashboard();
            default:
                abort(403, 'Unauthorized');
        }
    }

    private function mahasiswaDashboard()
    {
        $user = Auth::user();
        
        $data = [
            'totalBookings' => $user->bookings()->count(),
            'pendingBookings' => $user->bookings()->where('status', 'menunggu')->count(),
            'approvedBookings' => $user->bookings()->where('status', 'disetujui')->count(),
            'recentBookings' => $user->bookings()->with(['bookingDetails.facility', 'payment'])
                                    ->latest()->take(5)->get(),
            'availableFacilities' => Facility::where('status', 'available')->count(),
        ];

        return view('dashboard.mahasiswa', compact('data'));
    }

    private function penanggungjawabDashboard()
    {
        $data = [
            'pendingApprovals' => Booking::where('status', 'menunggu')->count(),
            'totalBookings' => Booking::count(),
            'todayApprovals' => Booking::whereDate('created_at', today())
                                    ->where('status', 'disetujui')->count(),
            'managedFacilities' => Facility::count(),
            'avgDaily' => round(Booking::count() / max(1, Booking::selectRaw('COUNT(DISTINCT DATE(created_at))')->value('COUNT(DISTINCT DATE(created_at))')), 1),
            'pendingBookings' => Booking::with(['user', 'bookingDetails.facility'])
                                      ->where('status', 'menunggu')
                                      ->latest()->take(10)->get(),
        ];

        return view('dashboard.penanggung-jawab', compact('data'));
    }

    private function adminDashboard()
    {
        $data = [
            'totalUsers' => User::count(),
            'totalFacilities' => Facility::count(),
            'totalBookings' => Booking::count(),
            'pendingPayments' => Payment::where('status', 'belum_dibayar')->count(),
            'monthlyBookings' => Booking::whereMonth('created_at', now()->month)->count(),
            'recentBookings' => Booking::with(['user', 'bookingDetails.facility', 'payment'])
                                      ->latest()->take(10)->get(),
            'facilitiesStatus' => [
                'available' => Facility::where('status', 'available')->count(),
                'unavailable' => Facility::where('status', 'unavailable')->count(),
            ],
        ];

        return view('dashboard.admin', compact('data'));
    }
}
