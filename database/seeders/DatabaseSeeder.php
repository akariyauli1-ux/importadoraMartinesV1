<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Sucursal;
use App\Models\ConfiguracionEmpresa;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Roles
        $roleGerente = Role::create(['name' => 'Gerente']);
        $roleAdmin = Role::create(['name' => 'Administrador']);
        $roleTecnico = Role::create(['name' => 'Técnico']);
        $roleRecepcionista = Role::create(['name' => 'Recepcionista']);

        // 2. Create Default company config
        ConfiguracionEmpresa::create([
            'nombre_comercial' => 'IMPORTADORA MARTINEZ',
            'logo_path' => null
        ]);

        // 3. Create Default Branch (Sucursal)
        $sucursal = Sucursal::create([
            'nombre' => 'Sede Central',
            'direccion' => 'Av. 6 de Agosto #150, Zona Sopocachi',
            'telefono' => '22445566',
            'activa' => true,
        ]);

        // 4. Create Gerente (does not belong to a specific branch necessarily, or can be null)
        $gerente = User::create([
            'name' => 'Don Juan',
            'apellido_paterno' => 'Martinez',
            'apellido_materno' => 'Miranda',
            'carnet_identidad' => '123456',
            'email' => 'gerente@importadoramartinez.com',
            'password' => Hash::make('martinez'), // Rule: father's last name in lowercase
            'password_changed' => false,
            'sucursal_id' => null,
        ]);
        $gerente->assignRole($roleGerente);

        // 5. Create Admin for Sede Central
        $admin = User::create([
            'name' => 'Carlos',
            'apellido_paterno' => 'Perez',
            'apellido_materno' => 'Torres',
            'carnet_identidad' => '777777',
            'email' => 'admin.central@importadoramartinez.com',
            'password' => Hash::make('perez'),
            'password_changed' => false,
            'sucursal_id' => $sucursal->id,
        ]);
        $admin->assignRole($roleAdmin);

        // 6. Create Técnico for Sede Central
        $tecnico = User::create([
            'name' => 'Ramiro',
            'apellido_paterno' => 'Gomez',
            'apellido_materno' => 'Vargas',
            'carnet_identidad' => '888888',
            'email' => 'ramiro.tecnico@importadoramartinez.com',
            'password' => Hash::make('gomez'),
            'password_changed' => false,
            'sucursal_id' => $sucursal->id,
        ]);
        $tecnico->assignRole($roleTecnico);

        // 7. Create Recepcionista for Sede Central
        $recepcionista = User::create([
            'name' => 'Ana',
            'apellido_paterno' => 'Lopez',
            'apellido_materno' => 'Camacho',
            'carnet_identidad' => '999999',
            'email' => 'ana.recepcion@importadoramartinez.com',
            'password' => Hash::make('lopez'),
            'password_changed' => false,
            'sucursal_id' => $sucursal->id,
        ]);
        $recepcionista->assignRole($roleRecepcionista);
    }
}
