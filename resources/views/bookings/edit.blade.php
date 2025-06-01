@extends('layouts.app')

@section('title', 'Edit Peminjaman')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-pencil-square"></i> Edit Peminjaman #{{ $booking->id }}
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('bookings.update', $booking) }}" enctype="multipart/form-data" id="editBookingForm">
                    @csrf
                    @method('PUT')
                    
                    
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle"></i> Informasi Peminjaman Saat Ini</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Fasilitas:</strong>
                                @foreach($booking->bookingDetails as $detail)
                                    <span class="badge bg-light text-dark me-1">{{ $detail->facility->name }}</span>
                                @endforeach
                            </div>
                            <div class="col-md-6">
                                <strong>Waktu:</strong> {{ $booking->start_time->format('d/m/Y H:i') }} - {{ $booking->end_time->format('H:i') }}
                            </div>
                        </div>
                    </div>
                    
                    
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
                                                   {{ in_array($facility->id, $booking->bookingDetails->pluck('facility_id')->toArray()) ? 'checked' : '' }}>
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
                                               id="perseorangan" value="perseorangan" 
                                               {{ $booking->booking_type === 'perseorangan' ? 'checked' : '' }}>
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
                                               id="organisasi" value="organisasi"
                                               {{ $booking->booking_type === 'organisasi' ? 'checked' : '' }}>
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

                    
                    <div class="mb-4" id="suratIzinSection" style="{{ $booking->booking_type === 'organisasi' ? 'display: block;' : 'display: none;' }}">
                        <label class="form-label">Surat Izin Organisasi</label>
                        @if($booking->surat_izin)
                        <div class="alert alert-success">
                            <i class="bi bi-paperclip"></i> 
                            Surat izin sudah ada: 
                            <a href="{{ Storage::url($booking->surat_izin) }}" target="_blank">Lihat surat saat ini</a>
                        </div>
                        @endif
                        <input type="file" class="form-control @error('surat_izin') is-invalid @enderror" 
                               name="surat_izin" accept=".pdf,.jpg,.jpeg,.png" id="suratIzinFile">
                        <div class="form-text">Format: PDF, JPG, PNG (Maks. 2MB). Kosongkan jika tidak ingin mengubah.</div>
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
                                   value="{{ old('booking_date', $booking->start_time->format('Y-m-d')) }}" required>
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                                   name="start_time_input" id="startTimeInput" 
                                   value="{{ old('start_time_input', $booking->start_time->format('H:i')) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                   name="end_time_input" id="endTimeInput" 
                                   value="{{ old('end_time_input', $booking->end_time->format('H:i')) }}" required>
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    
                    <input type="hidden" name="start_time" id="startTimeHidden">
                    <input type="hidden" name="end_time" id="endTimeHidden">

                    
                    <div class="alert alert-warning">
                        <h6><i class="bi bi-exclamation-triangle"></i> Perhatian</h6>
                        <ul class="mb-0">
                            <li>Perubahan hanya dapat dilakukan jika peminjaman masih berstatus <strong>menunggu</strong></li>
                            <li>Durasi peminjaman maksimal tetap <strong>2 jam</strong></li>
                            <li>Peminjaman yang sudah diproses tidak dapat diubah</li>
                        </ul>
                    </div>

                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('bookings.show', $booking) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-check-circle"></i> Update Peminjaman
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
    const form = document.getElementById('editBookingForm');
    const submitBtn = document.getElementById('submitBtn');
    const facilityCheckboxes = document.querySelectorAll('.facility-checkbox');
    const facilityError = document.getElementById('facilityError');
    

    function toggleSuratIzin() {
        if (organisasiRadio.checked) {
            suratIzinSection.style.display = 'block';
        } else {
            suratIzinSection.style.display = 'none';
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
        

        document.getElementById('startTimeHidden').value = `${bookingDate} ${startTime}:00`;
        document.getElementById('endTimeHidden').value = `${bookingDate} ${endTime}:00`;
        

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...';
        

        this.submit();
    });
});
</script>
@endsection 