<?php

namespace App\Http\Controllers\Api;

use App\Enums\DemandPriority;
use App\Enums\DemandStatus;
use App\Http\Controllers\Controller;
use App\Models\Demand;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DemandController extends Controller
{
    public function index(Request $request)
    {
        $demands = Demand::orderByRaw("CASE prioridade WHEN 'Alta' THEN 1 WHEN 'Normal' THEN 2 WHEN 'Baixa' THEN 3")
            ->orderBy('created_at', 'asc')->get();

        if ($demands->isEmpty()) {
            abort(404, 'Nenhum cliente encontrado');
        }
        return response()->json(['data' => $demands]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clients,id',
            'titulo' => 'required|string|max:255',
            'prioridade' => ['nullable', Rule::in(DemandPriority::values())],
            'setor' => 'nullable|string|max:255',
            'responsavel' => 'nullable|string|max:255',
            'quem_deve_testar' => 'nullable|string|max:255',
            'descricao_detalhada' => 'nullable|string',
            'midia' => 'nullable|string|max:255',
            'cobrada_do_cliente' => 'boolean',
            'valor_total' => 'nullable|numeric|min:0',
            'valor_pago' => 'nullable|numeric|min:0',
            'tempo_estimado' => 'nullable|integer|min:0',
            'tempo_gasto' => 'nullable|integer|min:0',
            'status' => ['nullable', Rule::in(DemandStatus::values())],
            'flag_retornou' => 'boolean',
            'feedback' => 'nullable|string',
        ]);

        $demand = Demand::create($validated);
        $demand->load('client');

        return response()->json([
            'data' => $demand,
            'message' => 'Demanda criada com sucesso',
        ], 201);
    }

    public function update(Request $request, Demand $demand)
    {
        $validated = $request->validate([
            'titulo' => 'string|max:255',
            'prioridade' => [Rule::in(DemandPriority::values())],
            'setor' => 'nullable|string|max:255',
            'responsavel' => 'nullable|string|max:255',
            'quem_deve_testar' => 'nullable|string|max:255',
            'descricao_detalhada' => 'nullable|string',
            'midia' => 'nullable|string|max:255',
            'cobrada_do_cliente' => 'boolean',
            'valor_total' => 'nullable|numeric|min:0',
            'valor_pago' => 'nullable|numeric|min:0',
            'tempo_estimado' => 'nullable|integer|min:0',
            'tempo_gasto' => 'nullable|integer|min:0',
            'status' => [Rule::in(DemandStatus::values())],
            'flag_retornou' => 'boolean',
            'feedback' => 'nullable|string',
        ]);

        $demand->update($validated);
        $demand->load('client');

        return response()->json([
            'data' => $demand,
            'message' => 'Demanda atualizada com sucesso',
        ]);
    }
}
