@extends('layouts.app')

@section('title', 'Edit Jenis Fasilitas')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit Jenis Fasilitas</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('facility-types.update', $facilityType) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Jenis Fasilitas <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $facilityType->name) }}" 
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Contoh: Ruang Kelas, Laboratorium, Aula, dll.</div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3">{{ old('description', $facilityType->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Deskripsi singkat tentang jenis fasilitas ini (opsional)</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('facility-types.index') }}" class="btn btn-secondary">
                            Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 