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



