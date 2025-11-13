<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Imports\CategoryImport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CategoryController extends Controller
{
    /**
     * Listar categorias com paginação e contagem de medicamentos.
     */
   public function index(Request $request)
{
    $query = Category::withCount('medicines')->orderBy('created_at', 'desc');

    if ($request->filled('search')) {
        $query->where('name', 'like', "%{$request->search}%");
    }

    $categories = $query->paginate(6)->withQueryString(); // aqui ajuste a quantidade por página

    return Inertia::render('Dashboard/Category/Index', [
        'categories' => $categories,
        'filters' => $request->only('search'),
    ]);
}


    /**
     * Criar nova categoria.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Categoria criada com sucesso.');
    }

    /**
     * Atualizar categoria.
     */
    public function update(Request $request, $uuid)
    {
        $category = Category::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Categoria atualizada com sucesso.');
    }

    /**
     * Soft delete de categoria.
     */
    public function destroy($uuid)
    {
        $category = Category::where('uuid', $uuid)->firstOrFail();
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Categoria eliminada com sucesso.');
    }

    /**
     * Restaurar categoria eliminada.
     */
    public function restore($uuid)
    {
        $category = Category::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $category->restore();

        return redirect()->route('categories.index')->with('success', 'Categoria restaurada com sucesso.');
    }

    /**
     * Alternar estado activo/inactivo.
     */
    public function toggle($uuid)
    {
        $category = Category::where('uuid', $uuid)->firstOrFail();
        $category->is_active = ! $category->is_active;
        $category->save();

        return redirect()->route('categories.index')->with('success', 'Estado da categoria atualizado com sucesso.');
    }

    /**
     * Bulk delete usando array de UUIDs.
     */
    public function bulkDelete(Request $request)
    {
        $uuids = $request->input('uuids', []);
        Category::whereIn('uuid', $uuids)->delete();

        return redirect()->route('categories.index')->with('success', 'Categorias eliminadas com sucesso.');
    }

    /**
     * Importar categorias via ficheiro Excel.
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $inserted = 0;
        $failed = 0;

        // Pular cabeçalho (começa na linha 2)
        foreach (array_slice($rows, 1) as $row) {
            try {
                Category::create([
                    'name' => $row['A'] ?? null,
                    'description' => $row['B'] ?? null,
                    'is_active' => isset($row['C']) ? (bool)$row['C'] : true,
                ]);
                $inserted++;
            } catch (\Throwable $e) {
                $failed++;
            }
        }

        return response()->json([
            'success' => true,
            'inserted' => $inserted,
            'failed' => $failed,
        ]);
    }

    public function template()
    {
        // Cria nova planilha
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Cabeçalho
        $sheet->setCellValue('A1', 'Nome');
        $sheet->setCellValue('B1', 'Descricao');
        $sheet->setCellValue('C1', 'Estado');

        // Linha de exemplo
        $sheet->setCellValue('A2', 'Categoria Exemplo');
        $sheet->setCellValue('B2', 'Descrição opcional');
        $sheet->setCellValue('C2', '1');

        // Formatação: negrito no cabeçalho
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);

        // Largura automática das colunas
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Nome do arquivo
        $filename = 'template_categorias.xlsx';

        // Cria o writer e envia direto para o browser
        $writer = new Xlsx($spreadsheet);

        // Limpa o buffer e envia o arquivo
        ob_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        $writer->save('php://output');
        exit;
    }
}
