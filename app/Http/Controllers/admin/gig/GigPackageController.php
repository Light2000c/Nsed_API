<?php

namespace App\Http\Controllers\admin\gig;

use App\Models\Gig;
use App\Models\GigPackage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GigPackageController extends Controller
{

    public function index()
    {
        $gigPackages = GigPackage::all();

        return response()->json([
            "data" => $gigPackages
        ]);
    }


    public function store(Request $request) {}


    public function show(string $id)
    {
        try {

            $gigPackage = GigPackage::find($id);

            if (!$gigPackage) {
                return response()->json()->response([
                    "status" => "failed",
                    "message" =>  "No gig package found with ID " . $id,
                ]);
            }

            return response()->json([
                "status" => "success",
                "gig_package" => $gigPackage
            ]);
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

        if (empty($validated)) {
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
                    "message" => "No gig package found with ID " . $id,
                ], 404);
            }

            $gig_package->update($validated);

            if ($gig_package->wasChanged()) {
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


    public function destroy(string $id)
    {
        try {

            $gig_package = GigPackage::find($id);

            if (!$gig_package) {
                return response()->json([
                    "status" => "failed",
                    "message" => "No gig package found with ID " . $id,
                ], 404);
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
