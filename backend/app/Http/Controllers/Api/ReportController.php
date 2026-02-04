<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Demand;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function monthlyByClient(Request $request, Client $client)
    {
        $validated = $request->validate([
            'month' => ['required', 'date_format:Y-m'],
        ]);

        $date = Carbon::createFromFormat('Y-m', $validated['month']);
        $now = Carbon::now();
        if ($date->year > $now->year || ($date->year === $now->year && $date->month > $now->month)) {
            return response()->json([
                'message' => 'O período do relatório não pode ser futuro. Selecione o mês atual ou um mês anterior.',
                'errors' => ['month' => ['Mês/ano não pode ser superior ao mês/ano atual.']],
            ], 422);
        }

        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        $demands = Demand::where('cliente_id', $client->id)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->orderBy('created_at', 'asc')
            ->get();

        $valorTotal = $demands->sum('valor_total');
        $valorPago = $demands->sum('valor_pago');

        return response()->json([
            'data' => [
                'client' => $client,
                'period' => [
                    'year' => $date->year,
                    'month' => $date->month,
                    'label' => $date->format('Y-m'),
                ],
                'demands' => $demands,
                'summary' => [
                    'total_demands' => $demands->count(),
                    'valor_total' => (float)$valorTotal,
                    'valor_pago' => (float)$valorPago,
                ],
            ],
        ]);
    }
}
