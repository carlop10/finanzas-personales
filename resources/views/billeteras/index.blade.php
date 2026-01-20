@extends('layouts.app')

@section('title', 'Billeteras')

@section('content')

    <div class="d-flex justify-content-between mb-3">
        <h4 class="mb-0">Billeteras</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('billeteras.transferencias.index') }}" class="btn btn-warning">Transferir entre billeteras</a>
            <a href="{{ route('billeteras.create') }}" class="btn btn-primary">Nueva billetera</a>
        </div>
    </div>

    <div class="card p-3">
        @if($billeteras->isEmpty())
        <div class="alert alert-warning text-center">
            No hay billeteras creadas.
        </div>
        @else
            <table class="table table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Saldo</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($billeteras as $b)
                        <tr>
                            <td>{{ $b->nombre }}</td>
                            <td>${{ number_format($b->saldo, 2) }}</td>
                            <td class="d-flex justify-content-center">
                                <a href="{{ route('billeteras.show', $b) }}" class="btn btn-sm" title="Ver">🔎</a>
                                <a href="{{ route('billeteras.edit', $b) }}" class="btn btn-sm" title="Editar">✏️</a>
                                <form action="{{ route('billeteras.destroy', $b) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('¿Eliminar billetera?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-smr" title="Eliminar">❌</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
