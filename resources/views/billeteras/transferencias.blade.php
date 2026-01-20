@extends('layouts.app')

@section('title', 'Transferencia entre billeteras')

@section('content')

    <div class="mb-3">
        <h4>Transferir saldo entre billeteras</h4>
        <p class="text-muted">Transfiere dinero manualmente de una billetera a otra.</p>
    </div>

    <div class="card">
        <div class="card-body">
            @if ($billeteras->isEmpty())
                <div class="alert alert-warning text-center">
                    No hay billeteras creadas.
                </div>
            @else
                @if ($billeteras->count() < 2)
                    <p class="text-warning mb-2 p-2">Necesitas al menos dos billeteras para realizar una transferencia.</p>
                @else
                    <form method="POST" action="{{ route('billeteras.transferencias.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Desde (billetera origen)</label>
                            <select name="origen_id" class="form-select" required>
                                <option value="">Selecciona la billetera de origen</option>
                                @foreach ($billeteras as $b)
                                    <option value="{{ $b->id }}" @selected(old('origen_id') == $b->id)>
                                        {{ $b->nombre }} (Saldo: ${{ number_format($b->saldo, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('origen_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Hacia (billetera destino)</label>
                            <select name="destino_id" class="form-select" required>
                                <option value="">Selecciona la billetera de destino</option>
                                @foreach ($billeteras as $b)
                                    <option value="{{ $b->id }}" @selected(old('destino_id') == $b->id)>
                                        {{ $b->nombre }} (Saldo: ${{ number_format($b->saldo, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('destino_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Monto a transferir</label>
                            <input type="number" name="monto" step="0.01" min="0.01" class="form-control"
                                value="{{ old('monto') }}" required>
                            @error('monto')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <button class="btn btn-success">Transferir</button>
                        <a href="{{ route('billeteras.index') }}" class="btn btn-secondary">Volver</a>
                    </form>
                @endif
            @endif
        </div>
    </div>

@endsection
