<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Categoria;
use App\Models\Billetera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class DashboardController extends Controller
{

    public function index(Request $request)
    {
        // Rango de fechas
        $fechaDesde = $request->fecha_desde;
        $fechaHasta = $request->fecha_hasta;

        // Atajo mes/año (opcional)
        $mes = $request->mes ?? now()->month;
        $anio = $request->anio ?? now()->year;

        // Si no hay rango, usar mes/año
        if (!$fechaDesde && !$fechaHasta) {
            $fechaDesde = Carbon::create($anio, $mes, 1)->startOfDay();
            $fechaHasta = Carbon::create($anio, $mes, 1)->endOfMonth();
        }

        // ============================
        // INGRESOS / GASTOS / BALANCE
        // ============================
        $resumen = Movimiento::select(
            DB::raw("SUM(CASE WHEN tipo='ingreso' THEN monto ELSE 0 END) as ingresos"),
            DB::raw("SUM(CASE WHEN tipo='gasto' THEN monto ELSE 0 END) as gastos")
        )
            ->where('user_id', auth()->id())
            ->whereBetween('fecha', [$fechaDesde, $fechaHasta])
            ->first();

        $ingresos = $resumen->ingresos ?? 0;
        $gastos = $resumen->gastos ?? 0;
        $balance = $ingresos - $gastos;

        // ============================
        // HORAS Y KILÓMETROS
        // ============================
        $horas = Movimiento::where('user_id', auth()->id())
            ->where('tipo', 'ingreso')
            ->whereBetween('fecha', [$fechaDesde, $fechaHasta])
            ->sum('horas_trabajadas');

        $kilometros = Movimiento::where('user_id', auth()->id())
            ->where('tipo', 'ingreso')
            ->whereBetween('fecha', [$fechaDesde, $fechaHasta])
            ->sum('kilometros_recorridos');

        // ============================
        // CATEGORÍAS
        // ============================
        $categorias = Categoria::where('user_id', auth()->id())
            ->where('tipo', 'gasto')
            ->get();

        $gastosPorCategoria = Movimiento::select(
            'categoria_id',
            DB::raw('SUM(monto) as total_gastado')
        )
            ->where('user_id', auth()->id())
            ->where('tipo', 'gasto')
            ->whereBetween('fecha', [$fechaDesde, $fechaHasta])
            ->groupBy('categoria_id')
            ->get()
            ->keyBy('categoria_id');

        // ============================
        // BILLETERAS
        // ============================
        $billeteras = Billetera::where('user_id', auth()->id())->get();

        $billeterasData = $billeteras->map(function ($billetera) use ($billeteras) {
            $totalSaldos = $billeteras->sum('saldo');
            $porcentaje = $totalSaldos > 0 ? ($billetera->saldo / $totalSaldos) * 100 : 0;

            return (object)[
                'id' => $billetera->id,
                'nombre' => $billetera->nombre,
                'saldo' => $billetera->saldo,
                'porcentaje' => $porcentaje
            ];
        });

        // ============================
        // GASTOS POR CATEGORÍA (para gráfico pie)
        // ============================
        $gastosPorCategoriaPie = Movimiento::select(
            'categoria_id',
            DB::raw('SUM(monto) as total')
        )
            ->where('user_id', auth()->id())
            ->where('tipo', 'gasto')
            ->whereBetween('fecha', [$fechaDesde, $fechaHasta])
            ->groupBy('categoria_id')
            ->with('categoria')
            ->get();

        // Preparar datos para el gráfico pie
        $nombresCategorias = $gastosPorCategoriaPie->map(function ($item) {
            return $item->categoria->nombre ?? 'Sin categoría';
        })->toArray();

        $montosCategorias = $gastosPorCategoriaPie->pluck('total')->toArray();

        // ============================
        // GRÁFICA
        // ============================
        $datosGrafica = (object)[
            'ingresos' => $ingresos,
            'gastos' => $gastos,
            'balance' => $balance,
        ];

        // ============================
        // GANANCIA POR SEMANA
        // ============================
        $gananciasSemanales = Movimiento::select(
            DB::raw('YEAR(fecha) as anio'),
            DB::raw('WEEK(fecha, 1) as semana'),
            DB::raw("SUM(CASE WHEN tipo='ingreso' THEN monto ELSE -monto END) as ganancia")
        )
            ->where('user_id', auth()->id())
            ->whereBetween('fecha', [$fechaDesde, $fechaHasta])
            ->groupBy('anio', 'semana')
            ->orderBy('anio')
            ->orderBy('semana')
            ->get();

        // ============================
        // ÚLTIMOS MOVIMIENTOS
        // ============================
        $ultimosMovimientos = Movimiento::where('user_id', auth()->id())
            ->with('categoria')
            ->orderBy('fecha', 'desc')
            ->paginate(3, ['*'], 'page', $request->get('page', 1));

        return view('dashboard.index', compact(
            'ingresos',
            'gastos',
            'balance',
            'horas',
            'kilometros',
            'categorias',
            'gastosPorCategoria',
            'datosGrafica',
            'gananciasSemanales',
            'fechaDesde',
            'fechaHasta',
            'mes',
            'anio',
            'billeterasData',
            'nombresCategorias',
            'montosCategorias',
            'ultimosMovimientos'
        ));
    }
}
