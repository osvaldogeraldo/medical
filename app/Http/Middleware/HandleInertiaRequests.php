<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class HandleInertiaRequests extends Middleware
{
    /**
     * Define o layout raiz.
     */
    protected $rootView = 'app';

    /**
     * Determina os dados compartilhados com todos os requests Inertia.
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [

            // Usuário autenticado
            'auth.user' => fn() => $request->user() ? [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'username' => $request->user()->username,
                'roles' => $request->user()->getRoleNames(), // Spatie
                'permissions' => $request->user()->getAllPermissions()->pluck('name'),
            ] : null,

            // Mensagens de sessão (success, error)
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
            ],

            // Roles disponíveis para dropdowns, etc.
            'roles' => Role::all()->map(fn($role) => [
                'id' => $role->id,
                'name' => $role->name,
            ]),

            // Exemplo: se quiseres alguma configuração global
            'app' => [
                'name' => config('app.name'),
                'env' => config('app.env'),
            ],
        ]);
    }
}
