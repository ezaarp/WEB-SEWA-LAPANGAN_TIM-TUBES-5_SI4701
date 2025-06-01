<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'area_id',
        'facility_type_id',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    
    public function facilityType()
    {
        return $this->belongsTo(FacilityType::class);
    }

    
    public function bookingDetails()
    {
        return $this->hasMany(BookingDetail::class);
    }

    
    public function bookings()
    {
        return $this->hasManyThrough(Booking::class, BookingDetail::class);
    }

    
    public function isAvailable()
    {
        return $this->status === 'available';
    }

    
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}
