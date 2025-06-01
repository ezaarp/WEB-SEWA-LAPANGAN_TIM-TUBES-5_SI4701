<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Display payment list for admin
     */
    public function index()
    {
        $payments = Payment::with(['booking.user', 'booking.bookingDetails.facility'])
                          ->latest()
                          ->paginate(10);

        return view('payments.index', compact('payments'));
    }

    /**
     * Upload payment proof by mahasiswa
     */
    public function uploadProof(Request $request, Payment $payment)
    {
        // Check if user owns this payment
        if ($payment->booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Store the file
        $path = $request->file('payment_proof')->store('payment-proofs', 'public');

        // Update payment record
        $payment->update([
            'payment_proof' => $path,
            'status' => 'dibayar',
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diupload.');
    }

    /**
     * Verify payment by admin
     */
    public function verify(Request $request, Payment $payment)
    {
        $request->validate([
            'status' => 'required|in:dibayar,gagal',
        ]);

        $payment->update(['status' => $request->status]);

        $message = $request->status === 'dibayar' 
            ? 'Pembayaran berhasil diverifikasi.' 
            : 'Pembayaran ditandai sebagai gagal.';

        return back()->with('success', $message);
    }

    /**
     * Show payment details
     */
    public function show(Payment $payment)
    {
        $payment->load(['booking.user', 'booking.bookingDetails.facility.area']);
        
        return view('payments.show', compact('payment'));
    }

    /**
     * Show payment proof file
     */
    public function showProof(Payment $payment)
    {
        // Check if payment proof exists
        if (!$payment->payment_proof) {
            abort(404, 'Bukti pembayaran tidak ditemukan.');
        }

        // Check if file exists in storage
        $filePath = storage_path('app/public/' . $payment->payment_proof);
        if (!file_exists($filePath)) {
            abort(404, 'File bukti pembayaran tidak ditemukan.');
        }

        // Get file extension to determine content type
        $extension = pathinfo($payment->payment_proof, PATHINFO_EXTENSION);
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'pdf' => 'application/pdf',
        ];

        $contentType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';

        return response()->file($filePath, [
            'Content-Type' => $contentType,
        ]);
    }

    /**
     * Show payment proof file for mahasiswa (own payments only)
     */
    public function showProofMahasiswa(Payment $payment)
    {
        // Check if user owns this payment
        if ($payment->booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Check if payment proof exists
        if (!$payment->payment_proof) {
            abort(404, 'Bukti pembayaran tidak ditemukan.');
        }

        // Check if file exists in storage
        $filePath = storage_path('app/public/' . $payment->payment_proof);
        if (!file_exists($filePath)) {
            abort(404, 'File bukti pembayaran tidak ditemukan.');
        }

        // Get file extension to determine content type
        $extension = pathinfo($payment->payment_proof, PATHINFO_EXTENSION);
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'pdf' => 'application/pdf',
        ];

        $contentType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';

        return response()->file($filePath, [
            'Content-Type' => $contentType,
        ]);
    }
}
