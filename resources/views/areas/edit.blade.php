@extends('layouts.app')

@section('title', 'Edit Area')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Edit Area</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('areas.update', $area) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Area</label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $area->name) }}" 
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

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('areas.index') }}" class="btn btn-secondary me-md-2">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Update Area
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 