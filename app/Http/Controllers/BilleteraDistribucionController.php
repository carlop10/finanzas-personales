<?php

namespace App\Http\Controllers;

use App\Models\Billetera;
use App\Models\BilleteraDistribucion;
use Illuminate\Http\Request;

class BilleteraDistribucionController extends Controller
{
    public function store(Request $request, Billetera $billetera)
    {
        $data = $this->validatedData($request, $billetera);

        $billetera->distribuciones()->create($data);

        return back()->with('success', 'Distribución creada');
    }

    public function update(Request $request, Billetera $billetera, BilleteraDistribucion $distribucion)
    {
        if ($distribucion->billetera_origen_id !== $billetera->id) {
            abort(404);
        }

        $data = $this->validatedData($request, $billetera, $distribucion->id);

        $distribucion->update($data);

        return back()->with('success', 'Distribución actualizada');
    }

    public function destroy(Billetera $billetera, BilleteraDistribucion $distribucion)
    {
        if ($distribucion->billetera_origen_id !== $billetera->id) {
            abort(404);
        }

        $distribucion->delete();

        return back()->with('success', 'Distribución eliminada');
    }

    private function validatedData(Request $request, Billetera $billetera, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'billetera_destino_id' => 'required|integer|exists:billeteras,id',
            'porcentaje' => 'required|numeric|min:0.01|max:100',
        ]);

        if ((int) $data['billetera_destino_id'] === $billetera->id) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'billetera_destino_id' => 'Debes elegir una billetera distinta a la de origen.'
            ]);
        }

        $existingTotal = $billetera->distribuciones()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->sum('porcentaje');

        if ($existingTotal + $data['porcentaje'] > 100) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'porcentaje' => 'La suma de porcentajes no puede superar 100%.'
            ]);
        }

        return $data;
    }
}
