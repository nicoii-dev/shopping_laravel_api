<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = DB::table('categories')
            ->select('id', 'name')
            ->get();

        $categoriesWithProducts = [];

        foreach ($categories as $category) {
            $products = DB::table('products')
                ->where('category_id', $category->id)
                ->select('id', 'name', 'price' , )
                ->get();

            $categoriesWithProducts[] = [
                'id' => $category->id,
                'name' => $category->name,
                'products' => $products->toArray(), // Convert products to an array
            ];
        }
        return response()->json($categoriesWithProducts, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
