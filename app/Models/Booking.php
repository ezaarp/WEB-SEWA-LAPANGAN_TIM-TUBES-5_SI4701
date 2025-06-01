<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'booking_type',
        'start_time',
        'end_time',
        'status',
        'surat_izin',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function bookingDetails()
    {
        return $this->hasMany(BookingDetail::class);
    }


    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'booking_details');
    }


    public function payment()
    {
        return $this->hasOne(Payment::class);
    }


    public function penanggungjawabApproval()
    {
        return $this->hasOne(ApprovalPenanggungjawab::class);
    }


    public function adminApproval()
    {
        return $this->hasOne(ApprovalAdmin::class);
    }

  
    public function isPending()
    {
        return $this->status === 'menunggu';
    }

    public function isApproved()
    {
        return $this->status === 'disetujui';
    }

    public function isRejected()
    {
        return $this->status === 'ditolak';
    }

    public function isCompleted()
    {
        return $this->status === 'selesai';
    }

    public function getDurationInHours()
    {
        return $this->start_time->diffInHours($this->end_time);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'menunggu');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'disetujui');
    }
}
