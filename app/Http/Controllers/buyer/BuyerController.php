<?php

namespace App\Http\Controllers\buyer;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use Illuminate\Http\Request;

class BuyerController extends Controller
{
    public function index()
    {
        $buyers = Buyer::get();

        return $buyers;
    }

    public function show(string $id)
    {

        try {

            $buyer = Buyer::find($id);

            if (!$buyer) {
                return response()->json([
                    "message" => "No buyer was found with id " . $id,
                ], 404);
            }

            return $buyer;
        } catch (\Exception $e) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }



    public function register(Request $request)
    {

        try {

            $user = $request->user();

            $existing_buyer = Buyer::where("user_id", $user->id)->exists();

            if ($existing_buyer) {
                return response()->json([
                    "message" => "User already has a buyer account"
                ], 403);
            }

            $buyer = $user->buyer()->create();

            if ($buyer) {
                return response()->json([
                    "message" => "Buyer account successfully created",
                    "buyer" => $buyer
                ], 201);
            } else {
                return response()->json([
                    "message" => "Something went wrong, buyer account was not successfully created"
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }


    public function update(Request $request, string $id)
    {

        $validated = $request->validate([
            "total_purchases" => "sometimes|required|numeric",
            "spending" => "sometimes|required|numeric",
        ]);

        if (empty($validated)) {
            return response()->json([
                "message" => "No data provided for update.",
            ], 400);
        }


        try {

            $user = $request->user();

            $buyer = $user->buyer()->find($id);

            if (!$buyer) {
                return response()->json([
                    "message" => "No buyer was found with ID " . $id,
                ], 404);
            }

            $updated = $buyer->update($validated);

            if ($updated) {
                return response()->json([
                    "message" => "Buyer details have been successfully updated",
                    "buyer" => $buyer,
                ], 200);
            } else {
                return response()->json([
                    "message" => "No changes were made to the buyer details.",
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }

    public function delete(Request $request, string $id)
    {

        try {

            $user = $request->user();

            $buyer = $user->buyer()->find($id);

            if (!$buyer) {
                return response()->json([
                   "message" => "No buyer was found with ID: " . $id,
                ], 404);
            }


            $deleted = $buyer->delete();

            if ($deleted) {
                return response()->json([
                    "message" => "Buyer account has been successfully deleted",
                ], 200);
            } else {
                return response()->json([
                    "message" => "Buyer account was not successfully deleted",
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }
}
