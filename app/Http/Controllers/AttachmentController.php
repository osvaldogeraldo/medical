<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Medicine;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    /**
     * Tela principal de anexos
     * Recebe UUID do medicamento
     */
    public function index($uuid)
    {
        $medicine = Medicine::with('attachments')
            ->where('uuid', $uuid)->firstOrFail();

        return Inertia::render('Dashboard/Medicine/AddImage', [
            'medicine' => $medicine
        ]);
    }

    /**
     * Adicionar novo anexo
     */
    public function store(Request $request, $uuid)
    {
        $medicine = Medicine::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'file' => 'required|file|max:10240', // até 10MB
            'name' => 'nullable|string|max:255'
        ]);

        $file = $request->file('file');
        $path = $file->store('attachments', 'public');

        $attachment = $medicine->attachments()->create([
            'name' => $request->name ?? $file->getClientOriginalName(),
            'path' => $path
        ]);

        return redirect()->back()->with('success', 'Anexo adicionado com sucesso.');
    }

    /**
     * Atualizar nome do anexo
     */

    public function update(Request $request, $medicine_uuid, $attachment_uuid)
    {
        $medicine = Medicine::where('uuid', $medicine_uuid)->firstOrFail();

        $request->validate([
            'name' => 'nullable|string|max:255',
            'file' => 'nullable|file|max:10240'
        ]);

        $attachment = $medicine->attachments()->where('uuid', $attachment_uuid)->firstOrFail();

        DB::transaction(function () use ($request, $attachment) {
            $updateData = [];

            if ($request->filled('name')) $updateData['name'] = $request->name;

            if ($request->hasFile('file')) {
                if (Storage::disk('public')->exists($attachment->path)) {
                    Storage::disk('public')->delete($attachment->path);
                }
                $file = $request->file('file');
                $updateData['path'] = $file->store('attachments', 'public');
                if (!$request->filled('name')) $updateData['name'] = $file->getClientOriginalName();
            }

            if (!empty($updateData)) $attachment->update($updateData);
        });

        return response()->json($attachment);
    }


    /**
     * Remover um anexo
     */
public function destroy($medicine, $attachment)
{
    $medicine = Medicine::where('uuid', $medicine)->firstOrFail();
    $attachment = $medicine->attachments()->where('uuid', $attachment)->firstOrFail();

    if (Storage::disk('public')->exists($attachment->path)) {
        Storage::disk('public')->delete($attachment->path);
    }

    $attachment->delete();

    return redirect()->back()->with('success', 'Anexo removido com sucesso.');
}


    /**
     * Baixar um anexo
     */
    public function download($uuid, $attachmentUuid)
    {
        $medicine = Medicine::where('uuid', $uuid)->firstOrFail();
        $attachment = $medicine->attachments()->where('uuid', $attachmentUuid)->firstOrFail();

        return Storage::disk('public')->download($attachment->path, $attachment->name);
    }
}
