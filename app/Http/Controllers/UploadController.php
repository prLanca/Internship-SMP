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
            ], [
                'files.*.mimes' => 'The uploaded file must be a PDF, Excel, Word, or PowerPoint file.', // Custom error message
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

                // Return a success response with toggled sections
                return redirect()->route('index', ['screen' => $screen])

                    ->with([

                        'montagem_toggled' => $montagemToggled,
                        'qualidade_toggled' => $qualidadeToggled

                    ]);


            } else {

                // Return an error message if no files are uploaded
                $errorMessage = 'No file uploaded';
                return redirect()->route('index', ['screen' => $screen])
                    ->with('errorMessage', $errorMessage)
                    ->with([
                        'montagem_toggled' => $montagemToggled,
                        'qualidade_toggled' => $qualidadeToggled
                    ]);

            }

        } catch (\Exception $e) {
            $errorMessage = 'File upload failed: ' . $e->getMessage();
            return view('index', compact('errorMessage', 'screen'))->with('errorMessage', $errorMessage);
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

