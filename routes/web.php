<?php

use App\Http\Controllers\auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*
 * Index
 */
Route::middleware('auth')->group(function (){
    Route::name('')->group(function (){

        Route::get('/index', function () {
            return view('index');
        })->name('index');

    }
    );
});

/*
 * Login and register
 */
Route::get('/',[LoginController::class, 'showLogin'])->name('login');
Route::post('/',[LoginController::class, 'login'])->name('login');

/*
 * Password Reset Routes
 */
Route::get('/forgot-password', function () {
    return view('auth.fgpass');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
        ? back()->with(['status' => __($status)])
        : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', __($status))
        : back()->withErrors(['email' => [__($status)]]);
})->middleware('guest')->name('password.update');



Route::post('/logout',[LoginController::class, 'logout'])->name('logout')
    ->middleware('auth');


Route::get('/register',[LoginController::class, 'showRegister'])->name('register');
Route::post('/register',[LoginController::class, 'register'])->name('register');

/*
|--------------------------------------------------------------------------
| Administrator
|--------------------------------------------------------------------------
 */
Route::middleware('role:admin')->group(function (){
    Route::prefix('/admin')->group(function (){
        Route::name('admin.')->group(function (){

            /* Dashboard */
            Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard')->middleware('auth');
            Route::put('/dashboard/update', [DashboardController::class, 'update'])->name('users.update')->middleware('auth');

            Route::post('/fetch-powerpoint-previews', 'PresentationController@fetchPowerPointPreviews')->name('fetch-powerpoint-previews');
            Route::post('fetch-excel-preview', [UploadController::class, 'fetchExcelPreview'])->name('admin.fetch-excel-preview');

            Route::post('/delete/file', [UploadController::class, 'deleteFile'])->name('delete.file');



            Route::post('/upload/Montagem', function (Request $request) {
                $storageLocation = 'Montagem';
                $uploadController = new UploadController();
                return $uploadController->Upload($request, $storageLocation);
            })->name('upload.montagem');

            Route::post('/upload/qualidade', function (Request $request) {
                $storageLocation = 'Qualidade';
                $uploadController = new UploadController();
                return $uploadController->upload($request, $storageLocation);
            })->name('upload.qualidade');


            Route::post('/upload/Example3', function (Request $request) {
                $storageLocation = 'Example3';
                $uploadController = new UploadController();
                return $uploadController->upload($request, $storageLocation);
            });

            Route::post('/upload/Example4', function (Request $request) {
                $storageLocation = 'Example4';
                $uploadController = new UploadController();
                return $uploadController->upload($request, $storageLocation);
            });

        }
        );
    });
});


/*
 * Users
 */
Route::get('/profile', [UserController::class, 'show'])->name('profile.show')->middleware('auth');







