<?php

namespace App\Http\Controllers\gig;

use App\Http\Controllers\Controller;
use App\Models\Category;
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
            $category = Category::find($validated["category_id"]);

            if (!$category) {
                return response()->json([
                    "status" => "failed",
                    "message" =>  "No category found with ID ". $validated["category_id"],
                ], 404);
            }

            $seller = $request->user()->seller()->first();

            if (!$seller) {
                return response()->json([
                    "status" => "failed",
                    "message" =>  "User doesn't have a seller account yet. Please create one first."
                ], 404);
            }

            $gig = $seller->gig()->create($validated);

            if ($gig) {
                return response()->json([
                    "status" => "success",
                    "message" => "Gig has been successfully created",
                    "gig" => $gig
                ], 201);
            } else {
                return response()->json([
                    "status" => "failed",
                    "message" => "Something went wrong, gig was not succesfully created"
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

            $gig = Gig::with("package", "file", "review")->find($id);

            if (!$gig) {
                return response()->json([
                    "status" => "failed",
                    "message" => "No gig found with id " . $id,
                ], 404);
            }

            return $gig;
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
            "title" => "sometimes|required",
            "description" => "sometimes|required",
            "status" => "sometimes|required|in:pending,completed,canceled",
            "view_count" => "sometimes|required|numeric",
            "order_count" => "sometimes|required|numeric",
            "terms_of_service" => "sometimes|required",
        ];

        $validated = $request->validate($rules);

        if (empty(array_filter($validated))) {
            return response()->json([
                "status" => "failed",
                "message" => "No data provided for update.",
            ], 400);
        }


        try {

            $gig = Gig::find($id);

            if (!$gig) {
                return response()->json([
                    "status" => "failed",
                    "message" => "No gig found with ID " . $id,
                ], 404);
            }

            $seller = $request->user()->seller()->first();


            if (!$seller || !$gig->seller->is($seller)) {
                return response()->json([
                    "status" => "failed",
                    "message" => "Invalid user making the request",
                ], 403);
            }

            $updated = $gig->update($validated);

            if ($updated) {
                return response()->json([
                    "status" => "success",
                    "message" => "Gig has been successfully updated",
                ], 200);
            } else {
                return response()->json([
                    "status" => "failed",
                    "message" => "Something went wrong, gig was not successfully updated",
                ], 500);
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

            $gig = Gig::find($id);

            if (!$gig) {
                return response()->json([
                    "status" => "failed",
                    "message" => "No gig found with ID " . $id,
                ], 404);
            }

            $seller = $request->user()->seller()->first();


            if (!$seller || !$gig->seller->is($seller)) {
                return response()->json([
                    "status" => "failed",
                    "message" => "Invalid user making the request",
                ], 403);
            }

            $deleted = $gig->delete();


            if ($deleted) {
                return response()->json([
                    "status" => "success",
                    "message" => "Gig has been successfully deleted",
                ], 200);
            } else {
                return response()->json([
                    "status" => "failed",
                    "message" => "Something went wrong, gig was not successfully deleted",
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                "status" => "failed",
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }



    public function search(string $keyword)
    {

        try {

            $gig = Gig::with("package", "file", "review")->where("title", "LIKE", "%" . $keyword . "%")->get();

            if (!$gig) {
                return response()->json([
                    "status" => "failed",
                    "message" => "Search with keyword " . $keyword . " didn't return any result",
                ]);
            }

            return $gig;
        } catch (\Exception $e) {
            return response()->json([
                "status" => "failed",
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }
}
