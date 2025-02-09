<?php

namespace App\Http\Controllers\gig;

use App\Http\Controllers\Controller;
use App\Models\Gig;
use App\Models\GigPackage;
use Illuminate\Http\Request;

class GigPackageController extends Controller
{

    public function index()
    {
        $gig_packages = GigPackage::with("gig")->get();

        return $gig_packages;
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            "gig_id" => "required|numeric",
            "package_name" => "required|in:basic,premium,standard",
            "description" => "required",
            "price" => "required|numeric",
            "delivery_time" => "required|integer|min:1",
            "revision_limit" => "required|integer|min:0",
        ]);

        try {

            $gig = Gig::with("seller")->find($request->gig_id);

            if (!$gig) {
                return response()->json([
                    "status" => "failed",
                    "message" => "No gig was found with ID " . $request->gig_id,
                ], 404);
            }

            $seller = $request->user()->seller()->first();


            if (!$seller || !$gig->seller()->is($seller)) {
                return response()->json([
                    "status" => "failed",
                    "message" => "Unauthorized action. You do not own this gig"
                ], 403);
            }

            $gig_package = $gig->package()->create($validated);

            if ($gig_package) {
                return response()->json([
                    "status" => "success",
                    "message" => "Gig package has been successfully created",
                    "gig_package" => $gig_package
                ], 201);
            } else {
                return response()->json([
                    "status" => "failed",
                    "message" => "Something went wrong, gig package was not succesfully created"
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                "status" => "failed",
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }


    public function show(string $id)
    {
        try {

            $gig_package = GigPackage::with("gig")->find($id);

            if (!$gig_package) {
                return response()->json([
                    "status" => "failed",
                    "message" => "No gig package found with id " . $id,
                ], 404);
            }

            return $gig_package;
        } catch (\Exception $e) {
            return response()->json([
                "status" => "failed",
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }


    public function update(Request $request, string $id)
    {

        $rules =  [
            "package_name" => "sometimes|required|in:basic,premium,standard",
            "description" => "sometimes|required",
            "price" => "sometimes|required|numeric",
            "delivery_time" => "sometimes|required|integer|min:1",
            "revision_limit" => "sometimes|required|integer|min:0",
        ];

        $validated = $request->validate($rules);

        if (empty(array_filter($validated))) {
            return response()->json([
                "status" => "failed",
                "message" => "No data provided for update.",
            ], 400);
        }

        try {

            $gig_package = GigPackage::find($id);

            if (!$gig_package) {
                return response()->json([
                    "status" => "failed",
                    "message" => "No gig package found with id " . $id,
                ], 404);
            }

            $seller = $request->user()->seller()->first();


            if (!$seller || !$gig_package->gig->seller()->is($seller)) {
                return response()->json([
                    "status" => "failed",
                    "message" => "Invalid user making the request",
                ], 403);
            }

            $updated = $gig_package->update($validated);

            if ($updated) {
                return response()->json([
                    "status" => "success",
                    "message" => "Gig package has been successfully updated",
                    "gig_package" => $gig_package
                ], 200);
            } else {
                return response()->json([
                    "status" => "failed",
                    "message" => "Gig package update processed but no fields were changed.",
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

            $gig_package = GigPackage::find($id);

            if (!$gig_package) {
                return response()->json([
                    "status" => "failed",
                    "message" => "No gig package found with id " . $id,
                ], 404);
            }

            $seller = $request->user()->seller()->first();


            if (!$seller || !$gig_package->gig->seller()->is($seller)) {
                return response()->json([
                    "status" => "failed",
                    "message" => "Invalid user making the request",
                ], 403);
            }


            $deleted = $gig_package->delete();


            if ($deleted) {
                return response()->json([
                    "status" => "success",
                    "message" => "Gig package has been successfully deleted",
                ], 200);
            } else {
                return response()->json([
                    "status" => "failed",
                    "message" => "Gig package deletion failed. Please try again.",
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                "status" => "failed",
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }
}
