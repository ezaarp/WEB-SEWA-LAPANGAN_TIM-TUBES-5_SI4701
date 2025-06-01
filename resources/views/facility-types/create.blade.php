@extends('layouts.app')

@section('title', 'Tambah Jenis Fasilitas Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-tags"></i> Tambah Jenis Fasilitas Baru
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('facility-types.store') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Jenis Fasilitas <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}" 
                               required 
                               autofocus
                               placeholder="Contoh: Ruang Kuliah, Laboratorium, Aula">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Masukkan nama jenis fasilitas yang jelas dan spesifik
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle"></i> Contoh Jenis Fasilitas Kampus</h6>
                        <ul class="mb-0 small">
                            <li><strong>Ruang Kuliah</strong> - Untuk kegiatan perkuliahan umum</li>
                            <li><strong>Laboratorium</strong> - Untuk praktikum dan penelitian</li>
                            <li><strong>Aula</strong> - Untuk acara besar dan seminar</li>
                            <li><strong>Ruang Pertemuan</strong> - Untuk meeting dan diskusi</li>
                            <li><strong>Lapangan Olahraga</strong> - Untuk kegiatan olahraga</li>
                            <li><strong>Studio</strong> - Untuk kegiatan seni dan kreativitas</li>
                        </ul>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('facility-types.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Simpan Jenis Fasilitas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 