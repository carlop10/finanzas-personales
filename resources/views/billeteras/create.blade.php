@extends('layouts.app')

@section('title', 'Nueva Billetera')

@section('content')

<h4 class="mb-3">Crear billetera</h4>

<form method="POST" action="{{ route('billeteras.store') }}">
    @csrf

    @include('billeteras.form')

    <button class="btn btn-success">Guardar</button>
    <a href="{{ route('billeteras.index') }}" class="btn btn-secondary">Cancelar</a>
</form>

@endsection
