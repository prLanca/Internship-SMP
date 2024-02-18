<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{


    public function admin(){

        // Fetch users data
        $users = User::all();

        return view('admin.index', compact('users'));
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



}
