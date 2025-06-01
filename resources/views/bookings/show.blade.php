@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('actions')
@if(auth()->user()->id === $booking->user_id && $booking->status === 'menunggu')
<div class="btn-group" role="group">
    <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-warning">
        <i class="bi bi-pencil"></i> Edit
    </a>
    <form method="POST" action="{{ route('bookings.destroy', $booking) }}" 
          style="display: inline;" 
          onsubmit="return confirm('Yakin ingin membatalkan peminjaman ini?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">
            <i class="bi bi-trash"></i> Batalkan
        </button>
    </form>
</div>
@endif

@if(auth()->user()->id === $booking->user_id && $booking->payment && $booking->payment->status === 'belum_dibayar')
<button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal">
    <i class="bi bi-credit-card"></i> Upload Bukti Pembayaran
</button>
@endif
@endsection

@section('content')
<div class="row">
    <!-- Booking Information -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-check"></i> Informasi Peminjaman
                </h5>
                <div>
                    @if($booking->status === 'menunggu')
                        <span class="badge bg-warning">Menunggu Persetujuan</span>
                    @elseif($booking->status === 'disetujui')
                        <span class="badge bg-info">Disetujui PJ</span>
                    @elseif($booking->status === 'ditolak')
                        <span class="badge bg-danger">Ditolak</span>
                    @else
                        <span class="badge bg-success">Selesai</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">ID Peminjaman:</th>
                                <td><strong>#{{ $booking->id }}</strong></td>
                            </tr>
                            <tr>
                                <th>Peminjam:</th>
                                <td>
                                    <strong>{{ $booking->user->name }}</strong>
                                    <br><small class="text-muted">{{ $booking->user->nim }}</small>
                                    <br><small class="text-muted">{{ $booking->user->email }}</small>
                                </td>
                            </tr>
                            <tr>
                                <th>Jenis Peminjaman:</th>
                                <td>
                                    @if($booking->booking_type === 'organisasi')
                                        <span class="badge bg-info">Organisasi</span>
                                        @if($booking->surat_izin)
                                            <br><small class="text-success mt-1">
                                                <i class="bi bi-paperclip"></i> 
                                                <a href="{{ Storage::url($booking->surat_izin) }}" target="_blank">
                                                    Lihat Surat Izin
                                                </a>
                                            </small>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">Perseorangan</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Tanggal Peminjaman:</th>
                                <td><strong>{{ $booking->start_time->format('d F Y') }}</strong></td>
                            </tr>
                            <tr>
                                <th>Waktu:</th>
                                <td>
                                    <strong>{{ $booking->start_time->format('H:i') }} - {{ $booking->end_time->format('H:i') }}</strong>
                                    <br><small class="text-muted">
                                        Durasi: {{ $booking->start_time->diffInHours($booking->end_time) }} jam
                                    </small>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Fasilitas yang Dipinjam:</h6>
                        @foreach($booking->bookingDetails as $detail)
                        <div class="card border-light mb-2">
                            <div class="card-body p-2">
                                <strong>{{ $detail->facility->name }}</strong>
                                <br><small class="text-muted">
                                    <i class="bi bi-geo-alt"></i> {{ $detail->facility->area->name }}
                                    | <i class="bi bi-tag"></i> {{ $detail->facility->facilityType->name }}
                                </small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline / Status History -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history"></i> Timeline Persetujuan
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <!-- Submitted -->
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Peminjaman Diajukan</h6>
                            <p class="timeline-text text-muted">
                                Peminjaman berhasil diajukan dan menunggu persetujuan penanggung jawab.
                            </p>
                            <small class="text-muted">{{ $booking->created_at->format('d F Y, H:i') }}</small>
                        </div>
                    </div>

                    <!-- PJ Approval -->
                    @if($booking->penanggungjawabApproval)
                    <div class="timeline-item">
                        <div class="timeline-marker {{ $booking->status === 'ditolak' ? 'bg-danger' : 'bg-success' }}"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">
                                {{ $booking->status === 'ditolak' ? 'Ditolak' : 'Disetujui' }} Penanggung Jawab
                            </h6>
                            <p class="timeline-text text-muted">
                                Oleh: {{ $booking->penanggungjawabApproval->approver->name }}
                                @if($booking->penanggungjawabApproval->note)
                                    <br>Catatan: {{ $booking->penanggungjawabApproval->note }}
                                @endif
                            </p>
                            <small class="text-muted">{{ $booking->penanggungjawabApproval->created_at->format('d F Y, H:i') }}</small>
                        </div>
                    </div>
                    @endif

                    <!-- Admin Approval -->
                    @if($booking->adminApproval && $booking->status !== 'ditolak')
                    <div class="timeline-item">
                        <div class="timeline-marker bg-info"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Diverifikasi Admin</h6>
                            <p class="timeline-text text-muted">
                                Oleh: {{ $booking->adminApproval->approver->name }}
                                @if($booking->adminApproval->note)
                                    <br>Catatan: {{ $booking->adminApproval->note }}
                                @endif
                            </p>
                            <small class="text-muted">{{ $booking->adminApproval->created_at->format('d F Y, H:i') }}</small>
                        </div>
                    </div>
                    @endif

                    <!-- Payment -->
                    @if($booking->payment && $booking->payment->status === 'dibayar')
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Pembayaran Berhasil</h6>
                            <p class="timeline-text text-muted">
                                Pembayaran sebesar Rp {{ number_format($booking->payment->amount, 0, ',', '.') }} berhasil diverifikasi.
                            </p>
                            <small class="text-muted">{{ $booking->payment->updated_at->format('d F Y, H:i') }}</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Payment Info -->
        @if($booking->payment)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-credit-card"></i> Informasi Pembayaran
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th>Jumlah:</th>
                        <td><strong>Rp {{ number_format($booking->payment->amount, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            @if($booking->payment->status === 'belum_dibayar')
                                <span class="badge bg-danger">Belum Dibayar</span>
                            @elseif($booking->payment->status === 'dibayar')
                                <span class="badge bg-success">Dibayar</span>
                            @else
                                <span class="badge bg-warning">Gagal</span>
                            @endif
                        </td>
                    </tr>
                    @if($booking->payment->payment_proof)
                    <tr>
                        <th>Bukti Bayar:</th>
                        <td>
                            <a href="{{ route('payments.proof', $booking->payment) }}" 
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-file-earmark"></i> Lihat
                            </a>
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-gear"></i> Aksi
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                    </a>
                    
                    @if(auth()->user()->id === $booking->user_id && $booking->status === 'menunggu')
                    <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit Peminjaman
                    </a>
                    @endif
                    
                    @if(auth()->user()->isPenanggungjawab() && $booking->status === 'menunggu')
                    <a href="{{ route('pj.approvals.index') }}" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Kelola Persetujuan
                    </a>
                    @endif
                    
                    @if(auth()->user()->isAdmin() && $booking->status === 'disetujui')
                    <a href="{{ route('admin.approvals.index') }}" class="btn btn-info">
                        <i class="bi bi-shield-check"></i> Verifikasi Admin
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
@if(auth()->user()->id === $booking->user_id && $booking->payment && $booking->payment->status === 'belum_dibayar')
<div class="modal fade" id="paymentModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('payments.upload', $booking->payment) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Upload Bukti Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Jumlah Pembayaran:</strong> Rp {{ number_format($booking->payment->amount, 0, ',', '.') }}
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bukti Pembayaran <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="payment_proof" required 
                               accept=".jpg,.jpeg,.png,.pdf">
                        <div class="form-text">Format: JPG, PNG, PDF (Maks. 2MB)</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 5px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    padding-left: 20px;
}

.timeline-title {
    margin-bottom: 5px;
    font-weight: 600;
}

.timeline-text {
    margin-bottom: 5px;
    font-size: 0.9rem;
}
</style>
@endsection 