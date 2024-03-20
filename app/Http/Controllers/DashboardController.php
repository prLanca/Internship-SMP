<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\Paginator;

class DashboardController extends Controller
{


    public function admin(){

        // Fetch users data
        $users = User::all();

        // Define the directories you want to count files from
        $directories = ['Injecao', 'Pintura', 'Montagem', 'Qualidade', 'Manutencao', 'Engenharia', 'Higiene', 'Lean', 'QCDD', 'RH', 'Empty', 'Empty2'];

        // Define an array to store file counts for each directory
        $fileInfo = [];

        // Define an array to store the count of files uploaded on each day
        $filesUploadedPerDay = [];

        // Loop through each directory
        foreach ($directories as $directory) {
            // Get the files in the current directory
            $files = Storage::disk('public')->files($directory);

            // Get the count of files in the current directory
            $fileCount = count($files);

            // Initialize an empty array to store last modified timestamps
            $lastModifiedTimestamps = [];

            // Get the last modified timestamp for each file
            foreach ($files as $file) {
                $lastModifiedTimestamp = Storage::disk('public')->lastModified($file);

                // Extract the date from the last modified timestamp
                $date = date('Y-m-d', $lastModifiedTimestamp);

                // Increment the count of files uploaded on this day
                if (!isset($filesUploadedPerDay[$date])) {
                    $filesUploadedPerDay[$date] = 1;
                } else {
                    $filesUploadedPerDay[$date]++;
                }

                // Store the last modified timestamp
                $lastModifiedTimestamps[] = $lastModifiedTimestamp;
            }

            // Store the file count and last modified timestamps for the current directory
            $fileInfo[$directory] = [
                'file_count' => $fileCount,
                'last_modified_timestamps' => $lastModifiedTimestamps,
            ];
        }

        return view('admin.index', compact('users', 'fileInfo', 'filesUploadedPerDay'));
    }


    public function update(Request $request)
    {
        // Validate request data
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'role' => 'required|string|in:admin,worker,viewer',
        ]);

        // Find the user by ID
        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($user->id === 1) {
            return redirect()->route('admin.dashboard')->withErrors(['error' => 'Cannot update the default admin user']);
        }

        // Update user data
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Get the role ID based on the selected role
        $roleId = DB::table('roles')->where('name', $request->role)->value('id');

        // Update the role_id in the model_has_roles table
        DB::table('model_has_roles')
            ->where('model_id', $request->user_id)
            ->where('model_type', 'App\Models\User')
            ->update(['role_id' => $roleId]);

        // Check if the request contains a 'referer' header
        $previousUrl = $request->header('referer');

        // Redirect the user back to the previous page
        return redirect($previousUrl)->with('success', 'User updated successfully');

    }

    public function delete(Request $request, $userId)
    {
        // Find the user by ID
        $user = User::find($userId);

        // Check if the user exists
        if (!$user) {
            // Handle case where user is not found
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($user->id === 1) {

            // Handle case where user is not found
            return redirect()->route('admin.dashboard')->with('error', 'Cannot delete the default admin user');

        }

        // Perform deletion logic here
        $user->delete();

        // Check if the request contains a 'referer' header
        $previousUrl = $request->header('referer');

        // Optionally, return a success response
        return redirect($previousUrl)->with('success', 'User deleted successfully');
    }

}
