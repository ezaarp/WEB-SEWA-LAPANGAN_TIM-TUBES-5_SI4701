@extends('layouts.app')

@section('title', 'Kelola Area')

@section('actions')
<a href="{{ route('areas.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-circle"></i> Tambah Area
</a>
@endsection

@section('content')
<!-- Statistics Cards -->
@if(isset($stats))
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-primary">
            <div class="card-body text-center">
                <h4>{{ $stats['total_areas'] ?? 0 }}</h4>
                <small>Total Area</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body text-center">
                <h4>{{ $stats['total_facilities'] ?? 0 }}</h4>
                <small>Total Fasilitas</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info">
            <div class="card-body text-center">
                <h4>{{ $stats['avg_facilities_per_area'] ?? 0 }}</h4>
                <small>Rata-rata Fasilitas/Area</small>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Areas List -->
@if(isset($areas) && $areas->count() > 0)
<div class="row">
    @foreach($areas as $area)
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">{{ $area->name }}</h6>
                <span class="badge bg-primary">{{ $area->facilities_count }} Fasilitas</span>
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    Dibuat: {{ $area->created_at->format('d F Y') }}
                </p>
                
                @if($area->facilities_count > 0)
                <h6 class="text-muted mb-2">Fasilitas:</h6>
                <div class="mb-3">
                    @foreach($area->facilities->take(5) as $facility)
                        <span class="badge bg-light text-dark me-1 mb-1">
                            {{ $facility->name }}
                        </span>
                    @endforeach
                    @if($area->facilities_count > 5)
                        <span class="badge bg-secondary">+{{ $area->facilities_count - 5 }} lainnya</span>
                    @endif
                </div>
                @else
                <div class="text-center py-3">
                    <i class="bi bi-building text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted mt-2 mb-0">Belum ada fasilitas</p>
                </div>
                @endif
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('facilities.index', ['area_id' => $area->id]) }}" 
                       class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i> Lihat Fasilitas
                    </a>
                    
                    <div class="btn-group" role="group">
                        <a href="{{ route('areas.edit', $area) }}" 
                           class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @if($area->facilities_count == 0)
                        <form method="POST" action="{{ route('areas.destroy', $area) }}" 
                              style="display: inline;" 
                              onsubmit="return confirm('Yakin ingin menghapus area ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @else
                        <button type="button" class="btn btn-sm btn-outline-danger" disabled 
                                title="Tidak dapat menghapus area yang memiliki fasilitas">
                            <i class="bi bi-trash"></i>
                        </button>
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
    {{ $areas->links() }}
</div>

@else
<div class="text-center py-5">
    <i class="bi bi-geo-alt text-muted" style="font-size: 4rem;"></i>
    <h4 class="text-muted mt-3">Belum ada area</h4>
    <p class="text-muted">Mulai dengan menambahkan area pertama untuk fasilitas kampus.</p>
    
    <a href="{{ route('areas.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Area Pertama
    </a>
</div>
@endif

@endsection 