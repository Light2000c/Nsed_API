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
                    "message" => "No seller was found with id " . $id,
                ]);
            }

            return $seller;
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Internal server error " . $e->getMessage(),
            ], 500);
        }
    }



    public function register(Request $request)
    {

        $validated = $request->validate([
            "display_name" => "required",
            "category" => "required",
            "bio" => "required",
        ]);


        try {

            $user = $request->user();

            $existing_seller = Seller::where("user_id", $user->id)->first();

            if ($existing_seller) {
                return response()->json([
                    "message" => "User already have a seller account"
                ]);
            }

            $seller = $user->seller()->create($validated);

            if ($seller) {
                return response()->json([
                    "message" => "Seller account successfully created",
                    "seller" => $seller
                ], 200);
            } else {
                return response()->json([
                    "message" => "Somethig went wrong, seller account was not successfully created"
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Internal server error " . $e->getMessage(),
            ], 500);
        }
    }


    public function update(Request $request, string $id)
    {

        $validated = $request->validate([
            "display_name" => "required",
            "category" => "required",
            "bio" => "required",
        ]);


        try {

            $user = $request->user();


            $seller = Seller::find($id);

            if (!$seller) {
                return response()->json([
                    "message" => "No seller was found with id " . $id,
                ]);
            }

            if (!$seller->user()->is($user)) {
                return response()->json([
                    "message" => "Invalid user making the request",
                ], 400);
            }

            $updated = $seller->update($validated);

            if ($updated) {
                return response()->json([
                    "message" => "Seller details has been successfuly updated",
                ], 200);
            } else {
                return response()->json([
                    "message" => "Seller details was not successfuly updated",
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Internal server error " . $e->getMessage(),
            ], 500);
        }
    }
}
