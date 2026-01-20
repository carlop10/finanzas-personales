@extends('layouts.app')

@section('title', 'Editar Billetera')

@section('content')

    <div class="card py-2 px-3 mb-3">
        <h4 class="mb-3">Editar billetera</h4>

        <form method="POST" action="{{ route('billeteras.update', $billetera) }}">
            @csrf
            @method('PUT')

            @include('billeteras.form')

            <button class="btn btn-success">Actualizar</button>
            <a href="{{ route('billeteras.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
@endsection
