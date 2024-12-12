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
        $gig_files = GigFile::get();

        return $gig_files;
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            "gig_id" => "required|numeric",
            "media_url" => "required",
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

            $gig_file = $gig->file()->create($validated);

            if ($gig_file) {
                return response()->json([
                    "message" => "Gig file has been successfully added",
                    "gig_file" => $gig_file
                ]);
            } else {
                return response()->json([
                    "message" => "Something went wrong, gig file was not succesfully added"
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

            $gig_file = GigFile::find($id);

            if (!$gig_file) {
                return response()->json([
                    "message" => "No gig file found with id " . $id,
                ]);
            }

            return $gig_file;
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Internal server error",
            ]);
        }
    }


    public function update(Request $request, string $id)
    {

        $rules =  [
            "media_url" => "required"
        ];

        $validated = $request->validate($rules);

        try {


            $gig_file = GigFile::find($id);

            if (!$gig_file) {
                return response()->json([
                    "message" => "No gig file found with id " . $id,
                ]);
            }

            $seller = $request->user()->seller()->first();


            if (!$seller && !$gig_file->gig()->seller()->is($seller)) {
                return response()->json([
                    "message" => "Invalid user making the request",
                ], 400);
            }

            $updated = $gig_file->update($validated);

            if ($updated) {
                return response()->json([
                    "message" => "Gig file has been successfully updated",
                ], 200);
            } else {
                return response()->json([
                    "message" => "Something went wrong, gig file was not successfully updated",
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

            $gig_file = GigFile::find($id);

            if (!$gig_file) {
                return response()->json([
                    "message" => "No gig file found with id " . $id,
                ]);
            }

            $seller = $request->user()->seller()->first();


            if (!$seller && !$gig_file->gig()->seller()->is($seller)) {
                return response()->json([
                    "message" => "Invalid user making the request",
                ], 400);
            }


            $deleted = $gig_file->delete();


            if ($deleted) {
                return response()->json([
                    "message" => "Gig file has been successfully deleted",
                ], 200);
            } else {
                return response()->json([
                    "message" => "Something went wrong, gig file was not successfully deleted",
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Internal server error" . $e->getMessage(),
            ]);
        }
    }
}
