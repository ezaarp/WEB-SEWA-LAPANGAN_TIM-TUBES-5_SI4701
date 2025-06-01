@extends('layouts.app')

@section('title', 'Dashboard Penanggung Jawab')

@section('actions')
<a href="{{ route('pj.approvals.index') }}" class="btn btn-primary">
    Persetujuan Menunggu
</a>
@endsection

@section('content')
<div class="row mb-4">

    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h5>Peminjaman Menunggu</h5>
                <h2 class="text-warning">{{ $data['pendingApprovals'] }}</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h5>Total Disetujui Hari Ini</h5>
                <h2 class="text-success">{{ $data['todayApprovals'] }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">

    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h6>Total Peminjaman</h6>
                <h3 class="text-primary">{{ $data['totalBookings'] }}</h3>
                <small class="text-muted">Bulan ini</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h6>Fasilitas Dikelola</h6>
                <h3 class="text-info">{{ $data['managedFacilities'] }}</h3>
                <small class="text-muted">Fasilitas</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h6>Rata-rata Harian</h6>
                <h3 class="text-secondary">{{ $data['avgDaily'] }}</h3>
                <small class="text-muted">Peminjaman/hari</small>
            </div>
        </div>
    </div>
</div>

<div class="row">

    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5>Persetujuan Menunggu</h5>
                <a href="{{ route('pj.approvals.index') }}" class="btn btn-sm btn-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body">
                @if($data['pendingBookings']->count() > 0)
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Mahasiswa</th>
                            <th>Fasilitas</th>
                            <th>Tanggal & Waktu</th>
                            <th>Jenis</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['pendingBookings'] as $booking)
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
                                <small>{{ ucfirst($booking->booking_type) }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <form action="{{ route('pj.approvals.approve', $booking) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="disetujui">
                                        <button type="submit" class="btn btn-sm btn-success">
                                            Setujui
                                        </button>
                                    </form>
                                    <a href="{{ route('pj.approvals.show', $booking) }}" class="btn btn-sm btn-secondary">
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center py-4">
                    <p class="text-muted">Tidak ada persetujuan menunggu</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection