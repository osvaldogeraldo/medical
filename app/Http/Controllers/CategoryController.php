<?php

namespace App\Http\Controllers;

use App\Imports\CategoryImport;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class CategoryController extends Controller
{
    /**
     * Listar categorias com paginação e contagem de medicamentos.
     */
    public function index(Request $request)
    {
        $query = Category::withCount('medicines')->orderBy('created_at', 'desc');

        // Filtro de pesquisa
        if ($request->has('search') && ! empty($request->search)) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $categories = $query->paginate(10)->withQueryString();

        return Inertia::render('Dashboard/Category/Index', [
            'categories' => $categories,
            'filters' => $request->all('search'),
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
            'name' => 'required|string|max:255|unique:categories,name,'.$category->id,
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

        Excel::import(new Category, $request->file('file'));

        return redirect()->route('categories.index')->with('success', 'Categorias importadas com sucesso.');
    }
}
