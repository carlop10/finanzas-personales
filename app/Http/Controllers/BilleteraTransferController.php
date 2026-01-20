<?php

namespace App\Http\Controllers;

use App\Models\Billetera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BilleteraTransferController extends Controller
{
    public function index()
    {
        $billeteras = Billetera::orderBy('nombre')
        ->where('user_id', auth()->id())
        ->get();

        return view('billeteras.transferencias', compact('billeteras'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'origen_id' => 'required|integer|exists:billeteras,id',
            'destino_id' => 'required|integer|exists:billeteras,id',
            'monto' => 'required|numeric|min:0.01',
        ]);

        if ($data['origen_id'] === $data['destino_id']) {
            return back()->withErrors(['destino_id' => 'Debes elegir billeteras distintas'])->withInput();
        }

        DB::transaction(function () use ($data) {
            $billeteras = Billetera::whereIn('id', [$data['origen_id'], $data['destino_id']])
                ->where('user_id', auth()->id())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $origen = $billeteras[$data['origen_id']] ?? null;
            $destino = $billeteras[$data['destino_id']] ?? null;

            if (! $origen || ! $destino) {
                throw ValidationException::withMessages(['origen_id' => 'Billeteras inválidas']);
            }

            if ($origen->saldo < $data['monto']) {
                throw ValidationException::withMessages(['monto' => 'Saldo insuficiente en la billetera de origen']);
            }

            $origen->saldo -= $data['monto'];
            $destino->saldo += $data['monto'];

            $origen->save();
            $destino->save();
        });

        return back()->with('success', 'Transferencia realizada');
    }
}
