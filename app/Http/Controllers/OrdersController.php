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
    public function index()
    {
        $orders = DB::table('orders')
            ->get();

        return response()->json($orders, 200);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function stripe(Request $request)
    {
        $request->validate([
            'amount' => 'required|max:255',
        ]);

        Stripe::setApiKey(config('stripe.sk'));

        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $request['amount'],
            'currency' => 'usd',
            'automatic_payment_methods' => ['enabled' => true],
        ]);

        // Return a client secret
        return response()->json(['client_secret' => $paymentIntent->client_secret]);

    }
    public function store(Request $request)
    {

        $request->validate([
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

        Stripe::setApiKey(config('stripe.sk'));

        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $request['amount'],
            'currency' => 'usd',
            'automatic_payment_methods' => ['enabled' => true],
        ]);

        // Return a payment intent for stripe UI
        return response()->json(['payment_intent' => $paymentIntent]);

        $order = Orders::create([
            'user_id' => Auth::user()->id,
            'sub_total' => $request['sub_total'],
            'discount' => $request['discount'],
            'tax' => $request['tax'],
            'total_amount' => $request['total_amount'],
            'order_status' => $request['order_status'],
            'billing_address' => $request['billing_address'],
            'shipping_address' => $request['shipping_address'],
            'payment_type' => $request['payment_type'],
        ]);

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
