<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::orderBy('nome')->get();

        if ($clients->isEmpty()) {
            abort(404, 'Nenhum cliente encontrado');
        }

        return response()->json(['data' => $clients]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email',
            'avisar_por_email' => 'boolean',
            'whatsapp' => 'nullable|string|max:50',
            'avisar_por_whatsapp' => 'boolean',
            'observacoes' => 'nullable|string',
        ]);

        $client = Client::create($validated);

        return response()->json([
            'data' => $client,
            'message' => 'Cliente criado com sucesso',
        ], 201);
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'nome' => 'string|max:255',
            'email' => 'email',
            'avisar_por_email' => 'boolean',
            'whatsapp' => 'nullable|string|max:50',
            'avisar_por_whatsapp' => 'boolean',
            'observacoes' => 'nullable|string',
        ]);

        $client->update($validated);

        return response()->json([
            'data' => $client,
            'message' => 'Cliente atualizado com sucesso',
        ]);
    }
}
