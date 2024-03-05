<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Events\NewUserCreated;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    protected $rules = [
        'name' => 'required|min:3|max:150',
        'email' => 'required',
        'password' => 'required|min:8|max:150'
    ];
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('User.index', [
            'users' => User::all(),
            'title' => 'Utilizadores'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $dados = $request->validate($this->rules);
        $user = new User($dados);
        $user->save();
        return redirect()->route('admin.users', $user);
    }

    public function controller_create(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->assignRole('viewer');
        $user->save();
        event(new NewUserCreated($user));
        event(new Registered($user));
        return $user;
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $user = auth()->user();
        return view('user.index', compact('user'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('user.edit', [
            'user' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $userId)
    {
        // Validate request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'role' => 'required|string|in:admin,worker,viewer',
        ]);

        // Find the user by ID
        $user = User::findOrFail($userId);

        // Update user data
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        // Return JSON response
        return response()->json(['success' => true]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index');
    }

    public function destroy_admin(User $user)
    {
        if (Auth::user()->id == $user->id){
            return redirect()->route('admin.users')->with('error', trans('validation_user.cannot_delete_own_user'));
        }

        if ($user->hasRole('admin') && Auth::user()->hasRole('admin')){
            return redirect()->route('admin.users')->with('error', trans('validation_user.cannot_delete_admin'));
        }

        if($user->hasRole('superuser')){
            return redirect()->route('admin.users')->with('error', trans('validation_user.cannot_delete_superuser'));
        }

        $user->delete();
        return redirect()->route('admin.users');
    }




    public function changePassword(Request $request)
    {
        $request->validate([
            'current-password' => 'required',
            'new-password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->input('current-password'), $user->password)) {
            // Passwords do not match
            return redirect()->back()->withErrors(['current-password' => 'The current password is incorrect.']);
        }

        $user->password = bcrypt($request->input('new-password'));
        $user->save();

        return redirect()->back()->with('success', 'Password changed successfully.');
    }

    public function changeName(Request $request)
    {
        $request->validate([
            'new-name' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->name = $request->input('new-name');
        $user->save();

        return redirect()->back()->with('success', 'Name changed successfully.');
    }


}
