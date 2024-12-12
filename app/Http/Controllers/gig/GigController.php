<?php

namespace App\Http\Controllers\gig;

use App\Http\Controllers\Controller;
use App\Models\Gig;
use Error;
use Illuminate\Http\Request;

class GigController extends Controller
{

    public function index()
    {
        $gigs = Gig::with("package", "file", "review")->get();

        return $gigs;
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            "category_id" => "required|numeric",
            "title" => "required",
            "description" => "required",
        ]);

        try {

            $seller = $request->user()->seller()->first();

            if (!$seller) {
                return response()->json([
                    "message" => "User doesn't have a seller account yet"
                ]);
            }

            $gig = $seller->gig()->create($validated);

            if ($gig) {
                return response()->json([
                    "message" => "Gig has been successfully created",
                    "gig" => $gig
                ]);
            } else {
                return response()->json([
                    "message" => "Something went wrong, gig was not succesfully created"
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Internal server error",
            ]);
        }
    }


    public function show(string $id)
    {

        try {

            $gig = Gig::find($id);

            if (!$gig) {
                return response()->json([
                    "message" => "No gig found with id " . $id,
                ]);
            }

            return $gig;
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Internal server error",
            ]);
        }
    }



    public function update(Request $request, string $id)
    {
        $rules =  [
            "title" => "required",
            "description" => "required",
        ];

        if ($request->has("status")) {
            $rules["status"] = "required|in:pending,completed,canceled";
        }
        if ($request->has("views_count")) {
            $rules["status"] = "required|numeric";
        }
        if ($request->has("orders_count")) {
            $rules["status"] = "required|numeric";
        }
        if ($request->has("terms_of_service")) {
            $rules["status"] = "required";
        }

        $validated = $request->validate($rules);

        try {

            $seller = $request->user()->seller()->first();

            if (!$seller) {
                return response()->json([
                    "message" => "User doesn't have a seller account yet"
                ]);
            }

            $gig = Gig::find($id);

            if (!$gig) {
                return response()->json([
                    "message" => "No gig found with id " . $id,
                ]);
            }

            if (!$gig->seller->is($seller)) {
                return response()->json([
                    "message" => "Invalid user making the request",
                ], 400);
            }

            $updated = $gig->update($validated);

            if ($updated) {
                return response()->json([
                    "message" => "Gig has been successfully updated",
                ], 200);
            } else {
                return response()->json([
                    "message" => "Something went wrong, gig was not successfully updated",
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

            $seller = $request->user()->seller()->first();

            if (!$seller) {
                return response()->json([
                    "message" => "User doesn't have a seller account yet"
                ]);
            }

            $gig = Gig::find($id);

            if (!$gig) {
                return response()->json([
                    "message" => "No gig found with id " . $id,
                ]);
            }

            if (!$gig->seller->is($seller)) {
                return response()->json([
                    "message" => "Invalid user making the request",
                ], 400);
            }

            $deleted = $gig->delete();


            if ($deleted) {
                return response()->json([
                    "message" => "Gig has been successfully deleted",
                ], 200);
            } else {
                return response()->json([
                    "message" => "Something went wrong, gig was not successfully deleted",
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Internal server error" . $e->getMessage(),
            ]);
        }
    }


    public function search(string $keyword)
    {

        try {

            $gig = Gig::where("title", "LIKE", "%" . $keyword . "%")->get();

            if (!$gig) {
                return response()->json([
                    "message" => "Search with keyword " . $keyword . " didn't return any result",
                ]);
            }

            return $gig;
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Internal server error",
            ]);
        }
    }
}
