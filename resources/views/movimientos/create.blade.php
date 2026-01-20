@extends('layouts.app')

@section('title', 'Nuevo Movimiento')

@section('content')

    <h4 class="mb-3">Registrar movimiento</h4>

    <form method="POST" action="{{ route('movimientos.store') }}">
        @csrf

        @include('movimientos.form')

        <button class="btn btn-success">Guardar</button>
        <a href="{{ route('movimientos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>

@endsection
