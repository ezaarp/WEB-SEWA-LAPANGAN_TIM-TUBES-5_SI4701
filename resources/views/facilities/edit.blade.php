@extends('layouts.app')

@section('title', 'Edit Fasilitas')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Edit Fasilitas</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('facilities.update', $facility) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Fasilitas</label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $facility->name) }}" 
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="area_id" class="form-label">Area</label>
                        <select class="form-select @error('area_id') is-invalid @enderror" 
                                id="area_id" 
                                name="area_id" 
                                required>
                            <option value="">Pilih Area</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" 
                                        {{ old('area_id', $facility->area_id) == $area->id ? 'selected' : '' }}>
                                    {{ $area->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('area_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="facility_type_id" class="form-label">Jenis Fasilitas</label>
                        <select class="form-select @error('facility_type_id') is-invalid @enderror" 
                                id="facility_type_id" 
                                name="facility_type_id" 
                                required>
                            <option value="">Pilih Jenis Fasilitas</option>
                            @foreach($facilityTypes as $type)
                                <option value="{{ $type->id }}" 
                                        {{ old('facility_type_id', $facility->facility_type_id) == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('facility_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                                id="status" 
                                name="status" 
                                required>
                            <option value="available" {{ old('status', $facility->status) == 'available' ? 'selected' : '' }}>
                                Tersedia
                            </option>
                            <option value="unavailable" {{ old('status', $facility->status) == 'unavailable' ? 'selected' : '' }}>
                                Tidak Tersedia
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('facilities.index') }}" class="btn btn-secondary me-md-2">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Update Fasilitas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 