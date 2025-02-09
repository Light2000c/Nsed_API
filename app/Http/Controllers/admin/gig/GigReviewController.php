<?php

namespace App\Http\Controllers\admin\gig;

use App\Http\Controllers\Controller;
use App\Models\Gig;
use App\Models\GigReview;
use Illuminate\Http\Request;

class GigReviewController extends Controller
{
    public function index()
    {
        $gigReviews = GigReview::all();

        return response()->json([
            "status" => "success",
            "data" => $gigReviews
        ]);
    }


    public function store(Request $request) {}


    public function show(string $id)
    {
        try {

            $gigReview = GigReview::find($id);

            if (!$gigReview) {
                return response()->json()->response([
                    "status" => "failed",
                    "message" =>  "No gig review found with ID " . $id,
                ]);
            }

            return response()->json([
                "status" => "success",
                "gig_review" => $gigReview
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
            "rating" => "sometimes|required|integer|min:1|max:5",
            "comment" => "sometimes|required"
        ];

        $validated = $request->validate($rules);

        if (empty($validated)) {
            return response()->json([
                "status" => "failed",
                "message" => "No data provided for update.",
            ], 400);
        }

        try {

            $gig_review = GigReview::find($id);

            if (!$gig_review) {
                return response()->json([
                    "status" => "failed",
                    "message" => "No gig review found with ID " . $id,
                ], 404);
            }

            $gig_review->update($validated);

            if ($gig_review->wasChanged()) {
                return response()->json([
                    "status" => "success",
                    "message" => "Gig package has been successfully updated",
                    "gig_package" => $gig_review
                ], 200);
            } else {
                return response()->json([
                    "status" => "failed",
                    "message" => "Gig review update processed but no fields were changed.",
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

            $gig_review = GigReview::find($id);

            if (!$gig_review) {
                return response()->json([
                    "status" => "failed",
                    "message" => "No gig review found with ID " . $id,
                ], 404);
            }

            $deleted = $gig_review->delete();

            if ($deleted) {
                return response()->json([
                    "status" => "success",
                    "message" => "Gig review has been successfully deleted",
                ], 200);
            } else {
                return response()->json([
                    "status" => "failed",
                    "message" => "Gig review deletion failed. Please try again.",
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
