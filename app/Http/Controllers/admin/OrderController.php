<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{


    public function index()
    {
        $orders = Order::get();

        return response()->json($orders, 200);
    }

    public function store(Request $request) {}


    public function show(string $id)
    {

        try {
            $order = Order::find($id);

            if (!$order) {
                return response()->json([
                    "status" => "failed",
                    "message" => "No order was found with ID " . $id,
                ], 404);
            }

            return response()->json($order, 200);
        } catch (\Exception) {
            return response()->json([
                "status" => "failed",
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }


    public function update(Request $request, string $id)
    {

        $validated = $request->validate([
            "quantity" => "sometimes|required|numeric",
            "total_price" => "sometimes|required|numeric",
            "status" => "sometimes|required|in:pending,processing,completed",
            "payment_status" => "sometimes|required|in:pending,processing,completed",
            "modification_request" => "sometimes|required",
            "transaction_reference" => "sometimes|required",
            "order_date" => "sometimes|required",
            "delivery_date" => "sometimes|required",
        ]);

        try {

            $order = Order::find($id);

            if (!$order) {
                return response()->json([
                    "status" => "failed",
                    "message" => "No order was found with ID " . $id,
                ], 404);
            }

            $order->update($validated);

            if ($order->wasChanged()) {
                return response()->json([
                    "status" => "success",
                    "message" => "Order has been successfully updated",
                    "order" => $order,
                ], 200);
            } else {
                return response()->json([
                    "status" => "failed",
                    "message" => "No changes were made to the order",
                ], 200);
            }
        } catch (\Exception) {
            return response()->json([
                "status" => "failed",
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }

    public function destroy(string $id)
    {

        try {
            $order = Order::find($id);

            if (!$order) {
                return response()->json([
                    "message" => "No order was found with ID " . $id,
                ], 404);
            }

            $deleted =  $order->delete();

            if ($deleted) {
                return response()->json([
                    "message" => "order has been successfully deleted",
                ], 200);
            } else {
                return response()->json([
                    "message" => "Something went wrong, order was not successfully deleted",
                ],422);
            }
        } catch (\Exception) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }



    // public function search(string $keyword)
    // {

    //     try {
    //         $gig = Gig::where("title", "%" . $keyword . "%")->where("description", "%" . $keyword . "%")->get();

    //         if (!$gig) {
    //             return response()->json([
    //                 "message" => "Your search " . $keyword . " didn't return any result.",
    //             ], 404);
    //         }
    //     } catch (\Exception) {
    //         return response()->json([
    //             "message" => "An unexpected error occurred. Please try again later.",
    //         ], 500);
    //     }
    // }
}
