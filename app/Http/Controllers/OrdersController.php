<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Orders;
use Stripe\Stripe;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orders = DB::table('orders')->paginate($request->per_page);
        return response()->json($orders, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'sub_total' => 'required|max:255',
            'discount' => 'required|max:255',
            'shipping_fee' => 'required|max:255',
            'tax' => 'required|max:255',
            'total_amount' => 'required|max:255',
            'order_status' => 'in:paid,canceled,failed,expired',
            'billing_address' => 'required|string|max:255',
            'shipping_address' => 'required|string|max:255',
            'payment_type' => 'in:card,cash',
        ]);

        if ($validate) {
            try {
                DB::beginTransaction();
                $order = new Orders();
                $order->user_id = Auth::user()->id;
                $order->sub_total = $request['sub_total'];
                $order->discount = $request['discount'];
                $order->tax = $request['tax'];
                $order->total_amount = $request['total_amount'];
                $order->order_status = $request['order_status'];
                $order->billing_address = $request['billing_address'];
                $order->shipping_address = $request['shipping_address'];
                $order->payment_type = $request['payment_type'];
                $order->save();
                DB::commit();
                Stripe::setApiKey(config('stripe.sk'));

                $paymentIntent = \Stripe\PaymentIntent::create([
                    'amount' => $request['amount'],
                    'currency' => 'usd',
                    'automatic_payment_methods' => ['enabled' => true],
                ]);

                // Return a payment intent for stripe UI
                return response()->json(['payment_intent' => $paymentIntent], 200);
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
        $order = DB::table(table: "orders")->where('id', $id)->get();
        return response()->json($order, status: 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validate = $request->validate([
            'sub_total' => 'required|max:255',
            'discount' => 'required|max:255',
            'shipping_fee' => 'required|max:255',
            'tax' => 'required|max:255',
            'total_amount' => 'required|max:255',
            'order_status' => 'in:paid,canceled,failed,expired',
            'billing_address' => 'required|string|max:255',
            'shipping_address' => 'required|string|max:255',
            'payment_type' => 'in:card,cash',
        ]);

        if ($validate) {
            try {
                DB::beginTransaction();
                $order = Orders::find($id);
                $order->order_status = $request['order_status'];
                $order->update();
                DB::commit();
                return response()->json("Deleted successfully.", 200);
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
        //
    }
}
