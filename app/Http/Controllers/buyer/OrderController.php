<?php

namespace App\Http\Controllers\buyer;

use App\Http\Controllers\Controller;
use App\Models\Gig;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{

    public function index(Request $request)
    {
        $user = $request->user();

        $orders = $user->buyer->order()->with("gig")->get();

        return $orders;
    }


    public function store(Request $request)
    {

        $reference =  Str::uuid()->toString();

        $validated = $request->validate([
            "seller_id" => "required|exists:sellers,id",
            "gig_id" => "required|exists:gigs,id",
            "total_price" => "required|numeric",
        ]);

        try {

            $user = $request->user();

            $gig = Gig::find($request->gig_id);

            $buyer = $user->buyer()->first();

            if (!$buyer) {
                return response()->json([
                    "status" => "failed",
                    "message" => "you don't have a buyer account to complete this request"
                ], 404);
            }

            $seller = $gig->seller()->first();

            if (!$seller) {
                return response()->json([
                    "status" => "failed",
                    "message" => "Gig doesn't belong to the seller."
                ], 403);
            }

            if ($gig->seller()->is($user->seller)) {
                return response()->json([
                    "status" => "failed",
                    "message" => "User cannot order their own gig."
                ], 400);
            }

            $validated["transaction_reference"] = $reference;

            $order = $buyer->order()->create($validated);

            if ($order) {
                return response()->json([
                    "status" => "success",
                    "message" => "Order has been successfully created",
                    "order" => $order
                ], 201);
            } else {
                return response()->json([
                    "status" => "failed",
                    "message" => "Something went wrong, Order was not successfully created"
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                "status" => "failed",
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }


    public function show(Request $request, string $id)
    {

        try {

            $user = $request->user();

            $order = $user->buyer->order()->with("gig")->find($id);

            if (!$order) {
                return response()->json([
                    "status" => "failed",
                    "message" => "No order was found with Id " . $id,
                ], 404);
            }

            return $order;
        } catch (\Exception $e) {
            return response()->json([
                "status" => "failed",
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }


    public function update(Request $request, string $id)
    {

        $validated = $request->validate([
            "quantity" => "sometimes|required",
            "total_price" => "sometimes|required|numeric",
            "status" => "sometimes|required|in:pending,processing,approved",
            "payment_status" => "sometimes|required|in:pending,processing,approved",
            "modification_request" => "sometimes|required",
            "order_date" => "sometimes|required",
            "delivery_date" => "sometimes|required",
        ]);


        if (empty($validated)) {
            return response()->json([
                "status" => "failed",
                "message" => "No data provided for update.",
            ], 400);
        }

        try {
            $order = Order::find($id);

            if (!$order) {
                return response()->json([
                    "status" => "failed",
                    "message" => "No order was found with ID " . $id,
                ], 404);
            }

            $buyer = $request->user()->buyer()->first();

            if (!$buyer || !$order->buyer()->is($buyer)) {
                return response()->json([
                    "status" => "failed",
                    "message" => "Unauthorized access to this order.",
                ], 403);
            }

            $updated = $order->update($validated);

            if ($updated) {
                return response()->json([
                    "success" => "failed",
                    "message" => "Order has been successfully updated",
                ], 200);
            } else {
                return response()->json([
                    "status" => "failed",
                    "message" => "No changes were made to the order.",
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                "status" => "failed",
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }


    public function destroy(Request $request, string $id)
    {
        try {

            $order = Order::find($id);

            if (!$order) {
                return response()->json([
                    "status" => "failed",
                    "message" => "No order was found with ID " . $id,
                ], 404);
            }

            $buyer = $request->user()->buyer()->first();

            if (!$buyer || !$order->buyer()->is($buyer)) {
                return response()->json([
                    "status" => "failed",
                    "message" => "Unauthorized access to this order.",
                ], 403);
            }

            $deleted = $order->delete();

            if ($deleted) {
                return response()->json([
                    "status" => "success",
                    "message" => "Order has been successfully deleted",
                ], 200);
            } else {
                return response()->json([
                    "status" => "failed",
                    "message" => "Order could not be deleted. Please try again later.",
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                "status" => "failed",
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }
}
