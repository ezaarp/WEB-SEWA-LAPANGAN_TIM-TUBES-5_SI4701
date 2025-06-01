<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ApprovalPenanggungjawab;
use App\Models\ApprovalAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    /**
     * Show pending approvals for penanggung jawab
     */
    public function penanggungjawabIndex()
    {
        $bookings = Booking::with(['user', 'bookingDetails.facility.area'])
                          ->where('status', 'menunggu')
                          ->latest()
                          ->paginate(10);

        return view('approvals.penanggung-jawab', compact('bookings'));
    }

    /**
     * Show specific booking details for penanggung jawab
     */
    public function penanggungjawabShow(Booking $booking)
    {
        $booking->load(['user', 'bookingDetails.facility.area', 'bookingDetails.facility.facilityType']);
        
        return view('approvals.penanggung-jawab-show', compact('booking'));
    }

    /**
     * Handle approval/rejection by penanggung jawab
     */
    public function penanggungjawabApprove(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'note' => 'nullable|string|max:500',
        ]);

        if ($booking->status !== 'menunggu') {
            return back()->with('error', 'Peminjaman sudah diproses sebelumnya.');
        }

        
        ApprovalPenanggungjawab::create([
            'booking_id' => $booking->id,
            'approved_by' => Auth::id(),
            'status' => $request->status,
            'note' => $request->note,
        ]);

        
        $booking->update(['status' => $request->status]);

        $message = $request->status === 'disetujui' 
            ? 'Peminjaman berhasil disetujui.' 
            : 'Peminjaman berhasil ditolak.';

        return back()->with('success', $message);
    }

    /**
     * Show bookings approved by penanggung jawab for admin verification
     */
    public function adminIndex()
    {
        $bookings = Booking::with(['user', 'bookingDetails.facility.area', 'payment', 'penanggungjawabApproval'])
                          ->where('status', 'disetujui')
                          ->whereDoesntHave('adminApproval')
                          ->latest()
                          ->paginate(10);

        return view('approvals.admin', compact('bookings'));
    }

    /**
     * Handle final approval/rejection by admin
     */
    public function adminApprove(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'note' => 'nullable|string|max:500',
        ]);

        if ($booking->status !== 'disetujui' || $booking->adminApproval) {
            return back()->with('error', 'Peminjaman tidak dapat diproses.');
        }

        
        ApprovalAdmin::create([
            'booking_id' => $booking->id,
            'approved_by' => Auth::id(),
            'status' => $request->status,
            'note' => $request->note,
        ]);

        
        $finalStatus = $request->status === 'disetujui' ? 'selesai' : 'ditolak';
        $booking->update(['status' => $finalStatus]);

        $message = $request->status === 'disetujui' 
            ? 'Peminjaman berhasil diverifikasi dan diselesaikan.' 
            : 'Peminjaman berhasil ditolak oleh admin.';

        return back()->with('success', $message);
    }

    /**
     * Verify booking by admin
     */
    public function adminVerify(Request $request, Booking $booking)
    {
        $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        if ($booking->status !== 'disetujui' || $booking->adminApproval) {
            return back()->with('error', 'Peminjaman tidak dapat diverifikasi.');
        }

        
        ApprovalAdmin::create([
            'booking_id' => $booking->id,
            'approved_by' => Auth::id(),
            'status' => 'disetujui',
            'note' => $request->note,
        ]);

        return back()->with('success', 'Peminjaman berhasil diverifikasi oleh admin.');
    }

    /**
     * Complete booking by admin
     */
    public function adminComplete(Request $request, Booking $booking)
    {
        if ($booking->status !== 'disetujui' || !$booking->adminApproval || !$booking->payment || $booking->payment->status !== 'dibayar') {
            return back()->with('error', 'Peminjaman tidak dapat diselesaikan.');
        }

        
        $booking->update(['status' => 'selesai']);

        return back()->with('success', 'Peminjaman berhasil diselesaikan.');
    }
}
