<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', // Permitindo que o campo 'name' seja preenchido
    ];

    // Relação: Um perfil pode ter vários usuários
    public function users()
    {
        return $this->hasMany(User::class);
    }
}