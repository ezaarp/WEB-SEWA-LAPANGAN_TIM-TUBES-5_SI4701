<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalAdmin extends Model
{
    use HasFactory;

    protected $table = 'approvals_admin';

    protected $fillable = [
        'booking_id',
        'approved_by',
        'status',
        'note',
    ];

    /**
     * Get the booking this approval belongs to
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the user who made this approval
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if approval is approved
     */
    public function isApproved()
    {
        return $this->status === 'disetujui';
    }

    /**
     * Check if approval is rejected
     */
    public function isRejected()
    {
        return $this->status === 'ditolak';
    }

    /**
     * Scope for approved records
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'disetujui');
    }

    /**
     * Scope for rejected records
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'ditolak');
    }
}
