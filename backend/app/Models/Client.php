<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'email',
        'avisar_por_email',
        'whatsapp',
        'avisar_por_whatsapp',
        'observacoes',
    ];

    /**
     * Um cliente possui várias demandas (relacionamento 1:N)
     */
    public function demands()
    {
        return $this->hasMany(Demand::class, 'cliente_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
