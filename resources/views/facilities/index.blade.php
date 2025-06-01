@extends('layouts.app')

@section('title', 'Daftar Fasilitas')

@section('actions')
@if(auth()->user()->isAdmin())
<a href="{{ route('facilities.create') }}" class="btn btn-primary">
    Tambah Fasilitas
</a>
@endif
@endsection

@section('content')

@if($facilities->count() > 0)
<div class="row">
    @foreach($facilities as $facility)
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100 {{ $facility->status === 'available' ? 'border-success' : 'border-danger' }}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">{{ $facility->name }}</h6>
                @if($facility->status === 'available')
                    <span class="badge bg-success">Tersedia</span>
                @else
                    <span class="badge bg-danger">Tidak Tersedia</span>
                @endif
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">
                        {{ $facility->area->name }}
                    </small>
                </div>
                <div class="mb-3">
                    <span class="badge bg-light text-dark">
                        {{ $facility->facilityType->name }}
                    </span>
                </div>
                
                @if(auth()->user()->isMahasiswa() && $facility->status === 'available')
                <div class="d-grid">
                    <a href="{{ route('bookings.create', ['facility' => $facility->id]) }}" 
                       class="btn btn-primary">
                        Ajukan Peminjaman
                    </a>
                </div>
                @endif
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('facilities.show', $facility) }}" class="btn btn-sm btn-outline-primary">
                        Detail
                    </a>
                    
                    @if(auth()->user()->isAdmin())
                    <div class="btn-group" role="group">
                        <a href="{{ route('facilities.edit', $facility) }}" 
                           class="btn btn-sm btn-outline-warning">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('facilities.destroy', $facility) }}" 
                              style="display: inline;" 
                              onsubmit="return confirm('Yakin ingin menghapus fasilitas ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                Hapus
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center">
    {{ $facilities->withQueryString()->links() }}
</div>

@else
<div class="text-center py-5">
    <h4 class="text-muted mt-3">Tidak ada fasilitas ditemukan</h4>
    <p class="text-muted">Belum ada fasilitas yang tersedia</p>
    
    @if(auth()->user()->isAdmin())
    <a href="{{ route('facilities.create') }}" class="btn btn-primary">
        Tambah Fasilitas Pertama
    </a>
    @endif
</div>
@endif

@endsection 