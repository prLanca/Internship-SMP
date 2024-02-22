<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use function Laravel\Prompts\alert;

class UploadController extends Controller
{
    public function upload(Request $request, $screen)
    {
        try {
            // Validate the uploaded files
            $request->validate([
                'files.*' => 'required|file|mimes:pdf,xlsx,xlsm,ppt,pptx,xls,docx'
            ]);

            // Get the current state of the toggled sections from the request
            $montagemToggled = $request->input('montagem_toggled', false);
            $qualidadeToggled = $request->input('qualidade_toggled', false);

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    // Define the destination directory based on the screen
                    $destinationDirectory = public_path('storage/' . $screen);

                    // Move the uploaded file to the destination directory
                    $fileName = $file->getClientOriginalName(); // Generate a unique filename
                    $file->move($destinationDirectory, $fileName);
                }

                // Return a success response
                return redirect()->route('index', ['screen' => $screen])->with(['montagem_toggled' => $montagemToggled, 'qualidade_toggled' => $qualidadeToggled]);
            }

            $errorMessage = 'No file uploaded';
            return view('index', compact('errorMessage', 'screen'));

        } catch (\Exception $e) {
            $errorMessage = 'File upload failed: ' . $e->getMessage();
            return view('index', compact('errorMessage', 'screen'));
        }
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

