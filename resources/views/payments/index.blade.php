@extends('layouts.app')

@section('title', 'Kelola Pembayaran')

@section('content')

@if(isset($stats))
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-danger">
            <div class="card-body text-center">
                <h4>{{ $stats['pending'] ?? 0 }}</h4>
                <small>Belum Dibayar</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body text-center">
                <h4>{{ $stats['uploaded'] ?? 0 }}</h4>
                <small>Menunggu Verifikasi</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body text-center">
                <h4>{{ $stats['verified'] ?? 0 }}</h4>
                <small>Terverifikasi</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body text-center">
                <h4>Rp {{ number_format($stats['total_revenue'] ?? 0, 0, ',', '.') }}</h4>
                <small>Total Pendapatan</small>
            </div>
        </div>
    </div>
</div>
@endif

@if(isset($payments) && $payments->count() > 0)
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Daftar Pembayaran</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mahasiswa</th>
                        <th>Peminjaman</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Bukti Bayar</th>
                        <th>Tanggal Upload</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr>
                        <td>
                            <strong>{{ $payment->booking->user->name }}</strong>
                            <br><small class="text-muted">{{ $payment->booking->user->nim }}</small>
                            <br><small class="text-muted">{{ $payment->booking->user->email }}</small>
                        </td>
                        <td>
                            <strong>#{{ $payment->booking->id }}</strong>
                            <br>
                            @foreach($payment->booking->bookingDetails as $detail)
                                <span class="badge bg-light text-dark me-1 mb-1">
                                    {{ $detail->facility->name }}
                                </span>
                            @endforeach
                            <br><small class="text-muted">
                                {{ $payment->booking->start_time->format('d/m/Y H:i') }}
                            </small>
                        </td>
                        <td>
                            <strong class="text-primary">
                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                            </strong>
                        </td>
                        <td>
                            @if($payment->status === 'belum_dibayar')
                                <span class="badge bg-danger">Belum Dibayar</span>
                            @elseif($payment->status === 'dibayar')
                                <span class="badge bg-success">Dibayar</span>
                                @if($payment->verified_at)
                                    <br><small class="text-muted">
                                        Diverifikasi {{ $payment->verified_at->diffForHumans() }}
                                    </small>
                                @endif
                            @else
                                <span class="badge bg-warning">Gagal</span>
                            @endif
                        </td>
                        <td>
                            @if($payment->payment_proof)
                                <a href="{{ route('admin.payments.proof', $payment) }}" 
                                   target="_blank" class="btn btn-sm btn-outline-primary">
                                    Lihat Bukti
                                </a>
                            @else
                                <span class="text-muted">Belum diupload</span>
                            @endif
                        </td>
                        <td>
                            @if($payment->payment_proof)
                                <small class="text-muted">
                                    {{ $payment->updated_at->diffForHumans() }}
                                </small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('bookings.show', $payment->booking) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                
                                @if($payment->payment_proof && $payment->status === 'belum_dibayar')
                                <button type="button" 
                                        class="btn btn-sm btn-success" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#verifyModal{{ $payment->id }}">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                                <button type="button" 
                                        class="btn btn-sm btn-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#rejectModal{{ $payment->id }}">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                                @endif
                            </div>

                            @if($payment->payment_proof && $payment->status === 'belum_dibayar')
                            <div class="modal fade" id="verifyModal{{ $payment->id }}">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.payments.verify', $payment) }}">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Verifikasi Pembayaran</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Apakah Anda yakin pembayaran ini valid?</p>
                                                <div class="alert alert-info">
                                                    <strong>Mahasiswa:</strong> {{ $payment->booking->user->name }}<br>
                                                    <strong>Jumlah:</strong> Rp {{ number_format($payment->amount, 0, ',', '.') }}<br>
                                                    <strong>Peminjaman:</strong> 
                                                    @foreach($payment->booking->bookingDetails as $detail)
                                                        {{ $detail->facility->name }}{{ !$loop->last ? ', ' : '' }}
                                                    @endforeach
                                                </div>
                                                <div class="text-center mb-3">
                                                    <img src="{{ route('admin.payments.proof', $payment) }}" 
                                                         class="img-fluid rounded" 
                                                         style="max-height: 300px;"
                                                         alt="Bukti Pembayaran">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="bi bi-check-circle"></i> Verifikasi
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="rejectModal{{ $payment->id }}">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.payments.reject', $payment) }}">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Tolak Pembayaran</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Apakah Anda yakin ingin menolak pembayaran ini?</p>
                                                <div class="alert alert-warning">
                                                    <strong>Perhatian:</strong> Setelah ditolak, mahasiswa perlu mengupload ulang bukti pembayaran yang valid.
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Alasan penolakan <span class="text-danger">*</span></label>
                                                    <textarea class="form-control" name="rejection_reason" rows="3" 
                                                            placeholder="Jelaskan alasan penolakan pembayaran" required></textarea>
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
        {{ $payments->withQueryString()->links() }}
    </div>
</div>

@else
<div class="text-center py-5">
    <i class="bi bi-credit-card text-muted" style="font-size: 4rem;"></i>
    <h4 class="text-muted mt-3">Tidak ada pembayaran ditemukan</h4>
    <p class="text-muted">Belum ada pembayaran yang sesuai dengan kriteria pencarian.</p>
</div>
@endif

@endsection 