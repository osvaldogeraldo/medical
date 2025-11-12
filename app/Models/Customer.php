<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'medical_history',
        'allergies'
    ];

    protected $casts = [
        'date_of_birth' => 'date'
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }


    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function doctorPrescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class, 'doctor_id');
    }
}
