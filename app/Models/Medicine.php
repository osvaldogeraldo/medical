<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'generic_name',
        'brand',
        'description',
        'sku',
        'price',
        'cost_price',
        'stock_quantity',
        'min_stock',
        'max_stock',
        'expiry_date',
        'batch_number',
        'dosage_form',
        'strength',
        'requires_prescription',
        'is_active',
        'category_id',
        'supplier_id'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'requires_prescription' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function prescriptionItems(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(InventoryLog::class);
    }
}
