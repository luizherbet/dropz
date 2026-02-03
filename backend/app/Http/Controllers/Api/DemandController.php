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
    public function updateStatus(Request $request, Demand $demand)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(DemandStatus::values())],
        ]);

        $newStatus = $validated['status'];
        $currentStatus = $demand->status;

        // AUTORIZAÇÃO >> FILA
        if ($newStatus === DemandStatus::Fila->value) {
            if ($currentStatus !== DemandStatus::Autorizacao->value) {
                return response()->json([
                    'message' => 'Só é possível ir para Fila a partir de Autorização.',
                    'errors' => ['status' => ['Transição de status inválida.']],
                ], 422);
            }

            $errors = [];

            $valorTotal = (float) $demand->valor_total;
            $valorPago = (float) $demand->valor_pago;
            if ($valorTotal <= 0 || $valorPago < 0.5 * $valorTotal) {
                $errors['valor_pago'] = ['Pagamento mínimo de 50% do valor total é necessário para entrar na Fila.'];
            }

            $descricao = $demand->descricao_detalhada ?? '';
            if (strlen($descricao) <= 50) {
                $errors['descricao_detalhada'] = ['A descrição detalhada deve ter mais de 50 caracteres para entrar na Fila.'];
            }

            if (empty(trim((string) $demand->responsavel))) {
                $errors['responsavel'] = ['O responsável deve estar definido para que a demanda seja autorizada.'];
            }
            if (empty(trim((string) $demand->setor))) {
                $errors['setor'] = ['O setor deve estar definido para que a demanda seja autorizada.'];
            }
            if (empty(trim((string) $demand->quem_deve_testar))) {
                $errors['quem_deve_testar'] = ['Quem deve testar deve estar definido para que a demanda seja autorizada.'];
            }

            if (! empty($errors)) {
                return response()->json([
                    'message' => 'Critérios para entrar na Fila não atendidos.',
                    'errors' => $errors,
                ], 422);
            }
        }

        // TESTE >> DEPLOY
        if ($newStatus === DemandStatus::Deploy->value) {
            if ($currentStatus !== DemandStatus::Teste->value) {
                return response()->json([
                    'message' => 'Só é possível ir para Deploy a partir de Teste.',
                    'errors' => ['status' => ['Transição de status inválida.']],
                ], 422);
            }

            $feedback = $demand->feedback;
            if (empty(trim((string) $feedback))) {
                return response()->json([
                    'message' => 'O feedback é obrigatório para avançar de Teste para Deploy.',
                    'errors' => ['feedback' => ['Preencha o feedback antes de avançar.']],
                ], 422);
            }
        }

        // FLUXO << CONCLUÍDO
        $updates = ['status' => $newStatus];
        if ($currentStatus === DemandStatus::Concluido->value && $newStatus !== DemandStatus::Concluido->value) {
            $updates['flag_retornou'] = true;
        }

        $demand->update($updates);
        $demand->load('client');

        return response()->json([
            'data' => $demand,
            'message' => 'Status atualizado com sucesso',
        ]);
    }
}
