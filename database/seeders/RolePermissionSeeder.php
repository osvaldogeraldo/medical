<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Enums\RoleEnum;
use App\Enums\PermissionEnum;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // === 1️⃣ Criar permissões ===
        foreach (PermissionEnum::cases() as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm->value],
                ['guard_name' => 'web']
            );
        }

        // === 2️⃣ Criar roles e atribuir permissões ===
        foreach (RoleEnum::cases() as $roleEnum) {
            $role = Role::firstOrCreate(
                ['name' => $roleEnum->value],
                ['guard_name' => 'web']
            );

            // === 3️⃣ Atribuição personalizada de permissões ===
            switch ($roleEnum) {
                case RoleEnum::ADMINISTRADOR:
                    // Tem todas as permissões
                    $role->syncPermissions(Permission::all());
                    break;

                case RoleEnum::GERENTE:
                    $role->syncPermissions([
                        PermissionEnum::VER_MEDICAMENTOS->value,
                        PermissionEnum::ADICIONAR_MEDICAMENTOS->value,
                        PermissionEnum::EDITAR_MEDICAMENTOS->value,
                        PermissionEnum::REMOVER_MEDICAMENTOS->value,
                        PermissionEnum::VER_COMPRAS->value,
                        PermissionEnum::REGISTAR_COMPRAS->value,
                        PermissionEnum::VER_VENDAS->value,
                        PermissionEnum::RELATORIO_DE_VENDAS->value,
                        PermissionEnum::RELATORIO_DE_COMPRAS->value,
                        PermissionEnum::RELATORIO_FINANCEIRO->value,
                    ]);
                    break;

                case RoleEnum::FARMACEUTICO:
                    $role->syncPermissions([
                        PermissionEnum::VER_MEDICAMENTOS->value,
                        PermissionEnum::ADICIONAR_MEDICAMENTOS->value,
                        PermissionEnum::EDITAR_MEDICAMENTOS->value,
                        PermissionEnum::VER_STOCK->value,
                        PermissionEnum::AJUSTAR_STOCK->value,
                        PermissionEnum::ALERTAS_DE_VALIDADE->value,
                    ]);
                    break;

                case RoleEnum::CAIXA:
                    $role->syncPermissions([
                        PermissionEnum::VER_MEDICAMENTOS->value,
                        PermissionEnum::REGISTAR_VENDAS->value,
                        PermissionEnum::ANULAR_VENDAS->value,
                        PermissionEnum::REEMBOLSAR_VENDAS->value,
                        PermissionEnum::VER_VENDAS->value,
                    ]);
                    break;

                case RoleEnum::CONTROLADOR_DE_STOCK:
                    $role->syncPermissions([
                        PermissionEnum::VER_STOCK->value,
                        PermissionEnum::AJUSTAR_STOCK->value,
                        PermissionEnum::TRANSFERIR_STOCK->value,
                        PermissionEnum::ALERTAS_DE_VALIDADE->value,
                        PermissionEnum::RELATORIO_DE_INVENTARIO->value,
                        PermissionEnum::RELATORIO_DE_VALIDADES->value,
                    ]);
                    break;

                case RoleEnum::ASSISTENTE:
                    $role->syncPermissions([
                        PermissionEnum::VER_MEDICAMENTOS->value,
                        PermissionEnum::VER_VENDAS->value,
                        PermissionEnum::VER_COMPRAS->value,
                    ]);
                    break;

                case RoleEnum::CLIENTE:
                    $role->syncPermissions([
                        PermissionEnum::VER_MEDICAMENTOS->value,
                        PermissionEnum::VER_VENDAS->value,
                    ]);
                    break;
            }
        }

        // === 4️⃣ Criar utilizador Administrador padrão ===
        if (!User::where('email', 'admin@farmacia.test')->exists()) {
            $admin = User::create([
                'name' => 'Administrador do Sistema',
                'email' => 'admin@farmacia.test',
                'password' => bcrypt('12345678'),
            ]);

            $admin->assignRole(RoleEnum::ADMINISTRADOR->value);
        }
    }
}
