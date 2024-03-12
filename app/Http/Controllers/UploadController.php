<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use function Laravel\Prompts\alert;

class UploadController extends Controller
{
    public function upload(Request $request, $screen)
    {
        try {

            // Validate the uploaded files
            $request->validate([
                'files.*' => 'required|file|mimes:pdf,xlsx,xlsm,ppt,pptx,xls,docx|max:102400' // 100MB file size limit
            ], [
                'files.*.mimes' => 'The uploaded file must be a PDF, Excel, Word, or PowerPoint.', // Custom error message
            ]);

            // Get the current state of the toggled sections from the request
            $injecaoToggled = $request->input('injecao_toggled', false);
            $pinturaToggled = $request->input('pintura_toggled', false);
            $montagemToggled = $request->input('montagem_toggled', false);
            $qualidadeToggled = $request->input('qualidade_toggled', false);
            $manutencaoToggled = $request->input('manutencao_toggled', false);
            $engenhariaToggled = $request->input('engenharia_toggled', false);
            $higieneToggled = $request->input('higiene_toggled', false);
            $leanToggled = $request->input('lean_toggled', false);
            $rhToggled = $request->input('rh_toggled', false);
            $emptyToggled = $request->input('empty_toggled', false);
            $empty2Toggled = $request->input('empty2_toggled', false);


            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    // Define the destination directory based on the screen
                    $destinationDirectory = public_path('storage/' . $screen);

                    // Check if the user is authenticated
                    if (Auth::check()) {
                        // Get the authenticated user's name
                        $userId = Auth::user()->id;
                        $uploaderName = Auth::user()->name;
                    } else {
                        // If the user is not authenticated, use a default name or handle the situation accordingly
                        $uploaderName = 'Anonymous';
                    }

                    // Generate a unique filename with the uploader's name
                    $fileName = $userId . '_' . $uploaderName . '_' . $file->getClientOriginalName();

                    $file->move($destinationDirectory, $fileName);


                }

                // Return a success response with toggled sections
                return redirect()->route('index', ['screen' => $screen])

                    ->with([

                        'injecao_toggled' => $injecaoToggled,
                        'pintura_toggled' => $pinturaToggled,
                        'montagem_toggled' => $montagemToggled,
                        'qualidade_toggled' => $qualidadeToggled,
                        'manutencao_toggled' => $manutencaoToggled,
                        'engenharia_toggled' => $engenhariaToggled,
                        'higiene_toggled' => $higieneToggled,
                        'lean_toggled' => $leanToggled,
                        'rh_toggled' => $rhToggled,
                        'empty_toggled' => $emptyToggled,
                        'empty2_toggled' => $empty2Toggled,

                    ]);


            } else {

                // Return an error message if no files are uploaded
                $errorMessage = 'No file uploaded';
                return redirect()->route('index', ['screen' => $screen])

                    ->with('errorMessage', $errorMessage)

                    ->with([

                        'injecao_toggled' => $injecaoToggled,
                        'pintura_toggled' => $pinturaToggled,
                        'montagem_toggled' => $montagemToggled,
                        'qualidade_toggled' => $qualidadeToggled,
                        'manutencao_toggled' => $manutencaoToggled,
                        'engenharia_toggled' => $engenhariaToggled,
                        'higiene_toggled' => $higieneToggled,
                        'lean_toggled' => $leanToggled,
                        'rh_toggled' => $rhToggled,
                        'empty_toggled' => $emptyToggled,
                        'empty2_toggled' => $empty2Toggled,

                    ]);

            }

        } catch (\Exception $e) {
            $errorMessage = 'File upload failed: ' . $e->getMessage();
            return view('index', compact('errorMessage', 'screen'))->with('errorMessage', $errorMessage);
        }
    }


    public function deleteFile(Request $request)
    {
        $filePath = $request->input('filePath');

        // Check if the file path is valid
        if ($filePath) {
            // Delete the file from storage
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);

                // Redirect to the index page with a custom success message
                return redirect()->route('index')->with('success', 'File has been successfully deleted.');
            } else {
                // If file not found, redirect with an error message
                return redirect()->route('index')->with('error', 'File not found.');
            }
        } else {
            // If invalid file path, redirect with an error message
            return redirect()->route('index')->with('error', 'Invalid file path.');
        }
    }

}

