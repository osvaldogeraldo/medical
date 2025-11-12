<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ===== 1️⃣ Garantir que todas as roles existem =====
        foreach (RoleEnum::cases() as $roleEnum) {
            Role::firstOrCreate(['name' => $roleEnum->value]);
        }

        $passwordDefault = Hash::make('Brasa2025$');

        // ===== 2️⃣ Criar 10 usuários para cada role =====
        foreach (RoleEnum::cases() as $roleEnum) {
            for ($i = 1; $i <= 10; $i++) {
                $username = Str::slug($roleEnum->label()) . $i; // ex: administrador1
                $email = strtolower($username) . '@farmacia.test';

                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $roleEnum->label() . ' ' . $i,
                        'username' => $username,
                        'phone' => '8400000' . str_pad($i, 3, '0', STR_PAD_LEFT),
                        'password' => $passwordDefault,
                    ]
                );

                $user->assignRole($roleEnum->value);
            }
        }

        $this->command->info('✅ 10 usuários criados para cada role com senha padrão Brasa2025$');
    }
}
