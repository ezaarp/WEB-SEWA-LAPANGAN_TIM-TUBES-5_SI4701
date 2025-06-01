@extends('layouts.app')

@section('title', 'Daftar Peminjaman')

@section('actions')
@if(auth()->user()->isMahasiswa())
<a href="{{ route('bookings.create') }}" class="btn btn-primary">
    Buat Peminjaman Baru
</a>
@endif
@endsection

@section('content')
<!-- Bookings List -->
@if($bookings->count() > 0)
<div class="row">
    @foreach($bookings as $booking)
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">
                        @if(!auth()->user()->isMahasiswa())
                            {{ $booking->user->name }}
                        @else
                            Peminjaman #{{ $booking->id }}
                        @endif
                    </h6>
                    @if(!auth()->user()->isMahasiswa())
                        <small class="text-muted">{{ $booking->user->nim }} | {{ $booking->user->email }}</small>
                    @endif
                </div>
                <div>
                    @if($booking->status === 'menunggu')
                        <span class="badge bg-warning">Menunggu</span>
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
                        <h6 class="text-muted">Fasilitas:</h6>
                        @foreach($booking->bookingDetails as $detail)
                            <span class="badge bg-light text-dark me-1 mb-1">
                                {{ $detail->facility->name }}
                            </span>
                        @endforeach
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Waktu:</h6>
                        <strong>{{ $booking->start_time->format('d/m/Y') }}</strong>
                        <br><small class="text-muted">
                            {{ $booking->start_time->format('H:i') }} - {{ $booking->end_time->format('H:i') }}
                        </small>
                    </div>
                </div>
                
                <hr class="my-3">
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Jenis Peminjaman:</h6>
                        @if($booking->booking_type === 'organisasi')
                            <span class="badge bg-info">Organisasi</span>
                            @if($booking->surat_izin)
                                <br><small class="text-success mt-1">
                                    Surat izin tersedia
                                </small>
                            @endif
                        @else
                            <span class="badge bg-secondary">Perseorangan</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Pembayaran:</h6>
                        @if($booking->payment)
                            @if($booking->payment->status === 'belum_dibayar')
                                <span class="badge bg-danger">Belum Dibayar</span>
                                <br><small class="text-muted">Rp {{ number_format($booking->payment->amount, 0, ',', '.') }}</small>
                            @elseif($booking->payment->status === 'dibayar')
                                <span class="badge bg-success">Dibayar</span>
                                <br><small class="text-muted">Rp {{ number_format($booking->payment->amount, 0, ',', '.') }}</small>
                            @else
                                <span class="badge bg-warning">Gagal</span>
                            @endif
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Diajukan {{ $booking->created_at->diffForHumans() }}
                    </small>
                    <div class="btn-group" role="group">
                        <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">
                            Detail
                        </a>
                        
                        @if(auth()->user()->id === $booking->user_id && $booking->status === 'menunggu')
                        <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-sm btn-outline-warning">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('bookings.destroy', $booking) }}" 
                              style="display: inline;" 
                              onsubmit="return confirm('Yakin ingin membatalkan peminjaman ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                Batal
                            </button>
                        </form>
                        @endif
                        
                        @if(auth()->user()->id === $booking->user_id && $booking->payment && $booking->payment->status === 'belum_dibayar')
                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal{{ $booking->id }}">
                            Bayar
                        </button>
                        
                        <!-- Payment Modal -->
                        <div class="modal fade" id="paymentModal{{ $booking->id }}">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center">
    {{ $bookings->withQueryString()->links() }}
</div>

@else
<div class="text-center py-5">
    <h4 class="text-muted mt-3">Tidak ada peminjaman ditemukan</h4>
    <p class="text-muted">Belum ada peminjaman yang dibuat</p>
    
    @if(auth()->user()->isMahasiswa())
    <a href="{{ route('bookings.create') }}" class="btn btn-primary">
        Buat Peminjaman Pertama
    </a>
    @endif
</div>
@endif

@endsection 