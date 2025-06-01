@extends('layouts.app')

@section('title', 'Verifikasi Admin')

@section('content')

@if(isset($stats))
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body text-center">
                <h4>{{ $stats['pending_admin'] ?? 0 }}</h4>
                <small>Menunggu Verifikasi</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body text-center">
                <h4>{{ $stats['pending_payment'] ?? 0 }}</h4>
                <small>Menunggu Pembayaran</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body text-center">
                <h4>{{ $stats['completed'] ?? 0 }}</h4>
                <small>Selesai</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body text-center">
                <h4>{{ $stats['total'] ?? 0 }}</h4>
                <small>Total Hari Ini</small>
            </div>
        </div>
    </div>
</div>
@endif


@if(isset($bookings) && $bookings->count() > 0)
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Daftar Peminjaman untuk Verifikasi</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mahasiswa</th>
                        <th>Fasilitas</th>
                        <th>Tanggal & Waktu</th>
                        <th>Jenis</th>
                        <th>Status PJ</th>
                        <th>Pembayaran</th>
                        <th>Status Admin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                    <tr>
                        <td>
                            <strong>{{ $booking->user->name }}</strong>
                            <br><small class="text-muted">{{ $booking->user->nim }}</small>
                            <br><small class="text-muted">{{ $booking->user->email }}</small>
                        </td>
                        <td>
                            @foreach($booking->bookingDetails as $detail)
                                <span class="badge bg-light text-dark me-1 mb-1">
                                    {{ $detail->facility->name }}
                                </span>
                            @endforeach
                        </td>
                        <td>
                            <strong>{{ $booking->start_time->format('d/m/Y') }}</strong>
                            <br><small class="text-muted">
                                {{ $booking->start_time->format('H:i') }} - 
                                {{ $booking->end_time->format('H:i') }}
                            </small>
                            <br><small class="text-muted">
                                Durasi: {{ $booking->start_time->diffInHours($booking->end_time) }} jam
                            </small>
                        </td>
                        <td>
                            @if($booking->booking_type === 'organisasi')
                                <span class="badge bg-info">Organisasi</span>
                                @if($booking->surat_izin)
                                    <br><small class="text-success">
                                        <i class="bi bi-paperclip"></i> 
                                        <a href="{{ Storage::url($booking->surat_izin) }}" target="_blank">
                                            Surat Izin
                                        </a>
                                    </small>
                                @endif
                            @else
                                <span class="badge bg-secondary">Perseorangan</span>
                            @endif
                        </td>
                        <td>
                            @if($booking->penanggungjawabApproval)
                                <span class="badge bg-success">Disetujui PJ</span>
                                <br><small class="text-muted">
                                    {{ $booking->penanggungjawabApproval->created_at->diffForHumans() }}
                                </small>
                                @if($booking->penanggungjawabApproval->note)
                                    <br><small class="text-muted">{{ $booking->penanggungjawabApproval->note }}</small>
                                @endif
                            @else
                                <span class="badge bg-warning">Menunggu PJ</span>
                            @endif
                        </td>
                        <td>
                            @if($booking->payment)
                                @if($booking->payment->status === 'belum_dibayar')
                                    <span class="badge bg-danger">Belum Dibayar</span>
                                @elseif($booking->payment->status === 'dibayar')
                                    <span class="badge bg-success">Dibayar</span>
                                    @if($booking->payment->payment_proof)
                                        <br><small class="text-success">
                                            <a href="{{ route('admin.payments.proof', $booking->payment) }}" target="_blank">
                                                Bukti Bayar
                                            </a>
                                        </small>
                                    @endif
                                @else
                                    <span class="badge bg-warning">Gagal</span>
                                @endif
                                <br><small class="text-muted">
                                    Rp {{ number_format($booking->payment->amount, 0, ',', '.') }}
                                </small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($booking->adminApproval)
                                <span class="badge bg-success">Terverifikasi</span>
                                <br><small class="text-muted">
                                    {{ $booking->adminApproval->created_at->diffForHumans() }}
                                </small>
                            @elseif($booking->status === 'disetujui')
                                <span class="badge bg-info">Menunggu Verifikasi</span>
                            @else
                                <span class="badge bg-secondary">Belum Disetujui PJ</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('bookings.show', $booking) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                
                                @if($booking->status === 'disetujui' && !$booking->adminApproval)
                                <button type="button" 
                                        class="btn btn-sm btn-info" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#verifyModal{{ $booking->id }}">
                                    <i class="bi bi-shield-check"></i>
                                </button>
                                @endif
                                
                                @if($booking->payment && $booking->payment->status === 'dibayar' && $booking->adminApproval)
                                <button type="button" 
                                        class="btn btn-sm btn-success" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#completeModal{{ $booking->id }}">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                                @endif
                            </div>

                            @if($booking->status === 'disetujui' && !$booking->adminApproval)
                            
                            <div class="modal fade" id="verifyModal{{ $booking->id }}">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.approvals.verify', $booking) }}">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Verifikasi Admin</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Apakah Anda yakin ingin memverifikasi peminjaman ini?</p>
                                                <div class="alert alert-info">
                                                    <strong>Mahasiswa:</strong> {{ $booking->user->name }}<br>
                                                    <strong>Fasilitas:</strong> 
                                                    @foreach($booking->bookingDetails as $detail)
                                                        {{ $detail->facility->name }}{{ !$loop->last ? ', ' : '' }}
                                                    @endforeach<br>
                                                    <strong>Waktu:</strong> {{ $booking->start_time->format('d/m/Y H:i') }} - {{ $booking->end_time->format('H:i') }}<br>
                                                    <strong>Disetujui PJ:</strong> {{ $booking->penanggungjawabApproval->approver->name }}
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Catatan Verifikasi (opsional)</label>
                                                    <textarea class="form-control" name="note" rows="3" 
                                                            placeholder="Tambahkan catatan verifikasi jika diperlukan"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-info">
                                                    <i class="bi bi-shield-check"></i> Verifikasi
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($booking->payment && $booking->payment->status === 'dibayar' && $booking->adminApproval)
                            
                            <div class="modal fade" id="completeModal{{ $booking->id }}">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.approvals.complete', $booking) }}">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Selesaikan Peminjaman</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Apakah Anda yakin ingin menyelesaikan peminjaman ini?</p>
                                                <div class="alert alert-success">
                                                    <strong>Mahasiswa:</strong> {{ $booking->user->name }}<br>
                                                    <strong>Pembayaran:</strong> Rp {{ number_format($booking->payment->amount, 0, ',', '.') }} (Sudah dibayar)<br>
                                                    <strong>Status:</strong> Terverifikasi Admin
                                                </div>
                                                <div class="alert alert-warning">
                                                    <strong>Perhatian:</strong> Setelah diselesaikan, status peminjaman tidak dapat diubah lagi.
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="bi bi-check-circle"></i> Selesaikan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $bookings->withQueryString()->links() }}
    </div>
</div>

@else
<div class="text-center py-5">
    <i class="bi bi-shield-check text-muted" style="font-size: 4rem;"></i>
    <h4 class="text-muted mt-3">Tidak ada peminjaman untuk diverifikasi</h4>
    <p class="text-muted">Belum ada peminjaman yang memerlukan verifikasi admin.</p>
</div>
@endif

@endsection 