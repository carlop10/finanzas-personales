@php($billetera = $billetera ?? null)

<div class="mb-3">
    <label class="form-label">Nombre</label>
    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', optional($billetera)->nombre) }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Saldo inicial</label>
    <input type="number" step="0.01" name="saldo" class="form-control" value="{{ old('saldo', optional($billetera)->saldo ?? 0) }}" required>
</div>
