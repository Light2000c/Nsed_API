<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Gig;
use Illuminate\Http\Request;

class GigController extends Controller
{

    public function index()
    {
        $gigs = Gig::with("package", "review", "file")->get();

        return response()->json($gigs, 200);
    }

    public function store(Request $request) {}


    public function show(string $id)
    {

        try {
            $gig = Gig::find($id);

            if (!$gig) {
                return response()->json([
                    "message" => "No gig was found with ID " . $id,
                ], 404);
            }

            return response()->json($gig, 200);
        } catch (\Exception) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }


    public function update(Request $request, string $id)
    {

        $validated = $request->validate([
            "title" => "sometimes|required",
            "description" => "sometimes|required|string",
            "status" => "sometimes|required|in:pending,completed,canceled",
            "views_count" => "sometimes|required|numeric",
            "orders_count" => "sometimes|required|numeric",
            "terms_of_service" => "sometimes|required",
        ]);

        try {
            $gig = Gig::find($id);

            if (!$gig) {
                return response()->json([
                    "message" => "No gig was found with Id " . $id,
                ], 404);
            }

            $gig->update($validated);

            if ($gig->wasChanged()) {
                return response()->json([
                    "message" => "Gig has been successfully updated",
                    "gig" => $gig,
                ], 200);
            } else {
                return response()->json([
                    "message" => "No changes were made to the gig.",
                ], 200);
            }
        } catch (\Exception) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }

    public function destroy(string $id)
    {

        try {
            $gig = Gig::find($id);

            if (!$gig) {
                return response()->json([
                    "message" => "No gig was found with ID " . $id,
                ], 404);
            }

            $deleted =  $gig->delete();

            if ($deleted) {
                return response()->json([
                    "message" => "Gig has been successfully deleted",
                ], 200);
            } else {
                return response()->json([
                    "message" => "Something went wrong, gig was not successfully deleted",
                ], 422);
            }
        } catch (\Exception) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }



    public function search(string $keyword)
    {

        try {
            $gigs = Gig::where("title", "LIKE", "%" . $keyword . "%")->orWhere("description", "LIKE", "%" . $keyword . "%")->get();

            if ($gigs->isEmpty()) {
                return response()->json([
                    "message" => "Your search " . $keyword . " didn't return any result.",
                ], 404);
            }


            return response()->json($gigs, 200);
        } catch (\Exception) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }
}
