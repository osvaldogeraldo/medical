<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMINISTRADOR = 'Administrador';
    case FARMACEUTICO = 'Farmaceutico';
    case CAIXA = 'Caixa';
    case GERENTE = 'Gerente';
    case CONTROLADOR_DE_STOCK = 'Controlador_de_stock';
    case ASSISTENTE = 'Assistente';
    case CLIENTE = 'Cliente';

    /**
     * Retorna nome legível (para dropdowns)
     */
    public function label(): string
    {
        return match ($this) {
            self::ADMINISTRADOR => 'Administrador',
            self::FARMACEUTICO => 'Farmacêutico',
            self::CAIXA => 'Caixa',
            self::GERENTE => 'Gerente',
            self::CONTROLADOR_DE_STOCK => 'Controlador de Stock',
            self::ASSISTENTE => 'Assistente',
            self::CLIENTE => 'Cliente',
        };
    }

    /**
     * Retorna opções para combobox
     */
    public static function options(): array
    {
        $data = [];
        foreach (self::cases() as $case) {
            $data[] = [
                'value' => $case->value,
                'label' => $case->label(),
            ];
        }
        return $data;
    }
}
