<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function facilities()
    {
        return $this->hasMany(Facility::class);
    }
}
