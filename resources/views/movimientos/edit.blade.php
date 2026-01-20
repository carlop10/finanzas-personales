@extends('layouts.app')

@section('content')
    <div class="card py-2 px-3 mb-3">
        <h4>Editar movimiento</h4>

        <form method="POST" action="{{ route('movimientos.update', $movimiento) }}">
            @csrf
            @method('PUT')

            @include('movimientos.form')

            <button class="btn btn-success">Actualizar</button>
            <a href="{{ route('movimientos.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
@endsection
