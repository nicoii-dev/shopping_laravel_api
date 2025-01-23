<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use App\Models\Plans;
class PlansController extends Controller
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
        $plans = DB::table("plans")->paginate($request->per_page);
        return response()->json($plans, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //store new plan
        $validate = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric|min:1',
            'post_per_day' => 'required|numeric',
        ]);
        if ($validate) {
            try {
                DB::beginTransaction();
                $plan = new Plans();
                // creating new product in stripe
                $stripe_product = \Stripe\Product::create([
                    'name' => $request->name,
                    'description' => $request->description,
                    "metadata" => [
                        "post" => $request->post_per_day,
                    ],
                ]);
                // creating new price for the product in stripe
                $stripe_price = \Stripe\Price::create([
                    'unit_amount' => $request->price * 100,
                    'currency' => 'usd',
                    'product' => $stripe_product->id,
                    'recurring' => [
                        'interval' => 'month'
                    ],
                ]);
                $plan->stripe_product_id = $stripe_product["id"];
                $plan->stripe_price_id = $stripe_price["id"];
                $plan->name = $request->name;
                $plan->price = $request->price;
                $plan->post_per_day = $request->post_per_day;
                $plan->description = $request->description;
                $plan->is_active = true;
                $plan->save();
                DB::commit();
                return response()->json($plan, 200);
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
    public function show(string $id)
    {
        // $plan = Plan::where('id','=', $id)->get();
        // $plan = Plan::find($id);
        $plan = DB::table(table: "plans")->where('id', $id)->get();
        return response()->json($plan, status: 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //store new plan
        $validate = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'numeric|min:1',
            'post_per_day' => 'required|numeric',
            'is_active' => 'required|numeric',
        ]);
        if ($validate) {
            if ($request->is_active == 1) {
                $active = true;
            } else {
                $active = false;
            }
            try {
                DB::beginTransaction();
                $plan = Plans::find($id);
                // updating product in stripe
                \Stripe\Product::update(
                    $plan->stripe_product_id,
                    [
                        'name' => $request->name,
                        'description' => $request->description,
                        "metadata" => [
                            "post" => $request->post_per_day,
                        ],
                        'active' => $active
                    ]
                );
                // updating price for the product in stripe
                // prices in stripe is immutable
                // need to archive previous price
                // and create a new one
                if ($request->exists('price')) {
                    \Stripe\Price::update(
                        $plan->stripe_price_id,
                        [
                            'active' => false
                        ]
                    );
                    \Stripe\Price::create(
                        [
                            'unit_amount' => $request->price * 100,
                            'currency' => 'usd',
                            'product' => $plan->stripe_product_id,
                            'recurring' => [
                                'interval' => 'month'
                            ],
                        ]
                    );
                    $plan->price = $request->price;
                }

                $plan->name = $request->name;
                $plan->post_per_day = $request->post_per_day;
                $plan->description = $request->description;
                $plan->is_active = $request->is_active;
                $plan->update();
                DB::commit();
                return response()->json($plan, 200);
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
        $plan = Plans::find($id);
        if (DB::table("plans")->where('id', $id)->delete()) {
            // updating product in stripe
            \Stripe\Product::update(
                $plan->stripe_product_id,
                [
                    'active' => false
                ]
            );
            return response()->json("Deleted successfully.", 200);
        } else {
            return response()->json("Something went wrong. Unable to delete.", 500);
        }
    }
}
