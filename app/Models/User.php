<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * Atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
    ];

    /**
     * Atributos ocultos ao serializar.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Conversão automática de tipos de campos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relação polimórfica — opcional.
     */
    public function authenticatable()
    {
        return $this->morphTo();
    }

    /**
     * Helper para exibir o nome completo ou username.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name ?? $this->username ?? 'Utilizador';
    }
}
