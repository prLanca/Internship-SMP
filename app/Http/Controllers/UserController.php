<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Notifications\EmailChangeNotification;
use Illuminate\Http\Request;
use App\Events\NewUserCreated;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;


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
     * Remove user from database.
     */
    public function delete(User $user)
    {
        // Ensure the user is authorized to delete their own account
        if ($user->id !== auth()->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        if (Auth::user()->id === 1) {
            return redirect()->back()->with('error', 'Can not delete the Administrator');
        }

        $user->delete();

        // Redirect to a suitable route after deletion
        return redirect()->route('profile.show');
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

    public function changeName(Request $request)
    {
        $request->validate([
            'new-name' => 'required|string|max:255',
        ]);

        if (Auth::user()->id === 1) {
            return redirect()->back()->with('error', 'Can not change the username of Administrator');
        }

        $user = Auth::user();
        $user->name = $request->input('new-name');

        if (stripos($user->name, 'administrator') !== false || stripos($user->name, 'admin') !== false) {
            return redirect()->back()->with('error', 'Can not change the username containing Administrator or admin.');
        }

        $user->save();

        return redirect()->back()->with('success-name', 'Name changed successfully.');
    }

    public function changePassword(Request $request)
    {

        $request->validate([
            'current-password' => 'required',
            'new-password' => 'required|min:8',
        ]);

        $user = Auth::user();

        if ($user->id === 1) {
            return redirect()->back()->with('error', 'Can not change the password of Administrator');
        }

        // Verifica se a senha atual fornecida pelo usuário é correta
        if (Hash::check($request->input('current-password'), $user->password)) {
            // Senha atual correta
            $request->validate([
                'new-password' => 'required',
                'confirm-password' => 'required|same:new-password',
            ]);

            $user->password = bcrypt($request->input('new-password'));
            $user->save();

            return redirect()->back()->with('success', 'Password changed successfully.');
        } else {
            // Senha atual incorreta
            return redirect()->back()->withErrors(['current-password' => 'The current password is incorrect.']);
        }
    }

}
