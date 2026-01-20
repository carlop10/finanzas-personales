@extends('layouts.app')

@section('title', 'Billetera')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">{{ $billetera->nombre }}</h4>
        <small class="text-muted">Saldo: ${{ number_format($billetera->saldo, 2) }}</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('billeteras.edit', $billetera) }}" class="btn btn-secondary btn-sm">Editar</a>
        <form method="POST" action="{{ route('billeteras.destroy', $billetera) }}" onsubmit="return confirm('¿Eliminar esta billetera?')">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger btn-sm">Eliminar</button>
        </form>
        <a href="{{ route('billeteras.index') }}" class="btn btn-outline-secondary btn-sm">Volver</a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <span>Distribución automática</span>
            <small class="text-muted">Configura cómo se reparte automáticamente cada ingreso</small>
        </div>
    </div>
    <div class="card-body">
        <div class="alert alert-info mb-3">
            <strong>ℹ️ ¿Cómo funciona?</strong><br>
            Cuando registres un <strong>ingreso</strong> en esta billetera, el monto se distribuirá automáticamente según los porcentajes configurados. Por ejemplo: si ingresan $1000 y tienes 30% para ahorros, se transferirán automáticamente $300 a la billetera de ahorros.
        </div>
        <form class="row g-2 align-items-end mb-3" method="POST" action="{{ route('billeteras.distribuciones.store', $billetera) }}">
            @csrf
            <div class="col-md-6">
                <label class="form-label">Billetera destino</label>
                <select name="billetera_destino_id" class="form-select" required>
                    <option value="">Selecciona una billetera</option>
                    @foreach($otrasBilleteras as $otra)
                        <option value="{{ $otra->id }}">{{ $otra->nombre }} (Saldo: ${{ number_format($otra->saldo, 2) }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Porcentaje</label>
                <div class="input-group">
                    <input type="number" step="0.01" min="0.01" max="100" name="porcentaje" class="form-control" required>
                    <span class="input-group-text">%</span>
                </div>
            </div>
            <div class="col-md-3">
                <button class="btn btn-success w-100">Agregar</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead>
                    <tr>
                        <th>Destino</th>
                        <th>Porcentaje</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($distribuciones as $dist)
                        <tr>
                            <td>{{ $dist->destino->nombre }}</td>
                            <td>
                                <form class="d-flex align-items-center gap-2" method="POST" action="{{ route('billeteras.distribuciones.update', [$billetera, $dist]) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="input-group input-group-sm" style="max-width: 150px;">
                                        <input type="number" step="0.01" min="0.01" max="100" name="porcentaje" class="form-control" value="{{ $dist->porcentaje }}" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <input type="hidden" name="billetera_destino_id" value="{{ $dist->billetera_destino_id }}">
                                    <button class="btn btn-primary btn-sm">Guardar</button>
                                </form>
                            </td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('billeteras.distribuciones.destroy', [$billetera, $dist]) }}" onsubmit="return confirm('¿Eliminar distribución?')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Sin distribuciones configuradas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<h6>Movimientos</h6>
<table class="table table-striped">
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
        @forelse($movimientos as $mov)
            <tr>
                <td>{{ $mov->fecha->format('Y-m-d') }}</td>
                <td>{{ $mov->categoria->nombre }}</td>
                <td>
                    <span class="badge bg-{{ $mov->tipo === 'ingreso' ? 'success' : 'danger' }}">
                        {{ ucfirst($mov->tipo) }}
                    </span>
                </td>
                <td>${{ number_format($mov->monto, 2) }}</td>
                <td>{{ $mov->descripcion }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">Sin movimientos</td>
            </tr>
        @endforelse
    </tbody>
</table>

@endsection
