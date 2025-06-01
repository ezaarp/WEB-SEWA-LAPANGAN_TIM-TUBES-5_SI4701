@extends('layouts.app')

@section('title', 'Detail Fasilitas')

@section('actions')
@if(auth()->user()->isAdmin())
<div class="btn-group" role="group">
    <a href="{{ route('facilities.edit', $facility) }}" class="btn btn-warning">
        <i class="bi bi-pencil"></i> Edit
    </a>
    <form method="POST" action="{{ route('facilities.destroy', $facility) }}" 
          style="display: inline;" 
          onsubmit="return confirm('Yakin ingin menghapus fasilitas ini?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">
            <i class="bi bi-trash"></i> Hapus
        </button>
    </form>
</div>
@elseif(auth()->user()->isMahasiswa() && $facility->status === 'available')
<a href="{{ route('bookings.create') }}" class="btn btn-primary">
    <i class="bi bi-calendar-plus"></i> Ajukan Peminjaman
</a>
@endif
@endsection

@section('content')
<div class="row">

    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-building"></i> Informasi Fasilitas
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="30%">Nama Fasilitas:</th>
                        <td><strong>{{ $facility->name }}</strong></td>
                    </tr>
                    <tr>
                        <th>Area:</th>
                        <td>{{ $facility->area->name }}</td>
                    </tr>
                    <tr>
                        <th>Jenis Fasilitas:</th>
                        <td>{{ $facility->facilityType->name }}</td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            @if($facility->status === 'available')
                                <span class="badge bg-success">Tersedia</span>
                            @else
                                <span class="badge bg-danger">Tidak Tersedia</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Dibuat:</th>
                        <td>{{ $facility->created_at->format('d F Y, H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Terakhir Update:</th>
                        <td>{{ $facility->updated_at->format('d F Y, H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-graph-up"></i> Statistik Peminjaman
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            <h4 class="text-primary mb-1">{{ $facility->bookingDetails()->count() }}</h4>
                            <small class="text-muted">Total Peminjaman</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            <h4 class="text-success mb-1">
                                {{ $facility->bookingDetails()->whereHas('booking', function($q) {
                                    $q->where('status', 'selesai');
                                })->count() }}
                            </h4>
                            <small class="text-muted">Berhasil</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <h4 class="text-warning mb-1">
                                {{ $facility->bookingDetails()->whereHas('booking', function($q) {
                                    $q->where('status', 'menunggu');
                                })->count() }}
                            </h4>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <h4 class="text-danger mb-1">
                                {{ $facility->bookingDetails()->whereHas('booking', function($q) {
                                    $q->where('status', 'ditolak');
                                })->count() }}
                            </h4>
                            <small class="text-muted">Ditolak</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-clock-history"></i> Riwayat Peminjaman
        </h5>
    </div>
    <div class="card-body p-0">
        @if($facility->bookingDetails->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Peminjam</th>
                        <th>Tanggal & Waktu</th>
                        <th>Jenis</th>
                        <th>Status</th>
                        <th>Pembayaran</th>
                        <th>Diajukan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($facility->bookingDetails()->with(['booking.user', 'booking.payment'])->latest()->limit(10)->get() as $detail)
                    @php $booking = $detail->booking @endphp
                    <tr>
                        <td>
                            <strong>{{ $booking->user->name }}</strong>
                            <br><small class="text-muted">{{ $booking->user->nim }}</small>
                        </td>
                        <td>
                            <strong>{{ $booking->start_time->format('d/m/Y') }}</strong>
                            <br><small class="text-muted">
                                {{ $booking->start_time->format('H:i') }} - 
                                {{ $booking->end_time->format('H:i') }}
                            </small>
                        </td>
                        <td>
                            @if($booking->booking_type === 'organisasi')
                                <span class="badge bg-info">Organisasi</span>
                            @else
                                <span class="badge bg-secondary">Perseorangan</span>
                            @endif
                        </td>
                        <td>
                            @if($booking->status === 'menunggu')
                                <span class="badge bg-warning">Menunggu</span>
                            @elseif($booking->status === 'disetujui')
                                <span class="badge bg-info">Disetujui PJ</span>
                            @elseif($booking->status === 'ditolak')
                                <span class="badge bg-danger">Ditolak</span>
                            @else
                                <span class="badge bg-success">Selesai</span>
                            @endif
                        </td>
                        <td>
                            @if($booking->payment)
                                @if($booking->payment->status === 'belum_dibayar')
                                    <span class="badge bg-danger">Belum Dibayar</span>
                                @elseif($booking->payment->status === 'dibayar')
                                    <span class="badge bg-success">Dibayar</span>
                                @else
                                    <span class="badge bg-warning">Gagal</span>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">
                                {{ $booking->created_at->diffForHumans() }}
                            </small>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-4">
            <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2">Belum ada riwayat peminjaman untuk fasilitas ini</p>
        </div>
        @endif
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('facilities.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Fasilitas
    </a>
</div>

@endsection 