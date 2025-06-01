@extends('layouts.app')

@section('title', 'Kelola Persetujuan')

@section('content')

@if(isset($stats))
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body text-center">
                <h4>{{ $stats['pending'] ?? 0 }}</h4>
                <small>Menunggu Persetujuan</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body text-center">
                <h4>{{ $stats['approved'] ?? 0 }}</h4>
                <small>Disetujui</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger">
            <div class="card-body text-center">
                <h4>{{ $stats['rejected'] ?? 0 }}</h4>
                <small>Ditolak</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
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
        <h5 class="mb-0">Daftar Peminjaman</h5>
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
                        <th>Diajukan</th>
                        <th>Status</th>
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
                            <small class="text-muted">
                                {{ $booking->created_at->diffForHumans() }}
                            </small>
                        </td>
                        <td>
                            @if($booking->status === 'menunggu')
                                <span class="badge bg-warning">Menunggu</span>
                            @elseif($booking->status === 'disetujui')
                                <span class="badge bg-success">Disetujui</span>
                            @else
                                <span class="badge bg-danger">Ditolak</span>
                                @if($booking->penanggungjawabApproval && $booking->penanggungjawabApproval->note)
                                    <br><small class="text-muted">{{ $booking->penanggungjawabApproval->note }}</small>
                                @endif
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('bookings.show', $booking) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                
                                @if($booking->status === 'menunggu')
                                <button type="button" 
                                        class="btn btn-sm btn-success" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#approveModal{{ $booking->id }}">
                                    <i class="bi bi-check"></i>
                                </button>
                                <button type="button" 
                                        class="btn btn-sm btn-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#rejectModal{{ $booking->id }}">
                                    <i class="bi bi-x"></i>
                                </button>
                                @endif
                            </div>

                            @if($booking->status === 'menunggu')
                            <div class="modal fade" id="approveModal{{ $booking->id }}">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('pj.approvals.approve', $booking) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="disetujui">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Setujui Peminjaman</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Apakah Anda yakin ingin menyetujui peminjaman ini?</p>
                                                <div class="alert alert-info">
                                                    <strong>Mahasiswa:</strong> {{ $booking->user->name }}<br>
                                                    <strong>Fasilitas:</strong> 
                                                    @foreach($booking->bookingDetails as $detail)
                                                        {{ $detail->facility->name }}{{ !$loop->last ? ', ' : '' }}
                                                    @endforeach<br>
                                                    <strong>Waktu:</strong> {{ $booking->start_time->format('d/m/Y H:i') }} - {{ $booking->end_time->format('H:i') }}
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Catatan (opsional)</label>
                                                    <textarea class="form-control" name="note" rows="3" 
                                                            placeholder="Tambahkan catatan jika diperlukan"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="bi bi-check-circle"></i> Setujui
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="modal fade" id="rejectModal{{ $booking->id }}">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('pj.approvals.approve', $booking) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="ditolak">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Tolak Peminjaman</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Apakah Anda yakin ingin menolak peminjaman ini?</p>
                                                <div class="alert alert-warning">
                                                    <strong>Mahasiswa:</strong> {{ $booking->user->name }}<br>
                                                    <strong>Fasilitas:</strong> 
                                                    @foreach($booking->bookingDetails as $detail)
                                                        {{ $detail->facility->name }}{{ !$loop->last ? ', ' : '' }}
                                                    @endforeach<br>
                                                    <strong>Waktu:</strong> {{ $booking->start_time->format('d/m/Y H:i') }} - {{ $booking->end_time->format('H:i') }}
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Alasan penolakan <span class="text-danger">*</span></label>
                                                    <textarea class="form-control" name="note" rows="3" 
                                                            placeholder="Jelaskan alasan penolakan" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="bi bi-x-circle"></i> Tolak
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
    <i class="bi bi-clipboard-check text-muted" style="font-size: 4rem;"></i>
    <h4 class="text-muted mt-3">Tidak ada peminjaman ditemukan</h4>
    <p class="text-muted">Belum ada peminjaman yang sesuai dengan kriteria pencarian.</p>
</div>
@endif

@endsection 