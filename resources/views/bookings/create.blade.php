@extends('layouts.app')

@section('title', 'Buat Peminjaman Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-plus"></i> Formulir Peminjaman Fasilitas
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('bookings.store') }}" enctype="multipart/form-data" id="bookingForm">
                    @csrf
                    
                    
                    <div class="mb-4">
                        <label class="form-label">Pilih Fasilitas <span class="text-danger">*</span></label>
                        <div class="row">
                            @foreach($facilities as $facility)
                            <div class="col-md-6 mb-3">
                                <div class="card {{ $facility->status === 'available' ? 'border-success' : 'border-danger' }}">
                                    <div class="card-body p-3">
                                        <div class="form-check">
                                            <input class="form-check-input facility-checkbox" 
                                                   type="checkbox" 
                                                   name="facility_ids[]" 
                                                   value="{{ $facility->id }}" 
                                                   id="facility{{ $facility->id }}"
                                                   {{ $facility->status !== 'available' ? 'disabled' : '' }}
                                                   {{ request('facility') == $facility->id ? 'checked' : '' }}>
                                            <label class="form-check-label w-100" for="facility{{ $facility->id }}">
                                                <strong>{{ $facility->name }}</strong>
                                                <br><small class="text-muted">
                                                    <i class="bi bi-geo-alt"></i> {{ $facility->area->name }}
                                                    | <i class="bi bi-tag"></i> {{ $facility->facilityType->name }}
                                                </small>
                                                @if($facility->status === 'available')
                                                    <br><span class="badge bg-success">Tersedia</span>
                                                @else
                                                    <br><span class="badge bg-danger">Tidak Tersedia</span>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @error('facility_ids')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                        <div id="facilityError" class="text-danger small" style="display: none;">
                            Silakan pilih minimal satu fasilitas.
                        </div>
                    </div>

                    
                    <div class="mb-4">
                        <label class="form-label">Jenis Peminjaman <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check card">
                                    <div class="card-body">
                                        <input class="form-check-input" type="radio" name="booking_type" 
                                               id="perseorangan" value="perseorangan" checked>
                                        <label class="form-check-label w-100" for="perseorangan">
                                            <strong>Perseorangan</strong>
                                            <br><small class="text-muted">Peminjaman untuk keperluan pribadi</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check card">
                                    <div class="card-body">
                                        <input class="form-check-input" type="radio" name="booking_type" 
                                               id="organisasi" value="organisasi">
                                        <label class="form-check-label w-100" for="organisasi">
                                            <strong>Organisasi</strong>
                                            <br><small class="text-muted">Peminjaman untuk kegiatan organisasi</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @error('booking_type')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    
                    <div class="mb-4" id="suratIzinSection" style="display: none;">
                        <label class="form-label">Surat Izin Organisasi <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('surat_izin') is-invalid @enderror" 
                               name="surat_izin" accept=".pdf,.jpg,.jpeg,.png" id="suratIzinFile">
                        <div class="form-text">Format: PDF, JPG, PNG (Maks. 2MB)</div>
                        @error('surat_izin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('start_time') is-invalid @enderror" 
                                   name="booking_date" id="bookingDate" 
                                   min="{{ now()->addDay()->format('Y-m-d') }}" 
                                   value="{{ old('booking_date') }}" required>
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                                   name="start_time_input" id="startTimeInput" 
                                   value="{{ old('start_time_input', '08:00') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                   name="end_time_input" id="endTimeInput" 
                                   value="{{ old('end_time_input', '10:00') }}" required>
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    
                    <input type="hidden" name="start_time" id="startTimeHidden">
                    <input type="hidden" name="end_time" id="endTimeHidden">

                    
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle"></i> Informasi Pembayaran</h6>
                        <ul class="mb-0">
                            <li>Biaya peminjaman fasilitas: <strong>Rp 20.000</strong></li>
                            <li>Pembayaran dilakukan setelah peminjaman disetujui</li>
                            <li>Upload bukti pembayaran untuk verifikasi admin</li>
                        </ul>
                    </div>

                    
                    <div class="alert alert-warning">
                        <h6><i class="bi bi-exclamation-triangle"></i> Ketentuan Peminjaman</h6>
                        <ul class="mb-0">
                            <li>Maksimal durasi peminjaman: <strong>2 jam</strong></li>
                            <li>Peminjaman harus diajukan minimal <strong>1 hari sebelumnya</strong></li>
                            <li>Surat izin wajib untuk peminjaman organisasi</li>
                            <li>Fasilitas yang sudah dipinjam tidak dapat digunakan bersamaan</li>
                        </ul>
                    </div>

                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-check-circle"></i> Ajukan Peminjaman
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const organisasiRadio = document.getElementById('organisasi');
    const perseorangangRadio = document.getElementById('perseorangan');
    const suratIzinSection = document.getElementById('suratIzinSection');
    const suratIzinFile = document.getElementById('suratIzinFile');
    const startTimeInput = document.getElementById('startTimeInput');
    const endTimeInput = document.getElementById('endTimeInput');
    const bookingDateInput = document.getElementById('bookingDate');
    const form = document.getElementById('bookingForm');
    const submitBtn = document.getElementById('submitBtn');
    const facilityCheckboxes = document.querySelectorAll('.facility-checkbox');
    const facilityError = document.getElementById('facilityError');
    
    
    function toggleSuratIzin() {
        if (organisasiRadio.checked) {
            suratIzinSection.style.display = 'block';
            suratIzinFile.required = true;
        } else {
            suratIzinSection.style.display = 'none';
            suratIzinFile.required = false;
            suratIzinFile.value = '';
        }
    }
    
    organisasiRadio.addEventListener('change', toggleSuratIzin);
    perseorangangRadio.addEventListener('change', toggleSuratIzin);
    
    
    startTimeInput.addEventListener('change', function() {
        if (this.value) {
            const startTime = new Date(`2000-01-01T${this.value}`);
            const endTime = new Date(startTime.getTime() + (2 * 60 * 60 * 1000)); // Add 2 hours
            endTimeInput.value = endTime.toTimeString().slice(0, 5);
        }
    });
    
    
    endTimeInput.addEventListener('change', function() {
        if (startTimeInput.value && this.value) {
            const startTime = new Date(`2000-01-01T${startTimeInput.value}`);
            const endTime = new Date(`2000-01-01T${this.value}`);
            const diffHours = (endTime - startTime) / (1000 * 60 * 60);
            
            if (diffHours > 2) {
                alert('Durasi peminjaman maksimal 2 jam!');
                const maxEndTime = new Date(startTime.getTime() + (2 * 60 * 60 * 1000));
                this.value = maxEndTime.toTimeString().slice(0, 5);
            } else if (diffHours <= 0) {
                alert('Waktu selesai harus setelah waktu mulai!');
                this.value = '';
            }
        }
    });
    
    
    function validateFacilities() {
        const selectedFacilities = document.querySelectorAll('.facility-checkbox:checked');
        if (selectedFacilities.length === 0) {
            facilityError.style.display = 'block';
            return false;
        } else {
            facilityError.style.display = 'none';
            return true;
        }
    }
    
    
    facilityCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', validateFacilities);
    });
    
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        
        if (!validateFacilities()) {
            alert('Silakan pilih minimal satu fasilitas.');
            return false;
        }
        
        const bookingDate = bookingDateInput.value;
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;
        
        if (!bookingDate || !startTime || !endTime) {
            alert('Mohon lengkapi tanggal dan waktu peminjaman.');
            return false;
        }
        
        
        const selectedDate = new Date(bookingDate);
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        tomorrow.setHours(0, 0, 0, 0);
        
        if (selectedDate < tomorrow) {
            alert('Peminjaman harus diajukan minimal 1 hari sebelumnya.');
            return false;
        }
        
        
        const startTimeObj = new Date(`2000-01-01T${startTime}`);
        const endTimeObj = new Date(`2000-01-01T${endTime}`);
        const diffHours = (endTimeObj - startTimeObj) / (1000 * 60 * 60);
        
        if (diffHours <= 0) {
            alert('Waktu selesai harus setelah waktu mulai.');
            return false;
        }
        
        if (diffHours > 2) {
            alert('Durasi peminjaman maksimal 2 jam.');
            return false;
        }
        
        
        if (organisasiRadio.checked && !suratIzinFile.files.length) {
            alert('Surat izin organisasi wajib diupload.');
            suratIzinFile.focus();
            return false;
        }
        
        
        document.getElementById('startTimeHidden').value = `${bookingDate} ${startTime}:00`;
        document.getElementById('endTimeHidden').value = `${bookingDate} ${endTime}:00`;
        
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...';
        
        
        this.submit();
    });
    
    
    if (startTimeInput.value) {
        const startTime = new Date(`2000-01-01T${startTimeInput.value}`);
        const endTime = new Date(startTime.getTime() + (2 * 60 * 60 * 1000));
        endTimeInput.value = endTime.toTimeString().slice(0, 5);
    }
});
</script>
@endsection 