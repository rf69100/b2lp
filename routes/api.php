<?php

use App\Http\Controllers\BilletController;
use App\Http\Controllers\CommentaireController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'show']);
    Route::post('/user/logout', function (Request $request) {
        $user = auth()->user();
        if ($user) {
            $user->tokens()->delete();
        }
    });
    Route::get('/billets/{id}', [BilletController::class, 'show'])->whereNumber('id');
    Route::post('/commentaires', [CommentaireController::class, 'store']);

    // CRUD des billets — réservé à l'administrateur (contrôle d'accès dans BilletPolicy).
    Route::post('/billets', [BilletController::class, 'store']);
    Route::put('/billets/{billet}', [BilletController::class, 'update'])->whereNumber('billet');
    Route::delete('/billets/{billet}', [BilletController::class, 'destroy'])->whereNumber('billet');
});

/*
|--------------------------------------------------------------------------
|Create an account.
|--------------------------------------------------------------------------
|
*/
Route::post('/register', [UserController::class, 'store']);

/*
|--------------------------------------------------------------------------
|Log in.
|--------------------------------------------------------------------------
|
*/

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email|max:50',
        'password' => 'required|string|min:8',
    ]);
    try {
        $user = User::where('email', $request->email)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'The provided credentials are erroneous.',
            ]);
        }

        return $user->createToken('auth_token')->plainTextToken;
    } catch (\Illuminate\Database\QueryException $e) {
        Log::error('Erreur accès base de données');

        return response()->json([
            'message' => 'Ressource indisponible.'], 500);
    }
});

/*
|--------------------------------------------------------------------------
|api/billets
|list blog posts.
|--------------------------------------------------------------------------
|
*/
Route::get('/billets', [BilletController::class, 'index']);
