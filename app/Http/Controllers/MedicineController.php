<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Category;
use App\Models\Medicine;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MedicineController extends Controller
{
    /**
     * Index - lista os medicamentos com paginação e busca
     */
    public function index(Request $request)
    {
        $query = Medicine::query()->with(['category', 'supplier'])->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('generic_name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $medicines = $query->paginate(10)->withQueryString();

        // Estatísticas gerais
        $statistics = [
            'total' => Medicine::count(),
            'active' => Medicine::where('is_active', true)->count(),
            'inactive' => Medicine::where('is_active', false)->count(),
            'low_stock' => Medicine::whereColumn('stock_quantity', '<=', 'min_stock')->count(),
        ];

        return Inertia::render('Dashboard/Medicine/Index', [
            'medicines' => $medicines,
            'filters' => $request->only('search'),
            'statistics' => $statistics,
        ]);
    }

    /**
     * Mostrar formulário de criação
     */
    // Controlador: MedicineController.php

    public function create()
    {
        $categories = Category::all();
        $suppliers = Supplier::all();

        return Inertia::render('Dashboard/Medicine/Create', [
            'categories' => $categories,
            'suppliers' => $suppliers,
            'isEdit' => false,
            'isView' => false,
        ]);
    }

    public function edit($uuid)
    {
        $medicine = Medicine::with(['category', 'supplier'])->where('uuid', $uuid)->firstOrFail();
        $categories = Category::all();
        $suppliers = Supplier::all();

        return Inertia::render('Dashboard/Medicine/Create', [
            'medicine' => $medicine,
            'categories' => $categories,
            'suppliers' => $suppliers,
            'isEdit' => true,
            'isView' => false,
        ]);
    }

    public function show($uuid)
    {
        $medicine = Medicine::with(['category', 'supplier'])->where('uuid', $uuid)->firstOrFail();

        return Inertia::render('Dashboard/Medicine/Create', [
            'medicine' => $medicine,
            'categories' => Category::all(),
            'suppliers' => Supplier::all(),
            'isEdit' => false,
            'isView' => true,
        ]);
    }


    /**
     * Criar novo medicamento
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|max:255|unique:medicines,sku',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
            'expiry_date' => 'nullable|date',
            'batch_number' => 'nullable|string|max:255',
            'dosage_form' => 'nullable|string|max:255',
            'strength' => 'nullable|string|max:255',
            'requires_prescription' => 'boolean',
            'is_active' => 'boolean',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ], [
            'name.required' => 'O nome do medicamento é obrigatório.',
            'name.max' => 'O nome do medicamento deve ter no máximo 255 caracteres.',
            'generic_name.max' => 'O nome genérico deve ter no máximo 255 caracteres.',
            'brand.max' => 'A marca deve ter no máximo 255 caracteres.',
            'sku.required' => 'O SKU é obrigatório.',
            'sku.unique' => 'Este SKU já está em uso.',
            'price.required' => 'O preço é obrigatório.',
            'price.numeric' => 'O preço deve ser um número.',
            'price.min' => 'O preço não pode ser negativo.',
            'cost_price.required' => 'O preço de custo é obrigatório.',
            'cost_price.numeric' => 'O preço de custo deve ser um número.',
            'cost_price.min' => 'O preço de custo não pode ser negativo.',
            'stock_quantity.required' => 'A quantidade em stock é obrigatória.',
            'stock_quantity.integer' => 'A quantidade em stock deve ser um número inteiro.',
            'stock_quantity.min' => 'A quantidade em stock não pode ser negativa.',
            'min_stock.integer' => 'O stock mínimo deve ser um número inteiro.',
            'min_stock.min' => 'O stock mínimo não pode ser negativo.',
            'max_stock.integer' => 'O stock máximo deve ser um número inteiro.',
            'max_stock.min' => 'O stock máximo não pode ser negativo.',
            'expiry_date.date' => 'A data de validade deve ser uma data válida.',
            'batch_number.max' => 'O número do lote deve ter no máximo 255 caracteres.',
            'dosage_form.max' => 'A forma farmacêutica deve ter no máximo 255 caracteres.',
            'strength.max' => 'A concentração deve ter no máximo 255 caracteres.',
            'requires_prescription.boolean' => 'O campo "Requer prescrição" deve ser verdadeiro ou falso.',
            'is_active.boolean' => 'O campo "Ativo" deve ser verdadeiro ou falso.',
            'category_id.required' => 'A categoria é obrigatória.',
            'category_id.exists' => 'A categoria selecionada não existe.',
            'supplier_id.exists' => 'O fornecedor selecionado não existe.',
        ]);


        Medicine::create($validated);

        return redirect()->route('medicines.index')->with('success', 'Medicamento criado com sucesso.');
    }

    /**
     * Atualizar medicamento
     */
    public function update(Request $request, $uuid)
    {
        $medicine = Medicine::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|max:255|unique:medicines,sku,' . $medicine->id,
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
            'expiry_date' => 'nullable|date',
            'batch_number' => 'nullable|string|max:255',
            'dosage_form' => 'nullable|string|max:255',
            'strength' => 'nullable|string|max:255',
            'requires_prescription' => 'boolean',
            'is_active' => 'boolean',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        // Processar novas imagens
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('medicines/images', 'public');
                $imagePaths[] = $path;
            }
            // Manter imagens existentes e adicionar novas
            $existingImages = $medicine->images ?? [];
            $validated['images'] = array_merge($existingImages, $imagePaths);
        }

        // Processar novos anexos
        if ($request->has('attachments')) {
            foreach ($request->attachments as $attachment) {
                if (isset($attachment['file']) && $attachment['file']) {
                    $path = $attachment['file']->store('medicines/attachments', 'public');
                    $medicine->attachments()->create([
                        'name' => $attachment['name'],
                        'file_path' => $path,
                        'file_name' => $attachment['file']->getClientOriginalName(),
                    ]);
                }
            }
        }

        $medicine->update($validated);

        return redirect()->route('medicines.index')->with('success', 'Medicamento actualizado com sucesso.');
    }

    /**
     * Remover anexo específico
     */
    public function removeAttachment($uuid, $attachmentId)
    {
        $medicine = Medicine::where('uuid', $uuid)->firstOrFail();
        $attachment = $medicine->attachments()->findOrFail($attachmentId);

        // Deletar arquivo físico
        Storage::disk('public')->delete($attachment->file_path);

        $attachment->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Remover imagem específica
     */
    public function removeImage($uuid, $imageIndex)
    {
        $medicine = Medicine::where('uuid', $uuid)->firstOrFail();
        $images = $medicine->images ?? [];

        if (isset($images[$imageIndex])) {
            // Deletar arquivo físico
            Storage::disk('public')->delete($images[$imageIndex]);
            array_splice($images, $imageIndex, 1);
            $medicine->update(['images' => $images]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Soft delete
     */
    public function destroy($uuid)
    {
        $medicine = Medicine::where('uuid', $uuid)->firstOrFail();
        $medicine->delete();

        return redirect()->route('medicines.index')->with('success', 'Medicamento eliminado com sucesso.');
    }

    public function addImage($uuid)
    {
        $medicine = Medicine::with('attachments')->where('uuid', $uuid)->firstOrFail();

        return Inertia::render('Dashboard/Medicine/AddImage', [
            'medicine' => $medicine,
        ]);
    }

    /**
     * Restaurar medicamento deletado
     */
    public function restore($uuid)
    {
        $medicine = Medicine::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $medicine->restore();

        return redirect()->route('medicines.index')->with('success', 'Medicamento restaurado com sucesso.');
    }

    /**
     * Alternar estado ativo/inativo
     */
    public function toggle($uuid)
    {
        $medicine = Medicine::where('uuid', $uuid)->firstOrFail();
        $medicine->is_active = ! $medicine->is_active;
        $medicine->save();

        return redirect()->route('medicines.index')->with('success', 'Estado do medicamento atualizado com sucesso.');
    }

    /**
     * Bulk delete via UUIDs
     */
    public function bulkDelete(Request $request)
    {
        $uuids = $request->input('uuids', []);
        Medicine::whereIn('uuid', $uuids)->delete();

        return redirect()->route('medicines.index')->with('success', 'Medicamentos eliminados com sucesso.');
    }

    /**
     * Importar Excel
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:10240',
        ]);

        Log::info('Iniciando importação de medicamentos via Excel.');

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $inserted = 0;
        $failed = 0;
        $errors = [];

        foreach (array_slice($rows, 1) as $index => $row) {
            try {
                // Validar dados básicos
                if (empty($row['A']) || empty($row['D'])) {
                    $errors[] = 'Linha ' . ($index + 2) . ': Nome e SKU são obrigatórios';
                    $failed++;

                    continue;
                }

                Medicine::create([
                    'name' => $row['A'] ?? null,
                    'generic_name' => $row['B'] ?? null,
                    'brand' => $row['C'] ?? null,
                    'sku' => $row['D'] ?? null,
                    'price' => floatval($row['E'] ?? 0),
                    'cost_price' => floatval($row['F'] ?? 0),
                    'stock_quantity' => intval($row['G'] ?? 0),
                    'category_id' => intval($row['H'] ?? 1),
                    'is_active' => isset($row['I']) ? (bool) $row['I'] : true,
                ]);
                $inserted++;
            } catch (\Throwable $e) {
                $errors[] = 'Linha ' . ($index + 2) . ': ' . $e->getMessage();
                $failed++;
            }
        }

        return response()->json([
            'success' => true,
            'inserted' => $inserted,
            'failed' => $failed,
            'errors' => $errors,
        ]);
    }

    /**
     * Gerar template Excel para importação
     */
    public function template()
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Cabeçalhos
        $sheet->setCellValue('A1', 'Nome*');
        $sheet->setCellValue('B1', 'Nome Genérico');
        $sheet->setCellValue('C1', 'Marca');
        $sheet->setCellValue('D1', 'SKU*');
        $sheet->setCellValue('E1', 'Preço*');
        $sheet->setCellValue('F1', 'Preço de Custo*');
        $sheet->setCellValue('G1', 'Quantidade em Stock*');
        $sheet->setCellValue('H1', 'Categoria ID');
        $sheet->setCellValue('I1', 'Ativo (1/0)');

        // Exemplo
        $sheet->setCellValue('A2', 'Paracetamol 500mg');
        $sheet->setCellValue('B2', 'Acetaminofeno');
        $sheet->setCellValue('C2', 'Genérico');
        $sheet->setCellValue('D2', 'PARA123');
        $sheet->setCellValue('E2', '5.50');
        $sheet->setCellValue('F2', '2.50');
        $sheet->setCellValue('G2', '100');
        $sheet->setCellValue('H2', '1');
        $sheet->setCellValue('I2', '1');

        // Lista de categorias disponíveis
        $categories = Category::where('is_active', true)->get();
        $sheet->setCellValue('J1', 'Categorias Disponíveis');
        foreach ($categories as $index => $category) {
            $sheet->setCellValue('J' . ($index + 2), $category->id . ' - ' . $category->name);
        }

        $sheet->getStyle('A1:I1')->getFont()->setBold(true);

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'template_medicamentos.xlsx';

        $writer = new Xlsx($spreadsheet);

        ob_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        $writer->save('php://output');
        exit;
    }
}
