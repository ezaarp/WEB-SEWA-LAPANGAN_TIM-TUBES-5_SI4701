@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@section('actions')
<a href="{{ route('bookings.create') }}" class="btn btn-primary">
    Buat Peminjaman Baru
</a>
@endsection

@section('content')
<div class="row mb-4">

    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h5>Total Peminjaman</h5>
                <h2 class="text-primary">{{ $data['totalBookings'] }}</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h5>Menunggu</h5>
                <h2 class="text-warning">{{ $data['pendingBookings'] }}</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h5>Disetujui</h5>
                <h2 class="text-success">{{ $data['approvedBookings'] }}</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h5>Fasilitas Tersedia</h5>
                <h2 class="text-info">{{ $data['availableFacilities'] }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row">

    <div class="col-md-8 mb-4">
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
                            <th>Fasilitas</th>
                            <th>Tanggal & Waktu</th>
                            <th>Status</th>
                            <th>Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['recentBookings'] as $booking)
                        <tr>
                            <td>
                                @foreach($booking->bookingDetails as $detail)
                                    {{ $detail->facility->name }}@if(!$loop->last), @endif
                                @endforeach
                            </td>
                            <td>
                                {{ $booking->start_time->format('d/m/Y H:i') }} - 
                                {{ $booking->end_time->format('H:i') }}
                            </td>
                            <td>
                                @if($booking->status === 'menunggu')
                                    <span class="badge bg-warning">Menunggu</span>
                                @elseif($booking->status === 'disetujui')
                                    <span class="badge bg-success">Disetujui</span>
                                @elseif($booking->status === 'ditolak')
                                    <span class="badge bg-danger">Ditolak</span>
                                @else
                                    <span class="badge bg-primary">Selesai</span>
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
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center py-4">
                    <p class="text-muted">Belum ada peminjaman</p>
                    <a href="{{ route('bookings.create') }}" class="btn btn-primary">
                        Buat Peminjaman Pertama
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    

    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5>Menu</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('bookings.create') }}" class="btn btn-primary">
                        Buat Peminjaman
                    </a>
                    <a href="{{ route('facilities.index') }}" class="btn btn-secondary">
                        Lihat Fasilitas
                    </a>
                    <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
                        Histori Peminjaman
                    </a>
                </div>
                
                <hr>
                
                <div class="text-center">
                    <small class="text-muted">
                        Biaya peminjaman: <strong>Rp 20.000</strong>
                    </small>
                </div>
            </div>
        </div>
        

        <div class="card mt-3">
            <div class="card-header">
                <h6>Tips Peminjaman</h6>
            </div>
            <div class="card-body">
                <ul class="small">
                    <li>Maksimal durasi peminjaman: 2 jam</li>
                    <li>Harap datang tepat waktu</li>
                    <li>Bawa KTM untuk verifikasi</li>
                    <li>Fasilitas harus dikembalikan dalam kondisi bersih</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection 