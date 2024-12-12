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
        $gig_packages = GigPackage::get();

        return $gig_packages;
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            "gig_id" => "required|numeric",
            "package_name" => "required|in:basic,premium,standard",
            "description" => "required",
            "price" => "required",
            "delivery_time" => "required",
            "revision_limit" => "required",
        ]);

        try {

            $gig = Gig::find($request->gig_id);

            if (!$gig) {
                return response()->json([
                    "message" => "No gig was found with id " . $request->gig_id,
                ]);
            }

            $seller = $request->user()->seller()->first();


            if (!$seller && !$gig->seller()->is($seller)) {
                return response()->json([
                    "message" => "Invalid user making the request."
                ]);
            }

            $gig_package = $gig->package()->create($validated);

            if ($gig_package) {
                return response()->json([
                    "message" => "Gig package has been successfully created",
                    "gig_package" => $gig_package
                ]);
            } else {
                return response()->json([
                    "message" => "Something went wrong, gig package was not succesfully created"
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Internal server error ",
            ]);
        }
    }


    public function show(string $id)
    {
        try {

            $gig_package = GigPackage::find($id);

            if (!$gig_package) {
                return response()->json([
                    "message" => "No gig package found with id " . $id,
                ]);
            }

            return $gig_package;
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Internal server error",
            ]);
        }
    }


    public function update(Request $request, string $id)
    {

        $rules =  [
            "package_name" => "required|in:basic,premium,standard"
        ];

        if ($request->has("description")) {
            $rules["description"] = "required";
        }

        if ($request->has("price")) {
            $rules["price"] = "required";
        }

        if ($request->has("delivery_time")) {
            $rules["delivery_time"] = "required";
        }

        if ($request->has("revision_limit")) {
            $rules["revision_limit"] = "required";
        }

        $validated = $request->validate($rules);

        try {


            $gig_package = GigPackage::find($id);

            if (!$gig_package) {
                return response()->json([
                    "message" => "No gig package found with id " . $id,
                ]);
            }

            $seller = $request->user()->seller()->first();


            if (!$seller && !$gig_package->gig()->seller()->is($seller)) {
                return response()->json([
                    "message" => "Invalid user making the request",
                ], 400);
            }

            $updated = $gig_package->update($validated);

            if ($updated) {
                return response()->json([
                    "message" => "Gig package has been successfully updated",
                ], 200);
            } else {
                return response()->json([
                    "message" => "Something  went wrong, gig package was not successfully updated",
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Internal server error",
            ]);
        }
    }


    public function destroy(Request $request, string $id)
    {
        try {

            $gig_package = GigPackage::find($id);

            if (!$gig_package) {
                return response()->json([
                    "message" => "No gig package found with id " . $id,
                ]);
            }

            $seller = $request->user()->seller()->first();


            if (!$seller && !$gig_package->gig()->seller()->is($seller)) {
                return response()->json([
                    "message" => "Invalid user making the request",
                ], 400);
            }


            $deleted = $gig_package->delete();


            if ($deleted) {
                return response()->json([
                    "message" => "Gig package has been successfully deleted",
                ], 200);
            } else {
                return response()->json([
                    "message" => "Something went wrong, gig package was not successfully deleted",
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Internal server error" . $e->getMessage(),
            ]);
        }
    }
}
