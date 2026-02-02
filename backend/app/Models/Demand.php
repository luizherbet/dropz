<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demand extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'titulo',
        'prioridade',
        'setor',
        'responsavel',
        'quem_deve_testar',
        'descricao_detalhada',
        'midia',
        'cobrada_do_cliente',
        'valor_total',
        'valor_pago',
        'tempo_estimado',
        'tempo_gasto',
        'status',
        'flag_retornou',
    ];

    /**
     * Uma demanda pertence a um cliente (relacionamento N:1)
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'cliente_id');
    }
}
