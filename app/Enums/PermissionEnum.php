<?php

namespace App\Enums;

enum PermissionEnum: string
{
    // ==== Utilizadores ====
    case VER_UTILIZADORES = 'ver_utilizadores';
    case CRIAR_UTILIZADORES = 'criar_utilizadores';
    case EDITAR_UTILIZADORES = 'editar_utilizadores';
    case ELIMINAR_UTILIZADORES = 'eliminar_utilizadores';

        // ==== Medicamentos / Produtos ====
    case VER_MEDICAMENTOS = 'ver_medicamentos';
    case ADICIONAR_MEDICAMENTOS = 'adicionar_medicamentos';
    case EDITAR_MEDICAMENTOS = 'editar_medicamentos';
    case REMOVER_MEDICAMENTOS = 'remover_medicamentos';

        // ==== Compras ====
    case VER_COMPRAS = 'ver_compras';
    case REGISTAR_COMPRAS = 'registar_compras';
    case EDITAR_COMPRAS = 'editar_compras';
    case ELIMINAR_COMPRAS = 'eliminar_compras';
    case APROVAR_COMPRAS = 'aprovar_compras';

        // ==== Vendas ====
    case VER_VENDAS = 'ver_vendas';
    case REGISTAR_VENDAS = 'registar_vendas';
    case ANULAR_VENDAS = 'anular_vendas';
    case REEMBOLSAR_VENDAS = 'reembolsar_vendas';

        // ==== Stock ====
    case VER_STOCK = 'ver_stock';
    case AJUSTAR_STOCK = 'ajustar_stock';
    case TRANSFERIR_STOCK = 'transferir_stock';
    case ALERTAS_DE_VALIDADE = 'alertas_de_validade';

        // ==== Relatórios ====
    case RELATORIO_DE_VENDAS = 'relatorio_de_vendas';
    case RELATORIO_DE_COMPRAS = 'relatorio_de_compras';
    case RELATORIO_DE_INVENTARIO = 'relatorio_de_inventario';
    case RELATORIO_DE_VALIDADES = 'relatorio_de_validades';
    case RELATORIO_FINANCEIRO = 'relatorio_financeiro';

        // ==== Configurações ====
    case GERIR_CONFIGURACOES = 'gerir_configuracoes';
    case GERIR_IMPOSTOS = 'gerir_impostos';
    case GERIR_BACKUPS = 'gerir_backups';

    /**
     * Nome legível
     */
    public function label(): string
    {
        return match ($this) {
            // Utilizadores
            self::VER_UTILIZADORES => 'Ver Utilizadores',
            self::CRIAR_UTILIZADORES => 'Criar Utilizadores',
            self::EDITAR_UTILIZADORES => 'Editar Utilizadores',
            self::ELIMINAR_UTILIZADORES => 'Eliminar Utilizadores',

            // Medicamentos
            self::VER_MEDICAMENTOS => 'Ver Medicamentos',
            self::ADICIONAR_MEDICAMENTOS => 'Adicionar Medicamentos',
            self::EDITAR_MEDICAMENTOS => 'Editar Medicamentos',
            self::REMOVER_MEDICAMENTOS => 'Remover Medicamentos',

            // Compras
            self::VER_COMPRAS => 'Ver Compras',
            self::REGISTAR_COMPRAS => 'Registar Compras',
            self::EDITAR_COMPRAS => 'Editar Compras',
            self::ELIMINAR_COMPRAS => 'Eliminar Compras',
            self::APROVAR_COMPRAS => 'Aprovar Compras',

            // Vendas
            self::VER_VENDAS => 'Ver Vendas',
            self::REGISTAR_VENDAS => 'Registar Vendas',
            self::ANULAR_VENDAS => 'Anular Vendas',
            self::REEMBOLSAR_VENDAS => 'Reembolsar Vendas',

            // Stock
            self::VER_STOCK => 'Ver Stock',
            self::AJUSTAR_STOCK => 'Ajustar Stock',
            self::TRANSFERIR_STOCK => 'Transferir Stock',
            self::ALERTAS_DE_VALIDADE => 'Alertas de Validade',

            // Relatórios
            self::RELATORIO_DE_VENDAS => 'Relatório de Vendas',
            self::RELATORIO_DE_COMPRAS => 'Relatório de Compras',
            self::RELATORIO_DE_INVENTARIO => 'Relatório de Inventário',
            self::RELATORIO_DE_VALIDADES => 'Relatório de Validades',
            self::RELATORIO_FINANCEIRO => 'Relatório Financeiro',

            // Configurações
            self::GERIR_CONFIGURACOES => 'Gerir Configurações',
            self::GERIR_IMPOSTOS => 'Gerir Impostos',
            self::GERIR_BACKUPS => 'Gerir Backups',
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
