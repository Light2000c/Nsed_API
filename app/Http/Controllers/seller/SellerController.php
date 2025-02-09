<?php

namespace App\Http\Controllers\seller;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;

class SellerController extends Controller
{

    public function index()
    {
        return Seller::get();
    }

    public function show(string $id)
    {

        try {

            $seller = Seller::find($id);

            if (!$seller) {
                return response()->json([
                    "status" => "failed",
                    "message" => "No seller was found with id " . $id,
                ], 404);
            }

            return $seller;
        } catch (\Exception $e) {
            return response()->json([
                "status" => "failed",
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }



    public function register(Request $request)
    {

        $validated = $request->validate([
            "display_name" => "required|string|max:255",
            "category" => "required|string|max:255",
            "bio" => "required|string|max:1000",
        ]);


        try {

            $user = $request->user();

            if ($user->seller()->exists()) {
                return response()->json([
                    "status" => "failed",
                    "message" => "User already has a selling account"
                ], 409);
            }

            $seller = $user->seller()->create($validated);

            if ($seller) {
                return response()->json([
                    "status" => "success",
                    "message" => "Seller account successfully created",
                    "seller" => $seller
                ], 201);
            } else {
                return response()->json([
                    "status" => "failed",
                    "message" => "Something went wrong. Seller account could not be created."
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                "status" => "failed",
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }



    public function update(Request $request)
    {

        $validated = $request->validate([
            "display_name" => "sometimes|required",
            "category" => "sometimes|required",
            "bio" => "sometimes|required",
        ]);


        if (empty($validated)) {
            return response()->json([
                "status" => "failed",
                "message" => "No data provided for update.",
            ], 400);
        }

        try {

            $user = $request->user();

            $seller = $user->seller;

            if (!$seller) {
                return response()->json([
                    "status" => "failed",
                    "message" => "No seller account found for the authenticated user",
                ], 404);
            }

            $updated = $seller->update($validated);


            if ($updated) {
                return response()->json([
                    "status" => "success",
                    "message" => "Seller details have been successfuly updated",
                    "seller" => $seller,
                ], 200);
            } else {
                return response()->json([
                    "message" => "Seller details could not be updated. Please try again.",
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }
}
