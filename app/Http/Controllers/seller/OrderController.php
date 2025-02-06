<?php

namespace App\Http\Controllers\seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $seller = $request->user()->seller()->first();

        $orders = Order::where("seller_id", $seller->id)->with("gig")->get();

        return $orders;
    }


    public function show(string $id)
    {

        try {

            $order = Order::with("gig")->find($id);

            if (!$order) {
                return response()->json([
                    "message" => "No order was found with Id " . $id,
                ], 404);
            }

            return $order;
        } catch (\Exception $e) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }


    public function update(Request $request, string $id)
    {

        $validated = $request->validate([
            "status" => "sometimes|required|in:pending,processing,completed",
            "modification_request" => "sometimes|required",
            "delivery_date" => "sometimes|required",
        ]);

        if (empty(array_filter($validated))) {
            return response()->json([
                "message" => "No data provided for update.",
            ], 400);
        }

        try {
            $order = Order::find($id);

            if (!$order) {
                return response()->json([
                    "message" => "No order was found with ID " . $id,
                ], 404);
            }

            $seller = $request->user()->seller()->first();

            if (!$seller || !$order->seller()->is($seller)) {
                return response()->json([
                    "message" => "Unauthorized access to this order.",
                ], 403);
            }


            $updated = $order->update($validated);

            if ($updated) {
                return response()->json([
                    "message" => "Order has been successfully updated",
                ], 200);
            } else {
                return response()->json([
                    "message" => "No changes were made to the order.",
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }
}
