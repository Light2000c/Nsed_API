<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index()
    {
        try {

            $categories = Category::get();

            return $categories;
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Internal server error"
            ]);
        }
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => "required",
        ]);

        try {

            $created = Category::create($validated);

            if ($created) {
                return response()->json([
                    "message" => "Category has been successfuly created."
                ]);
            } else {
                return response()->json([
                    "message" => "Category was not successfuly created."
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Internal server error"
            ]);
        }
    }


    public function show(string $id)
    {
        try {

            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    "message" => "No category was found with id " . $id,
                ]);
            }

            return $category;
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Internal server error"
            ]);
        }
    }


    public function update(Request $request, string $id)
    {

        $validated = $request->validate([
            "name" => "required",
        ]);

        try {

            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    "message" => "No category was found with id " . $id
                ]);
            }

            $updated = $category->update($validated);

            if ($updated) {
                return response()->json([
                    "message" => "Category has been successfuly created."
                ]);
            } else {
                return response()->json([
                    "message" => "Category was not successfuly created."
                ]);
            }


        } catch (\Exception $e) {
            return response()->json([
                "message" => "Internal server error"
            ]);
        }
    }


    public function destroy(string $id)
    {
        try{

            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    "message" => "No category was found with id " . $id
                ]);
            }

            $deleted = $category->delete();

            if ($deleted) {
                return response()->json([
                    "message" => "Category has been successfuly deleted."
                ]);
            } else {
                return response()->json([
                    "message" => "Category was not successfuly deleted."
                ]);
            }
            

        }catch(\Exception $e){
            return response()->json([
                "message" => "Internal server error"
            ]);
        }
    }
}
