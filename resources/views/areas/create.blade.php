@extends('layouts.app')

@section('title', 'Tambah Area Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-geo-alt"></i> Tambah Area Baru
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('areas.store') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Area <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}" 
                               required 
                               autofocus
                               placeholder="Contoh: Gedung A, Gedung B, Area Olahraga">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Masukkan nama area yang jelas dan mudah dipahami
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle"></i> Contoh Area Kampus</h6>
                        <ul class="mb-0 small">
                            <li><strong>Gedung A</strong> - Untuk ruang kuliah dan laboratorium</li>
                            <li><strong>Gedung B</strong> - Untuk ruang seminar dan pertemuan</li>
                            <li><strong>Area Olahraga</strong> - Untuk lapangan dan fasilitas olahraga</li>
                            <li><strong>Area Parkir</strong> - Untuk tempat parkir dan area terbuka</li>
                        </ul>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('areas.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Simpan Area
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 