<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use function Laravel\Prompts\alert;

class UploadController extends Controller
{
    public function upload(Request $request, $storageLocation)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|file|mimes:pdf,xlsx,pptx,xls', // Example validation rules for PDF and Excel files with a maximum size of 2MB
        ]);

        // Get the current state of the toggled sections from the request
        $montagemToggled = $request->input('montagem_toggled', false);
        $qualidadeToggled = $request->input('qualidade_toggled', false);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName(); // Generate unique filename
            $path = $storageLocation . '/' . $fileName; // Construct the file path with storage location
            Storage::disk('public')->put($path, file_get_contents($file)); // Store the file using Laravel's storage system

            // Redirect back to the same page with the toggled states preserved
            return redirect()->route('index')->with(['montagem_toggled' => $montagemToggled, 'qualidade_toggled' => $qualidadeToggled]);
        }

        return view('index')->with('error', 'File upload failed');
    }

    public function deleteFile(Request $request) {
        $filePath = $request->input('filePath');

        // Verificar se o arquivo existe
        if (Storage::disk('public')->exists($filePath)) {
            // Excluir o arquivo
            Storage::disk('public')->delete($filePath);
            return response()->json(['message' => 'File deleted successfully']);
        } else {
            return response()->json(['error' => 'File not found'], 404);
        }
    }

}

