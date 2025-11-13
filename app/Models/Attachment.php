<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'attachments';

    protected $fillable = [
        'uuid',
        'name',
        'path',
        'attachable_id',
        'attachable_type',
    ];

    /**
     * Polymorphic relationship: qualquer modelo pode ter anexos
     */
    public function attachable()
    {
        return $this->morphTo();
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                do {
                    $uuid = Str::random(128);
                } while (self::where('uuid', $uuid)->exists());
                $model->uuid = $uuid;
            }
        });
    }

    /**
     * Retorna o URL completo do arquivo acessível via navegador
     */
    public function getUrlAttribute()
    {
        return url('storage/' . $this->path);
    }
}
