Adding token to api.
Based on:
https://www.youtube.com/watch?v=MT-GJQIY3EU&t=101s
https://laravel.com/docs/9.x/sanctum

In laravel 9 Sanctum is allready enable.
Step 1, 2, 3, 5 and 6 not necessary.
Step 4 when using an spa. The line only needs to be uncommented.

1. install sanctum:
composer require laravel/sanctum

2. install config and migration file:
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

3. migrate
php artisan migrate

4. add to app/http/kernel.php:
'api' => [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    'throttle:api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],

5. add to user model:
use Laravel\Sanctum\HasApiTokens;
(and in class:)
use HasApiTokens,...

6. add an example route with sanctum middleware to api.php:
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

7. edit example:
Route::middleware('auth:sanctum')->get('/products', [ProductController::class, 'index']);
//or
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/products', [ProductController::class, 'index']);
});
//or
Route::group(['middleware' => 'auth:sanctum'], function () {
    // Route::get('/products', [ProductController::class, 'index']);
    Route::apiResource('products', ProductController::class);
});
// test in postman
// don't forget the accept header with application/json value

8. create public and private routes
// public routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
// Route::get('products/search/{name}', [ProductController::class, 'search']);


// protected routes
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('products', [ProductController::class, 'create'])->name('products.store');
    Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.delete');
});

9. create a new controller for authentication (register, logout, login) with token
php artisan make:controller AuthController

10. register function:
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed', // password_confirmation with password_confirmed
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            // 'password' => Hash::make($fields['password']),
            'password' => bcrypt($fields['password']),
        ]);

        $token = $user->createToken('maakt_niets_uit')->plainTextToken;
        Log::info('User created: ' . $user->name);
        Log::info('Token created: ' . $token);

        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response()->json($response, Response::HTTP_CREATED);
    }

}


11. logout function:
    public function logout(Request $request)
    {
        // Log::info('User logged out: ' . $request->user()->name);
        // Log::info('Token: ' . $request->user()->currentAccessToken());
        // $request->user()->currentAccessToken()->delete();
        // or all tokens of user
        auth()->user()->tokens()->delete();
        return response()->json(['message' => 'Successfully logged out'], Response::HTTP_OK);
    }


12. login function:
