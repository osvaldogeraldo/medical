<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_number',
        'customer_id',
        'doctor_id',
        'doctor_name',
        'doctor_license',
        'diagnosis',
        'notes',
        'issue_date',
        'expiry_date',
        'status',
        'user_id'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'doctor_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function prescriptionItems(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class);
    }
}
