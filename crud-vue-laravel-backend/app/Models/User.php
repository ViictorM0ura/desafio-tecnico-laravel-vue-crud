<?php

namespace App\Models;

// Importar as classes de relacionamento
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf', // Adicionado o campo CPF aqui
        'profile_id', // Adicionado o campo profile_id aqui
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relação: Um usuário pertence a um perfil (um-para-um inverso)
    // Regra: Um usuário só pode ser vinculado a um perfil 
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    // Relação: Um usuário pode ter vários endereços (muitos-para-muitos)
    // Regra: Um usuário pode ter mais de um endereço, e um mesmo endereço pode pertencer a mais de um usuário. 
    public function addresses(): BelongsToMany
    {
        return $this->belongsToMany(Address::class);
    }
}