@extends('layouts.app')

@section('title', 'Kelola Jenis Fasilitas')

@section('actions')
<a href="{{ route('facility-types.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-circle"></i> Tambah Jenis Fasilitas
</a>
@endsection

@section('content')
<!-- Statistics Cards -->
@if(isset($stats))
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-primary">
            <div class="card-body text-center">
                <h4>{{ $stats['total_types'] ?? 0 }}</h4>
                <small>Total Jenis Fasilitas</small>
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
                <h4>{{ $stats['avg_facilities_per_type'] ?? 0 }}</h4>
                <small>Rata-rata Fasilitas/Jenis</small>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Facility Types List -->
@if(isset($facilityTypes) && $facilityTypes->count() > 0)
<div class="row">
    @foreach($facilityTypes as $type)
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">{{ $type->name }}</h6>
                <span class="badge bg-primary">{{ $type->facilities_count }} Fasilitas</span>
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    Dibuat: {{ $type->created_at->format('d F Y') }}
                </p>
                
                @if($type->facilities_count > 0)
                <h6 class="text-muted mb-2">Fasilitas:</h6>
                <div class="mb-3">
                    @foreach($type->facilities->take(5) as $facility)
                        <span class="badge bg-light text-dark me-1 mb-1">
                            {{ $facility->name }}
                        </span>
                    @endforeach
                    @if($type->facilities_count > 5)
                        <span class="badge bg-secondary">+{{ $type->facilities_count - 5 }} lainnya</span>
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
                    <a href="{{ route('facilities.index', ['facility_type_id' => $type->id]) }}" 
                       class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i> Lihat Fasilitas
                    </a>
                    
                    <div class="btn-group" role="group">
                        <a href="{{ route('facility-types.edit', $type) }}" 
                           class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @if($type->facilities_count == 0)
                        <form method="POST" action="{{ route('facility-types.destroy', $type) }}" 
                              style="display: inline;" 
                              onsubmit="return confirm('Yakin ingin menghapus jenis fasilitas ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @else
                        <button type="button" class="btn btn-sm btn-outline-danger" disabled 
                                title="Tidak dapat menghapus jenis fasilitas yang memiliki fasilitas">
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
    {{ $facilityTypes->links() }}
</div>

@else
<div class="text-center py-5">
    <i class="bi bi-tags text-muted" style="font-size: 4rem;"></i>
    <h4 class="text-muted mt-3">Belum ada jenis fasilitas</h4>
    <p class="text-muted">Mulai dengan menambahkan jenis fasilitas pertama untuk mengkategorikan fasilitas kampus.</p>
    
    <a href="{{ route('facility-types.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Jenis Fasilitas Pertama
    </a>
</div>
@endif

@endsection 