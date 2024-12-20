<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;


class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        // Limpiar la tabla antes de sembrar
        Usuario::truncate();
        
        $faker = Faker::create('es_ES');
        
        // Crear un array de roles para distribuir
        $roles = ['admin', 'user'];
        
        // Usar chunk insert para mejor rendimiento
        $chunkSize = 1000;
        $numberOfUsers = 40000;
        
        for ($i = 0; $i < $numberOfUsers; $i += $chunkSize) {
            $users = [];
            
            for ($j = 0; $j < $chunkSize && ($i + $j) < $numberOfUsers; $j++) {
                $users[] = [
                    'apodo' => $faker->unique()->userName,
                    'contrasenha' => Hash::make('password'), // contraseña por defecto
                    'rol' => $roles[array_rand($roles)],
                    'created_at' => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
                    'updated_at' => now()
                ];
            }
            
            // Insertar en chunks
            Usuario::insert($users);
            
            //mostrar progreso
            $this->command->info("Insertados usuarios " . ($i + 1) . " a " . min($i + $chunkSize, $numberOfUsers));
        }
        
        $this->command->info('¡Seeding completado! Se han creado ' . $numberOfUsers . ' usuarios.');
    }
}
