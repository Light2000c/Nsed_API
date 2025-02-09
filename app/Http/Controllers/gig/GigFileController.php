<?php

namespace App\Http\Controllers\gig;

use App\Http\Controllers\Controller;
use App\Models\Gig;
use App\Models\GigFile;
use Illuminate\Http\Request;

class GigFileController extends Controller
{
    public function index()
    {
        $gig_files = GigFile::with("gig")->get();

        return $gig_files;
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            "gig_id" => "required|numeric",
            "media_url" => "required|url",
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


            if (!$seller || !$gig->seller->is($seller)) {
                return response()->json([
                    "status" => "failed",
                    "message" => "Invalid user making the request."
                ], 403);
            }

            $gig_file = $gig->file()->create($validated);

            if ($gig_file) {
                return response()->json([
                    "status" => "success",
                    "message" => "Gig file has been successfully added",
                    "gig_file" => $gig_file
                ], 201);
            } else {
                return response()->json([
                    "status" => "failed",
                    "message" => "Something went wrong, gig file was not succesfully added"
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

            $gig_file = GigFile::with("gig")->find($id);

            if (!$gig_file) {
                return response()->json([
                    "status" => "failed",
                    "message" => "No gig file found with ID " . $id,
                ], 404);
            }

            return $gig_file;
        } catch (\Exception $e) {
            return response()->json([
                "status" => "failed",
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }


    public function update(Request $request, string $id)
    {

        $validated = $request->validate([
            "media_url" => "sometimes|required|url"
        ]);

        if (empty(array_filter($validated))) {
            return response()->json([
                "status" => "failed",
                "message" => "No data provided for update.",
            ], 400);
        }


        try {

            $gig_file = GigFile::find($id);

            if (!$gig_file) {
                return response()->json([
                    "status" => "failed",
                    "message" => "No gig file found with ID " . $id,
                ], 404);
            }

            $seller = $request->user()->seller()->first();


            if (!$seller || !$gig_file->gig->seller()->is($seller)) {
                return response()->json([
                    "status" => "failed",
                    "message" => "Invalid user making the request",
                ], 403);
            }

            $updated = $gig_file->update($validated);

            if ($updated) {
                return response()->json([
                    "status" => "success",
                    "message" => "Gig file has been successfully updated",
                    "gig_file" => $gig_file
                ], 200);
            } else {
                return response()->json([
                    "status" => "failed",
                    "message" => "Gig file update processed but no fields were changed.",
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

            $gig_file = GigFile::find($id);

            if (!$gig_file) {
                return response()->json([
                    "status" => "failed",
                    "message" => "No gig file found with ID " . $id,
                ], 404);
            }

            $seller = $request->user()->seller()->first();


            if (!$seller || !$gig_file->gig->seller()->is($seller)) {
                return response()->json([
                    "status" => "failed",
                    "message" => "Invalid user making the request",
                ], 403);
            }

            $deleted = $gig_file->delete();


            if ($deleted) {
                return response()->json([
                    "status" => "success",
                    "message" => "Gig file has been successfully deleted",
                ], 200);
            } else {
                return response()->json([
                    "status" => "failed",
                    "message" => "Gig file deletion failed. Please try again.",
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
