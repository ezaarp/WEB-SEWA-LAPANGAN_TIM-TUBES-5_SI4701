<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'amount',
        'payment_proof',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function isPaid()
    {
        return $this->status === 'dibayar';
    }

    public function isUnpaid()
    {
        return $this->status === 'belum_dibayar';
    }

    public function isFailed()
    {
        return $this->status === 'gagal';
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'dibayar');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('status', 'belum_dibayar');
    }
}
