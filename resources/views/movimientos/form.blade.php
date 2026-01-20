@php($movimiento = $movimiento ?? null)

<div class="mb-3">
    <label class="form-label">Billetera</label>
    <select name="billetera_id" class="form-select" required>
        @foreach ($billeteras as $b)
            <option value="{{ $b->id }}" @selected(old('billetera_id', optional($movimiento)->billetera_id) == $b->id)>
                {{ $b->nombre }} (Saldo: ${{ number_format($b->saldo, 2) }})
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Tipo</label>
    <select name="tipo" class="form-select" required>
        <option value="ingreso" @selected(old('tipo', optional($movimiento)->tipo ?? 'ingreso') === 'ingreso')>Ingreso</option>
        <option value="gasto" @selected(old('tipo', optional($movimiento)->tipo ?? 'ingreso') === 'gasto')>Gasto</option>
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Categoría</label>
    <select name="categoria_id" class="form-select" required>
        <optgroup label="Ingresos">
            @foreach ($categoriasIngreso as $cat)
                <option value="{{ $cat->id }}" @selected(old('categoria_id', optional($movimiento)->categoria_id) == $cat->id)>
                    {{ $cat->nombre }}
                </option>
            @endforeach
        </optgroup>
        <optgroup label="Gastos">
            @foreach ($categoriasGasto as $cat)
                <option value="{{ $cat->id }}" @selected(old('categoria_id', optional($movimiento)->categoria_id) == $cat->id)>
                    {{ $cat->nombre }}
                </option>
            @endforeach
        </optgroup>
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Monto</label>
    <input type="number" step="0.01" name="monto" class="form-control" value="{{ old('monto', optional($movimiento)->monto) }}" required>
</div>

<div id="campos-trabajo" style="{{ old('tipo', optional($movimiento)->tipo ?? 'ingreso') === 'ingreso' ? '' : 'display:none;' }}">

    <div class="mb-3">
        <label class="form-label">Horas trabajadas</label>
        <input type="number" step="0.25" name="horas_trabajadas" class="form-control" value="{{ old('horas_trabajadas', optional($movimiento)->horas_trabajadas) }}">
    </div>

    <div class="mb-3">
        <label class="form-label">Kilómetros recorridos</label>
        <input type="number" step="0.1" name="kilometros_recorridos" class="form-control" value="{{ old('kilometros_recorridos', optional($movimiento)->kilometros_recorridos) }}">
    </div>

</div>

<div class="mb-3">
    <label class="form-label">Fecha</label>
    <input type="date" name="fecha" class="form-control" value="{{ old('fecha', optional(optional($movimiento)->fecha)->format('Y-m-d') ?? now()->toDateString()) }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Descripción</label>
    <textarea name="descripcion" class="form-control">{{ old('descripcion', optional($movimiento)->descripcion) }}</textarea>
</div>

@push('scripts')
    <script>
        const tipoSelect = document.querySelector('[name="tipo"]');
        const camposTrabajo = document.getElementById('campos-trabajo');

        function toggleCamposTrabajo() {
            camposTrabajo.style.display = tipoSelect.value === 'ingreso' ? 'block' : 'none';
        }

        tipoSelect.addEventListener('change', toggleCamposTrabajo);
        toggleCamposTrabajo();
    </script>
@endpush
