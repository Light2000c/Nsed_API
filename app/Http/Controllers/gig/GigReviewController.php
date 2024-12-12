<?php

namespace App\Http\Controllers\gig;

use App\Http\Controllers\Controller;
use App\Models\Gig;
use App\Models\GigReview;
use Illuminate\Http\Request;

class GigReviewController extends Controller
{
    public function index()
    {
        $gig_reviews = GigReview::get();

        return $gig_reviews;
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            "gig_id" => "required|numeric",
            "rating" => "required|numeric",
            "comment" => "required",
        ]);

        try {

            $user = $request->user();

            $gig = Gig::find($request->gig_id);

            if (!$gig) {
                return response()->json([
                    "message" => "No gig was found with id " . $request->gig_id,
                ]);
            }


            $gig_review = $user->review()->create($validated);

            if ($gig_review) {
                return response()->json([
                    "message" => "Gig review has been successfully added",
                    "gig_file" => $gig_review
                ]);
            } else {
                return response()->json([
                    "message" => "Something went wrong, gig review was not succesfully added"
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

            $gig_review = GigReview::find($id);

            if (!$gig_review) {
                return response()->json([
                    "message" => "No gig review found with id " . $id,
                ]);
            }

            return $gig_review;
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Internal server error",
            ]);
        }
    }


    public function update(Request $request, string $id)
    {

        $rules =  [];

        if ($request->has("rating")) {
            $rules["rating"] = "required|numeric";
        }

        if ($request->has("comment")) {
            $rules["comment"] = "required";
        }

        $validated = $request->validate($rules);

        try {



            $gig_review = GigReview::find($id);

            if (!$gig_review) {
                return response()->json([
                    "message" => "No gig review found with id " . $id,
                ]);
            }

            $user = $request->user();
            $seller = $request->user()->seller()->first();

            //Add check if there's a successful order between the buyer and seller

            $is_buyer = $gig_review->user()->is($user);
            $is_seller = $seller ? $gig_review->gig()->seller()->is($user) : false;

            if (!$is_buyer && !$is_seller) {
                return response()->json([
                    "message" => "Invalid user making the request",
                ], 400);
            }

            $updated = $gig_review->update($validated);


            if ($updated) {
                return response()->json([
                    "message" => "Gig review has been successfully updated",
                ], 200);
            } else {
                return response()->json([
                    "message" => "Something went wrong, gig review was not successfully updated",
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

            $gig_review = GigReview::find($id);

            if (!$gig_review) {
                return response()->json([
                    "message" => "No gig review found with id " . $id,
                ]);
            }

            $user = $request->user();
            $seller = $request->user()->seller()->first();

            $is_buyer = $gig_review->user()->is($user);
            $is_seller = $seller ? $gig_review->gig()->seller()->is($user) : false;

            if (!$is_buyer && !$is_seller) {
                return response()->json([
                    "message" => "Invalid user making the request",
                ], 400);
            }


            $deleted = $gig_review->delete();

            if ($deleted) {
                return response()->json([
                    "message" => "Gig review has been successfully deleted",
                ], 200);
            } else {
                return response()->json([
                    "message" => "Something went wrong, gig review was not successfully deleted",
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Internal server error" . $e->getMessage(),
            ]);
        }
    }
}
