@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Detail Peminjaman #{{ $booking->id }}</h5>
            </div>
            <div class="card-body">
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Informasi Pemohon</h6>
                        <table class="table table-sm">
                            <tr>
                                <td>Nama</td>
                                <td>: {{ $booking->user->name }}</td>
                            </tr>
                            <tr>
                                <td>NIM</td>
                                <td>: {{ $booking->user->nim }}</td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>: {{ $booking->user->email }}</td>
                            </tr>
                            <tr>
                                <td>Kontak</td>
                                <td>: {{ $booking->user->contact }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Informasi Peminjaman</h6>
                        <table class="table table-sm">
                            <tr>
                                <td>Tanggal Mulai</td>
                                <td>: {{ $booking->start_time->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td>Tanggal Selesai</td>
                                <td>: {{ $booking->end_time->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td>Jenis Peminjaman</td>
                                <td>: {{ ucfirst($booking->booking_type) }}</td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>: 
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
                            </tr>
                        </table>
                    </div>
                </div>

                
                <div class="mb-4">
                    <h6>Fasilitas yang Dipinjam</h6>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nama Fasilitas</th>
                                    <th>Area</th>
                                    <th>Jenis</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($booking->bookingDetails as $detail)
                                <tr>
                                    <td>{{ $detail->facility->name }}</td>
                                    <td>{{ $detail->facility->area->name }}</td>
                                    <td>{{ $detail->facility->facilityType->name }}</td>
                                    <td>
                                        @if($detail->facility->status === 'available')
                                            <span class="badge bg-success">Tersedia</span>
                                        @else
                                            <span class="badge bg-danger">Tidak Tersedia</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                
                @if($booking->booking_type === 'organisasi' && $booking->surat_izin)
                <div class="mb-4">
                    <h6>Surat Izin Organisasi</h6>
                    <p>
                        <a href="{{ Storage::url($booking->surat_izin) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            Lihat Surat Izin
                        </a>
                    </p>
                </div>
                @endif

                
                @if($booking->status === 'menunggu')
                <div class="row">
                    <div class="col-md-6">
                        <form method="POST" action="{{ route('pj.approvals.approve', $booking) }}">
                            @csrf
                            <input type="hidden" name="status" value="disetujui">
                            <div class="mb-3">
                                <label for="approve_note" class="form-label">Catatan (opsional)</label>
                                <textarea class="form-control" id="approve_note" name="note" rows="3" 
                                          placeholder="Tambahkan catatan jika diperlukan"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                Setujui Peminjaman
                            </button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form method="POST" action="{{ route('pj.approvals.approve', $booking) }}">
                            @csrf
                            <input type="hidden" name="status" value="ditolak">
                            <div class="mb-3">
                                <label for="reject_note" class="form-label">Alasan penolakan <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="reject_note" name="note" rows="3" 
                                          placeholder="Jelaskan alasan penolakan" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger w-100">
                                Tolak Peminjaman
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('pj.approvals.index') }}" class="btn btn-secondary">
                    Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 