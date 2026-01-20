<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Billetera;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\MovimientosExport;
use Maatwebsite\Excel\Facades\Excel;

class MovimientoController extends Controller
{
    /**
     * Listado de movimientos (por fecha y categoría)
     */
    public function index(Request $request)
    {

        $fechaDesde = $request->fecha_desde;
        $fechaHasta = $request->fecha_hasta;
        $categoriaId = $request->categoria_id;
        $busqueda = $request->busqueda;

        $query = Movimiento::with('categoria')
                ->where('user_id', auth()->id());


        // Filtro por rango de fechas
        if ($fechaDesde && $fechaHasta) {
            $query->whereBetween('fecha', [$fechaDesde, $fechaHasta]);
        } elseif ($fechaDesde) {
            $query->whereDate('fecha', '>=', $fechaDesde);
        } elseif ($fechaHasta) {
            $query->whereDate('fecha', '<=', $fechaHasta);
        } else {
            // Por defecto: mes actual
            $query->whereMonth('fecha', now()->month)
                ->whereYear('fecha', now()->year);
        }

        // Filtro por categoría
        if ($categoriaId) {
            $query->where('categoria_id', $categoriaId);
        }

        // FILTRO POR BÚSQUEDA (descripción o monto)
        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('descripcion', 'like', "%{$busqueda}%")
                  ->orWhere('monto', 'like', "%{$busqueda}%");
            });
        }

        $movimientos = $query
            ->orderBy('fecha', 'desc')
            ->paginate(4);

        $categorias = Categoria::where('user_id', auth()->id())
            ->orderBy('nombre')
            ->get();

        return view('movimientos.index', compact(
            'movimientos',
            'categorias',
            'fechaDesde',
            'fechaHasta',
            'categoriaId',
            'busqueda'
        ));
    }

    /**
     * Formulario de creación
     */
    public function create()
    {
        $billeteras = Billetera::all();
        $categoriasIngreso = Categoria::where('tipo', 'ingreso')->get();
        $categoriasGasto   = Categoria::where('tipo', 'gasto')->get();

        return view('movimientos.create', compact(
            'billeteras',
            'categoriasIngreso',
            'categoriasGasto'
        ));
    }

    /**
     * Guardar movimiento
     */
    public function store(Request $request)
    {
        $request->validate([
            'billetera_id' => 'required|exists:billeteras,id',
            'categoria_id' => 'required|exists:categorias,id',
            'tipo' => 'required|in:ingreso,gasto',
            'monto' => 'required|numeric|min:0.01',
            'horas_trabajadas' => 'nullable|required_if:tipo,ingreso|numeric|min:0',
            'kilometros_recorridos' => 'nullable|required_if:tipo,ingreso|numeric|min:0',
            'fecha' => 'required|date',
            'descripcion' => 'nullable|string'
        ]);


        DB::transaction(function () use ($request) {

            $billetera = Billetera::lockForUpdate()
                ->findOrFail($request->billetera_id);

            Movimiento::create(
                array_merge(
                    $request->all(),
                    ['user_id' => auth()->id()]
                )
            );


            if ($request->tipo === 'ingreso') {
                $billetera->saldo += $request->monto;
                $this->aplicarDistribucionesIngreso($billetera, (float) $request->monto);
            } else {
                $billetera->saldo -= $request->monto;
            }

            $billetera->save();
        });

        // Advertencia de presupuesto (NO bloquea)
        $categoria = Categoria::find($request->categoria_id);

        if ($request->tipo === 'gasto' && $categoria?->presupuesto_mensual) {

            $totalGasto = Movimiento::where('categoria_id', $categoria->id)
                ->where('tipo', 'gasto')
                ->whereMonth('fecha', date('m', strtotime($request->fecha)))
                ->whereYear('fecha', date('Y', strtotime($request->fecha)))
                ->sum('monto');

            if ($totalGasto > $categoria->presupuesto_mensual) {
                session()->flash(
                    'warning',
                    'Advertencia: superaste el presupuesto de la categoría "' . $categoria->nombre . '".'
                );
            }
        }

        return redirect()
            ->route('movimientos.index')
            ->with('success', 'Movimiento registrado correctamente');
    }

    /**
     * Formulario de edición
     */
    public function edit(Movimiento $movimiento)
    {

        abort_if($movimiento->user_id !== auth()->id(), 403);

        $billeteras = Billetera::all();
        $categoriasIngreso = Categoria::where('tipo', 'ingreso')->get();
        $categoriasGasto   = Categoria::where('tipo', 'gasto')->get();

        return view('movimientos.edit', compact(
            'movimiento',
            'billeteras',
            'categoriasIngreso',
            'categoriasGasto'
        ));
    }

    /**
     * Actualizar movimiento (revierte saldo anterior)
     */
    public function update(Request $request, Movimiento $movimiento)
    {

        abort_if($movimiento->user_id !== auth()->id(), 403);

        $request->validate([
            'billetera_id' => 'required|exists:billeteras,id',
            'categoria_id' => 'required|exists:categorias,id',
            'tipo' => 'required|in:ingreso,gasto',
            'monto' => 'required|numeric|min:0.01',
            'horas_trabajadas' => 'nullable|required_if:tipo,ingreso|numeric|min:0',
            'kilometros_recorridos' => 'nullable|required_if:tipo,ingreso|numeric|min:0',
            'fecha' => 'required|date',
            'descripcion' => 'nullable|string'
        ]);


        DB::transaction(function () use ($request, $movimiento) {

            $billetera = Billetera::lockForUpdate()
                ->findOrFail($movimiento->billetera_id);

            // Revertir efecto anterior
            if ($movimiento->tipo === 'ingreso') {
                $billetera->saldo -= $movimiento->monto;
            } else {
                $billetera->saldo += $movimiento->monto;
            }

            // Aplicar nuevo efecto
            if ($request->tipo === 'ingreso') {
                $billetera->saldo += $request->monto;
            } else {
                $billetera->saldo -= $request->monto;
            }

            $billetera->save();
            $movimiento->update(
                array_merge(
                    $request->all(),
                    ['user_id' => auth()->id()]
                )
            );
        });

        return redirect()
            ->route('movimientos.index')
            ->with('success', 'Movimiento actualizado correctamente');
    }

    /**
     * Eliminar movimiento (revierte saldo)
     */
    public function destroy(Movimiento $movimiento)
    {

        abort_if($movimiento->user_id !== auth()->id(), 403);

        DB::transaction(function () use ($movimiento) {

            $billetera = Billetera::lockForUpdate()
                ->findOrFail($movimiento->billetera_id);

            if ($movimiento->tipo === 'ingreso') {
                $billetera->saldo -= $movimiento->monto;
            } else {
                $billetera->saldo += $movimiento->monto;
            }

            $billetera->save();
            $movimiento->delete();
        });

        return redirect()
            ->route('movimientos.index')
            ->with('success', 'Movimiento eliminado correctamente');
    }

    private function aplicarDistribucionesIngreso(Billetera $billetera, float $montoBase): void
    {
        $distribuciones = $billetera->distribuciones()->get();

        foreach ($distribuciones as $dist) {
            $montoDistribuido = round($montoBase * ((float) $dist->porcentaje / 100), 2);
            if ($montoDistribuido <= 0) {
                continue;
            }

            $destino = Billetera::lockForUpdate()->find($dist->billetera_destino_id);
            if (! $destino) {
                continue;
            }

            $billetera->saldo -= $montoDistribuido;
            $destino->saldo += $montoDistribuido;

            $destino->save();
        }
    }

    /**
     * Exportar movimientos a Excel
     */
    public function export(Request $request)
    {
        $fechaDesde = $request->fecha_desde;
        $fechaHasta = $request->fecha_hasta;
        $categoriaId = $request->categoria_id;

        $query = Movimiento::with('categoria')
                ->where('user_id', auth()->id());

        // Filtro por rango de fechas
        if ($fechaDesde && $fechaHasta) {
            $query->whereBetween('fecha', [$fechaDesde, $fechaHasta]);
        } elseif ($fechaDesde) {
            $query->whereDate('fecha', '>=', $fechaDesde);
        } elseif ($fechaHasta) {
            $query->whereDate('fecha', '<=', $fechaHasta);
        } else {
            // Por defecto: mes actual
            $query->whereMonth('fecha', now()->month)
                ->whereYear('fecha', now()->year);
        }

        // Filtro por categoría
        if ($categoriaId) {
            $query->where('categoria_id', $categoriaId);
        }

        $movimientos = $query->orderBy('fecha', 'desc')->get();

        $filename = 'movimientos_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new MovimientosExport($movimientos), $filename);
    }
}
