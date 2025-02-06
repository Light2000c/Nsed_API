<?php

namespace App\Http\Controllers\gig;

use App\Models\Gig;
use App\Models\Order;
use App\Models\GigReview;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GigReviewController extends Controller
{
    public function index()
    {
        $gig_reviews = GigReview::with("gig")->get();

        return $gig_reviews;
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            "gig_id" => "required|numeric",
            "rating" => "required|integer|min:1|max:5",
            "comment" => "required",
        ]);

        try {

            $user = $request->user();

            $buyer = $user->buyer;

            if (!$buyer) {
                return response()->json([
                    "message" => "Unauthorized access to this request.",
                ], 403);
            }

            $gig = Gig::with("seller")->find($request->gig_id);

            if (!$gig) {
                return response()->json([
                    "message" => "No gig was found with id " . $request->gig_id,
                ], 404);
            }


            // Check if there's a successful order between the buyer and seller
            if (!$gig->order()->where("buyer_id", $buyer->id)->exists()) {
                return response()->json([
                    "message" => "You don't have a history with the seller, so you can't add a review.",
                ], 400);
            }

            if ($user->review()->where('gig_id', $request->gig_id)->exists()) {
                return response()->json([
                    "message" => "You have already reviewed this gig.",
                ], 400);
            }

            $gig_review = $user->review()->create($validated);

            if ($gig_review) {
                return response()->json([
                    "message" => "Gig review has been successfully added",
                    "gig_review" => $gig_review
                ], 201);
            } else {
                return response()->json([
                    "message" => "Something went wrong, gig review was not successfully added"
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.". $e->getMessage(),
            ], 500);
        }
    }


    public function show(string $id)
    {
        try {

            $gig_review = GigReview::with("gig")->find($id);

            if (!$gig_review) {
                return response()->json([
                    "message" => "No gig review found with id " . $id,
                ], 404);
            }

            return $gig_review;
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
                    "message" => "No gig review found with id " . $id,
                ], 404);
            }

            $user = $request->user();


            if (!$this->canModifyGigReview($gig_review, $user)) {
                return response()->json([
                    "message" => "Invalid user making the request",
                ], 403);
            }

            $updated = $gig_review->update($validated);


            if ($updated) {
                return response()->json([
                    "message" => "Gig review has been successfully updated",
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


    public function destroy(Request $request, string $id)
    {
        try {

            $gig_review = GigReview::find($id);

            if (!$gig_review) {
                return response()->json([
                    "message" => "No gig review found with id " . $id,
                ], 404);
            }

            $user = $request->user();
           

            if (!$this->canModifyGigReview($gig_review, $user)) {
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
                    "message" => "Gig review deletion failed. Please try again.",
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }


    public function canModifyGigReview($gig_review, $user){

        $seller = $user->seller()->first();

        $is_buyer = $gig_review->user()->is($user);
        $is_seller = $seller && $gig_review->gig->seller()->is($seller);

        return !$is_buyer || !$is_seller;
    }
}
