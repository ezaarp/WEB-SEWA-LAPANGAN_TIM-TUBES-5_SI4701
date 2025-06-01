@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('actions')
<div class="btn-group" role="group">
    <a href="{{ route('facilities.create') }}" class="btn btn-primary">
        Tambah Fasilitas
    </a>
    <a href="{{ route('admin.approvals.index') }}" class="btn btn-success">
        Verifikasi
    </a>
</div>
@endsection

@section('content')
<div class="row mb-4">

    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h5>Total Pengguna</h5>
                <h2 class="text-primary">{{ $data['totalUsers'] }}</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h5>Total Fasilitas</h5>
                <h2 class="text-info">{{ $data['totalFacilities'] }}</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h5>Total Peminjaman</h5>
                <h2 class="text-success">{{ $data['totalBookings'] }}</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h5>Pembayaran Pending</h5>
                <h2 class="text-warning">{{ $data['pendingPayments'] }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">

    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h6>Peminjaman Bulan Ini</h6>
                <h3 class="text-primary">{{ $data['monthlyBookings'] }}</h3>
                <small class="text-muted">{{ now()->format('F Y') }}</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h6>Fasilitas Tersedia</h6>
                <h3 class="text-success">{{ $data['facilitiesStatus']['available'] }}</h3>
                <small class="text-muted">Siap digunakan</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h6>Fasilitas Tidak Tersedia</h6>
                <h3 class="text-danger">{{ $data['facilitiesStatus']['unavailable'] }}</h3>
                <small class="text-muted">Sedang maintenance</small>
            </div>
        </div>
    </div>
</div>

<div class="row">

    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5>Peminjaman Terbaru</h5>
                <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-secondary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body">
                @if($data['recentBookings']->count() > 0)
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Mahasiswa</th>
                            <th>Fasilitas</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['recentBookings'] as $booking)
                        <tr>
                            <td>
                                {{ $booking->user->name }}
                                <br><small class="text-muted">{{ $booking->user->nim }}</small>
                            </td>
                            <td>
                                @foreach($booking->bookingDetails as $detail)
                                    {{ $detail->facility->name }}@if(!$loop->last), @endif
                                @endforeach
                            </td>
                            <td>
                                {{ $booking->start_time->format('d/m/Y') }}
                                <br><small class="text-muted">
                                    {{ $booking->start_time->format('H:i') }} - 
                                    {{ $booking->end_time->format('H:i') }}
                                </small>
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
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('bookings.show', $booking) }}" 
                                   class="btn btn-sm btn-secondary">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center py-4">
                    <p class="text-muted">Belum ada peminjaman</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 