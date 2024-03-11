<?php

use App\Http\Controllers\auth\LoginController;
use App\Http\Controllers\auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        /*
         * Users
         */
        Route::get('/profile', [UserController::class, 'show'])->name('profile.show');

        Route::delete('/profile/{user}', [UserController::class, 'delete'])->name('profile.delete');


        Route::get('/email/verify', [VerificationController::class, 'verify'])
            ->middleware(['auth', 'signed', 'throttle:6,1'])
            ->name('verification.verify');

        Route::get('/email/verify/resend', [VerificationController::class, 'resend'])
            ->middleware(['auth', 'throttle:6,1'])
            ->name('verification.resend');



        Route::post('/change-password', [UserController::class, 'changePassword'])->name('change.password');
        Route::post('/change-name', [UserController::class, 'changeName'])->name('change.name');




    }
    );
});

/*
 * Login and register
 */
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
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


Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);



Auth::routes(['verify' => true]);

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

            Route::match(['post', 'delete'], '/delete/{userid}', [DashboardController::class, 'delete'])->name('users.delete');



            Route::post('/upload/Injecao', function (Request $request) {
                $storageLocation = 'Injecao';
                $uploadController = new UploadController();
                return $uploadController->upload($request, $storageLocation);
            })->name('upload.injecao');

            Route::post('/upload/Pintura', function (Request $request) {
                $storageLocation = 'Pintura';
                $uploadController = new UploadController();
                return $uploadController->upload($request, $storageLocation);
            })->name('upload.pintura');

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

            Route::post('/upload/manutencao', function (Request $request) {
                $storageLocation = 'Manutencao';
                $uploadController = new UploadController();
                return $uploadController->upload($request, $storageLocation);
            })->name('upload.manutencao');

            Route::post('/upload/engenharia', function (Request $request) {
                $storageLocation = 'Engenharia';
                $uploadController = new UploadController();
                return $uploadController->upload($request, $storageLocation);
            })->name('upload.engenharia');

            Route::post('/upload/higiene', function (Request $request) {
                $storageLocation = 'Higiene';
                $uploadController = new UploadController();
                return $uploadController->upload($request, $storageLocation);
            })->name('upload.higiene');

            Route::post('/upload/lean', function (Request $request) {
                $storageLocation = 'Lean';
                $uploadController = new UploadController();
                return $uploadController->upload($request, $storageLocation);
            })->name('upload.lean');

            Route::post('/upload/qcdd', function (Request $request) {
                $storageLocation = 'QCDD';
                $uploadController = new UploadController();
                return $uploadController->upload($request, $storageLocation);
            })->name('upload.qcdd');

            Route::post('/upload/rh', function (Request $request) {
                $storageLocation = 'RH';
                $uploadController = new UploadController();
                return $uploadController->upload($request, $storageLocation);
            })->name('upload.rh');

            Route::post('/upload/empty', function (Request $request) {
                $storageLocation = 'Empty';
                $uploadController = new UploadController();
                return $uploadController->upload($request, $storageLocation);
            })->name('upload.empty');

            Route::post('/upload/empty2', function (Request $request) {
                $storageLocation = 'Empty2';
                $uploadController = new UploadController();
                return $uploadController->upload($request, $storageLocation);
            })->name('upload.empty2');

        }

        );
    });
});









