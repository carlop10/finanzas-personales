@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <form method="GET" class="row g-2 mb-4 align-items-end">

        <div class="col-md-3">
            <label class="form-label">Desde</label>
            <input type="date" name="fecha_desde" value="{{ $fechaDesde }}" class="form-control">
        </div>

        <div class="col-md-3">
            <label class="form-label">Hasta</label>
            <input type="date" name="fecha_hasta" value="{{ $fechaHasta }}" class="form-control">
        </div>

        <div class="col-md-2">
            <label class="form-label">Mes <small class="text-muted">(Opcional)</small></label>
            <select name="mes" class="form-select">
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" @selected($m == $mes)>
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Año <small class="text-muted">(Opcional)</small></label>
            <select name="anio" class="form-select">
                @for ($y = now()->year - 2; $y <= now()->year + 1; $y++)
                    <option value="{{ $y }}" @selected($y == $anio)>{{ $y }}</option>
                @endfor
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-dark w-100">Filtrar</button>
        </div>

    </form>

    <div class="card p-3 mb-4">

        <div class="row text-center">
            <div class="col-md-4">
                <div class="card border-success">
                    <div class="card-body p-1">
                        <h6>Ingresos</h6>
                        <h3 class="text-success">${{ number_format($ingresos, 2) }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-danger">
                    <div class="card-body p-1">
                        <h6>Gastos</h6>
                        <h3 class="text-danger">${{ number_format($gastos, 2) }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-primary">
                    <div class="card-body p-1">
                        <h6>Balance</h6>
                        <h3 class="text-primary">${{ number_format($balance, 2) }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mt-3">
                <div class="card border-Dark">
                    <div class="card-body p-1">
                        <h6>Horas trabajadas</h6>
                        <h3 class="text-Dark">{{ $horas ?? 0 }} h</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mt-3">
                <div class="card border-Dark">
                    <div class="card-body p-1">
                        <h6>Kilómetros recorridos</h6>
                        <h3 class="text-Dark">{{ $kilometros ?? 0 }} Km</h3>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ÚLTIMOS MOVIMIENTOS --}}
    <div class="card mb-4 p-3">

        <h5 class="mb-1">Últimos Movimientos</h5>

        <table class="table table mb-0">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Categoría</th>
                    <th>Tipo</th>
                    <th>Monto</th>
                    <th>Descripción</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ultimosMovimientos as $mov)
                    <tr>
                        <td>{{ $mov->fecha->format('d/m/Y') }}</td>
                        <td>{{ $mov->categoria->nombre }}</td>
                        <td>
                            <span class="badge bg-{{ $mov->tipo === 'ingreso' ? 'success' : 'danger' }}">
                                {{ ucfirst($mov->tipo) }}
                            </span>
                        </td>
                        <td>${{ number_format($mov->monto, 2) }}</td>
                        <td><small>{{ $mov->descripcion ?? '-' }}</small></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Sin movimientos</td>
                    </tr>
                @endforelse
            </tbody>
        </table>


        @if ($ultimosMovimientos->count())
            <div class="px-2 mt-3">
                {{ $ultimosMovimientos->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    {{-- BILLETERAS --}}

    <div class="card p-3 mb-4">

        <h5>Billeteras</h5>

        <table class="table table">
            <thead>
                <tr>
                    <th>Billetera</th>
                    <th>Saldo</th>
                    <th>Porcentaje asignado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($billeterasData as $billetera)
                    <tr>
                        <td>{{ $billetera->nombre }}</td>
                        <td>${{ number_format($billetera->saldo, 2) }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span>{{ number_format($billetera->porcentaje, 1) }}%</span>
                                <div class="progress flex-grow-1" style="height: 20px;">
                                    <div class="progress-bar" role="progressbar"
                                        style="width: {{ $billetera->porcentaje }}%;"
                                        aria-valuenow="{{ $billetera->porcentaje }}" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>

    {{-- GRÁFICAS --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-center">Ingresos vs Gastos</h5>
                    <div style="height: 250px;">
                        <canvas id="grafica-ingresos-gastos"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-center">Distribución de Gastos por Categoría</h5>
                    <div style="height: 250px;">
                        <canvas id="grafica-gastos-categoria"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Gráfico de barras: Ingresos vs Gastos
        const ctxIngresoGasto = document.getElementById('grafica-ingresos-gastos');
        new Chart(ctxIngresoGasto, {
            type: 'bar',
            data: {
                labels: ['Ingresos', 'Gastos', 'Balance'],
                datasets: [{
                    label: 'Monto en $',
                    data: [
                        {{ $datosGrafica->ingresos ?? 0 }},
                        {{ $datosGrafica->gastos ?? 0 }},
                        {{ $datosGrafica->balance ?? 0 }}
                    ],
                    backgroundColor: [
                        'rgba(25, 135, 84, 0.7)', // verde
                        'rgba(220, 53, 69, 0.7)', // rojo
                        'rgba(13, 110, 253, 0.7)' // azul
                    ],
                    borderColor: [
                        'rgba(25, 135, 84, 1)',
                        'rgba(220, 53, 69, 1)',
                        'rgba(13, 110, 253, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de pastel: Gastos por Categoría
        const ctxGastosCat = document.getElementById('grafica-gastos-categoria');
        const colores = [
            'rgba(255, 99, 132, 0.7)',
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 206, 86, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(255, 159, 64, 0.7)',
            'rgba(199, 199, 199, 0.7)',
            'rgba(83, 102, 255, 0.7)'
        ];

        new Chart(ctxGastosCat, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($nombresCategorias) !!},
                datasets: [{
                    data: {!! json_encode($montosCategorias) !!},
                    backgroundColor: colores.slice(0, {!! count($nombresCategorias) !!}),
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(199, 199, 199, 1)',
                        'rgba(83, 102, 255, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '$' + context.parsed.toFixed(2);
                            }
                        }
                    }
                }
            }
        });
    </script>
@endpush
