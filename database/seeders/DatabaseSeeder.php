<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Billetera;
use App\Models\Categoria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear usuario de prueba
        $user = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('123456789'),
        ]);

        // Crear billeteras
        Billetera::create([
            'user_id' => $user->id,
            'nombre' => 'Billetera Principal',
            'saldo' => 0,
        ]);

        Billetera::create([
            'user_id' => $user->id,
            'nombre' => 'Estudios',
            'saldo' => 0,
        ]);

        Billetera::create([
            'user_id' => $user->id,
            'nombre' => 'Ahorros',
            'saldo' => 0,
        ]);

        Billetera::create([
            'user_id' => $user->id,
            'nombre' => 'Gastos',
            'saldo' => 0,
        ]);

        Billetera::create([
            'user_id' => $user->id,
            'nombre' => 'Inversiones',
            'saldo' => 0,
        ]);

        Billetera::create([
            'user_id' => $user->id,
            'nombre' => 'Emergencias',
            'saldo' => 0,
        ]);

        Billetera::create([
            'user_id' => $user->id,
            'nombre' => 'Ocio',
            'saldo' => 0,
        ]);

        // Crear categorías de ingreso
        Categoria::create([
            'user_id' => $user->id,
            'nombre' => 'Trabajo',
            'tipo' => 'ingreso',
        ]);

        Categoria::create([
            'user_id' => $user->id,
            'nombre' => 'Otros ingresos',
            'tipo' => 'ingreso',
        ]);

        // Crear categorías de gasto
        Categoria::create([
            'user_id' => $user->id,
            'nombre' => 'Alimentación',
            'tipo' => 'gasto',
        ]);

        Categoria::create([
            'user_id' => $user->id,
            'nombre' => 'Servicios',
            'tipo' => 'gasto',
        ]);

        Categoria::create([
            'user_id' => $user->id,
            'nombre' => 'Ocio',
            'tipo' => 'gasto',
        ]);

        Categoria::create([
            'user_id' => $user->id,
            'nombre' => 'Gasolina',
            'tipo' => 'gasto',
        ]);

        Categoria::create([
            'user_id' => $user->id,
            'nombre' => 'Salud',
            'tipo' => 'gasto',
        ]);

        Categoria::create([
            'user_id' => $user->id,
            'nombre' => 'Educación',
            'tipo' => 'gasto',
        ]);

        Categoria::create([
            'user_id' => $user->id,
            'nombre' => 'Mantenimiento moto',
            'tipo' => 'gasto',
        ]);

        Categoria::create([
            'user_id' => $user->id,
            'nombre' => 'Otros gastos',
            'tipo' => 'gasto',
        ]);

    }
}
