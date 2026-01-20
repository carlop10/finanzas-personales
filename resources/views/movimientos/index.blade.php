@extends('layouts.app')

@section('title', 'Movimientos')

@section('content')

    <div class="d-flex justify-content-between mb-3">
        <h4>
            Movimientos
            @if ($fechaDesde || $fechaHasta)
                <small class="text-muted">
                    ({{ $fechaDesde ?? '...' }} - {{ $fechaHasta ?? '...' }})
                </small>
            @else
                <small class="text-muted">(Mes actual)</small>
            @endif
        </h4>


        <a href="{{ route('movimientos.create') }}" class="btn btn-warning">
            Nuevo movimiento
        </a>
    </div>

    <form method="GET" class="row g-2 mb-3 align-items-end">

        <div class="col-md-2">
            <label class="form-label">Buscar</label>
            <input type="text" name="busqueda" placeholder="Descripción o monto..." class="form-control"
                value="{{ $busqueda ?? '' }}">
        </div>

        <div class="col-md-2">
            <label class="form-label">Desde</label>
            <input type="date" name="fecha_desde" value="{{ $fechaDesde }}" class="form-control">
        </div>

        <div class="col-md-2">
            <label class="form-label">Hasta</label>
            <input type="date" name="fecha_hasta" value="{{ $fechaHasta }}" class="form-control">
        </div>

        <div class="col-md-3">
            <label class="form-label">Categoría</label>
            <select name="categoria_id" class="form-select">
                <option value="">Todas las categorías</option>
                @foreach ($categorias as $cat)
                    <option value="{{ $cat->id }}" @selected($categoriaId == $cat->id)>
                        {{ $cat->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-dark flex-grow-1">Filtrar</button>
                <a href="{{ route('movimientos.export', request()->query()) }}" class="btn btn-success"
                    title="Exportar a Excel">
                    📊 Exportar
                </a>
            </div>
        </div>

    </form>


    <div class="card py-2 px-1">
        <table class="table table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Categoría</th>
                    <th>Tipo</th>
                    <th>Monto</th>
                    <th>Descripción</th>
                    <th class="text-center">Acciones</th>
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
                        <td class="d-flex justify-content-center gap-1">
                            <a href="{{ route('movimientos.edit', $mov) }}" class="btn btn-sm" title="Editar">
                                ✏️
                            </a>
                            <form action="{{ route('movimientos.destroy', $mov) }}" method="POST" class="d-inline"
                                title="Eliminar">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm "
                                    onclick="return confirm('¿Estás seguro de eliminar este movimiento?')">
                                    ❌
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Sin movimientos</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- PAGINACIÓN --}}
        @if ($movimientos->count())
            <nav aria-label="Page navigation" class="px-2">
                {{ $movimientos->appends(request()->query())->links('pagination::bootstrap-5') }}
            </nav>
        @else
            <div class="alert alert-info text-center py-2">
                No hay movimientos que coincidan con los filtros aplicados
            </div>
        @endif
    </div>

@endsection
