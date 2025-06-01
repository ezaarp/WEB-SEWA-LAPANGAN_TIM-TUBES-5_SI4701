@extends('layouts.app')

@section('title', 'Tambah Fasilitas Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-building"></i> Tambah Fasilitas Baru
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('facilities.store') }}">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nama Fasilitas <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required 
                                   autofocus
                                   placeholder="Contoh: Ruang A101, Lab Komputer 1">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" 
                                    name="status" 
                                    required>
                                <option value="">Pilih Status</option>
                                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Tersedia</option>
                                <option value="unavailable" {{ old('status') == 'unavailable' ? 'selected' : '' }}>Tidak Tersedia</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="area_id" class="form-label">Area <span class="text-danger">*</span></label>
                            <select class="form-select @error('area_id') is-invalid @enderror" 
                                    id="area_id" 
                                    name="area_id" 
                                    required>
                                <option value="">Pilih Area</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>
                                        {{ $area->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('area_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($areas->count() == 0)
                                <div class="form-text text-warning">
                                    <i class="bi bi-exclamation-triangle"></i> 
                                    Belum ada area. <a href="{{ route('areas.create') }}">Tambah area terlebih dahulu</a>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label for="facility_type_id" class="form-label">Jenis Fasilitas <span class="text-danger">*</span></label>
                            <select class="form-select @error('facility_type_id') is-invalid @enderror" 
                                    id="facility_type_id" 
                                    name="facility_type_id" 
                                    required>
                                <option value="">Pilih Jenis Fasilitas</option>
                                @foreach($facilityTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('facility_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('facility_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($facilityTypes->count() == 0)
                                <div class="form-text text-warning">
                                    <i class="bi bi-exclamation-triangle"></i> 
                                    Belum ada jenis fasilitas. <a href="{{ route('facility-types.create') }}">Tambah jenis fasilitas terlebih dahulu</a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle"></i> Tips Penamaan Fasilitas</h6>
                        <ul class="mb-0 small">
                            <li><strong>Gunakan kode yang jelas:</strong> A101, B205, Lab-Kom-1</li>
                            <li><strong>Sertakan lantai/lokasi:</strong> Ruang A101 Lt.1, Lab Kimia Lt.2</li>
                            <li><strong>Hindari nama yang ambigu:</strong> Gunakan "Aula Utama" bukan "Aula"</li>
                            <li><strong>Konsisten dengan nama existing:</strong> Ikuti pola penamaan yang sudah ada</li>
                        </ul>
                    </div>

                    @if($areas->count() == 0 || $facilityTypes->count() == 0)
                    <div class="alert alert-warning">
                        <h6><i class="bi bi-exclamation-triangle"></i> Data Master Belum Lengkap</h6>
                        <p class="mb-2">Sebelum menambah fasilitas, pastikan sudah ada:</p>
                        <div class="d-flex gap-2">
                            @if($areas->count() == 0)
                                <a href="{{ route('areas.create') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-geo-alt"></i> Tambah Area
                                </a>
                            @endif
                            @if($facilityTypes->count() == 0)
                                <a href="{{ route('facility-types.create') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-tags"></i> Tambah Jenis Fasilitas
                                </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('facilities.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" 
                                class="btn btn-primary"
                                @if($areas->count() == 0 || $facilityTypes->count() == 0) disabled @endif>
                            <i class="bi bi-check-circle"></i> Simpan Fasilitas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 