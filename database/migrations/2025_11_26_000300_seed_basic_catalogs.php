<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tipos de animales
        if (Schema::hasTable('animal_types')) {
            if (!DB::table('animal_types')->where('nombre', 'Silvestre')->exists()) {
                DB::table('animal_types')->insert([
                    'nombre' => 'Silvestre',
                    'permite_adopcion' => false,
                    'permite_liberacion' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Tipos de cuidado
        if (Schema::hasTable('care_types')) {
            $now = now();
            $careTypes = [
                ['nombre' => 'Alimentación', 'descripcion' => 'Cuidado enfocado en la alimentación del animal.'],
                ['nombre' => 'Intensivo', 'descripcion' => 'Cuidado intensivo y monitoreo constante.'],
                ['nombre' => 'Emergencia', 'descripcion' => 'Cuidado de emergencia y atención inmediata.'],
                ['nombre' => 'Control', 'descripcion' => 'Cuidado de control y seguimiento.'],
                ['nombre' => 'Recuperación', 'descripcion' => 'Cuidado de recuperación y reintegración.'],
            ];
            foreach ($careTypes as $care) {
                if (!DB::table('care_types')->where('nombre', $care['nombre'])->exists()) {
                    DB::table('care_types')->insert([
                        'nombre' => $care['nombre'],
                        'descripcion' => $care['descripcion'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        // Tipos de tratamiento
        if (Schema::hasTable('treatment_types')) {
            $now = now();
            $treatments = [
                ['nombre' => 'Malestar estomacal'],
                ['nombre' => 'Antiinflamatorio'],
                ['nombre' => 'Crema para quemaduras'],
                ['nombre' => 'Antiparasitario'],
                ['nombre' => 'Antibiotico'],
                ['nombre' => 'Antiviral']

            ];
            foreach ($treatments as $t) {
                if (!DB::table('treatment_types')->where('nombre', $t['nombre'])->exists()) {
                    DB::table('treatment_types')->insert([
                        'nombre' => $t['nombre'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        // Estados de animales
        if (Schema::hasTable('animal_statuses')) {
            $now = now();
            $statuses = [
                ['nombre' => 'Estable'],
                ['nombre' => 'En atención'],
                ['nombre' => 'En recuperación'],
                ['nombre' => 'Crítico'],
                ['nombre' => 'Observación'],
            ];
            foreach ($statuses as $st) {
                if (!DB::table('animal_statuses')->where('nombre', $st['nombre'])->exists()) {
                    DB::table('animal_statuses')->insert([
                        'nombre' => $st['nombre'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        // Frecuencias de alimentación
        if (Schema::hasTable('feeding_frequencies')) {
            $now = now();
            $freqs = [
                ['nombre' => '3 veces al día', 'descripcion' => '3 veces al día, por la mañana, tarde y noche.'],
                ['nombre' => '4 veces al día', 'descripcion' => '4 veces al día, por la mañana, tarde y noche.'],
                ['nombre' => 'Cada 2 horas', 'descripcion' => '12 veces al día, cada 2 horas.'],
                ['nombre' => 'Cada 4 horas', 'descripcion' => '6 veces al día, cada 4 horas.'],


            ];
            foreach ($freqs as $f) {
                if (!DB::table('feeding_frequencies')->where('nombre', $f['nombre'])->exists()) {
                    DB::table('feeding_frequencies')->insert([
                        'nombre' => $f['nombre'],
                        'descripcion' => $f['descripcion'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        // Porciones de alimentación
        if (Schema::hasTable('feeding_portions')) {
            $now = now();
            $portions = [
                ['cantidad' => 50, 'unidad' => 'gramos'],
                ['cantidad' => 100, 'unidad' => 'gramos'],
                ['cantidad' => 250, 'unidad' => 'gramos'],
                ['cantidad' => 500, 'unidad' => 'gramos'],
                ['cantidad' => 1, 'unidad' => 'kilogramo'],
                ['cantidad' => 2, 'unidad' => 'kilogramos'],
            ];
            foreach ($portions as $p) {
                if (!DB::table('feeding_portions')
                    ->where('cantidad', $p['cantidad'])
                    ->where('unidad', $p['unidad'])
                    ->exists()
                ) {
                    DB::table('feeding_portions')->insert([
                        'cantidad' => $p['cantidad'],
                        'unidad' => $p['unidad'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        // Especies
        if (Schema::hasTable('species')) {
            $now = now();
            $species = [
                'Desconocido',
                'Ave',
                'Mamífero',
                'Reptil',
            ];
            foreach ($species as $name) {
                if (!DB::table('species')->where('nombre', $name)->exists()) {
                    DB::table('species')->insert([
                        'nombre' => $name,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        // En down eliminamos solo los registros insertados por nombre para no borrar datos de producción.

        if (Schema::hasTable('animal_types')) {
            DB::table('animal_types')->where('nombre', 'Silvestre')->delete();
        }

        if (Schema::hasTable('care_types')) {
            DB::table('care_types')->whereIn('nombre', ['Alimentación', 'Intensivo'])->delete();
        }

        if (Schema::hasTable('treatment_types')) {
            DB::table('treatment_types')->whereIn('nombre', [
                'Malestar estomacal',
                'Antiinflamatorio',
                'Crema para quemaduras',
            ])->delete();
        }

        if (Schema::hasTable('animal_statuses')) {
            DB::table('animal_statuses')->whereIn('nombre', [
                'Estable',
                'En recuperación',
                'Crítico',
                'Observación',
            ])->delete();
        }

        if (Schema::hasTable('feeding_frequencies')) {
            DB::table('feeding_frequencies')->whereIn('nombre', [
                '3 veces al día',
                '4 veces al día',
                'Cada 2 horas',
            ])->delete();
        }

        if (Schema::hasTable('feeding_portions')) {
            DB::table('feeding_portions')->whereIn('unidad', ['g', 'kg'])->whereIn('cantidad', [
                50, 100, 250, 500, 1, 2,
            ])->delete();
        }

        if (Schema::hasTable('species')) {
            DB::table('species')->whereIn('nombre', [
                'Desconocido',
                'Ave',
                'Mamífero',
                'Reptil',
            ])->delete();
        }
    }
};


