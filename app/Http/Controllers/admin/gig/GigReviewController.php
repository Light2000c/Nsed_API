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
                    "message" =>  "No gig review found with ID " . $id,
                ]);
            }

            return response()->json([
                "gig_review" => $gigReview
            ]);
        } catch (\Exception $e) {
            return response()->json([
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
                "message" => "No data provided for update.",
            ], 400);
        }

        try {

            $gig_review = GigReview::find($id);

            if (!$gig_review) {
                return response()->json([
                    "message" => "No gig review found with ID " . $id,
                ], 404);
            }

            $gig_review->update($validated);

            if ($gig_review->wasChanged()) {
                return response()->json([
                    "message" => "Gig package has been successfully updated",
                    "gig_package" => $gig_review
                ], 200);
            } else {
                return response()->json([
                    "message" => "Gig review update processed but no fields were changed.",
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
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
                    "message" => "No gig review found with ID " . $id,
                ], 404);
            }

            $deleted = $gig_review->delete();

            if ($deleted) {
                return response()->json([
                    "message" => "Gig review has been successfully deleted",
                ], 200);
            } else {
                return response()->json([
                    "message" => "Gig review deletion failed. Please try again.",
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }
}
