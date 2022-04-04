<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // todo: use tenary operator for checks

        $products = Product::query();

        // name parameter for search
        if ($request->has('name')) {
            $products->where('name', 'like', '%' . $request->name . '%');
        }

        // perPage parameter for pagination
        if ($request->has('perPage')) {
            $limit = $request->perPage;
        }else{
            // don't go to pagination if perPage is not set
            return $products->get();
        }

        // page parameter is added by the pagination library
        return $products->paginate($limit);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Test if the request is valid
        // in postman uncheck form data
        // but add in header:
        // key: Accept
        // value: application/json
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $product = Product::create($request->all());
        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return $product;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    // public function edit(Product $product)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $product->update($request->all());
        return response()->json($product, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(null, 204);
    }

    // other methods for using api
    // must have a route in routes.php
    // Route::get('/products/search/{name}', [ProductController::class, 'index']);
    // or use the option within index method
    public function search(string $name)
    {
        if(Str::length($name) > 0){
            $products = Product::where('name', 'like', '%' . $name . '%')->get();
        }else{
            $products = Product::all();
        }
        return $products;
    }

    public function admin(){
        if(auth()->user()->tokenCan('admin')){
            return 'you are admin';
        }
    }

}
