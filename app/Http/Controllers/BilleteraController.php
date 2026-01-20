<?php

namespace App\Http\Controllers;

use App\Models\Billetera;
use Illuminate\Http\Request;

class BilleteraController extends Controller
{
    public function create()
    {
        $billetera = new Billetera();

        return view('billeteras.create', compact('billetera'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'saldo' => 'required|numeric|min:0'
        ]);

        Billetera::create(
            array_merge(
                $data,
                ['user_id' => auth()->id()]
            )
        );


        return redirect()
            ->route('billeteras.index')
            ->with('success', 'Billetera creada correctamente');
    }

    public function index()
    {
        $billeteras = Billetera::withSum('movimientos as total_ingresos', 'monto')
            ->where('user_id', auth()->id())
            ->get();


        return view('billeteras.index', compact('billeteras'));
    }

    public function show(Billetera $billetera)
    {

        abort_if($billetera->user_id !== auth()->id(), 403);

        $movimientos = $billetera->movimientos()
            ->with('categoria')
            ->orderBy('fecha', 'desc')
            ->get();

        $distribuciones = $billetera->distribuciones()
            ->with('destino')
            ->orderBy('id')
            ->get();

        $otrasBilleteras = Billetera::where('id', '!=', $billetera->id)
            ->where('user_id', auth()->id())
            ->orderBy('nombre')
            ->get();

        return view('billeteras.show', compact(
            'billetera',
            'movimientos',
            'distribuciones',
            'otrasBilleteras'
        ));
    }

    public function edit(Billetera $billetera)
    {
        abort_if($billetera->user_id !== auth()->id(), 403);

        return view('billeteras.edit', compact('billetera'));
    }

    public function update(Request $request, Billetera $billetera)
    {
        abort_if($billetera->user_id !== auth()->id(), 403);

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'saldo' => 'required|numeric|min:0'
        ]);

        $billetera->update($data);

        return redirect()
            ->route('billeteras.index')
            ->with('success', 'Billetera actualizada correctamente');
    }

    public function destroy(Billetera $billetera)
    {
        abort_if($billetera->user_id !== auth()->id(), 403);

        $billetera->delete();

        return redirect()
            ->route('billeteras.index')
            ->with('success', 'Billetera eliminada');
    }
}

