<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;

class ProductsController extends Controller
{

    public function __construct()
    {
        Stripe::setApiKey(apiKey: config('stripe.sk'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $products = Products::all();
        $products = DB::table(table: "products")->where('name', 'LIKE', "%$request->search%")->orderBy('name')->paginate($request->per_page);
        return response()->json($products, status: 200);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'category_id' => 'required|string',
            'name' => 'required|string',
            'description' => 'required|string',
            'quantity' => 'required|numeric',
            'price' => 'required|numeric|min:1',
        ]);
        if ($validate) {
            try {
                DB::beginTransaction();
                $product = new Products();
                $product->category_id = $request->category_id;
                $product->name = $request->name;
                $product->price = $request->price;
                $product->description = $request->description;
                $product->quantity = $request->quantity;
                $product->is_active = true;
                $product->save();
                DB::commit();
                return response()->json($product, 200);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'error' => $e->getMessage(),
                ], 400);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $products = DB::table(table: "products")->where('id', $id)->get();
        return response()->json($products, status: 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validate = $request->validate([
            'category_id' => 'required|string',
            'name' => 'required|string',
            'description' => 'required|string',
            'quantity' => 'required|numeric',
            'price' => 'required|numeric|min:1',
        ]);
        if ($validate) {
            try {
                DB::beginTransaction();
                $product = Products::find($id);
                $product->category_id = $request->category_id;
                $product->name = $request->name;
                $product->price = $request->price;
                $product->description = $request->description;
                $product->quantity = $request->quantity;
                $product->is_active = true;
                $product->update();
                DB::commit();
                return response()->json($product, 200);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'error' => $e->getMessage(),
                ], 400);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (DB::table("products")->where('id', $id)->delete()) {
            return response()->json("Deleted successfully.", 200);
        } else {
            return response()->json("Something went wrong. Unable to delete.", 500);
        }
    }
}
