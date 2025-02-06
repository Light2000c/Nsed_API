<?php

namespace App\Http\Controllers\admin\gig;

use App\Http\Controllers\Controller;
use App\Models\Gig;
use App\Models\GigFile;
use Illuminate\Http\Request;

class GigFileController extends Controller
{
    public function index()
    {
        $gigFiles = GigFile::all();

        return response()->json([
            "gig_files" => $gigFiles
        ]);
    }


    public function store(Request $request) {}


    public function show(string $id)
    {
        try {

            $gigFile = GigFile::find($id);

            if (!$gigFile) {
                return response()->json()->response([
                    "message" =>  "No gig file found with ID " . $id,
                ]);
            }

            return response()->json([
                "gig_file" => $gigFile
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }


    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            "media_url" => "sometimes|required|url"
        ]);


        if (empty($validated)) {
            return response()->json([
                "message" => "No data provided for update.",
            ], 400);
        }

        try {

            $gig_file = GigFile::find($id);

            if (!$gig_file) {
                return response()->json([
                    "message" => "No gig file found with ID " . $id,
                ], 404);
            }

            $gig_file->update($validated);

            if ($gig_file->wasChanged()) {
                return response()->json([
                    "message" => "Gig file has been successfully updated",
                    "gig_package" => $gig_file
                ], 200);
            } else {
                return response()->json([
                    "message" => "Gig file update processed but no fields were changed.",
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

            $gig_file = GigFile::find($id);

            if (!$gig_file) {
                return response()->json([
                    "message" => "No gig file found with ID " . $id,
                ], 404);
            }

            $deleted = $gig_file->delete();

            if ($deleted) {
                return response()->json([
                    "message" => "Gig file has been successfully deleted",
                ], 200);
            } else {
                return response()->json([
                    "message" => "Gig file deletion failed. Please try again.",
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }
}
